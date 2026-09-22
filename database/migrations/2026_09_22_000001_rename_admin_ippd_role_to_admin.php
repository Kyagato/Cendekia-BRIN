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
        // 1. Tambahkan 'Admin' ke definisi ENUM kolom role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super Admin', 'Admin Pusat', 'Admin IPPD', 'Admin', 'Anggota', 'Analisis Pengetahuan', 'Moderator') NOT NULL DEFAULT 'Anggota'");

        // 2. Ubah data pengguna yang ber-role 'Admin IPPD' menjadi 'Admin'
        DB::table('users')
            ->where('role', 'Admin IPPD')
            ->update(['role' => 'Admin']);

        // 3. Finalisasi ENUM kolom role (resmi tanpa Admin IPPD)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super Admin', 'Admin Pusat', 'Admin', 'Anggota', 'Analisis Pengetahuan', 'Moderator') NOT NULL DEFAULT 'Anggota'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Tambahkan kembali 'Admin IPPD' ke ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super Admin', 'Admin Pusat', 'Admin IPPD', 'Admin', 'Anggota', 'Analisis Pengetahuan', 'Moderator') NOT NULL DEFAULT 'Anggota'");

        // 2. Kembalikan data 'Admin' menjadi 'Admin IPPD'
        DB::table('users')
            ->where('role', 'Admin')
            ->update(['role' => 'Admin IPPD']);

        // 3. Kembalikan ENUM ke format lama
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('Super Admin', 'Admin Pusat', 'Admin IPPD', 'Anggota', 'Analisis Pengetahuan', 'Moderator') NOT NULL DEFAULT 'Anggota'");
    }
};
