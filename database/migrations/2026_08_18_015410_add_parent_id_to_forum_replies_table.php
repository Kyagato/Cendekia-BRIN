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
        Schema::table('forum_replies', function (Blueprint $table) {
            // Self-referencing FK for nested replies
            $table->unsignedBigInteger('parent_id')->nullable()->after('user_id');
            $table->foreign('parent_id')->references('id')->on('forum_replies')->nullOnDelete();
            // Stores the @mentioned username string for display
            $table->string('mention_user')->nullable()->after('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'mention_user']);
        });
    }
};
