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
        Schema::table('knowledge', function (Blueprint $table) {
            // Indeks komposit untuk kueri materi terpopuler berdasarkan status persetujuan
            $table->index(['status', 'views_count'], 'idx_knowledge_status_views_count');

            // Indeks komposit untuk filter repositori materi publik
            $table->index(['status', 'status_akses', 'created_at'], 'idx_knowledge_status_akses_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge', function (Blueprint $table) {
            $table->dropIndex('idx_knowledge_status_views_count');
            $table->dropIndex('idx_knowledge_status_akses_created');
        });
    }
};
