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
            $table->longText('detail')->nullable()->after('deskripsi');
            $table->date('tanggal_terbit')->nullable()->after('url_teks');
            $table->string('status_akses')->nullable()->default('public')->after('tanggal_terbit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge', function (Blueprint $table) {
            $table->dropColumn(['detail', 'tanggal_terbit', 'status_akses']);
        });
    }
};
