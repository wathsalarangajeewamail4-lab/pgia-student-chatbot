<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->string('filename')->unique();
                $table->string('original_name');
                $table->string('file_path')->nullable();
                $table->unsignedBigInteger('file_size')->default(0);
                $table->string('mime_type')->default('application/pdf');
                $table->string('checksum')->nullable();
                $table->integer('chunk_count')->default(0);
                $table->string('status')->default('active'); // active, processing, pending_sync, failed
                $table->text('error_message')->nullable();
                $table->string('uploaded_by')->nullable();
                $table->timestamp('last_synced_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
