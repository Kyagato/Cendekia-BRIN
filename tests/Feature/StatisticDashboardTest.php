<?php

use App\Models\Category;
use App\Models\Knowledge;
use App\Models\User;
use Carbon\Carbon;

it('allows admin to access statistics dashboard with default view', function () {
    $admin = User::factory()->create(['role' => 'Super Admin']);
    $category = Category::create(['nama_kategori' => 'Kategori Statistik']);

    Knowledge::create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'judul' => 'Pengetahuan Statistik',
        'tipe' => 'Teks',
        'status' => 'Disetujui',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.statistik'));
    $response->assertStatus(200);
    $response->assertViewIs('admin.statistik');
    $response->assertViewHas('periode', 'semua');
    $response->assertViewHas('totalKnowledge', 1);
    $response->assertViewHas('countTeks', 1);
    $response->assertViewHas('periodeTeks', 1);
    $response->assertViewHas('periodeLabel', 'Semua Waktu');
});

it('allows admin to filter statistics dashboard by harian (1 hari), mingguan (7 hari), bulanan (1 bulan), and tahunan (1 tahun)', function () {
    $admin = User::factory()->create(['role' => 'Admin']);
    $category = Category::create(['nama_kategori' => 'Kategori Periode']);

    // Konten hari ini (1 hari)
    Knowledge::create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'judul' => 'Pengetahuan Hari Ini',
        'tipe' => 'Video',
        'status' => 'Disetujui',
        'created_at' => Carbon::today()->addHours(2),
    ]);

    // Konten 3 hari lalu (tidak masuk harian, tapi masuk mingguan)
    Knowledge::create([
        'user_id' => $admin->id,
        'category_id' => $category->id,
        'judul' => 'Pengetahuan 3 Hari Lalu',
        'tipe' => 'Video',
        'status' => 'Disetujui',
        'created_at' => Carbon::today()->subDays(3),
    ]);

    // 1. Harian: Hanya 1 hari (hari ini) -> harus bernilai 1
    $responseHarian = $this->actingAs($admin)->get(route('admin.statistik', ['periode' => 'harian']));
    $responseHarian->assertStatus(200);
    $responseHarian->assertViewHas('periode', 'harian');
    $responseHarian->assertViewHas('periodeLabel', 'Harian');
    $responseHarian->assertViewHas('periodeVideo', 1);

    // 2. Mingguan: 7 hari terakhir -> harus bernilai 2
    $responseMingguan = $this->actingAs($admin)->get(route('admin.statistik', ['periode' => 'mingguan']));
    $responseMingguan->assertStatus(200);
    $responseMingguan->assertViewHas('periode', 'mingguan');
    $responseMingguan->assertViewHas('periodeLabel', 'Mingguan');
    $responseMingguan->assertViewHas('periodeVideo', 2);

    // 3. Bulanan: 1 bulan ini -> label 'Bulanan'
    $responseBulanan = $this->actingAs($admin)->get(route('admin.statistik', ['periode' => 'bulanan']));
    $responseBulanan->assertStatus(200);
    $responseBulanan->assertViewHas('periode', 'bulanan');
    $responseBulanan->assertViewHas('periodeLabel', 'Bulanan');

    // 4. Tahunan: 1 tahun ini -> label 'Tahunan'
    $responseTahunan = $this->actingAs($admin)->get(route('admin.statistik', ['periode' => 'tahunan']));
    $responseTahunan->assertStatus(200);
    $responseTahunan->assertViewHas('periode', 'tahunan');
    $responseTahunan->assertViewHas('periodeLabel', 'Tahunan');
});

it('forbids regular members from accessing statistics dashboard', function () {
    $member = User::factory()->create(['role' => 'Anggota']);

    $response = $this->actingAs($member)->get(route('admin.statistik'));
    $response->assertStatus(403);
});
