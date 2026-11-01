<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;

// Interactive Web Chat Box
Route::get('/', function () {
    return view('chat');
});

// Direct Web/API chat endpoint
Route::post('/api/chat', [WhatsAppController::class, 'directChat']);

// System status and tunnel info
Route::get('/api/status', [WhatsAppController::class, 'getSystemStatus']);

// WhatsApp Webhook routes
Route::get('/api/whatsapp/webhook', [WhatsAppController::class, 'verifyWebhook']);
Route::post('/api/whatsapp/webhook', [WhatsAppController::class, 'handleWebhook']);
