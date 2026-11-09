@extends('admin.layout')

@section('title', 'Dashboard Overview')
@section('page-title', 'Knowledge Base & Model Operations Dashboard')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Active Documents -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400">Active Documents</p>
                    <h3 class="text-2xl font-bold text-white mt-1.5">{{ $totalDocuments }}</h3>
                    <p class="text-[11px] text-emerald-400 mt-1 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>7 Handbooks & Circulars</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Vector Chunks -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400">Embedded Chunks</p>
                    <h3 class="text-2xl font-bold text-white mt-1.5">{{ number_format($totalChunks) }}</h3>
                    <p class="text-[11px] text-teal-400 mt-1 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-teal-400"></span>
                        <span>768-dim nomic-embed-text</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-teal-400 border border-teal-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Hybrid Daemon Health -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400">FastAPI RAG Daemon</p>
                    <h3 class="text-2xl font-bold {{ $daemonHealth['status'] === 'ok' ? 'text-emerald-400' : 'text-amber-400' }} mt-1.5">
                        {{ strtoupper($daemonHealth['status']) }}
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                        @if($daemonHealth['latency_ms'])
                            <span class="text-emerald-400 font-mono">{{ $daemonHealth['latency_ms'] }}ms latency</span>
                        @else
                            <span>Port 8003</span>
                        @endif
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl {{ $daemonHealth['status'] === 'ok' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }} flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Hot-Reload Status -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400">In-Memory Sync</p>
                    <h3 class="text-2xl font-bold text-white mt-1.5">Zero-Downtime</h3>
                    <p class="text-[11px] text-emerald-400 mt-1 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>Atomic Pointer Swapping</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid: Documents Overview + Quick Ingestion & Test Box -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left 2 Cols: Active Documents List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-bold text-white">Active Knowledge Documents</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Documents currently powering student RAG search</p>
                    </div>
                    <a href="{{ route('admin.documents') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1">
                        <span>View All & Upload</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 font-semibold pb-2">
                                <th class="pb-3 pl-1">Document Source</th>
                                <th class="pb-3">Chunks</th>
                                <th class="pb-3">Status</th>
                                <th class="pb-3">Last Synced</th>
                                <th class="pb-3 text-right pr-1">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @forelse($recentDocuments as $doc)
                                <tr class="hover:bg-slate-800/30 transition-all">
                                    <td class="py-3.5 pl-1 font-medium text-white flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <span class="truncate max-w-xs" title="{{ $doc->filename }}">{{ $doc->filename }}</span>
                                    </td>
                                    <td class="py-3.5 text-slate-300 font-mono">
                                        <span class="bg-slate-800 px-2 py-0.5 rounded text-emerald-400 border border-slate-700/60">{{ $doc->chunk_count }}</span>
                                    </td>
                                    <td class="py-3.5">
                                        @if($doc->status === 'active')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Active
                                            </span>
                                        @elseif($doc->status === 'processing')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Ingesting
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                                Failed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 text-slate-400 text-[11px]">
                                        {{ $doc->last_synced_at ? $doc->last_synced_at->diffForHumans() : 'Previously synced' }}
                                    </td>
                                    <td class="py-3.5 text-right pr-1">
                                        <button onclick="openChunkModal({{ $doc->id }})" class="text-xs text-slate-300 hover:text-emerald-400 font-medium px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 transition-all border border-slate-700/60">
                                            Preview
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">No documents registered.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Test Query Widget -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-base font-bold text-white">Live Knowledge Query Diagnostic</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Test query retrieval directly against the active in-memory models</p>
                    </div>
                    <span class="text-[10px] text-slate-500 font-mono">POST /api/chat</span>
                </div>

                <div class="flex gap-2">
                    <input type="text" id="diag-query" placeholder="Ask a test question (e.g. What are MPhil research fees?)"
                           class="flex-1 bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                    <button onclick="runDiagQuery()" id="diag-btn"
                            class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-xs flex items-center gap-1.5 transition-all">
                        <span>Execute Search</span>
                    </button>
                </div>

                <div id="diag-result" class="hidden mt-4 p-4 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 space-y-2">
                    <!-- Dynamic result injected here -->
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Quick Upload & Recent Audits -->
        <div class="space-y-6">
            <!-- Quick Upload Form -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-white mb-1">Quick Ingest PDF</h3>
                <p class="text-xs text-slate-400 mb-4">Add a new handbook or guideline to the active knowledge base</p>

                <form action="{{ route('admin.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Select PDF Document</label>
                        <input type="file" name="pdf_file" accept=".pdf" required
                               class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-slate-950 hover:file:bg-emerald-500 cursor-pointer bg-slate-950 p-2 rounded-xl border border-slate-700/60">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Custom Filename (Optional)</label>
                        <input type="text" name="custom_name" placeholder="e.g. Circular_2026_Intake.pdf"
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold py-2.5 rounded-xl text-xs transition-all shadow-md shadow-emerald-600/20 flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <span>Upload, Embed & Hot-Reload</span>
                    </button>
                </form>
            </div>

            <!-- Recent Audit Trail -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-white">Recent Audit Ledger</h3>
                    <a href="{{ route('admin.audit') }}" class="text-xs text-slate-400 hover:text-white">View All</a>
                </div>

                <div class="space-y-3">
                    @forelse($recentAudits as $log)
                        <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80 text-xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-emerald-400 font-mono">{{ $log->action }}</span>
                                <span class="text-[10px] text-slate-500">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-400 text-[11px] truncate">{{ $log->target_id ?: ($log->user_email ?: 'System') }}</p>
                        </div>
                    @empty
                        <p class="text-center py-4 text-slate-500 text-xs">No audit logs recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function runDiagQuery() {
    const input = document.getElementById('diag-query');
    const btn = document.getElementById('diag-btn');
    const resultBox = document.getElementById('diag-result');
    const q = input.value.trim();
    if (!q) return;

    btn.disabled = true;
    btn.innerHTML = '<span>Searching...</span>';
    resultBox.classList.remove('hidden');
    resultBox.innerHTML = '<p class="text-slate-400">Consulting hybrid vector matrix and BGE cross-encoder...</p>';

    try {
        const res = await fetch('/api/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: q })
        });
        const data = await res.json();
        
        resultBox.innerHTML = `
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <span class="text-emerald-400 font-semibold">Match Confidence: ${Math.round((data.top_score || 0) * 100)}%</span>
                <span class="text-[10px] text-slate-400 font-mono">Sources: ${(data.sources || []).join(', ')}</span>
            </div>
            <div class="text-slate-200 leading-relaxed whitespace-pre-wrap mt-2">${escapeHtml(data.answer || 'No answer')}</div>
        `;
    } catch (e) {
        resultBox.innerHTML = `<p class="text-rose-400">Query error: ${e.message}</p>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>Execute Search</span>';
    }
}
</script>
@endsection
