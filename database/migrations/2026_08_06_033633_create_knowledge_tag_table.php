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
    Schema::create('knowledge_tag', function (Blueprint $table) {
        // Menghubungkan ID dari tabel knowledge dan tabel tags
        $table->foreignId('knowledge_id')->constrained('knowledge')->cascadeOnDelete();
        $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();

        // Mencegah label yang sama ditambahkan 2 kali pada satu artikel yang sama
        $table->primary(['knowledge_id', 'tag_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_tag');
    }
};
