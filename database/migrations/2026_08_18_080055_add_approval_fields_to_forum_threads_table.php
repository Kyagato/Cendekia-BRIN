<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_locked');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_note')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['status', 'approved_at', 'rejection_note']);
        });
    }
};
