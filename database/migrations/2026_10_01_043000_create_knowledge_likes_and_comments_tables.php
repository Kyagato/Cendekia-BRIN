<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Likes untuk Pengetahuan
        Schema::create('knowledge_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_id')->constrained('knowledge')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['knowledge_id', 'user_id']);
        });

        // Tabel Komentar untuk Pengetahuan (Mendukung balasan bersarang / nested replies)
        Schema::create('knowledge_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_id')->constrained('knowledge')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('knowledge_comments')->cascadeOnDelete();
            $table->text('konten');
            $table->timestamps();

            $table->index(['knowledge_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_comments');
        Schema::dropIfExists('knowledge_likes');
    }
};
