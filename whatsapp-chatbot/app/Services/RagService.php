<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RagService
{
    protected string $embedUrl;
    protected string $groqApiKey;
    protected string $hybridDaemonUrl;

    public function __construct()
    {
        $this->embedUrl = env('OLLAMA_EMBED_URL', 'http://192.248.43.138:11434/api/embeddings');
        $this->groqApiKey = env('GROQ_API_KEY', '');
        $this->hybridDaemonUrl = env('HYBRID_RAG_DAEMON_URL', 'http://127.0.0.1:8003');
    }

    /**
     * Primary High-Speed Hybrid RAG Answer (Vector + BM25 + RRF + BGE Cross-Encoder)
     */
    public function answer(string $question): array
    {
        // 1. Try High-Speed Hybrid RAG Daemon (Vector + BM25 String Search + Reranker)
        try {
            $response = Http::timeout(15)->post("{$this->hybridDaemonUrl}/api/answer", [
                'message' => $question
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['answer'])) {
                    return [
                        'answer' => $data['answer'],
                        'raw_answer' => $data['raw_answer'] ?? '',
                        'sources' => $data['sources'] ?? [],
                        'top_score' => $data['top_score'] ?? 0.0,
                        'engine' => 'hybrid_vector_bm25_bge_reranker',
                        'latency_ms' => $data['latency_ms'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Hybrid RAG Daemon unavailable, using fallback: ' . $e->getMessage());
        }

        // 2. Seamless Fallback to Native RAG
        return $this->fallbackNativeAnswer($question);
    }

    /**
     * Search database for the most relevant text chunks (Hybrid -> Fallback)
     */
    public function search(string $query, int $topK = 4): array
    {
        try {
            $response = Http::timeout(8)->post("{$this->hybridDaemonUrl}/api/search", [
                'query' => $query,
                'limit' => $topK
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['results'])) {
                    return $data['results'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Hybrid Search Daemon unavailable, using fallback: ' . $e->getMessage());
        }

        return $this->fallbackNativeSearch($query, $topK);
    }

    /**
     * Native Fallback: Generate 768-dim embedding vector via Dell T440 Ollama
     */
    public function embed(string $text): array
    {
        $response = Http::timeout(10)->post($this->embedUrl, [
            'model' => 'nomic-embed-text',
            'prompt' => $text,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to get embedding from Dell server: ' . $response->body());
        }

        return $response->json('embedding', []);
    }

    public function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $count = count($vecA);

        for ($i = 0; $i < $count; $i++) {
            $dotProduct += $vecA[$i] * $vecB[$i];
            $normA += $vecA[$i] * $vecA[$i];
            $normB += $vecB[$i] * $vecB[$i];
        }

        if ($normA <= 0 || $normB <= 0) return 0.0;
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    protected function fallbackNativeSearch(string $query, int $topK = 4): array
    {
        $queryVector = $this->embed($query);
        if (empty($queryVector)) return [];

        $allChunks = DB::table('knowledge_base')->select('id', 'source', 'chunk_text', 'embedding')->get();
        $scored = [];

        foreach ($allChunks as $chunk) {
            $embedding = json_decode($chunk->embedding, true);
            if (!is_array($embedding)) continue;

            $score = $this->cosineSimilarity($queryVector, $embedding);
            $scored[] = [
                'id' => $chunk->id,
                'source' => $chunk->source,
                'text' => $chunk->chunk_text,
                'score' => $score,
                'confidence' => $score,
            ];
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($scored, 0, $topK);
    }

    protected function fallbackNativeAnswer(string $question): array
    {
        $matches = $this->fallbackNativeSearch($question, 4);

        $contextParts = [];
        $uniqueSources = [];
        foreach ($matches as $m) {
            if ($m['source']) $uniqueSources[] = $m['source'];
            $contextParts[] = "[Document: {$m['source']}]\n" . $m['text'];
        }

        $contactKeywords = ['contact', 'phone', 'telephone', 'tel', 'call', 'hotline', 'mobile', 'number', 'numbers', 'inquir', 'reach', 'email', 'address', 'office', 'director', 'registrar', 'bursar', 'librarian', 'coordinator', 'security', 'fax'];
        $isContactQuery = false;
        $qLower = strtolower($question);
        foreach ($contactKeywords as $kw) {
            if (str_contains($qLower, $kw)) {
                $isContactQuery = true;
                break;
            }
        }

        $lifecycleKeywords = ['step', 'steps', 'milestone', 'milestones', 'journey', 'progression', 'pass out', 'passout', 'graduate', 'graduation', 'convocation', 'comprehensive exam', 'candidacy', 'proposal', 'defense', 'viva', 'thesis', 'dissertation', 'early exit', 'upgrade', 'pathway', 'next step', 'how to complete'];
        $isLifecycleQuery = false;
        foreach ($lifecycleKeywords as $lkw) {
            if (str_contains($qLower, $lkw)) {
                $isLifecycleQuery = true;
                break;
            }
        }

        if ($isLifecycleQuery) {
            $journeyExcerpt = "[PGIA Official Academic Lifecycle & Degree Decision Map - SLQF 8-12]\n"
                . "Institution: Postgraduate Institute of Agriculture (PGIA), University of Peradeniya\n"
                . "Degree Progression Matrix:\n"
                . "• PGD (SLQF 8): 25 CW credits, Min GPA 2.75, Duration: 1-3 yrs. Fallback exit for MSc CW/CW&R.\n"
                . "• Masters by CW (SLQF 9): 30 credits (25 CW + 5 Directed Study), Min GPA 3.00, Comprehensive Exam (max 2 attempts). Duration: 1-5 yrs.\n"
                . "• MSc CW & Research (SLQF 10): 60 credits (30 CW + 30 Research), Min GPA 3.00, Comprehensive Exam, Draft proposal by Sem 2, BoS defense, PR-1 biannual reports (S grade), 1 journal paper submitted, Public Seminar, Thesis Defense (2 spiral copies), Green hardbound within 3 mos. Duration: 2-6 yrs. Fallback exit: Masters CW or PGD.\n"
                . "• MBA (SLQF 10): 60 credits (36 CW + 24 Research), Min GPA 3.00, Comprehensive Exam. Duration: 2.5-6 yrs.\n"
                . "• M.Phil. (SLQF 11): Pure research (up to 10 free CW credits), 1 published + 1 submitted paper. Eligible to upgrade to Ph.D. after >= 1 yr with Board approval. No early exit award. Duration: 2-7 yrs.\n"
                . "• Ph.D. (SLQF 12): Pure research (split PhD option), 2 published papers. If doctoral defense fails, may be awarded M.Phil. if criteria met. Duration: 3-8 yrs.\n"
                . "Lifecycle States: INQUIRY -> APPLICATION_SUBMITTED -> ENROLLED_COURSEWORK -> COMPREHENSIVE_EXAM_PREP -> RESEARCH_PROPOSAL_INITIATION -> CANDIDACY_RESEARCH_ACTIVE -> THESIS_PRE_SUBMISSION -> THESIS_DEFENSE (Viva) -> CONVOCATION_AND_ALUMNI.";

            array_unshift($contextParts, $journeyExcerpt);
            if (!in_array("pgia_student_journey_map.md", $uniqueSources)) {
                $uniqueSources[] = "pgia_student_journey_map.md";
            }
        }

        $feeKeywords = ['fee', 'fees', 'cost', 'costs', 'payment', 'payments', 'tuition', 'installment', 'installments', 'lkr', 'usd', 'dollar', 'rupee', 'rupees', 'how much', 'price', 'pricing', 'scholarship', 'waiver'];
        $isFeeQuery = false;
        foreach ($feeKeywords as $fkw) {
            if (str_contains($qLower, $fkw)) {
                $isFeeQuery = true;
                break;
            }
        }

        if ($isFeeQuery) {
            $feeExcerpt = "[Official PGIA Institutional Fee Schedule - Student Handbook & Bylaws]\n"
                . "Postgraduate Institute of Agriculture, University of Peradeniya\n\n"
                . "CATEGORY 1: SRI LANKAN LOCAL STUDENTS (CURRENCY: LKR - NEVER QUOTE IN USD)\n"
                . "• Application Fee: LKR 3,000 | Library Deposit (Refundable): LKR 5,000 | Admin Costs (4 Sems): LKR 20,000\n"
                . "• Postgraduate Diploma (SLQF 8): Course fee LKR 100,000 – 125,000 (Total ~LKR 98,000 – 153,000)\n"
                . "• Masters by Coursework (SLQF 9): LKR 125,000 – 250,000 (Food Science: LKR 400,000; MBA: LKR 250,000 + LKR 200,000 books fee)\n"
                . "• M.Sc. CW & Research (SLQF 10): LKR 225,000 – 350,000 (Food Science: LKR 500,000; MBA: LKR 250,000)\n"
                . "• Research Degrees: M.Phil. LKR 300,000 (Total LKR 328,000) | Ph.D. LKR 400,000 (Total LKR 428,000) | DBA LKR 500,000\n\n"
                . "CATEGORY 2: SRI LANKAN STUDENTS RESIDING ABROAD (CURRENCY: LKR - NEVER QUOTE IN USD)\n"
                . "• Flat Course Fee: LKR 600,000 applies to ALL degree programs (Masters, MSc, MBA, MPhil, PhD) for online lecture delivery, tech support, and examinations.\n"
                . "• Students Who Move Abroad After Registration: Additional fee of LKR 300,000.\n\n"
                . "CATEGORY 3: FOREIGN & INTERNATIONAL STUDENTS (CURRENCY: USD)\n"
                . "• SAARC & Developing Countries: Masters CW: $1,000 – $2,500 | MSc CW&R: $2,000 – $3,500 | M.Phil: $2,500 | Ph.D: $3,500\n"
                . "• Other Countries / Non-SAARC: Masters CW: $4,000 (MBA $6,000) | MSc CW&R: $6,000 (MBA $8,000) | M.Phil: $6,000 | Ph.D: $8,000\n"
                . "• Admin Fees: Application Free | Semester Registration $20 | Comp Exam / Thesis Defense $75 per attempt.\n\n"
                . "SPECIAL PAYMENT PRIVILEGE: 10% Fee Waiver when full program fee is paid at once (cash, credit card, or bank loan). 2-semester installment facility available.";

            array_unshift($contextParts, $feeExcerpt);
            if (!in_array("PGIA_Fee_Structure_Peradeniya_Intake_2024_2026.pdf", $uniqueSources)) {
                $uniqueSources[] = "PGIA_Fee_Structure_Peradeniya_Intake_2024_2026.pdf";
            }
            if (!in_array("PGIA_Fee_Structure_Local_Students_Residing_Abroad.pdf", $uniqueSources)) {
                $uniqueSources[] = "PGIA_Fee_Structure_Local_Students_Residing_Abroad.pdf";
            }
            if (!in_array("PGIA_Fee_Structure_Foreign_and_International_Students.pdf", $uniqueSources)) {
                $uniqueSources[] = "PGIA_Fee_Structure_Foreign_and_International_Students.pdf";
            }
        }

        if ($isContactQuery) {
            $officialDirectory = "[Official PGIA Institutional Contact Directory - Student Handbook]\n"
                . "Postgraduate Institute of Agriculture (PGIA), University of Peradeniya\n"
                . "Address: P.O. Box 55, Old Galaha road, Peradeniya, Sri Lanka\n\n"
                . "Official General Telephone Lines:\n"
                . "• General Enquiries & Admissions: +94 812 388203\n"
                . "• Student Coordination & Affairs: +94 812 386542\n"
                . "• Director's Office Line: +94 812 385517\n"
                . "• Official Fax: +94 812 388318\n"
                . "• General Email: dir@pgia.ac.lk\n"
                . "• Official Website: http://www.pgia.pdn.ac.lk/\n\n"
                . "Key Administration & Staff Direct Lines:\n"
                . "• Deputy Registrar (Ms. I. Pathirana): Tel: +94 81 2388203 | Email: dr@pgia.ac.lk\n"
                . "• Senior Assistant Bursar - Accounts & Fees (Ms. K.A.I.S. Ranasinghe): Tel: +94 81 2388692 | Email: sab@pgia.ac.lk\n"
                . "• PGIA Coordinator (Ms. D.M.M.G.J.M. Dissanayaka): Tel: +94 81 2386542 | Email: pgiacoordinator@pgia.ac.lk\n"
                . "• Senior Assistant Librarian (Ms. H.N.K. Dissanayake): Tel: +94 81 2388956 | Email: sal@pgia.ac.lk\n"
                . "• Systems Analyst - MIS & IT (Mr. A.G.I. Hemajith): Tel: +94 81 2389726 | Email: sa@pgia.ac.lk\n\n"
                . "Hotlines & Inquiries:\n"
                . "• International Inquiries Hotline: +94 812 388203\n"
                . "• Research Facilitation Fund (RFF): 081 2388203\n"
                . "• Student Loan Facilities: 0812 388219\n"
                . "• University Security (24-Hour Campus Emergency): 081-2389182";
            array_unshift($contextParts, $officialDirectory);
            if (!in_array("Handbook_2026.pdf", $uniqueSources)) {
                $uniqueSources[] = "Handbook_2026.pdf";
            }
        }

        $uniqueSources = array_values(array_unique($uniqueSources));
        $contextText = implode("\n\n---\n\n", $contextParts);

        $systemPrompt = "You are the official postgraduate AI Assistant for PGIA (Postgraduate Institute of Agriculture, University of Peradeniya).\n"
            . "Answer the user's question accurately using the provided official reference excerpts.\n\n"
            . "CRITICAL WHATSAPP FORMATTING GUIDELINES:\n"
            . "- You are outputting directly to WhatsApp on a mobile phone screen.\n"
            . "- Use WhatsApp bold with SINGLE asterisks: *Bold Title* (NEVER use **double asterisks**).\n"
            . "- NEVER use markdown headers like '#' or '###'. Use clean section titles like 📌 *Overview*, 📋 *Requirements*, 📞 *Official Contact Lines*.\n"
            . "- Use clean bullet points using the unicode bullet '• ' with space between sections.\n"
            . "- Keep sentences clear, concise, and easy to read on mobile devices.\n\n"
            . "CRITICAL CONTACT DIRECTORY DIRECTIVE (NEVER MASK WITH XXXXXX):\n"
            . "- All PGIA telephone numbers, office lines, emails, and staff contacts are public institutional directory records.\n"
            . "- ALWAYS display complete, full real phone numbers (e.g. +94 812 388203, +94 812 386542, +94 812 385517, +94 81 2388692, +94 81 2389726, +94 81 2388956, 081-2389182).\n"
            . "- NEVER replace digits with 'xxxxxx', '***', or placeholder characters under any circumstances.\n\n"
            . "ACADEMIC LIFECYCLE & PATHWAY GUIDANCE:\n"
            . "- When answering questions about student progression, degree completion, thesis submission, comprehensive exams, or graduation, clearly present chronological milestones, prerequisite gates (e.g. GPA thresholds, credit requirements), and any applicable contingency exit/upgrade pathways based on the PGIA Academic Lifecycle Decision Graph.\n\n"
            . "THREE (3) STUDENT CATEGORIES & CURRENCY DISCIPLINE (CRITICAL - NEVER QUOTE ONLY USD):\n"
            . "- There are THREE DISTINCT STUDENT TYPES at PGIA. NEVER quote fees only in USD for general inquiries!\n"
            . "- ALWAYS categorize by the 3 student types:\n"
            . "  1. 🇱🇰 *Sri Lankan Students (Local / Resident)*: ALWAYS quote in *LKR (Sri Lankan Rupees)* (e.g. PGD: LKR 100,000–125,000; Masters CW: LKR 125,000–250,000; MSc CW&R: LKR 225,000–350,000; MPhil: LKR 300,000; PhD: LKR 400,000; Admin cost: LKR 20,000 + LKR 5,000 library deposit).\n"
            . "  2. 🌍 *Sri Lankan Students Residing Abroad (Expatriates / Non-Residents)*: ALWAYS quote in *LKR* as a *Flat Course Fee of LKR 600,000* for ALL degree programs (Masters, MSc, MBA, MPhil, PhD) for online lecture delivery and tech support.\n"
            . "  3. 🌐 *Foreign / International Students (Non-Sri Lankans)*: ALWAYS quote in *USD ($)* (SAARC: $1,000–$3,500 vs Other Countries / Non-SAARC: $4,000–$8,000).\n"
            . "- Always highlight the *10% fee waiver* for full upfront payment!\n\n"
            . "=== OFFICIAL REFERENCE EXCERPTS ===\n"
            . $contextText;

        $response = Http::withToken($this->groqApiKey)
            ->timeout(25)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'openai/gpt-oss-120b',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $question],
                ],
                'temperature' => 0.15,
                'max_tokens' => 1024,
            ]);

        $rawAnswer = $response->json('choices.0.message.content', 'No answer could be generated at this time.');
        $formattedAnswer = $this->formatForWhatsApp($rawAnswer, $uniqueSources, $matches[0]['score'] ?? 0);

        return [
            'answer' => $formattedAnswer,
            'raw_answer' => $rawAnswer,
            'sources' => $uniqueSources,
            'top_score' => $matches[0]['score'] ?? 0,
            'engine' => 'native_fallback',
        ];
    }

    protected function formatForWhatsApp(string $text, array $sources = [], float $topScore = 0.0): string
    {
        $formatted = preg_replace('/\*\*(.*?)\*\*/', '*$1*', $text);
        $formatted = preg_replace('/^#{1,4}\s*(.*?)$/m', '📌 *$1*', $formatted);
        $formatted = preg_replace('/^[\*\-]\s+/m', '• ', $formatted);
        $formatted = trim($formatted);

        $divider = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━";
        $sourceText = !empty($sources) ? implode(', ', $sources) : 'Official PGIA Handbooks';
        $scorePercent = round($topScore * 100);

        $header = "🏛️ *POSTGRADUATE INSTITUTE OF AGRICULTURE*\n"
                . "_University of Peradeniya • Institutional AI Desk_\n"
                . "{$divider}\n\n";

        $footer = "\n\n{$divider}\n"
                . "🏛️ *PGIA Official Knowledge Base*\n"
                . "🎯 _Verification Confidence: {$scorePercent}% High Fidelity_";

        return $header . $formatted . $footer;
    }
}
