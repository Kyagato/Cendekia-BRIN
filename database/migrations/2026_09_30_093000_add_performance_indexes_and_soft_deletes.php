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
        // 1. Tambah soft deletes dan indeks performa ke tabel knowledge
        Schema::table('knowledge', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');

            // Komposit indeks untuk filter yang sering dipakai bersama
            $table->index(['status', 'tipe', 'created_at'], 'idx_knowledge_status_tipe_created');
            $table->index(['status', 'category_id'], 'idx_knowledge_status_category');
            $table->index(['status', 'unggulan'], 'idx_knowledge_status_unggulan');
            $table->index(['user_id', 'status'], 'idx_knowledge_user_status');
        });

        // 2. Tambah soft deletes dan indeks ke forum_threads
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');

            $table->index(['status', 'is_pinned', 'created_at'], 'idx_threads_status_pinned_created');
            $table->index(['status', 'category_id'], 'idx_threads_status_category');
            $table->index(['user_id', 'status'], 'idx_threads_user_status');
        });

        // 3. Tambah soft deletes dan indeks ke forum_replies
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');

            $table->index(['thread_id', 'parent_id', 'created_at'], 'idx_replies_thread_parent');
            $table->index(['user_id'], 'idx_replies_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge', function (Blueprint $table) {
            $table->dropIndex('idx_knowledge_status_tipe_created');
            $table->dropIndex('idx_knowledge_status_category');
            $table->dropIndex('idx_knowledge_status_unggulan');
            $table->dropIndex('idx_knowledge_user_status');
            $table->dropSoftDeletes();
        });

        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropIndex('idx_threads_status_pinned_created');
            $table->dropIndex('idx_threads_status_category');
            $table->dropIndex('idx_threads_user_status');
            $table->dropSoftDeletes();
        });

        Schema::table('forum_replies', function (Blueprint $table) {
            $table->dropIndex('idx_replies_thread_parent');
            $table->dropIndex('idx_replies_user');
            $table->dropSoftDeletes();
        });
    }
};
