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
        // 1. Update data user lama yang ber-role 'Anggota', 'member', 'Guest', 'Publik', 'Karyawan BRIN' menjadi 'Kreator Pengetahuan'
        DB::table('users')
            ->whereIn('role', ['Anggota', 'member', 'Member', 'Guest', 'Publik', 'Karyawan BRIN'])
            ->orWhereNull('role')
            ->update(['role' => 'Kreator Pengetahuan']);

        // 2. Ubah definisi kolom role di tabel users (tanpa 'Anggota' / 'Guest', default set ke 'Kreator Pengetahuan')
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'Super Admin',
                'Admin Pusat',
                'Admin IPPD',
                'Kreator Pengetahuan',
                'Analisis Pengetahuan',
                'Moderator',
            ])->default('Kreator Pengetahuan')->after('instansi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'Super Admin',
                'Admin Pusat',
                'Admin IPPD',
                'Kreator Pengetahuan',
                'Analisis Pengetahuan',
                'Moderator',
                'Anggota',
                'Guest',
            ])->default('Anggota')->after('instansi');
        });
    }
};
