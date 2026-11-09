@extends('admin.layout')

@section('title', 'Student Academic Journey Map')
@section('page-title', 'Student Academic Journey & Degree Decision Graph')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- Top Architecture Banner -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 flex items-center justify-center shrink-0 text-xl font-bold">
                    🗺️
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-white tracking-tight">Institutional Postgraduate Lifecycle State Machine</h3>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                            SLQF Levels 8–12
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1 max-w-3xl leading-relaxed">
                        Deterministic decision graph connecting student admission, MIS course enrolment, comprehensive examination prerequisite gates, research proposals, ethics approval, biannual progress reports (PR-1), refereed journal publications, thesis defense, and early exit/upgrade contingency routes.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-[11px] font-mono bg-slate-950 px-3 py-1.5 rounded-xl text-slate-400 border border-slate-800 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Graph Engine Active in RAG</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Master Degree Track KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
        <!-- PGD -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-bold text-white">PG Diploma</span>
                <span class="text-[10px] font-semibold bg-slate-800 px-1.5 py-0.5 rounded text-indigo-400">SLQF 8</span>
            </div>
            <div class="text-xl font-bold text-emerald-400">25 <span class="text-xs font-normal text-slate-400">Credits</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Min GPA: <strong class="text-sky-400">2.75</strong></div>
            <div class="text-[10px] text-amber-400 mt-1">1–3 Yrs &bull; Early Exit Option</div>
        </div>

        <!-- Masters CW -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-bold text-white">Masters CW</span>
                <span class="text-[10px] font-semibold bg-slate-800 px-1.5 py-0.5 rounded text-indigo-400">SLQF 9</span>
            </div>
            <div class="text-xl font-bold text-emerald-400">30 <span class="text-xs font-normal text-slate-400">Credits</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Min GPA: <strong class="text-sky-400">3.00</strong></div>
            <div class="text-[10px] text-slate-400 mt-1">1–5 Yrs &bull; Comp Exam Req</div>
        </div>

        <!-- MSc CW&R -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-sm border-indigo-500/30">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-bold text-white">M.Sc. CW&R</span>
                <span class="text-[10px] font-semibold bg-slate-800 px-1.5 py-0.5 rounded text-indigo-400">SLQF 10</span>
            </div>
            <div class="text-xl font-bold text-emerald-400">60 <span class="text-xs font-normal text-slate-400">Credits</span></div>
            <div class="text-[11px] text-slate-400 mt-1">Min GPA: <strong class="text-sky-400">3.00</strong></div>
            <div class="text-[10px] text-emerald-400 mt-1">2–6 Yrs &bull; 1 Paper Sub</div>
        </div>

        <!-- MBA -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-bold text-white">MBA (CW&R)</span>
                <span class="text-[10px] font-semibold bg-slate-800 px-1.5 py-0.5 rounded text-indigo-400">SLQF 10</span>
            </div>
            <div class="text-xl font-bold text-emerald-400">60 <span class="text-xs font-normal text-slate-400">Credits</span></div>
            <div class="text-[11px] text-slate-400 mt-1">36 CW + 24 Res</div>
            <div class="text-[10px] text-slate-400 mt-1">2.5–6 Yrs &bull; BoS BusAdmin</div>
        </div>

        <!-- MPhil -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-bold text-white">M.Phil.</span>
                <span class="text-[10px] font-semibold bg-slate-800 px-1.5 py-0.5 rounded text-indigo-400">SLQF 11</span>
            </div>
            <div class="text-base font-bold text-emerald-400 mt-1">Pure Research</div>
            <div class="text-[11px] text-slate-400 mt-1">1 Pub + 1 Sub Paper</div>
            <div class="text-[10px] text-purple-400 mt-1">Upgrade to PhD after ≥1 yr</div>
        </div>

        <!-- PhD -->
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-sm border-purple-500/30">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-bold text-white">Ph.D. / DBA</span>
                <span class="text-[10px] font-semibold bg-slate-800 px-1.5 py-0.5 rounded text-purple-400">SLQF 12</span>
            </div>
            <div class="text-base font-bold text-purple-400 mt-1">Doctoral Res</div>
            <div class="text-[11px] text-slate-400 mt-1">2 Published Papers</div>
            <div class="text-[10px] text-amber-400 mt-1">3–8 Yrs &bull; MPhil Fallback</div>
        </div>
    </div>

    <!-- Interactive Master Flowchart Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span>🌐 Master Decision Flowchart</span>
                    <span class="text-[10px] font-mono text-slate-500">Mermaid Graph Engine</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Visual representation of student state progression, examination gates, and exit/upgrade triggers</p>
            </div>
            <button onclick="rerunMermaid()" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-1.5 rounded-lg border border-slate-700 transition-all font-medium">
                Redraw Diagram
            </button>
        </div>

        <div class="bg-slate-950 border border-slate-800 rounded-xl p-5 overflow-x-auto text-center shadow-inner">
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
    </div>

    <!-- Detailed Degree Tabs & Rules -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div>
                <h3 class="text-base font-bold text-white">Degree Track Regulations & Gates</h3>
                <p class="text-xs text-slate-400 mt-0.5">Explore prerequisite gates, credit compositions, and exit fallbacks for each program</p>
            </div>
            <!-- Tab Buttons -->
            <div class="flex flex-wrap gap-1 bg-slate-950 p-1 rounded-xl border border-slate-800 text-xs">
                <button onclick="switchTab('cwr')" id="btn-tab-cwr" class="journey-tab-btn px-3 py-1.5 rounded-lg font-semibold bg-indigo-600 text-white">M.Sc. CW&R</button>
                <button onclick="switchTab('cw')" id="btn-tab-cw" class="journey-tab-btn px-3 py-1.5 rounded-lg font-semibold text-slate-400 hover:text-white">Masters CW</button>
                <button onclick="switchTab('pgd')" id="btn-tab-pgd" class="journey-tab-btn px-3 py-1.5 rounded-lg font-semibold text-slate-400 hover:text-white">PGD</button>
                <button onclick="switchTab('mphil')" id="btn-tab-mphil" class="journey-tab-btn px-3 py-1.5 rounded-lg font-semibold text-slate-400 hover:text-white">M.Phil.</button>
                <button onclick="switchTab('phd')" id="btn-tab-phd" class="journey-tab-btn px-3 py-1.5 rounded-lg font-semibold text-slate-400 hover:text-white">Ph.D.</button>
                <button onclick="switchTab('json')" id="btn-tab-json" class="journey-tab-btn px-3 py-1.5 rounded-lg font-semibold text-slate-400 hover:text-white">⚙️ JSON Schema</button>
            </div>
        </div>

        <!-- TAB CONTENT: MSC CW&R -->
        <div id="tab-cwr" class="tab-pane space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
                    <h4 class="font-bold text-white text-sm text-indigo-400">Coursework & Gate 1</h4>
                    <p class="text-slate-300">• <strong>Coursework Credits:</strong> 30 Credits completed across Board of Study curriculum.</p>
                    <p class="text-slate-300">• <strong>Minimum Final GPA:</strong> 3.00 on a 4.00 scale with no pending "Incomplete" (I) grades.</p>
                    <p class="text-slate-300">• <strong>Comprehensive Examination:</strong> Mandatory written + oral examination. Maximum 2 attempts permitted.</p>
                </div>
                <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
                    <h4 class="font-bold text-white text-sm text-emerald-400">Research & Gate 2</h4>
                    <p class="text-slate-300">• <strong>Research Component:</strong> 30 Credits of original research (minimum 15 Research Credits).</p>
                    <p class="text-slate-300">• <strong>Proposal & Ethical Clearance:</strong> Draft proposal in Sem 2, defend before BoS within 3 months, obtain ERC approval.</p>
                    <p class="text-slate-300">• <strong>Progress Reports:</strong> Form PR-1 submitted every 6 months, graded Satisfactory (S).</p>
                    <p class="text-slate-300">• <strong>Publication:</strong> At least 1 paper submitted to a refereed journal prior to thesis defense.</p>
                </div>
            </div>
            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-xs text-slate-300 space-y-1.5">
                <h4 class="font-bold text-white text-sm text-amber-400">Thesis Defense & Early Exit Contingencies</h4>
                <p>• <strong>Pre-Submission:</strong> Deliver public seminar at PGIA + obtain plagiarism clearance certificate + submit 2 spiral copies signed by supervisors.</p>
                <p>• <strong>Final Submission:</strong> 2 Green hardbound copies with gold lettering + PDF CD submitted within 3 months of defense.</p>
                <p>• <strong>Contingency Exits:</strong> Candidate may exit with a Masters by Coursework (if 30 CW credits and CE passed) or a Postgraduate Diploma (if 25 CW credits and GPA ≥ 2.75).</p>
            </div>
        </div>

        <!-- TAB CONTENT: MASTERS CW -->
        <div id="tab-cw" class="tab-pane hidden space-y-4 text-xs">
            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
                <h4 class="font-bold text-white text-sm text-emerald-400">Program Structure</h4>
                <p class="text-slate-300">• <strong>Total Credits:</strong> 30 Credits (25 Coursework Credits + 5 Credits Directed Study / Project).</p>
                <p class="text-slate-300">• <strong>Duration:</strong> 1 to 5 academic years.</p>
                <p class="text-slate-300">• <strong>Passing GPA:</strong> Minimum cumulative GPA of 3.00.</p>
                <p class="text-slate-300">• <strong>Comprehensive Examination:</strong> Mandatory after completing 30 credits (maximum 2 attempts).</p>
                <p class="text-slate-300">• <strong>Fallback:</strong> Exit with PGD if Comprehensive Exam is failed at 2nd attempt and GPA ≥ 2.75.</p>
            </div>
        </div>

        <!-- TAB CONTENT: PGD -->
        <div id="tab-pgd" class="tab-pane hidden space-y-4 text-xs">
            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
                <h4 class="font-bold text-white text-sm text-sky-400">Postgraduate Diploma (SLQF Level 8)</h4>
                <p class="text-slate-300">• <strong>Total Credits:</strong> 25 Coursework Credits.</p>
                <p class="text-slate-300">• <strong>Passing GPA:</strong> Minimum cumulative GPA of 2.75.</p>
                <p class="text-slate-300">• <strong>Duration:</strong> 1 to 3 academic years.</p>
                <p class="text-slate-300">• <strong>Role in Institute:</strong> Operates both as a standalone qualification and as the formal institutional <strong>early exit award</strong> for Master of Science students who do not proceed to research or fail the comprehensive exam.</p>
            </div>
        </div>

        <!-- TAB CONTENT: MPHIL -->
        <div id="tab-mphil" class="tab-pane hidden space-y-4 text-xs">
            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
                <h4 class="font-bold text-white text-sm text-purple-400">Master of Philosophy (SLQF Level 11)</h4>
                <p class="text-slate-300">• <strong>Degree Nature:</strong> Pure Research Degree (up to 10 coursework credits allowed free of charge with supervisor approval).</p>
                <p class="text-slate-300">• <strong>Duration:</strong> 2 to 7 academic years.</p>
                <p class="text-slate-300">• <strong>Publication Threshold:</strong> Minimum 1 published paper and 1 submitted paper in refereed journals.</p>
                <p class="text-slate-300">• <strong>Upgrade to Ph.D.:</strong> Candidate may apply to <strong>upgrade to Ph.D. after 1 year</strong> of active research, subject to demonstrable outstanding progress and BoS/Senate approval.</p>
                <p class="text-slate-300">• <strong>Early Exit:</strong> No early exit degree is awarded from M.Phil.</p>
            </div>
        </div>

        <!-- TAB CONTENT: PHD -->
        <div id="tab-phd" class="tab-pane hidden space-y-4 text-xs">
            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
                <h4 class="font-bold text-white text-sm text-purple-400">Doctor of Philosophy (SLQF Level 12)</h4>
                <p class="text-slate-300">• <strong>Degree Nature:</strong> Doctoral Research Degree (Split Ph.D. options with foreign universities permitted under MOU).</p>
                <p class="text-slate-300">• <strong>Duration:</strong> 3 to 8 academic years.</p>
                <p class="text-slate-300">• <strong>Publication Threshold:</strong> Minimum 2 published papers in peer-reviewed indexed journals.</p>
                <p class="text-slate-300">• <strong>Thesis Defense:</strong> Requires submission of 3 spiral copies, followed by oral defense with internal and external examiners.</p>
                <p class="text-slate-300">• <strong>M.Phil. Fallback Award:</strong> If the thesis defense does not meet doctoral standards, the examiners may recommend conferment of an <strong>M.Phil. degree</strong> instead.</p>
            </div>
        </div>

        <!-- TAB CONTENT: JSON SCHEMA -->
        <div id="tab-json" class="tab-pane hidden space-y-4 text-xs">
            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 space-y-2">
                <div class="flex items-center justify-between pb-2 border-b border-slate-800 font-mono text-slate-400 text-[11px]">
                    <span>File: python/pgia_student_journey_map.json</span>
                    <span class="text-emerald-400">Used by pgia_hybrid_engine.py</span>
                </div>
                <pre class="overflow-x-auto text-emerald-400 font-mono text-[11px] leading-relaxed max-h-96">{{ json_encode($journeyData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>

    <!-- Live Test Diagnostic Box -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="text-base font-bold text-white">Journey Guidance Query Diagnostic</h3>
                <p class="text-xs text-slate-400 mt-0.5">Test how the Hybrid RAG engine responds to student academic milestone inquiries</p>
            </div>
            <span class="text-[10px] text-indigo-400 font-mono bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded-full">Lifecycle RAG Active</span>
        </div>

        <div class="flex gap-2">
            <input type="text" id="journey-diag-query" value="I am an MSc CW&R student who completed coursework with GPA 3.2. What are my exact next steps to complete research and pass out?"
                   class="flex-1 bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
            <button onclick="runJourneyDiagQuery()" id="journey-diag-btn"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center gap-1.5 transition-all">
                <span>Test Guidance</span>
            </button>
        </div>

        <div id="journey-diag-result" class="hidden mt-4 p-4 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 space-y-2">
            <!-- Dynamic result injected here -->
        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.journey-tab-btn').forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white');
            btn.classList.add('text-slate-400');
        });

        const targetPane = document.getElementById('tab-' + tab);
        if (targetPane) targetPane.classList.remove('hidden');

        const targetBtn = document.getElementById('btn-tab-' + tab);
        if (targetBtn) {
            targetBtn.classList.remove('text-slate-400');
            targetBtn.classList.add('bg-indigo-600', 'text-white');
        }
    }

    function rerunMermaid() {
        if (window.mermaid) {
            mermaid.run();
        }
    }

    async function runJourneyDiagQuery() {
        const input = document.getElementById('journey-diag-query');
        const btn = document.getElementById('journey-diag-btn');
        const resultBox = document.getElementById('journey-diag-result');
        const q = input.value.trim();
        if (!q) return;

        btn.disabled = true;
        btn.innerHTML = '<span>Evaluating...</span>';
        resultBox.classList.remove('hidden');
        resultBox.innerHTML = '<p class="text-slate-400">Consulting state machine and hybrid vector context...</p>';

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
                    <span class="text-emerald-400 font-semibold">Engine: ${data.engine || 'hybrid'} &bull; Match: ${Math.round((data.top_score || 0) * 100)}%</span>
                    <span class="text-[10px] text-slate-400 font-mono">Sources: ${(data.sources || []).join(', ')}</span>
                </div>
                <div class="text-slate-200 leading-relaxed whitespace-pre-wrap mt-2">${escapeHtml(data.answer || 'No answer')}</div>
            `;
        } catch (e) {
            resultBox.innerHTML = `<p class="text-rose-400">Query error: ${e.message}</p>`;
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span>Test Guidance</span>';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.mermaid) {
            mermaid.initialize({
                startOnLoad: false,
                theme: 'dark',
                securityLevel: 'loose'
            });
            setTimeout(() => mermaid.run(), 100);
        }
    });
</script>
@endsection
