<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RagService;
use Illuminate\Support\Facades\DB;

class BuildKnowledge extends Command
{
    protected $signature = 'knowledge:build {--clear : Clear existing knowledge base before importing}';
    protected $description = 'Ingest combined_knowledge.txt into SQLite using Dell T440 embeddings';

    public function handle(RagService $rag)
    {
        $filePath = storage_path('app/combined_knowledge.txt');
        if (!file_exists($filePath)) {
            $this->error("File not found at: {$filePath}");
            return 1;
        }

        if ($this->option('clear')) {
            $this->warn("Clearing existing knowledge base...");
            DB::table('knowledge_base')->truncate();
        }

        $this->info("Reading combined knowledge file...");
        $content = file_get_contents($filePath);

        // Split into files based on === SOURCE: filename ===
        $sections = preg_split('/=== SOURCE: (.*?) ===/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $chunksToInsert = [];
        $currentSource = 'General Document';

        for ($i = 0; $i < count($sections); $i++) {
            $part = trim($sections[$i]);
            if (empty($part)) continue;

            if (str_ends_with($part, '.pdf')) {
                $currentSource = $part;
                continue;
            }

            // Chunk using safe sliding window
            $chunks = $this->chunkText($part, 700, 100);
            foreach ($chunks as $c) {
                $chunksToInsert[] = [
                    'source' => $currentSource,
                    'text' => $c,
                ];
            }
        }

        $total = count($chunksToInsert);
        $this->info("Prepared {$total} safe chunks (< 700 chars). Generating embeddings on Dell T440...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $saved = 0;
        foreach ($chunksToInsert as $item) {
            try {
                $vector = $rag->embed($item['text']);
                if (!empty($vector)) {
                    DB::table('knowledge_base')->insert([
                        'source' => $item['source'],
                        'chunk_text' => $item['text'],
                        'embedding' => json_encode($vector),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $saved++;
                }
            } catch (\Exception $e) {
                // If any error, log and continue without crashing
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Successfully saved {$saved}/{$total} chunks into knowledge_base!");

        return 0;
    }

    private function chunkText(string $text, int $chunkSize = 700, int $overlap = 100): array
    {
        $chunks = [];
        $start = 0;
        $length = strlen($text);

        while ($start < $length) {
            $sub = substr($text, $start, $chunkSize);
            if ($start + $chunkSize < $length) {
                $lastSpace = strrpos($sub, ' ');
                if ($lastSpace !== false && $lastSpace > $chunkSize * 0.6) {
                    $sub = substr($sub, 0, $lastSpace);
                }
            }
            $trimmed = trim($sub);
            if (strlen($trimmed) >= 40) {
                $chunks[] = $trimmed;
            }
            $advance = strlen($sub) - $overlap;
            if ($advance <= 0) {
                $advance = $chunkSize;
            }
            $start += $advance;
        }

        return $chunks;
    }
}
