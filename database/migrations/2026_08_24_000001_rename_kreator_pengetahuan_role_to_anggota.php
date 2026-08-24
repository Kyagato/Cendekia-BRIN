<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modify enum column definition to include 'Anggota'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super Admin', 'Admin Pusat', 'Admin IPPD', 'Anggota', 'Kreator Pengetahuan', 'Analisis Pengetahuan', 'Moderator') NOT NULL DEFAULT 'Anggota'");

        // 2. Update existing 'Kreator Pengetahuan' records to 'Anggota'
        DB::table('users')
            ->where('role', 'Kreator Pengetahuan')
            ->update(['role' => 'Anggota']);

        // 3. Finalize enum column definition to use 'Anggota' as primary default
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super Admin', 'Admin Pusat', 'Admin IPPD', 'Anggota', 'Analisis Pengetahuan', 'Moderator') NOT NULL DEFAULT 'Anggota'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super Admin', 'Admin Pusat', 'Admin IPPD', 'Kreator Pengetahuan', 'Analisis Pengetahuan', 'Moderator', 'Anggota') NOT NULL DEFAULT 'Kreator Pengetahuan'");
        
        DB::table('users')
            ->where('role', 'Anggota')
            ->update(['role' => 'Kreator Pengetahuan']);
    }
};
