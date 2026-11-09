<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\AuditLog;

class DeployWebhookController extends Controller
{
    /**
     * Handle incoming CI/CD deployment webhook from GitHub Actions or Webhooks
     */
    public function handle(Request $request)
    {
        $expectedSecret = env('DEPLOY_WEBHOOK_SECRET', 'pgia_ci_cd_secret_2026');
        $providedSecret = $request->header('X-Deploy-Secret')
                       ?? $request->bearerToken()
                       ?? $request->input('secret');

        // Optional GitHub HMAC SHA-256 signature check
        $githubSignature = $request->header('X-Hub-Signature-256');
        $isSignatureValid = false;
        if ($githubSignature && $expectedSecret) {
            $payload = $request->getContent();
            $computed = 'sha256=' . hash_hmac('sha256', $payload, $expectedSecret);
            $isSignatureValid = hash_equals($computed, $githubSignature);
        }

        if (!$isSignatureValid && $providedSecret !== $expectedSecret) {
            Log::warning('Unauthorized CI/CD deploy attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Unauthorized deploy token'], 401);
        }

        $deployScript = '/var/www/whatsapp-chatbot/scripts/deploy.sh';
        if (!file_exists($deployScript)) {
            return response()->json(['status' => 'error', 'message' => 'Deploy script not found'], 500);
        }

        Log::info('CI/CD Deployment triggered via Webhook', [
            'ip' => $request->ip(),
            'sender' => $request->input('sender.login', 'github-actions')
        ]);

        // Execute deploy script and capture output
        $output = [];
        $exitCode = 0;
        exec("/bin/bash {$deployScript} 2>&1", $output, $exitCode);
        $logOutput = implode("\n", $output);

        // Record in Audit Ledger
        try {
            AuditLog::create([
                'user_id' => 1,
                'user_email' => 'ci-cd@pgia.pdn.ac.lk',
                'action' => 'ci_cd_deployment',
                'target_type' => 'System',
                'target_id' => 1,
                'details' => [
                    'exit_code' => $exitCode,
                    'commit' => exec('cd /opt/pgia-student-chatbot && git rev-parse --short HEAD'),
                    'trigger' => $request->input('ref', 'webhook_dispatch')
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent() ?? 'GitHub-Hookshot',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log deploy audit: ' . $e->getMessage());
        }

        if ($exitCode === 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'PGIA AI Chatbot deployed and verified successfully',
                'output' => $output
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Deployment script failed',
            'exit_code' => $exitCode,
            'output' => $output
        ], 500);
    }
}
