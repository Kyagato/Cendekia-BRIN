<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Infrastruktur & Keamanan',
            'Layanan Publik Digital',
            'Tata Kelola SPBE',
            'Sumber Daya Manusia',
            'Inovasi & Riset',
            'Kebijakan & Regulasi',
            'Transformasi Digital',
            'Data & Informasi',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'nama_kategori' => $category,
            ]);
        }
    }
}
