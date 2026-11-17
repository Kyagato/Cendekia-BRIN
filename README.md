# MojoPedia

MojoPedia adalah aplikasi manajemen dan berbagi pengetahuan berbasis Laravel. Aplikasi menyediakan pengetahuan, forum, profil pengguna, dan dashboard sesuai peran, serta integrasi autentikasi dan pengelolaan pengguna dengan Keycloak.

## Fitur utama

- Pengetahuan dengan kategori, lampiran, pilihan akses, dan proses validasi.
- Forum dan komentar pengetahuan, termasuk upvote, downvote, dan pengurutan komentar.
- Profil pengguna, bookmark, dan pengaturan password.
- Dashboard berdasarkan peran: Super Admin, Admin Pusat, Admin, Analisis Pengetahuan, Moderator, dan Anggota.
- Manajemen pengguna Keycloak berdasarkan hak akses. Pembuatan pengguna tersedia untuk Super Admin dan Admin Pusat.
- API Laravel Sanctum dan dokumentasi Swagger pada `/api/documentation` sesuai pengaturan akses aplikasi.

Pada dashboard Super Admin, Admin Pusat, dan Admin, dropdown **Manajemen** berisi Manajemen Pengguna, Validasi Pengetahuan, dan Atur Forum. Menu Validasi bagi Analis dan Atur Forum bagi Moderator tetap tersedia secara terpisah.

Audit Log tersedia sebagai menu tersendiri pada dashboard Super Admin, Admin Pusat, dan Admin. Halaman bersifat baca-saja dan menyediakan filter tanggal, aktivitas, pelaku, dan hasil. Pencatatan yang sudah ada tidak mencakup tindakan yang dilakukan langsung di konsol Keycloak.

## Persyaratan

- PHP 8.2 atau lebih baru beserta ekstensi yang diperlukan dependensi Laravel.
- Composer, Node.js, dan npm.
- Database yang sesuai konfigurasi `.env` dan akses ke instance Keycloak untuk fitur SSO dan manajemen pengguna.

## Menjalankan secara lokal

1. Instal dependensi: `composer install` dan `npm install`.
2. Salin `.env.example` menjadi `.env`, lalu sesuaikan `APP_URL` dan konfigurasi database. Jika menggunakan SQLite, siapkan file database sesuai konfigurasi. Jangan menyalin atau membagikan secret dari lingkungan lain.
3. Buat kunci aplikasi dengan `php artisan key:generate`, lalu jalankan `php artisan migrate`.
4. Untuk SSO dan manajemen pengguna, tambahkan dan sesuaikan `KEYCLOAK_BASE_URL`, `KEYCLOAK_REALM`, `KEYCLOAK_CLIENT_ID`, dan `KEYCLOAK_CLIENT_SECRET` di `.env`. Variabel ini tidak tersedia pada `.env.example` saat ini. Ikuti panduan Keycloak di bawah untuk pengaturan service account dan role.
5. Jalankan server dengan `php artisan serve` dan, di terminal lain, `npm run dev`. Untuk membangun aset gunakan `npm run build`.

Untuk lingkungan produksi, sesuaikan konfigurasi keamanan, penyimpanan file, database, HTTPS, dan Keycloak dengan server tujuan. Jangan menjalankan migrasi atau prosedur pemindahan file lama tanpa memastikan backup dan target database.

## Pengujian

Jalankan `php artisan test` atau `composer test`. Tes tertentu menggunakan HTTP palsu untuk Keycloak; hasilnya tidak menggantikan pengujian integrasi terhadap instance Keycloak sebenarnya.

## Dokumentasi terkait

- [Manajemen pengguna Keycloak](docs/keycloak-user-directory.md)
- [Sinkronisasi password profil](docs/profile-password-sync.md)
- [Perlindungan file pengetahuan](docs/private-knowledge-files.md)
- [Panduan desain](DESIGN_SYSTEM.md)
- [Catatan review visual kartu pengetahuan, November 2026](docs/review-visual-card-2026-11-05.md)
