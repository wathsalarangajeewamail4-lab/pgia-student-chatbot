@extends('admin.layout')

@section('title', 'Knowledge Documents')
@section('page-title', 'PDF Knowledge Base & Document Registry')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">

    <!-- Upload Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-white">Ingest New Institutional Document</h3>
                <p class="text-xs text-slate-400 mt-0.5">Upload a PDF handbook, prospectus, or academic circular to be embedded and added to the RAG memory</p>
            </div>
            <span class="text-xs text-emerald-400 font-semibold bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">
                Auto-Chunking & Dell T440 Embedding
            </span>
        </div>

        <form action="{{ route('admin.documents.upload') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">PDF Document File (*.pdf)</label>
                <input type="file" name="pdf_file" accept=".pdf" required
                       class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-slate-950 hover:file:bg-emerald-500 cursor-pointer bg-slate-950 p-2 rounded-xl border border-slate-700/60">
            </div>

            <div class="md:col-span-1">
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Target Filename in Knowledge Base (Optional)</label>
                <input type="text" name="custom_name" placeholder="e.g. Handbook_2026.pdf"
                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
            </div>

            <div class="md:col-span-1">
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-slate-950 font-bold py-2.5 rounded-xl text-xs transition-all shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <span>Extract, Embed & Sync Model</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Documents Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-base font-bold text-white">Registered Documents ({{ $documents->total() }})</h3>
                <p class="text-xs text-slate-400 mt-0.5">Total of {{ number_format($totalChunks) }} vector chunks actively indexed in SQLite</p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('admin.documents') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search documents..."
                       class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs px-3 py-1.5 rounded-xl border border-slate-700 transition-all">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.documents') }}" class="text-xs text-slate-400 hover:text-white">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 font-semibold pb-3">
                        <th class="pb-3 pl-2">Document Name</th>
                        <th class="pb-3">File Size</th>
                        <th class="pb-3">Vector Chunks</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Checksum (SHA-256)</th>
                        <th class="pb-3">Last Synced</th>
                        <th class="pb-3 text-right pr-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-slate-800/30 transition-all">
                            <td class="py-3.5 pl-2 font-medium text-white">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-white">{{ $doc->filename }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $doc->original_name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 text-slate-400 font-mono">{{ $doc->formatted_size }}</td>
                            <td class="py-3.5 text-slate-300 font-mono">
                                <span class="bg-slate-800 px-2 py-0.5 rounded text-emerald-400 border border-slate-700/60">{{ number_format($doc->chunk_count) }}</span>
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
                            <td class="py-3.5 text-slate-500 font-mono text-[10px]">
                                {{ $doc->checksum ? substr($doc->checksum, 0, 10) . '...' : 'Pre-seeded' }}
                            </td>
                            <td class="py-3.5 text-slate-400 text-[11px]">
                                {{ $doc->last_synced_at ? $doc->last_synced_at->format('M d, Y H:i') : 'Archived' }}
                            </td>
                            <td class="py-3.5 text-right pr-2">
                                <div class="inline-flex items-center gap-1.5">
                                    <button onclick="openChunkModal({{ $doc->id }})" 
                                            class="text-xs text-slate-300 hover:text-emerald-400 font-medium px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 transition-all border border-slate-700/60">
                                        Chunks
                                    </button>

                                    <form action="{{ route('admin.documents.delete', $doc->id) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to permanently purge \'{{ $doc->filename }}\' and all its {{ $doc->chunk_count }} vector embeddings from the active knowledge base?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 px-2 py-1 rounded bg-rose-950/40 hover:bg-rose-900/50 border border-rose-500/20 transition-all">
                                            Purge
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <p>No documents found matching your filter criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $documents->links() }}
        </div>
    </div>
</div>
@endsection
