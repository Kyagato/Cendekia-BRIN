<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'pertanyaan' => 'Apa itu Cendekia BRIN?',
                'jawaban' => 'Cendekia BRIN adalah platform Knowledge Management System untuk mengelola pengetahuan.',
                'kategori_faq' => 'Umum',
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Siapa yang dapat menggunakan Cendekia BRIN?',
                'jawaban' => 'Seluruh pegawai BRIN dan instansi terkait.',
                'kategori_faq' => 'Umum',
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Apa saja tipe konten yang tersedia?',
                'jawaban' => 'Terdapat berbagai macam tipe konten seperti artikel, dokumen, video, dll.',
                'kategori_faq' => 'Umum',
                'urutan' => 3,
            ],
            [
                'pertanyaan' => 'Bagaimana cara mendaftar?',
                'jawaban' => 'Anda bisa mendaftar melalui halaman registrasi menggunakan email institusi.',
                'kategori_faq' => 'Akun',
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Bagaimana cara mereset password?',
                'jawaban' => 'Gunakan fitur lupa password di halaman login.',
                'kategori_faq' => 'Akun',
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Bagaimana cara mengunggah pengetahuan?',
                'jawaban' => 'Login dan masuk ke dashboard, lalu pilih menu tambah pengetahuan.',
                'kategori_faq' => 'Konten',
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Berapa ukuran maksimal file yang dapat diunggah?',
                'jawaban' => 'Ukuran maksimal file adalah 50MB.',
                'kategori_faq' => 'Konten',
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Browser apa yang direkomendasikan?',
                'jawaban' => 'Kami merekomendasikan Google Chrome, Mozilla Firefox, atau Microsoft Edge terbaru.',
                'kategori_faq' => 'Teknis',
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Bagaimana jika menemukan bug?',
                'jawaban' => 'Silakan hubungi tim support kami melalui email atau form kontak.',
                'kategori_faq' => 'Teknis',
                'urutan' => 2,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
