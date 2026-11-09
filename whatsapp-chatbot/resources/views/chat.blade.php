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
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                        🗺️ Academic Journey Map Included
                    </span>
                </div>
                <p class="text-xs text-slate-400">Grounded with 39 Official University Handbooks, Prospectuses & Academic Journey Map (1,644 Vector Chunks)</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs bg-slate-800 px-3 py-1.5 rounded-lg text-slate-300 border border-slate-700/60 font-mono flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>Official Postgraduate Advisory Desk</span>
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
                <button onclick="askPreset('I am an MSc CW&R student who finished coursework with GPA 3.2. What are my exact next steps to complete research, submit thesis, and pass out?')" class="bg-indigo-950/60 hover:bg-indigo-900/80 border border-indigo-500/40 text-indigo-200 px-3 py-1.5 rounded-lg transition-all text-left flex items-center gap-1.5">
                    🗺️ MSc CW&R Next Steps & Thesis Flow
                </button>
                <button onclick="askPreset('What are the credit and GPA requirements to pass out from PG Diploma vs Masters CW vs MSc CW&R?')" class="bg-indigo-950/60 hover:bg-indigo-900/80 border border-indigo-500/40 text-indigo-200 px-3 py-1.5 rounded-lg transition-all text-left flex items-center gap-1.5">
                    📊 Degree Pathway & Exit Award Matrix
                </button>
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
</body>
</html>

