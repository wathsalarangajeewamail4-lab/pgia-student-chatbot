<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected string $daemonUrl;

    public function __construct()
    {
        $this->daemonUrl = env('HYBRID_RAG_DAEMON_URL', 'http://127.0.0.1:8003');
    }

    public function index()
    {
        $totalDocuments = Document::where('status', 'active')->count();
        $totalChunks = DB::table('knowledge_base')->count();
        $recentDocuments = Document::latest()->take(5)->get();
        $recentAudits = AuditLog::latest('created_at')->take(6)->get();

        // Check live daemon health
        $daemonHealth = [
            'status' => 'offline',
            'chunks_loaded' => 0,
            'engine' => 'N/A',
            'latency_ms' => null,
        ];

        try {
            $t0 = microtime(true);
            $resp = Http::timeout(3)->get("{$this->daemonUrl}/health");
            $duration = round((microtime(true) - $t0) * 1000, 1);

            if ($resp->successful()) {
                $data = $resp->json();
                $daemonHealth = [
                    'status' => $data['status'] ?? 'ok',
                    'chunks_loaded' => $data['chunks_loaded'] ?? $totalChunks,
                    'engine' => $data['engine'] ?? 'Hybrid RAG',
                    'latency_ms' => $duration,
                ];
            }
        } catch (\Exception $e) {
            $daemonHealth['error'] = $e->getMessage();
        }

        return view('admin.dashboard', compact(
            'totalDocuments',
            'totalChunks',
            'recentDocuments',
            'recentAudits',
            'daemonHealth'
        ));
    }

    public function journey()
    {
        $journeyJsonPath = base_path('python/pgia_student_journey_map.json');
        $journeyData = [];
        if (file_exists($journeyJsonPath)) {
            $journeyData = json_decode(file_get_contents($journeyJsonPath), true);
        }
        return view('admin.journey', compact('journeyData'));
    }

    public function documents(Request $request)
    {
        $query = Document::query();

        if ($search = $request->input('search')) {
            $query->where('filename', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%");
        }

        $documents = $query->latest()->paginate(15);
        $totalChunks = DB::table('knowledge_base')->count();

        return view('admin.documents', compact('documents', 'totalChunks'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'pdf_file' => [
                'required',
                'file',
                'mimes:pdf',
                'max:51200', // 50MB max
            ],
            'custom_name' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('pdf_file');
        $originalName = $file->getClientOriginalName();
        $customName = $request->input('custom_name');
        
        $cleanFilename = $customName ? Str::slug(pathinfo($customName, PATHINFO_FILENAME)) . '.pdf' : $originalName;
        // Sanitize filename
        $cleanFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $cleanFilename);

        $checksum = hash_file('sha256', $file->getRealPath());
        $fileSize = $file->getSize();

        // Store file in knowledge_documents storage disk/folder
        $storedPath = $file->storeAs('knowledge_documents', $cleanFilename);
        $absolutePath = storage_path('app/' . $storedPath);

        $document = Document::updateOrCreate(
            ['filename' => $cleanFilename],
            [
                'original_name' => $originalName,
                'file_path' => $storedPath,
                'file_size' => $fileSize,
                'mime_type' => 'application/pdf',
                'checksum' => $checksum,
                'status' => 'processing',
                'uploaded_by' => auth()->user()?->email,
            ]
        );

        // Dispatch ingestion & hot-reload to Python FastAPI daemon
        try {
            $response = Http::timeout(120)->post("{$this->daemonUrl}/api/documents/process-pdf", [
                'file_path' => $absolutePath,
                'source_name' => $cleanFilename,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $chunksSaved = $result['chunks_saved'] ?? 0;

                $document->update([
                    'status' => 'active',
                    'chunk_count' => $chunksSaved,
                    'error_message' => null,
                    'last_synced_at' => now(),
                ]);

                AuditLog::record('upload_and_sync', 'document', $cleanFilename, [
                    'chunks_saved' => $chunksSaved,
                    'file_size' => $fileSize,
                    'checksum' => $checksum,
                ]);

                return redirect()->route('admin.documents')->with('success', "Document '{$cleanFilename}' successfully ingested and embedded! ({$chunksSaved} chunks generated and synced to live model with zero downtime).");
            } else {
                $err = $response->json('detail') ?? $response->body();
                $document->update([
                    'status' => 'failed',
                    'error_message' => $err,
                ]);

                return redirect()->route('admin.documents')->with('error', "PDF parsing failed: {$err}");
            }
        } catch (\Exception $e) {
            $document->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return redirect()->route('admin.documents')->with('error', "Ingestion engine error: {$e->getMessage()}");
        }
    }

    public function delete(Document $document)
    {
        $filename = $document->filename;

        // 1. Purge physical file if exists
        if ($document->file_path && Storage::exists($document->file_path)) {
            Storage::delete($document->file_path);
        }

        // 2. Notify Python daemon to purge source and hot-reload memory
        try {
            Http::timeout(15)->post("{$this->daemonUrl}/api/documents/remove-source", [
                'source_name' => $filename,
            ]);
        } catch (\Exception $e) {
            // Fallback: direct SQLite deletion if daemon is offline
            DB::table('knowledge_base')->where('source', $filename)->delete();
        }

        // 3. Delete database record
        $document->delete();

        AuditLog::record('delete_document', 'document', $filename, [
            'original_name' => $document->original_name,
        ]);

        return redirect()->route('admin.documents')->with('success', "Document '{$filename}' and all associated vector embeddings successfully purged from knowledge base.");
    }

    public function syncAll()
    {
        try {
            $resp = Http::timeout(20)->post("{$this->daemonUrl}/api/reload");
            if ($resp->successful()) {
                $data = $resp->json();
                $totalLoaded = $data['total_chunks'] ?? 0;

                // Sync chunk counts in documents table
                $sources = DB::table('knowledge_base')
                    ->select('source', DB::raw('count(*) as count'))
                    ->groupBy('source')
                    ->get();

                foreach ($sources as $s) {
                    Document::where('filename', $s->source)->update([
                        'chunk_count' => $s->count,
                        'status' => 'active',
                        'last_synced_at' => now(),
                    ]);
                }

                AuditLog::record('manual_model_sync', 'model', 'all', [
                    'chunks_loaded' => $totalLoaded,
                ]);

                return back()->with('success', "Model Knowledge Base successfully synchronized! {$totalLoaded} chunks active in memory with zero downtime.");
            }
            return back()->with('error', "Sync failed: " . $resp->body());
        } catch (\Exception $e) {
            return back()->with('error', "Could not reach Hybrid RAG Daemon: {$e->getMessage()}");
        }
    }

    public function previewChunks(Document $document)
    {
        $chunks = DB::table('knowledge_base')
            ->where('source', $document->filename)
            ->select('id', 'chunk_text', 'created_at')
            ->orderBy('id', 'asc')
            ->take(15)
            ->get();

        return response()->json([
            'document' => $document->filename,
            'total_chunks' => $document->chunk_count,
            'preview_count' => $chunks->count(),
            'chunks' => $chunks,
        ]);
    }

    public function auditLogs(Request $request)
    {
        $query = AuditLog::query();

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('target_id', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $auditLogs = $query->latest('created_at')->paginate(20);

        return view('admin.audit', compact('auditLogs'));
    }
}
