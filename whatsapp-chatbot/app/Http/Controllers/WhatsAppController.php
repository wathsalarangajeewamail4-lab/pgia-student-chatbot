<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RagService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    /**
     * Meta WhatsApp Webhook Verification (GET)
     */
    public function verifyWebhook(Request $request)
    {
        $verifyToken = env('WHATSAPP_VERIFY_TOKEN', 'my_secret_token_123');

        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        Log::info('WhatsApp Webhook verification attempt:', [
            'mode' => $mode,
            'token_match' => ($token === $verifyToken),
            'challenge' => $challenge,
        ]);

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    /**
     * Incoming WhatsApp Messages (POST)
     */
    public function handleWebhook(Request $request, RagService $rag)
    {
        $payload = $request->all();
        Log::info('WhatsApp Webhook received:', $payload);

        // Check if message exists
        $message = $payload['entry'][0]['changes'][0]['value']['messages'][0] ?? null;
        if (!$message || ($message['type'] ?? '') !== 'text') {
            return response()->json(['status' => 'ignored']);
        }

        $fromNumber = $message['from']; // User's WhatsApp number
        $userText = trim($message['text']['body'] ?? '');

        if ($userText === '') {
            return response()->json(['status' => 'empty_message']);
        }

        try {
            // Grounded Hybrid RAG lookup (Vector + BM25 + BGE Reranker)
            $result = $rag->answer($userText);
            $replyText = $result['answer'];

            // WhatsApp single message body limit is 4096 chars
            if (mb_strlen($replyText) > 4000) {
                $replyText = mb_substr($replyText, 0, 3950) . "\n\n...(truncated for WhatsApp)";
            }

            // Send back via Meta Cloud API if configured
            $phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
            $metaToken = env('WHATSAPP_ACCESS_TOKEN');

            if ($phoneNumberId && $metaToken) {
                $metaResponse = Http::withToken($metaToken)
                    ->timeout(15)
                    ->post("https://graph.facebook.com/v20.0/{$phoneNumberId}/messages", [
                        'messaging_product' => 'whatsapp',
                        'recipient_type' => 'individual',
                        'to' => $fromNumber,
                        'type' => 'text',
                        'text' => ['body' => $replyText],
                    ]);

                Log::info('WhatsApp Meta Send Response:', [
                    'to' => $fromNumber,
                    'status' => $metaResponse->status(),
                    'body' => $metaResponse->json(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'reply' => $replyText,
                'sources' => $result['sources'],
                'engine' => $result['engine'] ?? 'hybrid',
            ]);
        } catch (\Exception $e) {
            Log::error('RAG Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Web/JSON Direct Chat Test endpoint
     */
    public function directChat(Request $request, RagService $rag)
    {
        $question = $request->input('message') ?? $request->input('question');
        if (!$question) {
            return response()->json(['error' => 'Please provide a "message" field in JSON'], 400);
        }

        try {
            $result = $rag->answer($question);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get system status, active tunnel info, and Hybrid RAG health
     */
    public function getSystemStatus()
    {
        $tunnelUrl = null;
        $tunnelFile = public_path('tunnel_url.txt');
        if (file_exists($tunnelFile)) {
            $tunnelUrl = trim(file_get_contents($tunnelFile));
        }

        $hybridHealth = null;
        try {
            $res = Http::timeout(2)->get('http://127.0.0.1:8003/health');
            if ($res->successful()) {
                $hybridHealth = $res->json();
            }
        } catch (\Exception $e) {
            $hybridHealth = ['status' => 'fallback'];
        }

        return response()->json([
            'tunnel_url' => $tunnelUrl,
            'webhook_url' => $tunnelUrl ? "{$tunnelUrl}/api/whatsapp/webhook" : null,
            'verify_token' => env('WHATSAPP_VERIFY_TOKEN', 'my_secret_token_123'),
            'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
            'hybrid_rag' => $hybridHealth,
            'status' => 'active',
        ]);
    }
}
