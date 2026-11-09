<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;

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

// Automated CI/CD Deployment Webhook (GitHub Actions / Webhooks)
Route::post('/api/deploy/webhook', [\App\Http\Controllers\DeployWebhookController::class, 'handle']);

// -----------------------------------------------------------------------------
// Admin Authentication Routes
// -----------------------------------------------------------------------------
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// -----------------------------------------------------------------------------
// Protected Admin Console & Knowledge Lifecycle Management
// -----------------------------------------------------------------------------
Route::middleware(['web', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/journey', [DashboardController::class, 'journey'])->name('journey');
    Route::get('/documents', [DashboardController::class, 'documents'])->name('documents');
    Route::post('/documents/upload', [DashboardController::class, 'upload'])->name('documents.upload');
    Route::delete('/documents/{document}', [DashboardController::class, 'delete'])->name('documents.delete');
    Route::get('/documents/{document}/chunks', [DashboardController::class, 'previewChunks'])->name('documents.chunks');
    Route::post('/sync', [DashboardController::class, 'syncAll'])->name('sync');
    Route::get('/audit', [DashboardController::class, 'auditLogs'])->name('audit');
});
