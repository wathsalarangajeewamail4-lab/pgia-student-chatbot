<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PGIA AI Assistant | Grounded Knowledge Base</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Marked.js for Markdown parsing -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .markdown-content p { margin-bottom: 0.65rem; line-height: 1.6; }
        .markdown-content p:last-child { margin-bottom: 0; }
        .markdown-content ul { list-style-type: none; padding-left: 0.25rem; margin-bottom: 0.75rem; }
        .markdown-content ul li { position: relative; padding-left: 1.25rem; margin-bottom: 0.35rem; line-height: 1.5; }
        .markdown-content ul li::before { content: "•"; color: #34d399; font-weight: bold; position: absolute; left: 0.25rem; }
        .markdown-content ol { list-style-type: decimal; padding-left: 1.25rem; margin-bottom: 0.5rem; }
        .markdown-content strong { color: #38bdf8; font-weight: 600; }
        .markdown-content h3, .markdown-content h4 { font-weight: 600; margin-top: 0.85rem; margin-bottom: 0.35rem; color: #38bdf8; display: flex; align-items: center; gap: 0.5rem; }
        .markdown-content blockquote { border-left: 3px solid #10b981; background: rgba(16, 185, 129, 0.08); padding: 0.6rem 0.85rem; border-radius: 0 0.5rem 0.5rem 0; margin: 0.75rem 0; color: #a7f3d0; font-size: 0.85rem; line-height: 1.5; }
        .markdown-content blockquote strong { color: #34d399; }
        .markdown-content a { color: #38bdf8; text-decoration: underline; text-underline-offset: 3px; }
        .markdown-content a:hover { color: #7dd3fc; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 flex flex-col h-screen overflow-hidden">

    <!-- Top Header -->
    <header class="bg-slate-900/80 backdrop-blur border-b border-slate-800 px-6 py-3.5 flex items-center justify-between z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 font-bold text-slate-950 text-lg">
                P
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base font-bold tracking-tight text-white">PGIA Institutional AI Assistant</h1>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Dell T440 RAG Active
                    </span>
                </div>
                <p class="text-xs text-slate-400">Grounded with 7 Official PDF Handbooks & Guides (1,313 Vector Chunks)</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="text-xs bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 px-3 py-1.5 rounded-lg flex items-center gap-1.5 font-medium transition-all shadow-sm">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Admin Console</span>
            </a>
            <button onclick="toggleWebhookModal()" class="text-xs bg-emerald-950/70 hover:bg-emerald-900 border border-emerald-500/40 text-emerald-300 px-3 py-1.5 rounded-lg flex items-center gap-1.5 font-medium transition-all shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>WhatsApp Webhook</span>
            </button>
            <span class="hidden sm:inline-block text-xs bg-slate-800 px-3 py-1 rounded-lg text-slate-300 border border-slate-700/60 font-mono">
                Institutional AI Engine &bull; Hybrid RAG (Vector + BM25 + Reranker)
            </span>
        </div>
    </header>

    <!-- Chat Messages Container -->
    <main id="chat-box" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 max-w-4xl w-full mx-auto">
        <!-- Welcome Card -->
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 text-center my-4 max-w-2xl mx-auto shadow-xl">
            <div class="w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-400 mx-auto flex items-center justify-center mb-3 border border-emerald-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <h2 class="text-lg font-bold text-white mb-1">Welcome to PGIA Knowledge Assistant</h2>
            <p class="text-xs text-slate-400 max-w-md mx-auto mb-4">
                Ask any question about postgraduate degrees, admission criteria, research procedures, or fees. All answers are strictly grounded in official documents.
            </p>
            <div class="flex flex-wrap justify-center gap-2 text-xs">
                <button onclick="askPreset('What are the admission requirements for MPhil and PhD degrees?')" class="bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-slate-200 px-3 py-1.5 rounded-lg transition-all text-left">
                    🎓 MPhil & PhD Requirements
                </button>
                <button onclick="askPreset('What are the fees for international and local students?')" class="bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-slate-200 px-3 py-1.5 rounded-lg transition-all text-left">
                    💰 Course & Registration Fees
                </button>
                <button onclick="askPreset('What is the procedure for thesis submission and research guides?')" class="bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-slate-200 px-3 py-1.5 rounded-lg transition-all text-left">
                    📑 Thesis & Research Guidelines
                </button>
                <button onclick="askPreset('What programs are available under MSc and MBA?')" class="bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-slate-200 px-3 py-1.5 rounded-lg transition-all text-left">
                    📋 MSc & MBA Programs
                </button>
            </div>
        </div>
    </main>

    <!-- Bottom Input Area -->
    <footer class="bg-slate-900/90 border-t border-slate-800 p-4">
        <div class="max-w-4xl mx-auto">
            <form id="chat-form" onsubmit="sendMessage(event)" class="flex gap-2 items-center">
                <div class="relative flex-1">
                    <input type="text" id="user-input" placeholder="Ask a question about PGIA courses, admissions, fees..." 
                        autocomplete="off"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 pr-10 shadow-inner">
                </div>
                <button type="submit" id="send-btn" class="bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-slate-950 font-semibold px-5 py-3 rounded-xl transition-all shadow-lg shadow-emerald-500/20 flex items-center gap-1.5">
                    <span>Send</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
            <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2 px-1">
                <span>Press Enter to send</span>
                <span>Institutional Stack: Dell T440 Enterprise &bull; Ubuntu (Laravel) &bull; Neural Inference Core</span>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        const chatBox = document.getElementById('chat-box');
        const userInput = document.getElementById('user-input');
        const sendBtn = document.getElementById('send-btn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function appendMessage(role, content, sources = [], score = null) {
            const div = document.createElement('div');
            
            if (role === 'user') {
                div.className = 'flex justify-end';
                div.innerHTML = `
                    <div class="bg-emerald-600 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 max-w-xl text-sm shadow-md">
                        ${escapeHtml(content)}
                    </div>
                `;
            } else {
                div.className = 'flex justify-start';
                let parsedMarkdown = marked.parse(content);
                
                let sourceBadge = '';
                if (sources && sources.length > 0) {
                    const uniqueSources = [...new Set(sources.filter(s => s))];
                    sourceBadge = `
                        <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex flex-wrap items-center gap-1.5 text-[11px] text-slate-400">
                            <span class="font-medium text-slate-400">📄 Sources:</span>
                            ${uniqueSources.map(s => `<span class="bg-slate-800 text-slate-300 px-2 py-0.5 rounded font-mono border border-slate-700/60">${s}</span>`).join('')}
                            ${score ? `<span class="ml-auto text-emerald-400 font-medium">🎯 ${Math.round(score * 100)}% match</span>` : ''}
                        </div>
                    `;
                }

                div.innerHTML = `
                    <div class="bg-slate-900 border border-slate-800 text-slate-200 rounded-2xl rounded-tl-sm p-4 max-w-2xl text-sm shadow-md w-full">
                        <div class="markdown-content text-slate-200 leading-relaxed">${parsedMarkdown}</div>
                        ${sourceBadge}
                    </div>
                `;
            }

            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function appendLoading() {
            const id = 'loading-' + Date.now();
            const div = document.createElement('div');
            div.id = id;
            div.className = 'flex justify-start';
            div.innerHTML = `
                <div class="bg-slate-900 border border-slate-800 rounded-2xl rounded-tl-sm p-4 max-w-xs text-xs text-slate-400 flex items-center gap-2">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                    </div>
                    <span>Consulting Knowledge Base...</span>
                </div>
            `;
            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
            return id;
        }

        async function sendMessage(e) {
            if (e) e.preventDefault();
            const text = userInput.value.trim();
            if (!text) return;

            userInput.value = '';
            appendMessage('user', text);

            userInput.disabled = true;
            sendBtn.disabled = true;
            const loadingId = appendLoading();

            try {
                const res = await fetch('/api/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message: text })
                });

                const data = await res.json();
                document.getElementById(loadingId)?.remove();

                if (data.answer) {
                    appendMessage('bot', data.answer, data.sources, data.top_score);
                } else if (data.error) {
                    appendMessage('bot', `⚠️ **Error:** ${data.error}`);
                } else {
                    appendMessage('bot', 'No response generated.');
                }
            } catch (err) {
                document.getElementById(loadingId)?.remove();
                appendMessage('bot', `⚠️ **Connection Error:** ${err.message}`);
            } finally {
                userInput.disabled = false;
                sendBtn.disabled = false;
                userInput.focus();
            }
        }

        function askPreset(question) {
            userInput.value = question;
            sendMessage();
        }

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, m => map[m]);
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

    <script>
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

        // Auto-fetch status on load
        fetchStatus();
    </script>
</body>
</html>

