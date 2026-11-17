# Manajemen pengguna Keycloak

Halaman `/admin/users` tersedia untuk Super Admin, Admin Pusat, dan Admin. Daftar membaca pengguna Keycloak, termasuk yang belum pernah masuk secara lokal. Pencarian, filter peran, dan pengurutan tersedia. Foto profil berasal dari akun lokal yang terhubung; jika tidak ada, inisial ditampilkan. Pada dashboard ketiga role tersebut, halaman ini berada di menu **Manajemen > Manajemen Pengguna**. Menu Manajemen juga memuat Validasi Pengetahuan dan Atur Forum; Analis dan Moderator tetap melihat menu yang sesuai secara terpisah.

## Fitur dan hak akses

- Super Admin dan Admin Pusat dapat membuat pengguna. Super Admin dapat memilih semua peran aplikasi; Admin Pusat dapat memilih Admin, Analisis Pengetahuan, Moderator, atau Anggota.
- Ketiga admin dapat melihat daftar dan mengedit, mengubah password, atau menghapus pengguna sesuai hierarki. Super Admin dapat mengelola pengguna lain; Admin Pusat dapat mengelola Admin dan peran nonadmin; Admin dapat mengelola peran nonadmin.
- Pengelolaan akun sendiri dan akun layanan Keycloak diblokir. Aplikasi mempertimbangkan peran efektif Keycloak dan peran lokal yang terhubung, memakai tingkat yang lebih tinggi. Jika peran tidak dapat dibaca, akses pengelolaan ditolak.
- Username, nama depan, nama belakang, email, dan peran dapat diubah. Status aktif tidak dapat diubah melalui formulir edit. Password memiliki formulir terpisah. Penghapusan bersifat permanen dan perlu dikonfirmasi; periksa dampak pada data terkait di dialog konfirmasi.
- Pengguna dikenali berdasarkan ID Keycloak; akun tidak otomatis ditautkan berdasarkan email atau username.

## Konfigurasi

1. Atur `KEYCLOAK_BASE_URL`, `KEYCLOAK_REALM`, `KEYCLOAK_CLIENT_ID`, dan `KEYCLOAK_CLIENT_SECRET` pada server. Aktifkan service account pada client confidential.
2. Berikan izin `realm-management` untuk mencari, membaca, dan mengelola pengguna, serta membaca client dan pemetaan perannya. Peran yang umum digunakan adalah `query-users`, `view-users`, `manage-users`, `view-clients`, dan `view-realm`; gunakan izin lebih khusus jika tersedia.
3. Pastikan peran aplikasi tersedia sebagai **client role** pada client `KEYCLOAK_CLIENT_ID` untuk pembuatan pengguna, dan sebagai **realm role** untuk alur edit peran saat ini. Periksa kebijakan username, email, dan password realm.
4. Jalankan migrasi database sebelum menggunakan fitur, termasuk migrasi username dan catatan sinkronisasi. Pada produksi, jalankan `php artisan migrate --force` sesuai prosedur deployment.

Jangan menyimpan client secret atau token admin di frontend. Pembukaan daftar tidak mengubah pengguna Keycloak.

## Pembuatan pengguna

Formulir menerima username, nama depan, nama belakang, email, peran, password awal, dan konfirmasi password. Username dinormalisasi menjadi huruf kecil. Password awal dikirim ke Keycloak sebagai password permanen (`temporary=false`); formulir tidak mewajibkan perubahan password saat login pertama.

Aplikasi membuat akun Keycloak dalam keadaan nonaktif, memasang peran pada client `KEYCLOAK_CLIENT_ID`, lalu mengaktifkan akun. Identitas, peran efektif, dan client role dibaca kembali sebelum akun lokal dibuat dengan ID Keycloak yang dikembalikan. Kunci permintaan unik dan cache lock membantu membatasi pengiriman ganda.

## Perubahan pengguna dan lokasi peran

Perubahan identitas dan peran dikirim ke Keycloak dan dibaca kembali. Setelah terkonfirmasi, akun lokal yang terhubung diperbarui berdasarkan ID Keycloak. Perubahan email mereset verifikasi email di Keycloak dan akun lokal. Jika belum terhubung, akun lokal tidak dibuat atau ditautkan otomatis.

**Lokasi peran berbeda menurut operasi:**

- Pembuatan pengguna memasang peran sebagai client role pada client aplikasi yang dikonfigurasi.
- Edit peran menghapus pemetaan langsung peran aplikasi yang dikenali pada realm dan client aplikasi, lalu memasang peran yang dipilih sebagai realm role. Setelah edit, peran baru tidak selalu muncul pada daftar pengguna client role. Client lain tidak menjadi target penghapusan ini. Peran efektif diperiksa kembali.

Password diperbarui melalui endpoint reset-password Keycloak dengan `temporary=false`. Setelah keberhasilan Keycloak terkonfirmasi, hash password diperbarui pada akun lokal yang terhubung, token Sanctum dicabut, dan sesi lokal dengan driver database dihapus. Ini tidak menjamin pencabutan semua sesi SSO Keycloak. Password asli tidak disimpan atau dicatat; hash sementara dienkripsi dalam catatan sinkronisasi dan dihapus setelah penyelesaian atau penolakan.

Penghapusan menghapus akun di Keycloak terlebih dahulu, kemudian akun lokal yang terhubung. Kedua sistem tidak dapat berbagi satu transaksi atomik; gangguan dapat membuat salah satu sisi berubah sementara sisi lainnya belum selesai.

## Penanganan kegagalan dan rekonsiliasi

1. Jika sinkronisasi belum selesai, jangan langsung mengulang pembuatan atau perubahan. Periksa koneksi, izin service account, kebijakan realm, catatan `keycloak_user_syncs`, dan audit berdasarkan ID referensi yang tersedia. Batasi akses ke catatan karena snapshot memuat data identitas.
2. Setelah penyebab gangguan ditangani, administrator dapat menjalankan `php artisan keycloak:reconcile-users`. Perintah memeriksa status `reconciliation_required` dan `pending` yang tidak diperbarui selama lebih dari lima menit. Perintah dijalankan secara manual; perhatikan hasil `Synced`, `Review required`, atau `Synchronization pending`, lalu periksa statusnya.
3. Rekonsiliasi memeriksa kondisi Keycloak sebelum menyelesaikan perubahan lokal. Data yang bertentangan atau lebih baru dapat ditandai `requires_review`; status ini perlu pemeriksaan manual dan tidak otomatis diproses ulang oleh perintah tersebut.
4. Jika hasil pembuatan tidak diketahui atau ID Keycloak belum didapat, periksa secara manual sebelum mencoba lagi. Sistem tidak otomatis menautkan akun berdasarkan email atau username. Jika ID diketahui tetapi pemasangan peran gagal dan akun masih nonaktif, administrator harus memeriksa dan menyelesaikannya; rekonsiliasi tidak diam-diam mengaktifkan atau menaikkan peran akun.
5. Jika hasil perubahan password tidak pasti karena timeout atau gangguan, lakukan pemeriksaan manual. Pemulihan tidak mengasumsikan password telah berubah. Jika perubahan Keycloak terkonfirmasi tetapi pembaruan lokal gagal, rekonsiliasi dapat menyelesaikan bagian lokal berdasarkan data pemulihan yang tersedia.

Audit mencatat aktor, ID Keycloak, hasil, dan referensi sinkronisasi tanpa mencatat password atau token admin. Dashboard audit dikerjakan terpisah.

## Pengujian

Tes fitur menggunakan `Http::fake` dan SQLite sementara sehingga tidak mengubah Keycloak sungguhan. Pemeriksaan langsung membutuhkan instance Keycloak, konfigurasi client dan peran yang sesuai, serta izin service account di atas. Gunakan akun uji dan periksa lokasi realm role atau client role sesuai operasinya.

Referensi API: https://www.keycloak.org/docs-api/latest/rest-api/index.html
