<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('original_name')->nullable();                         // null para links
            $table->enum('type', ['image', 'video', 'document', 'link']);
            $table->string('mime_type')->nullable();                             // null para links
            $table->string('extension', 20)->nullable();                         // null para links
            $table->unsignedBigInteger('size')->default(0);                      // 0 para links
            $table->string('path')->nullable();                                  // ruta en storage (null para links)
            $table->string('disk')->default('private');                           // disco privado
            $table->string('external_url', 2048)->nullable();                    // URL externa para links
            $table->string('folder')->nullable();
            $table->string('description')->nullable();
            $table->json('tags')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedBigInteger('downloads')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index('folder');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};