<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RagService;

class AskQuestion extends Command
{
    protected $signature = 'rag:ask {question : The question to ask the knowledge base}';
    protected $description = 'Ask a question grounded against the knowledge base';

    public function handle(RagService $rag)
    {
        $question = $this->argument('question');
        $this->info("User Question: {$question}");
        $this->comment("1. Searching vector database using Dell T440 embeddings...");
        $this->comment("2. Generating answer via Groq API...");

        $start = microtime(true);
        $res = $rag->answer($question);
        $elapsed = round(microtime(true) - $start, 2);

        $this->newLine();
        $this->info("AI Answer ({$elapsed}s):");
        $this->line($res['answer']);
        $this->newLine();
        $this->comment("Top Matched Sources: " . implode(', ', array_unique($res['sources'])));
        $this->comment("Match Confidence Score: " . round($res['top_score'] * 100, 1) . "%");

        return 0;
    }
}
