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
    <!-- Mermaid.js for Academic Journey Decision Graphs -->
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            mermaid.initialize({
                startOnLoad: false,
                theme: 'dark',
                securityLevel: 'loose',
                flowchart: { curve: 'basis' }
            });
        });
    </script>
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
                <p class="text-xs text-slate-400">Grounded with 39 Official University Handbooks, Prospectuses & Regulations (1,644 Vector Chunks)</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="toggleJourneyModal()" class="text-xs bg-indigo-950/80 hover:bg-indigo-900 border border-indigo-500/50 text-indigo-300 px-3 py-1.5 rounded-lg flex items-center gap-1.5 font-semibold transition-all shadow-sm shadow-indigo-500/20">
                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                <span>Student Journey Map</span>
            </button>
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

    <!-- Student Academic Journey & Decision Map Modal -->
    <div id="journey-modal" class="hidden fixed inset-0 z-50 bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-6 overflow-hidden">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl max-w-5xl w-full max-h-[92vh] flex flex-col shadow-2xl relative overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-900/90 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20 font-bold text-lg">
                        🗺️
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <span>PGIA Postgraduate Academic Journey & Decision Graph</span>
                            <span class="text-[10px] font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-2 py-0.5 rounded-full">SLQF 8–12</span>
                        </h3>
                        <p class="text-xs text-slate-400">Application to Convocation • Coursework Gates • Research Milestones • Contingency Exits & Upgrades</p>
                    </div>
                </div>
                <button onclick="toggleJourneyModal()" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Tab Navigation -->
            <div class="px-6 pt-3 pb-2 border-b border-slate-800/80 bg-slate-950/50 flex flex-wrap gap-1.5 shrink-0">
                <button id="journey-btn-flowchart" onclick="showJourneyTab('flowchart')" class="journey-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all bg-indigo-600 text-white border border-indigo-400">
                    🌐 Visual Decision Graph
                </button>
                <button id="journey-btn-pgd" onclick="showJourneyTab('pgd')" class="journey-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all bg-slate-800 text-slate-300 border border-slate-700 hover:text-white">
                    📘 PGD (SLQF 8)
                </button>
                <button id="journey-btn-masters-cw" onclick="showJourneyTab('masters-cw')" class="journey-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all bg-slate-800 text-slate-300 border border-slate-700 hover:text-white">
                    📗 Masters CW (SLQF 9)
                </button>
                <button id="journey-btn-msc-cwr" onclick="showJourneyTab('msc-cwr')" class="journey-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all bg-slate-800 text-slate-300 border border-slate-700 hover:text-white">
                    📙 M.Sc. CW&R (SLQF 10)
                </button>
                <button id="journey-btn-mba" onclick="showJourneyTab('mba')" class="journey-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all bg-slate-800 text-slate-300 border border-slate-700 hover:text-white">
                    📕 MBA (SLQF 10)
                </button>
                <button id="journey-btn-mphil" onclick="showJourneyTab('mphil')" class="journey-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all bg-slate-800 text-slate-300 border border-slate-700 hover:text-white">
                    🔬 M.Phil. (SLQF 11)
                </button>
                <button id="journey-btn-phd" onclick="showJourneyTab('phd')" class="journey-tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition-all bg-slate-800 text-slate-300 border border-slate-700 hover:text-white">
                    🎓 Ph.D. (SLQF 12)
                </button>
            </div>

            <!-- Tab Content (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                <!-- TAB 1: FLOWCHART -->
                <div id="journey-tab-flowchart" class="journey-tab-content space-y-4">
                    <div class="bg-indigo-950/40 border border-indigo-500/30 rounded-xl p-3.5 text-xs text-indigo-200 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-base">💡</span>
                            <span><strong>Institutional State Machine:</strong> Mined directly from PGIA Handbooks, Prospectuses, and Bylaws. Click any quick query below to ask the AI.</span>
                        </div>
                    </div>

                    <!-- Mermaid Container -->
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 overflow-x-auto text-center shadow-inner">
                        <pre class="mermaid text-xs font-mono">
graph TD
    A["1. INQUIRY & ADMISSION<br/>Prospectus & Call for Applications<br/>Online App + Certificates + 2 Referees"] --> B["2. ENROLLED COURSEWORK<br/>MIS Enrolment (Wk 1-2) • Modular Exams<br/>Pass Grade C • Min GPA ≥ 3.00 (PGD ≥ 2.75)"]
    
    B --> C{"GATE 1: Comprehensive Exam<br/>All Coursework Credits Completed<br/>GPA ≥ 3.00 • No Incomplete (I) Grades<br/>Max 2 Attempts"}
    
    C -->|"Fail 2nd Attempt<br/>Credits ≥ 25 & GPA ≥ 2.75"| EXIT_PGD["EARLY EXIT: Postgraduate Diploma<br/>SLQF 8 • 25 Credits Awarded"]
    
    C -->|"Pass (CW Track)"| GRAD_CW["CONFERMENT: Master of Science (Course Work)<br/>SLQF 9 • 25 CW + 5 Directed Study Credits"]
    
    C -->|"Pass (Research Track)"| D["3. RESEARCH PROPOSAL INITIATION<br/>Concept Note & Interim Supervisor (Sem 2)<br/>Full Proposal Defense within 3 Months<br/>Ethical Review Clearance Approved"]
    
    D --> E["4. CANDIDACY & ACTIVE RESEARCH<br/>30 Res Credits (MSc) • 60 (MPhil) • 90 (PhD)<br/>Biannual PR-1 Reports (S/U Grade)<br/>Refereed Journal Publications"]
    
    E -->|"MPhil Upgrade after ≥1 yr<br/>BoS & Senate Approval"| UPGRADE_PHD["UPGRADE: Ph.D. Candidacy (SLQF 12)"]
    
    E --> F["5. THESIS PRE-SUBMISSION<br/>Public Seminar Delivered at PGIA<br/>Plagiarism Check Cleared<br/>Spiral Copies Signed by Supervisors"]
    
    F --> G{"GATE 2: Thesis Defense (Viva Voce)<br/>Examination Committee (Internal & External)"}
    
    G -->|"Accepted / Minor Revisions<br/>(Final within 3 Months)"| H["6. FINAL DEGREE CONVOCATION<br/>2 Green Hardbound Copies + Gold Lettering<br/>Library Clearance & Senate Degree Award"]
    
    G -->|"Major Revisions"| RE_DEF["Repeat Defense within 6-12 Months"]
    RE_DEF --> G
    
    G -->|"PhD Defense Unsatisfactory<br/>(MPhil criteria fulfilled)"| FALLBACK_MPHIL["FALLBACK AWARD: M.Phil. Degree (SLQF 11)"]
                        </pre>
                    </div>

                    <!-- Quick Exploration Prompts -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2">
                        <button onclick="askJourneyQuestion('What are the exact steps for an MSc CW&R student to submit a thesis defense application and prepare spiral copies?')" class="bg-slate-800/80 hover:bg-slate-800 border border-slate-700/80 p-3 rounded-xl text-left transition-all hover:border-indigo-500/50">
                            <span class="block text-xs font-semibold text-white mb-1">📑 Thesis Defense & Viva Rules</span>
                            <span class="block text-[11px] text-slate-400">Spiral bound copies, public seminar, plagiarism clearance, examiners.</span>
                        </button>
                        <button onclick="askJourneyQuestion('What happens if an MSc student fails the Comprehensive Examination twice? Can they exit with a Postgraduate Diploma?')" class="bg-slate-800/80 hover:bg-slate-800 border border-slate-700/80 p-3 rounded-xl text-left transition-all hover:border-indigo-500/50">
                            <span class="block text-xs font-semibold text-white mb-1">🚪 Early Exit to PGD</span>
                            <span class="block text-[11px] text-slate-400">25 credits threshold, GPA 2.75 minimum, fallback degree conferment.</span>
                        </button>
                        <button onclick="askJourneyQuestion('How can an MPhil research student upgrade to a PhD degree at PGIA? What are the requirements?')" class="bg-slate-800/80 hover:bg-slate-800 border border-slate-700/80 p-3 rounded-xl text-left transition-all hover:border-indigo-500/50">
                            <span class="block text-xs font-semibold text-white mb-1">🚀 MPhil to PhD Upgrade</span>
                            <span class="block text-[11px] text-slate-400">1 year minimum residency, outstanding progress evaluation, BoS approval.</span>
                        </button>
                    </div>
                </div>

                <!-- TAB 2: PGD -->
                <div id="journey-tab-pgd" class="journey-tab-content hidden space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Total Credits</span>
                            <p class="text-lg font-bold text-emerald-400 mt-0.5">25 Credits</p>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Passing GPA</span>
                            <p class="text-lg font-bold text-sky-400 mt-0.5">≥ 2.75</p>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Duration</span>
                            <p class="text-lg font-bold text-amber-400 mt-0.5">1 – 3 Years</p>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">SLQF Level</span>
                            <p class="text-lg font-bold text-indigo-400 mt-0.5">Level 8</p>
                        </div>
                    </div>

                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-2 text-xs text-slate-300">
                        <h4 class="font-bold text-white text-sm mb-2 flex items-center gap-2">
                            <span>📘 Overview & Exit Pathways</span>
                        </h4>
                        <p>• The Postgraduate Diploma (PGD) can be registered as a direct standalone program or awarded as an <strong>early exit degree</strong> for students registered in Master of Science programs who complete coursework but do not proceed to research.</p>
                        <p>• Also serves as the formal fallback qualification for students who fail the Comprehensive Examination at their second attempt, provided they have earned at least 25 credits with an OGPA of at least 2.75.</p>
                    </div>

                    <div class="pt-2">
                        <button onclick="askJourneyQuestion('What are the exact regulations and exit rules for Postgraduate Diploma (PGD) at PGIA?')" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition-all">
                            <span>Ask AI Desk About PGD Requirements</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- TAB 3: MASTERS CW -->
                <div id="journey-tab-masters-cw" class="journey-tab-content hidden space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Total Credits</span>
                            <p class="text-lg font-bold text-emerald-400 mt-0.5">30 Credits</p>
                            <span class="text-[10px] text-slate-500">25 CW + 5 Project</span>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Passing GPA</span>
                            <p class="text-lg font-bold text-sky-400 mt-0.5">≥ 3.00</p>
                            <span class="text-[10px] text-slate-500">No pending 'I' grades</span>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Duration</span>
                            <p class="text-lg font-bold text-amber-400 mt-0.5">1 – 5 Years</p>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">SLQF Level</span>
                            <p class="text-lg font-bold text-indigo-400 mt-0.5">Level 9</p>
                        </div>
                    </div>

                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-2 text-xs text-slate-300">
                        <h4 class="font-bold text-white text-sm mb-2 flex items-center gap-2">
                            <span>📗 Key Gates & Examination</span>
                        </h4>
                        <p>• <strong>Comprehensive Examination:</strong> Required once all 30 credits are complete and GPA ≥ 3.00. Maximum of two attempts permitted.</p>
                        <p>• <strong>Directed Study / Special Project:</strong> 5 credits guided project under a designated faculty supervisor.</p>
                        <p>• <strong>Fallback:</strong> Students who fail the Comprehensive Exam twice or fail to reach GPA 3.00 may exit with a PGD if GPA ≥ 2.75 and credits ≥ 25.</p>
                    </div>

                    <div class="pt-2">
                        <button onclick="askJourneyQuestion('What are the rules, comprehensive exam, and project requirements for Master of Science by Course Work at PGIA?')" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition-all">
                            <span>Ask AI Desk About Masters Course Work</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- TAB 4: MSC CW&R -->
                <div id="journey-tab-msc-cwr" class="journey-tab-content hidden space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Total Credits</span>
                            <p class="text-lg font-bold text-emerald-400 mt-0.5">60 Credits</p>
                            <span class="text-[10px] text-slate-500">30 CW + 30 Research</span>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Passing GPA</span>
                            <p class="text-lg font-bold text-sky-400 mt-0.5">≥ 3.00</p>
                            <span class="text-[10px] text-slate-500">Coursework threshold</span>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Duration</span>
                            <p class="text-lg font-bold text-amber-400 mt-0.5">2 – 6 Years</p>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">SLQF Level</span>
                            <p class="text-lg font-bold text-indigo-400 mt-0.5">Level 10</p>
                        </div>
                    </div>

                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-2.5 text-xs text-slate-300">
                        <h4 class="font-bold text-white text-sm mb-1 flex items-center gap-2">
                            <span>📙 Complete Research Progression Pipeline</span>
                        </h4>
                        <ol class="list-decimal list-inside space-y-1.5 text-slate-300">
                            <li><strong>Coursework Completion:</strong> Complete 30 coursework credits with cumulative GPA ≥ 3.00.</li>
                            <li><strong>Comprehensive Examination:</strong> Pass the Comprehensive Examination (maximum 2 attempts).</li>
                            <li><strong>Research Proposal Defense:</strong> Draft proposal in 2nd semester; full defense before Board of Study within 3 months; Ethical Review Committee clearance.</li>
                            <li><strong>Biannual Progress Reports (Form PR-1):</strong> Submitted every 6 months to PGIA; must receive Satisfactory (S) grade.</li>
                            <li><strong>Publication:</strong> At least one paper submitted to a refereed journal based on the thesis research.</li>
                            <li><strong>Pre-Submission:</strong> Plagiarism clearance certificate + Deliver Public Seminar at PGIA.</li>
                            <li><strong>Thesis Examination:</strong> Submit 2 spiral-bound copies signed by supervisory committee. Oral defense (Viva Voce).</li>
                            <li><strong>Final Bound Copies:</strong> 2 Green hardbound copies with gold lettering + PDF CD submitted within 3 months.</li>
                        </ol>
                    </div>

                    <div class="pt-2">
                        <button onclick="askJourneyQuestion('I am an MSc CW&R student who completed coursework with GPA 3.2. What are my exact next steps to complete research, submit thesis, and pass out?')" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition-all">
                            <span>Ask AI Desk About MSc CW&R Next Steps</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- TAB 5: MBA -->
                <div id="journey-tab-mba" class="journey-tab-content hidden space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Total Credits</span>
                            <p class="text-lg font-bold text-emerald-400 mt-0.5">60 Credits</p>
                            <span class="text-[10px] text-slate-500">36 CW + 24 Research</span>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Passing GPA</span>
                            <p class="text-lg font-bold text-sky-400 mt-0.5">≥ 3.00</p>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Duration</span>
                            <p class="text-lg font-bold text-amber-400 mt-0.5">2.5 – 6 Years</p>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">SLQF Level</span>
                            <p class="text-lg font-bold text-indigo-400 mt-0.5">Level 10</p>
                        </div>
                    </div>

                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-2 text-xs text-slate-300">
                        <h4 class="font-bold text-white text-sm mb-2 flex items-center gap-2">
                            <span>📕 Business Administration Track</span>
                        </h4>
                        <p>• Administered under the Board of Study in Business Administration.</p>
                        <p>• Requires 36 credits of advanced coursework modules and 24 research/business project credits.</p>
                        <p>• Mandatory Comprehensive Examination and Oral Research Defense.</p>
                    </div>

                    <div class="pt-2">
                        <button onclick="askJourneyQuestion('What are the degree requirements and coursework credits for MBA at PGIA?')" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition-all">
                            <span>Ask AI Desk About MBA Program</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- TAB 6: MPHIL -->
                <div id="journey-tab-mphil" class="journey-tab-content hidden space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Degree Nature</span>
                            <p class="text-lg font-bold text-emerald-400 mt-0.5">Pure Research</p>
                            <span class="text-[10px] text-slate-500">Up to 10 free CW credits</span>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Publications</span>
                            <p class="text-sm font-bold text-sky-400 mt-1">1 Pub + 1 Sub</p>
                            <span class="text-[10px] text-slate-500">Refereed journals</span>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Duration</span>
                            <p class="text-lg font-bold text-amber-400 mt-0.5">2 – 7 Years</p>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">SLQF Level</span>
                            <p class="text-lg font-bold text-indigo-400 mt-0.5">Level 11</p>
                        </div>
                    </div>

                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-2 text-xs text-slate-300">
                        <h4 class="font-bold text-white text-sm mb-2 flex items-center gap-2">
                            <span>🔬 M.Phil. Pathway & Ph.D. Upgrade Route</span>
                        </h4>
                        <p>• <strong>Pure Research Degree:</strong> No mandatory coursework credits required for degree calculation, but candidates may take up to 10 coursework credits free of charge with supervisor recommendation.</p>
                        <p>• <strong>Upgrade to Ph.D.:</strong> A candidate registered for M.Phil. may apply to <strong>upgrade to Ph.D. after a minimum of 1 year</strong> of active research, subject to demonstrable outstanding research progress and Board of Study + Senate approval.</p>
                        <p>• <strong>Exit Award:</strong> No early exit degree is permitted from M.Phil.</p>
                    </div>

                    <div class="pt-2">
                        <button onclick="askJourneyQuestion('What are the research guidelines, publication requirements, and PhD upgrade criteria for MPhil students at PGIA?')" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition-all">
                            <span>Ask AI Desk About M.Phil. Requirements</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- TAB 7: PHD -->
                <div id="journey-tab-phd" class="journey-tab-content hidden space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Degree Nature</span>
                            <p class="text-lg font-bold text-emerald-400 mt-0.5">Doctoral Research</p>
                            <span class="text-[10px] text-slate-500">Split PhD option available</span>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Publications</span>
                            <p class="text-sm font-bold text-sky-400 mt-1">2 Published</p>
                            <span class="text-[10px] text-slate-500">Peer-reviewed journals</span>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Duration</span>
                            <p class="text-lg font-bold text-amber-400 mt-0.5">3 – 8 Years</p>
                        </div>
                        <div class="bg-slate-950 p-3.5 rounded-xl border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">SLQF Level</span>
                            <p class="text-lg font-bold text-indigo-400 mt-0.5">Level 12</p>
                        </div>
                    </div>

                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 space-y-2 text-xs text-slate-300">
                        <h4 class="font-bold text-white text-sm mb-2 flex items-center gap-2">
                            <span>🎓 Doctoral Candidacy & Fallback Safeguards</span>
                        </h4>
                        <p>• <strong>Doctoral Defense:</strong> Requires submission of 3 spiral-bound copies, signed by all members of the supervisory committee, followed by oral defense before internal and external examiners.</p>
                        <p>• <strong>Fallback Award:</strong> If a Ph.D. candidate's doctoral defense is not deemed acceptable for the award of a Doctorate, the Examination Committee may recommend the award of an <strong>M.Phil. degree</strong> instead, provided the thesis satisfies M.Phil. research standards.</p>
                        <p>• <strong>Split Ph.D.:</strong> Candidates may collaborate with recognized foreign universities under split degree MOUs with co-supervisors abroad.</p>
                    </div>

                    <div class="pt-2">
                        <button onclick="askJourneyQuestion('What are the admission, publication, defense rules, and fallback degree options for PhD students at PGIA?')" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2 rounded-xl text-xs flex items-center gap-2 transition-all">
                            <span>Ask AI Desk About Ph.D. Guidelines</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 border-t border-slate-800 bg-slate-950/80 flex items-center justify-between text-xs text-slate-400 shrink-0">
                <span>Postgraduate Institute of Agriculture • University of Peradeniya Regulations</span>
                <button onclick="toggleJourneyModal()" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-4 py-1.5 rounded-lg border border-slate-700 font-medium transition-all">
                    Close Journey Map
                </button>
            </div>
        </div>
    </div>

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

        let journeyMermaidRendered = false;

        function toggleJourneyModal() {
            const modal = document.getElementById('journey-modal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden') && !journeyMermaidRendered) {
                if (window.mermaid) {
                    try {
                        mermaid.run();
                        journeyMermaidRendered = true;
                    } catch (err) {
                        console.warn('Mermaid rendering notice:', err);
                    }
                }
            }
        }

        function showJourneyTab(tabId) {
            document.querySelectorAll('.journey-tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.journey-tab-btn').forEach(btn => {
                btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-400');
                btn.classList.add('bg-slate-800', 'text-slate-300', 'border-slate-700');
            });

            const activeContent = document.getElementById('journey-tab-' + tabId);
            if (activeContent) activeContent.classList.remove('hidden');

            const activeBtn = document.getElementById('journey-btn-' + tabId);
            if (activeBtn) {
                activeBtn.classList.remove('bg-slate-800', 'text-slate-300', 'border-slate-700');
                activeBtn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-400');
            }

            if (tabId === 'flowchart' && window.mermaid) {
                setTimeout(() => {
                    try { mermaid.run(); } catch (e) {}
                }, 100);
            }
        }

        function askJourneyQuestion(question) {
            toggleJourneyModal();
            askPreset(question);
        }

        // Auto-fetch status on load
        fetchStatus();
    </script>
</body>
</html>

