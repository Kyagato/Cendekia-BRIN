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
    Schema::create('knowledge', function (Blueprint $table) {
        $table->id();
        
        // Relasi ke pembuat (User) dan Kategori
        $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        
        // Informasi Konten
        $table->string('judul');
        $table->text('deskripsi')->nullable();
        
        // 4 Tipe sesuai rancanganmu
        $table->enum('tipe', ['Teks', 'Video', 'Gambar', 'Audio']); 
        
        // Lokasi penyimpanan file (PDF, MP4, JPG, dll)
        $table->string('file_path')->nullable(); 
        
        // Status untuk alur validasi oleh role Analisis Pengetahuan
        $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak'])->default('Draft');
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge');
    }
};
