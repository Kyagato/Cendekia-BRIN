<template>
  <PublicLayout>
    <div class="py-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

      <!-- Breadcrumbs -->
      <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
        <Link href="/" class="hover:text-[#2563eb] dark:hover:text-blue-400 transition">Beranda</Link>
        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="font-semibold text-slate-800 dark:text-slate-200">Pengaturan Profil</span>
      </nav>

      <!-- Main Profile Settings Card -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6 sm:p-8">

        <!-- Card Header -->
        <header class="pb-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-slate-100">
              Pengaturan Profil
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
              Perbarui informasi pribadi dan kata sandi Anda.
            </p>
          </div>
          <Link
            :href="`/users/${user.id}`"
            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition self-start sm:self-auto border border-slate-200 dark:border-slate-700 shadow-sm"
          >
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span>Lihat Profil Saya</span>
          </Link>
        </header>

        <!-- Form -->
        <form @submit.prevent="submit" class="mt-6 space-y-6">

          <!-- Foto Profil Section -->
          <div class="p-4 bg-slate-50/70 dark:bg-slate-900/40 rounded-xl border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row items-center gap-5">
            <div class="relative shrink-0">
              <div class="w-24 h-24 rounded-full overflow-hidden bg-slate-200 dark:bg-slate-700 border-2 border-slate-300 dark:border-slate-600 shadow-sm flex items-center justify-center text-slate-600 dark:text-slate-200 font-bold text-2xl">
                <img
                  v-if="photoPreview"
                  :src="photoPreview"
                  :alt="user.name"
                  class="w-full h-full object-cover"
                />
                <span v-else>{{ userInitials }}</span>
              </div>
            </div>

            <div class="flex-1 text-center sm:text-left">
              <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Foto Profil</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Unggah foto profil Anda. Format didukung: JPG, PNG, GIF, atau WebP (Maks. 2MB).
              </p>
              <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                <label class="cursor-pointer inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#2563eb] hover:bg-[#1d4ed8] text-white text-xs font-semibold rounded-lg shadow-sm transition">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span>Pilih Foto Baru</span>
                  <input
                    type="file"
                    ref="photoInput"
                    class="hidden"
                    accept="image/*"
                    @change="updatePreview"
                  />
                </label>

                <button
                  type="button"
                  v-if="photoPreview"
                  @click="clearPhoto"
                  class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm transition"
                >
                  <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  <span>Hapus Foto</span>
                </button>
              </div>
              <div v-if="form.errors.foto_profil" class="mt-2 text-xs text-rose-500">
                {{ form.errors.foto_profil }}
              </div>
            </div>
          </div>

          <!-- Form Fields Grid (2 Kolom) -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 pt-2">
            <!-- Nama Lengkap -->
            <div>
              <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                Nama Lengkap
              </label>
              <input
                type="text"
                id="name"
                v-model="form.name"
                required
                class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition"
                placeholder="Nama lengkap Anda"
              />
              <div v-if="form.errors.name" class="mt-1 text-xs text-rose-500">
                {{ form.errors.name }}
              </div>
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                Email
              </label>
              <input
                type="email"
                id="email"
                v-model="form.email"
                required
                class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition"
                placeholder="nama@email.com"
              />
              <div v-if="form.errors.email" class="mt-1 text-xs text-rose-500">
                {{ form.errors.email }}
              </div>
            </div>

            <!-- Nomor Telepon -->
            <div>
              <label for="no_telepon" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                Nomor Telepon
              </label>
              <input
                type="text"
                id="no_telepon"
                v-model="form.no_telepon"
                class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition"
                placeholder="08xxxxxxxxxx"
              />
              <div v-if="form.errors.no_telepon" class="mt-1 text-xs text-rose-500">
                {{ form.errors.no_telepon }}
              </div>
            </div>

            <!-- Pekerjaan -->
            <div>
              <label for="pekerjaan" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                Pekerjaan
              </label>
              <input
                type="text"
                id="pekerjaan"
                v-model="form.pekerjaan"
                class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition"
                placeholder="Contoh: Mahasiswa, Pegawai Negeri, dll."
              />
              <div v-if="form.errors.pekerjaan" class="mt-1 text-xs text-rose-500">
                {{ form.errors.pekerjaan }}
              </div>
            </div>

            <!-- Jenis Kelamin -->
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">
                Jenis Kelamin
              </label>
              <div class="flex items-center gap-6">
                <label class="inline-flex items-center gap-2.5 cursor-pointer text-sm font-medium text-slate-700 dark:text-slate-200">
                  <input
                    type="radio"
                    name="jenis_kelamin"
                    value="Laki-laki"
                    v-model="form.jenis_kelamin"
                    class="w-4 h-4 text-[#2563eb] focus:ring-[#2563eb] border-slate-300 dark:border-slate-600 dark:bg-slate-800"
                  />
                  <span>Laki-laki</span>
                </label>
                <label class="inline-flex items-center gap-2.5 cursor-pointer text-sm font-medium text-slate-700 dark:text-slate-200">
                  <input
                    type="radio"
                    name="jenis_kelamin"
                    value="Perempuan"
                    v-model="form.jenis_kelamin"
                    class="w-4 h-4 text-[#2563eb] focus:ring-[#2563eb] border-slate-300 dark:border-slate-600 dark:bg-slate-800"
                  />
                  <span>Perempuan</span>
                </label>
              </div>
              <div v-if="form.errors.jenis_kelamin" class="mt-1 text-xs text-rose-500">
                {{ form.errors.jenis_kelamin }}
              </div>
            </div>

            <!-- Alamat -->
            <div class="md:col-span-2">
              <label for="alamat" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                Alamat
              </label>
              <textarea
                id="alamat"
                v-model="form.alamat"
                rows="3"
                class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition resize-y"
                placeholder="Alamat lengkap Anda"
              ></textarea>
              <div v-if="form.errors.alamat" class="mt-1 text-xs text-rose-500">
                {{ form.errors.alamat }}
              </div>
            </div>

            <!-- Ubah Kata Sandi -->
            <div class="md:col-span-2 pt-6 border-t border-slate-100 dark:border-slate-700/60">
              <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">
                Ubah Kata Sandi
              </h3>
              <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 mb-4">
                Kosongkan jika Anda tidak ingin mengubah kata sandi.
              </p>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <!-- Kata Sandi Baru -->
                <div>
                  <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                    Kata Sandi Baru
                  </label>
                  <div class="relative">
                    <input
                      :type="showNewPass ? 'text' : 'password'"
                      id="password"
                      v-model="form.password"
                      class="w-full px-3.5 py-2.5 pr-10 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition"
                      placeholder="Minimal 8 karakter"
                      autocomplete="new-password"
                    />
                    <button
                      type="button"
                      @click="showNewPass = !showNewPass"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none"
                    >
                      <svg v-if="!showNewPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                      </svg>
                    </button>
                  </div>
                  <div v-if="form.errors.password" class="mt-1 text-xs text-rose-500">
                    {{ form.errors.password }}
                  </div>
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div>
                  <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                    Konfirmasi Kata Sandi
                  </label>
                  <div class="relative">
                    <input
                      :type="showConfirmPass ? 'text' : 'password'"
                      id="password_confirmation"
                      v-model="form.password_confirmation"
                      class="w-full px-3.5 py-2.5 pr-10 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-[#2563eb] focus:ring-2 focus:ring-blue-500/20 transition"
                      placeholder="Ulangi kata sandi baru"
                      autocomplete="new-password"
                    />
                    <button
                      type="button"
                      @click="showConfirmPass = !showConfirmPass"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none"
                    >
                      <svg v-if="!showConfirmPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                      </svg>
                    </button>
                  </div>
                  <div v-if="form.errors.password_confirmation" class="mt-1 text-xs text-rose-500">
                    {{ form.errors.password_confirmation }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Bar: Pojok Kiri Bawah (Hapus Akun) & Pojok Kanan Bawah (Simpan Perubahan) -->
          <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100 dark:border-slate-700/60">
            <!-- Tombol Hapus Akun dengan Outline Merah & Animasi Warna Memenuhi Tombol saat Diarahkan Cursor -->
            <button
              type="button"
              @click="showDeleteModal = true"
              class="btn-delete-account group"
            >
              <!-- Lapisan Animasi Warna yang Memenuhi Tombol saat Cursor diarahkan -->
              <span class="btn-fill-wrapper" aria-hidden="true">
                <span class="btn-fill-effect"></span>
              </span>

              <!-- Konten Tombol (Ikon & Teks) -->
              <span class="btn-content">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Hapus Akun</span>
              </span>
            </button>

            <!-- Pojok Kanan Bawah: Status Alert & Tombol Simpan Perubahan -->
            <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
              <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-1"
              >
                <p
                  v-if="showSuccessStatus"
                  class="text-sm font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span>Perubahan berhasil disimpan.</span>
                </p>
              </transition>

              <button
                type="submit"
                :disabled="form.processing"
                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-[#2563eb] hover:bg-[#1d4ed8] active:bg-[#1e40af] text-white font-medium text-sm rounded-lg shadow-sm transition duration-150 ease-in-out disabled:opacity-50 gap-2"
              >
                <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8zm0 0v4h8V4M6 20v-6h12v6"/>
                </svg>
                <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
              </button>
            </div>
          </div>

        </form>
      </div>

      <!-- Modal Konfirmasi Hapus Akun -->
      <transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showDeleteModal"
          class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        >
          <div
            @click.stop
            class="w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 p-6 space-y-4 animate-in fade-in zoom-in-95 duration-150"
          >
            <div class="flex items-center gap-3.5">
              <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-950/60 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                  Hapus Akun
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                  Konfirmasi penghapusan akun
                </p>
              </div>
            </div>

            <p class="text-sm text-slate-700 dark:text-slate-300 font-medium">
              Apakah yakin ingin menghapus akun anda?
            </p>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Semua data dan informasi yang terkait dengan akun ini akan dihapus secara permanen.
            </p>

            <form @submit.prevent="submitDelete" class="space-y-4 pt-2">
              <div v-if="!user.keycloak_id">
                <label for="delete_password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                  Masukkan kata sandi saat ini untuk konfirmasi:
                </label>
                <input
                  type="password"
                  id="delete_password"
                  v-model="formDelete.password"
                  required
                  placeholder="Kata sandi saat ini"
                  class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:border-red-500 focus:ring-2 focus:ring-red-500/20"
                />
                <p v-if="formDelete.errors.password" class="mt-1 text-xs text-red-600 dark:text-red-400">
                  {{ formDelete.errors.password }}
                </p>
              </div>

              <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                <button
                  type="button"
                  @click="showDeleteModal = false"
                  class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  :disabled="formDelete.processing"
                  class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-sm transition disabled:opacity-50"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  <span>{{ formDelete.processing ? 'Menghapus...' : 'Ya, Hapus Akun' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>

    </div>
  </PublicLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  status: {
    type: String,
    default: '',
  },
});

const userInitials = computed(() => {
  return props.user?.name ? props.user.name.charAt(0).toUpperCase() : 'U';
});

const photoInput = ref(null);
const photoPreview = ref(props.user?.foto_profil ? `/storage/${props.user.foto_profil}` : '');

const showNewPass = ref(false);
const showConfirmPass = ref(false);

const showSuccessStatus = ref(props.status === 'profile-updated');

watch(() => props.status, (newVal) => {
  if (newVal === 'profile-updated') {
    showSuccessStatus.value = true;
    setTimeout(() => {
      showSuccessStatus.value = false;
    }, 3000);
  }
});

const form = useForm({
  _method: 'patch',
  name: props.user?.name || '',
  email: props.user?.email || '',
  no_telepon: props.user?.no_telepon || '',
  pekerjaan: props.user?.pekerjaan || '',
  jenis_kelamin: (props.user?.jenis_kelamin === 'L' ? 'Laki-laki' : (props.user?.jenis_kelamin === 'P' ? 'Perempuan' : props.user?.jenis_kelamin)) || '',
  alamat: props.user?.alamat || '',
  foto_profil: null,
  remove_foto_profil: '0',
  password: '',
  password_confirmation: '',
});

const updatePreview = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.foto_profil = file;
    form.remove_foto_profil = '0';
    photoPreview.value = URL.createObjectURL(file);
  }
};

const clearPhoto = () => {
  form.foto_profil = null;
  form.remove_foto_profil = '1';
  photoPreview.value = '';
  if (photoInput.value) {
    photoInput.value.value = '';
  }
};

const submit = () => {
  form.post('/profile', {
    preserveScroll: true,
    onSuccess: () => {
      form.password = '';
      form.password_confirmation = '';
      showSuccessStatus.value = true;
      setTimeout(() => {
        showSuccessStatus.value = false;
      }, 3000);
    },
  });
};

// Modal Hapus Akun
const showDeleteModal = ref(false);
const formDelete = useForm({
  password: '',
});

const submitDelete = () => {
  formDelete.delete('/profile', {
    preserveScroll: true,
    onError: () => {
      showDeleteModal.value = true;
    },
    onSuccess: () => {
      showDeleteModal.value = false;
    },
  });
};
</script>

<style scoped>
/* Tombol Hapus Akun dengan Outline Merah & Animasi Warna Memenuhi Tombol */
.btn-delete-account {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.625rem 1.35rem;
  border-radius: 0.75rem;
  border: 1.5px solid #ef4444;
  background-color: transparent;
  color: #ef4444;
  font-size: 0.875rem;
  font-weight: 600;
  overflow: hidden;
  cursor: pointer;
  box-shadow: none;
  transition: border-color 0.4s ease, box-shadow 0.4s ease, transform 0.15s ease;
  z-index: 1;
}

.btn-delete-account:active {
  transform: scale(0.96);
}

.btn-delete-account:hover {
  border-color: #991b1b;
  box-shadow: 0 4px 14px -1px rgba(153, 27, 27, 0.35);
}

/* Dark mode */
:global(.dark) .btn-delete-account {
  background-color: transparent;
  border-color: #f87171;
  color: #f87171;
}

:global(.dark) .btn-delete-account:hover {
  border-color: #ef4444;
  box-shadow: 0 4px 14px -1px rgba(239, 68, 68, 0.35);
}

/* Wrapper lapisan animasi warna */
.btn-fill-wrapper {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  overflow: hidden;
  border-radius: inherit;
  z-index: 1;
}

/* Lingkaran warna merah gelap yang membesar memenuhi seluruh tombol saat cursor diarahkan */
.btn-fill-effect {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 320px;
  height: 320px;
  background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%);
  border-radius: 50%;
  opacity: 0;
  transform: translate(-50%, -50%) scale(0);
  transition: transform 0.45s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
  will-change: transform, opacity;
}

.btn-delete-account:hover .btn-fill-effect {
  opacity: 1;
  transform: translate(-50%, -50%) scale(1);
}

/* Konten tombol: teks dan ikon berubah ke putih */
.btn-delete-account .btn-content {
  position: relative;
  z-index: 2;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: color 0.35s ease;
}

.btn-delete-account:hover .btn-content {
  color: #ffffff !important;
}

.btn-delete-account .btn-icon {
  width: 1.125rem;
  height: 1.125rem;
  transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.btn-delete-account:hover .btn-icon {
  transform: scale(1.15) rotate(-6deg);
}
</style>
