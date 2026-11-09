<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Document;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SetupAdmin extends Command
{
    protected $signature = 'admin:setup {--email=admin@pgia.pdn.ac.lk} {--password=PgiaAdmin@2026!}';
    protected $description = 'Setup default PGIA administrator and register active knowledge base documents';

    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'PGIA Institutional Administrator',
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->info("Administrator created/updated successfully: {$email}");

        // Scan existing sources in knowledge_base table
        $sources = DB::table('knowledge_base')
            ->select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->get();

        $this->info("Scanning existing knowledge base sources ({$sources->count()} detected)...");

        foreach ($sources as $s) {
            $filename = $s->source ?: 'General_Handbook.pdf';
            Document::updateOrCreate(
                ['filename' => $filename],
                [
                    'original_name' => $filename,
                    'file_path' => 'knowledge_documents/' . $filename,
                    'file_size' => 1024 * 1024 * 2, // estimated standard PDF size
                    'mime_type' => 'application/pdf',
                    'chunk_count' => $s->count,
                    'status' => 'active',
                    'uploaded_by' => $email,
                    'last_synced_at' => now(),
                ]
            );
            $this->line("  ✓ Registered document: {$filename} ({$s->count} vector chunks)");
        }

        AuditLog::create([
            'user_id' => $user->id,
            'user_email' => $email,
            'action' => 'system_init',
            'target_type' => 'admin_setup',
            'target_id' => (string) $user->id,
            'details' => ['documents_registered' => $sources->count()],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Artisan CLI / Admin Setup',
            'created_at' => now(),
        ]);

        $this->info("Initialization complete! You can now log into the Admin Dashboard with:");
        $this->info("  Email:    {$email}");
        $this->info("  Password: {$password}");

        return 0;
    }
}
