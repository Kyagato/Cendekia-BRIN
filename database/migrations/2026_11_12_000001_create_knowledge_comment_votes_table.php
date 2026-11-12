<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_comment_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->smallInteger('value');
            $table->timestamps();
            $table->unique(['knowledge_comment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_comment_votes');
    }
};
