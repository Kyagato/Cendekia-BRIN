<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            FaqSeeder::class,
        ]);

        // ============================================================
        // Akun Super Admin (utama)
        // ============================================================
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@brin.go.id',
            'password' => bcrypt('admin123'),
            'role' => 'Super Admin',
            'instansi' => 'BRIN Pusat',
            'jenis_kelamin' => 'L',
        ]);

        // ============================================================
        // 6 Akun Dummy untuk setiap role (password: password123)
        // ============================================================
        $dummyUsers = [
            [
                'name' => 'Admin Pusat',
                'email' => 'pusat@simpan.brin',
                'role' => 'Admin Pusat',
                'instansi' => 'BRIN Pusat',
                'jenis_kelamin' => 'L',
            ],
            [
                'name' => 'Admin IPPD',
                'email' => 'ippd@simpan.brin',
                'role' => 'Admin IPPD',
                'instansi' => 'IPPD Serpong',
                'jenis_kelamin' => 'P',
            ],
            [
                'name' => 'Kreator Pengetahuan',
                'email' => 'kreator@simpan.brin',
                'role' => 'Anggota',
                'instansi' => 'Pusat Riset Teknologi',
                'jenis_kelamin' => 'L',
            ],
            [
                'name' => 'Analis Pengetahuan',
                'email' => 'analis@simpan.brin',
                'role' => 'Analisis Pengetahuan',
                'instansi' => 'Pusat Riset Kebijakan',
                'jenis_kelamin' => 'P',
            ],
            [
                'name' => 'Moderator Forum',
                'email' => 'moderator@simpan.brin',
                'role' => 'Moderator',
                'instansi' => 'BRIN Pusat',
                'jenis_kelamin' => 'L',
            ],
            [
                'name' => 'Anggota Biasa (Pengguna Umum)',
                'email' => 'anggota@simpan.brin',
                'role' => 'Anggota',
                'instansi' => 'Universitas Negeri Surabaya',
                'jenis_kelamin' => 'P',
            ],

        ];

        foreach ($dummyUsers as $userData) {
            User::create(array_merge($userData, [
                'password' => bcrypt('password123'),
            ]));
        }

        $this->command->info('✅ 7 akun pengguna berhasil dibuat (1 Super Admin + 6 dummy).');
    }
}
