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
        $uniqueSources = array_values(array_unique($uniqueSources));
        $contextText = implode("\n\n---\n\n", $contextParts);

        $systemPrompt = "You are the official postgraduate AI Assistant for PGIA (Postgraduate Institute of Agriculture, University of Peradeniya).\n"
            . "Answer the user's question accurately using ONLY the provided official reference excerpts.\n\n"
            . "CRITICAL WHATSAPP FORMATTING GUIDELINES:\n"
            . "- You are outputting directly to WhatsApp on a mobile phone screen.\n"
            . "- Use WhatsApp bold with SINGLE asterisks: *Bold Title* (NEVER use **double asterisks**).\n"
            . "- NEVER use markdown headers like '#' or '###'. Use clean section titles like ?? *Overview*, ?? *Requirements*, ?? *How to Apply*.\n"
            . "- Use clean bullet points using the unicode bullet '? ' with space between sections.\n"
            . "- Keep sentences clear, concise, and easy to read on mobile devices.\n\n"
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
        $formatted = preg_replace('/^#{1,4}\s*(.*?)$/m', '*$1*', $formatted);
        $formatted = preg_replace('/^[\*\-]\s+/m', '? ', $formatted);
        $formatted = trim($formatted);

        $sourceText = !empty($sources) ? implode(', ', $sources) : 'Official PGIA Handbooks';
        $scorePercent = round($topScore * 100);

        $footer = "\n\n????????????????\n"
                . "??? *PGIA Official Assistant*\n"
                . "?? _Ref: {$sourceText}_\n"
                . "?? _Match Confidence: {$scorePercent}%_";

        return $formatted . $footer;
    }
}
