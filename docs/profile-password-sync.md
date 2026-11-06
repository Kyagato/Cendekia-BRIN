# Sinkronisasi kata sandi profil

Form Pengaturan Profil dan endpoint PUT /password menggunakan PasswordSyncService.
Untuk akun dengan keycloak_id, layanan memanggil PUT /admin/realms/{realm}/users/{id}/reset-password,
dengan kredensial permanen (temporary=false), lalu menyelesaikan transaksi lokal.
Akun tanpa keycloak_id hanya memperbarui hash lokal. Token Sanctum lama dicabut.

## Konfigurasi

- KEYCLOAK_BASE_URL, KEYCLOAK_REALM, KEYCLOAK_CLIENT_ID, dan KEYCLOAK_CLIENT_SECRET harus sesuai server tujuan.
- Client perlu client authentication dan service account yang diizinkan mengelola kredensial pengguna
  pada realm tujuan. Pada konfigurasi realm-management standar, periksa role manage-users;
  instalasi dengan fine-grained permissions perlu izin setara. Jangan menambahkan realm-admin hanya untuk fitur ini.
- Gunakan HTTPS untuk server di luar localhost. Jangan menyimpan secret atau kata sandi di Git.
- Respons 400 (misalnya kebijakan kata sandi), 403 (izin), kegagalan token, atau koneksi gagal
  menghasilkan error pada form dan rollback hash lokal. Jangan tampilkan respons mentah yang sensitif.

## Verifikasi

Pengujian otomatis memalsukan HTTP Keycloak; belum mengubah password akun nyata.
Untuk uji menyeluruh gunakan akun uji: ubah melalui profil, logout, login SSO dengan password baru,
kemudian verifikasi login API lokal. Jangan memakai kredensial produksi sebagai data tes.

Database dan Keycloak bukan satu transaksi terdistribusi: timeout setelah Keycloak menerima perubahan
atau kegagalan commit database tetap dapat memerlukan rekonsiliasi/manual reset. Tidak ada klaim atomicity lintas sistem.

Perubahan ini khusus perubahan password setelah login melalui profil dan PUT /password.
Alur lupa password OTP serta perubahan password melalui admin belum menggunakan layanan ini.

Referensi: https://www.keycloak.org/docs-api/latest/rest-api/index.html
