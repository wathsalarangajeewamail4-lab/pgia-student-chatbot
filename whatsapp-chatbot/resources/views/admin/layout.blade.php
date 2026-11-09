<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Console') | PGIA Knowledge Base Manager</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Mermaid.js for Academic Journey Decision Graphs -->
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="h-full flex overflow-hidden">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-slate-900/90 border-r border-slate-800 flex flex-col shrink-0 select-none">
        <!-- Brand Header -->
        <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800 bg-slate-900">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center font-bold text-slate-950 shadow-lg shadow-emerald-500/20 text-base">
                P
            </div>
            <div>
                <h1 class="text-sm font-bold text-white tracking-tight">PGIA AI Console</h1>
                <p class="text-[10px] text-emerald-400 font-medium">Knowledge Control Plane</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard Overview</span>
            </a>

            <a href="{{ route('admin.journey') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.journey') ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                <span>Student Journey Map</span>
            </a>

            <a href="{{ route('admin.documents') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.documents') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>PDF Knowledge Base</span>
            </a>

            <a href="{{ route('admin.audit') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('admin.audit') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span>Audit & Security Logs</span>
            </a>

            <div class="pt-4 mt-4 border-t border-slate-800/80">
                <p class="px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-2">Live Endpoints</p>
                
                <a href="/" target="_blank" 
                   class="flex items-center justify-between px-3 py-2 rounded-xl text-xs text-slate-400 hover:text-white hover:bg-slate-800/60 transition-all">
                    <span class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <span>Student Web Chat</span>
                    </span>
                    <span class="text-[10px] bg-slate-800 px-1.5 py-0.5 rounded text-slate-400 font-mono">:8090</span>
                </a>
            </div>
        </nav>

        <!-- Current User Profile & Logout -->
        <div class="p-3 border-t border-slate-800 bg-slate-900/60">
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-950 border border-slate-800/80">
                <div class="min-w-0 pr-2">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@pgia.pdn.ac.lk' }}</p>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Sign out" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Application Header -->
        <header class="h-16 bg-slate-900/80 backdrop-blur border-b border-slate-800 px-8 flex items-center justify-between shrink-0 z-10">
            <div class="flex items-center gap-3">
                <h2 class="text-sm font-bold text-white tracking-tight">@yield('page-title', 'Dashboard')</h2>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Dell T440 Hybrid Engine Online</span>
                </span>
            </div>

            <div class="flex items-center gap-3">
                <button onclick="toggleWebhookModal()" class="text-xs bg-emerald-950/70 hover:bg-emerald-900 border border-emerald-500/40 text-emerald-300 px-3.5 py-1.5 rounded-lg flex items-center gap-1.5 font-medium transition-all shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>WhatsApp Webhook</span>
                </button>

                <!-- Trigger Global Sync Button -->
                <form action="{{ route('admin.sync') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-slate-950 font-bold px-3.5 py-1.5 rounded-lg flex items-center gap-1.5 transition-all shadow-md shadow-emerald-600/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Sync Knowledge & Reload Model</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Notification Alerts -->
        @if(session('success'))
            <div class="bg-emerald-950/60 border-b border-emerald-500/30 px-8 py-2.5 flex items-center justify-between text-xs text-emerald-300">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-950/60 border-b border-rose-500/30 px-8 py-2.5 flex items-center justify-between text-xs text-rose-300">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white">&times;</button>
            </div>
        @endif

        <!-- Scrollable Page Content -->
        <main class="flex-1 overflow-y-auto p-8 bg-slate-950">
            @yield('content')
        </main>
    </div>

    <!-- Chunk Preview Modal -->
    <div id="chunk-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-6">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-3xl w-full p-6 shadow-2xl flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div>
                    <h3 id="modal-doc-name" class="text-base font-bold text-white">Document Vector Chunks</h3>
                    <p id="modal-chunk-meta" class="text-xs text-slate-400 mt-0.5">Showing embedded segments from SQLite knowledge base</p>
                </div>
                <button onclick="closeChunkModal()" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="modal-chunks-list" class="flex-1 overflow-y-auto py-4 space-y-3 pr-1 text-xs">
                <!-- Chunks injected dynamically via JS -->
            </div>

            <div class="pt-4 border-t border-slate-800 flex justify-end">
                <button onclick="closeChunkModal()" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs px-4 py-2 rounded-lg font-medium transition-all">Close Preview</button>
            </div>
        </div>
    </div>

    <script>
        async function openChunkModal(docId) {
            const modal = document.getElementById('chunk-modal');
            const list = document.getElementById('modal-chunks-list');
            list.innerHTML = '<div class="text-center py-8 text-slate-400">Loading vector chunks from SQLite...</div>';
            modal.classList.remove('hidden');

            try {
                const res = await fetch(`/admin/documents/${docId}/chunks`);
                const data = await res.json();
                
                document.getElementById('modal-doc-name').textContent = data.document;
                document.getElementById('modal-chunk-meta').textContent = `Showing first ${data.preview_count} of ${data.total_chunks} embedded chunks`;

                if (!data.chunks || data.chunks.length === 0) {
                    list.innerHTML = '<div class="text-center py-8 text-slate-400">No embedded chunks found for this document.</div>';
                    return;
                }

                list.innerHTML = data.chunks.map((c, i) => `
                    <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800/80">
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1.5 pb-1 border-b border-slate-800/60 font-mono">
                            <span class="text-emerald-400 font-semibold">Chunk #${i + 1} (DB ID: ${c.id})</span>
                            <span>${c.created_at ?? ''}</span>
                        </div>
                        <p class="text-slate-300 leading-relaxed whitespace-pre-wrap">${escapeHtml(c.chunk_text)}</p>
                    </div>
                `).join('');
            } catch (err) {
                list.innerHTML = `<div class="text-center py-8 text-rose-400">Error loading chunks: ${err.message}</div>`;
            }
        }

        function closeChunkModal() {
            document.getElementById('chunk-modal').classList.add('hidden');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function toggleWebhookModal() {
            const modal = document.getElementById('webhook-modal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                fetchStatus();
            }
        }

        async function fetchStatus() {
            try {
                const res = await fetch('/api/status');
                const data = await res.json();
                if (data.webhook_url) {
                    document.getElementById('modal-webhook-url').value = data.webhook_url;
                }
                if (data.verify_token) {
                    document.getElementById('modal-verify-token').value = data.verify_token;
                }
            } catch (e) {
                console.error('Failed to fetch status', e);
            }
        }

        function copyToClipboard(elementId, btn) {
            const input = document.getElementById(elementId);
            navigator.clipboard.writeText(input.value).then(() => {
                const orig = btn.innerText;
                btn.innerText = 'Copied!';
                btn.classList.add('bg-emerald-600', 'text-white');
                setTimeout(() => {
                    btn.innerText = orig;
                    btn.classList.remove('bg-emerald-600', 'text-white');
                }, 2000);
            });
        }
    </script>

    <!-- WhatsApp Webhook Status Modal -->
    <div id="webhook-modal" class="hidden fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl relative">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20 font-bold">
                        WA
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Meta WhatsApp Webhook Configuration</h3>
                        <p class="text-xs text-slate-400">Live endpoint connected via Cloudflare Tunnel</p>
                    </div>
                </div>
                <button onclick="toggleWebhookModal()" class="text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Callback URL (Meta Developers Portal)</label>
                    <div class="flex items-center gap-2">
                        <input id="modal-webhook-url" readonly type="text" class="w-full bg-slate-950 border border-slate-800 text-xs text-emerald-400 font-mono rounded-lg px-3 py-2 focus:outline-none select-all" value="" />
                        <button onclick="copyToClipboard('modal-webhook-url', this)" class="shrink-0 bg-slate-800 hover:bg-slate-700 text-xs text-white px-3 py-2 rounded-lg transition-all border border-slate-700">Copy</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Verify Token</label>
                    <div class="flex items-center gap-2">
                        <input id="modal-verify-token" readonly type="text" class="w-full bg-slate-950 border border-slate-800 text-xs text-white font-mono rounded-lg px-3 py-2 focus:outline-none select-all" value="my_secret_token_123" />
                        <button onclick="copyToClipboard('modal-verify-token', this)" class="shrink-0 bg-slate-800 hover:bg-slate-700 text-xs text-white px-3 py-2 rounded-lg transition-all border border-slate-700">Copy</button>
                    </div>
                </div>

                <div class="p-3 bg-slate-950/60 rounded-xl border border-slate-800/80 text-xs text-slate-300 space-y-1.5">
                    <div class="font-semibold text-emerald-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Meta Developer Configuration Steps:
                    </div>
                    <ol class="list-decimal list-inside space-y-1 text-slate-400 text-[11px] leading-relaxed">
                        <li>Open <span class="text-white font-mono">developers.facebook.com &gt; PGIA Assistant</span></li>
                        <li>Click <span class="text-white font-mono">WhatsApp &gt; Configuration</span></li>
                        <li>Click <span class="text-white font-medium">Edit</span> on the Webhook section</li>
                        <li>Paste the <span class="text-emerald-400 font-mono">Callback URL</span> and <span class="text-white font-mono">Verify Token</span> above</li>
                        <li>Click <span class="text-white font-medium">Verify and save</span>, then subscribe to <span class="text-white font-mono">messages</span></li>
                    </ol>
                </div>
            </div>

            <div class="mt-5 flex justify-end">
                <button onclick="toggleWebhookModal()" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs px-4 py-2 rounded-lg transition-all border border-slate-700 font-medium">Close</button>
            </div>
        </div>
    </div>
    </script>
</body>
</html>
