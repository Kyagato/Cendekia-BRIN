<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom role dengan 8 opsi, default ke 'Reader' (Pengguna Terdaftar)
            $table->enum('role', [
                'Guest', 
                'Reader', 
                'Creator', 
                'Analyst', 
                'Manager', 
                'Auditor', 
                'Executive', 
                'Admin'
            ])->default('Reader')->after('instansi');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
