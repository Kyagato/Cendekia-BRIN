<section x-data="{
    photoPreview: '{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : '' }}',
    removePhoto: false,
    showDeleteModal: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }},
    updatePreview(event) {
        const file = event.target.files[0];
        if (file) {
            this.photoPreview = URL.createObjectURL(file);
            this.removePhoto = false;
        }
    },
    clearPhoto() {
        this.photoPreview = '';
        this.removePhoto = true;
        if (this.$refs.photoInput) {
            this.$refs.photoInput.value = '';
        }
    }
}">
    <header class="pb-5 border-b border-slate-200 dark:border-slate-700">
        <h2 class="text-xl font-bold text-slate-800 dark:text-slate-100">
            {{ __('Pengaturan Profil') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            {{ __('Perbarui informasi pribadi dan kata sandi Anda.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Hidden input for remove photo flag --}}
        <input type="hidden" name="remove_foto_profil" :value="removePhoto ? '1' : '0'">

        {{-- Edit Foto Profil (Disesuaikan di bagian atas) --}}
        <div class="p-4 bg-slate-50/70 dark:bg-slate-900/40 rounded-xl border border-slate-200/80 dark:border-slate-700/80 flex flex-col sm:flex-row items-center sm:items-center gap-5">
            <div class="relative shrink-0">
                <div class="w-24 h-24 rounded-full overflow-hidden bg-slate-200 dark:bg-slate-700 border-2 border-slate-300 dark:border-slate-600 shadow-sm flex items-center justify-center text-slate-600 dark:text-slate-200 font-bold text-2xl">
                    <template x-if="photoPreview">
                        <img :src="photoPreview" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!photoPreview">
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </template>
                </div>
            </div>

            <div class="flex-1 text-center sm:text-left">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Foto Profil</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Unggah foto profil Anda. Format didukung: JPG, PNG, GIF, atau WebP (Maks. 2MB).</p>
                <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                    <label class="cursor-pointer inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Pilih Foto Baru</span>
                        <input type="file" name="foto_profil" class="hidden" accept="image/*" x-ref="photoInput" @change="updatePreview($event)">
                    </label>

                    <button type="button" x-show="photoPreview" @click="clearPhoto()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm transition">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Hapus Foto</span>
                    </button>
                </div>
                <x-input-error class="mt-2 text-xs" :messages="$errors->get('foto_profil')" />
            </div>
        </div>

        {{-- Form Fields Grid (2 Kolom Bersih Sesuai Mockup) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 pt-2">
            {{-- Nama Lengkap --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                    Nama Lengkap
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                       placeholder="Nama lengkap Anda">
                <x-input-error class="mt-1 text-xs" :messages="$errors->get('name')" />
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                    Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                       placeholder="nama@email.com">
                <x-input-error class="mt-1 text-xs" :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                        Email Anda belum terverifikasi.
                        <button form="send-verification" class="underline text-blue-600 hover:text-blue-700 dark:text-blue-400">
                            Kirim ulang email verifikasi.
                        </button>
                    </div>
                @endif
            </div>

            {{-- Nomor Telepon --}}
            <div>
                <label for="no_telepon" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                    Nomor Telepon
                </label>
                <input type="text" id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}"
                       class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                       placeholder="08xxxxxxxxxx">
                <x-input-error class="mt-1 text-xs" :messages="$errors->get('no_telepon')" />
            </div>

            {{-- Pekerjaan --}}
            <div>
                <label for="pekerjaan" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                    Pekerjaan
                </label>
                <input type="text" id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan', $user->pekerjaan) }}"
                       class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                       placeholder="Contoh: Mahasiswa, Pegawai Negeri, dll.">
                <x-input-error class="mt-1 text-xs" :messages="$errors->get('pekerjaan')" />
            </div>

            {{-- Jenis Kelamin --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-2">
                    Jenis Kelamin
                </label>
                <div class="flex items-center gap-6">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer text-sm font-medium text-slate-700 dark:text-slate-200">
                        <input type="radio" name="jenis_kelamin" value="Laki-laki"
                               {{ (old('jenis_kelamin', $user->jenis_kelamin) == 'Laki-laki' || old('jenis_kelamin', $user->jenis_kelamin) == 'L') ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                        <span>Laki-laki</span>
                    </label>
                    <label class="inline-flex items-center gap-2.5 cursor-pointer text-sm font-medium text-slate-700 dark:text-slate-200">
                        <input type="radio" name="jenis_kelamin" value="Perempuan"
                               {{ (old('jenis_kelamin', $user->jenis_kelamin) == 'Perempuan' || old('jenis_kelamin', $user->jenis_kelamin) == 'P') ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                        <span>Perempuan</span>
                    </label>
                </div>
                <x-input-error class="mt-1 text-xs" :messages="$errors->get('jenis_kelamin')" />
            </div>

            {{-- Alamat --}}
            <div class="md:col-span-2">
                <label for="alamat" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                    Alamat
                </label>
                <textarea id="alamat" name="alamat" rows="3"
                          class="w-full px-3.5 py-2.5 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition resize-y"
                          placeholder="Alamat lengkap Anda">{{ old('alamat', $user->alamat) }}</textarea>
                <x-input-error class="mt-1 text-xs" :messages="$errors->get('alamat')" />
            </div>

            {{-- Ubah Kata Sandi (Ditempatkan di bawah kelamin & alamat) --}}
            <div class="md:col-span-2 pt-6 border-t border-slate-100 dark:border-slate-700/60"
                 x-data="{ showNewPass: false, showConfirmPass: false }">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">
                    {{ __('Ubah Kata Sandi') }}
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5 mb-4">
                    {{ __('Kosongkan jika Anda tidak ingin mengubah kata sandi.') }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    {{-- Kata Sandi Baru --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                            Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <input :type="showNewPass ? 'text' : 'password'"
                                   id="password"
                                   name="password"
                                   class="w-full px-3.5 py-2.5 pr-10 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                                   placeholder="Minimal 8 karakter"
                                   autocomplete="new-password">
                            <button type="button"
                                    @click="showNewPass = !showNewPass"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                                <template x-if="!showNewPass">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </template>
                                <template x-if="showNewPass">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                    </svg>
                                </template>
                            </button>
                        </div>
                        <x-input-error class="mt-1 text-xs" :messages="$errors->get('password')" />
                    </div>

                    {{-- Konfirmasi Kata Sandi --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                            Konfirmasi Kata Sandi
                        </label>
                        <div class="relative">
                            <input :type="showConfirmPass ? 'text' : 'password'"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="w-full px-3.5 py-2.5 pr-10 bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
                                   placeholder="Ulangi kata sandi baru"
                                   autocomplete="new-password">
                            <button type="button"
                                    @click="showConfirmPass = !showConfirmPass"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                                <template x-if="!showConfirmPass">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </template>
                                <template x-if="showConfirmPass">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                    </svg>
                                </template>
                            </button>
                        </div>
                        <x-input-error class="mt-1 text-xs" :messages="$errors->get('password_confirmation')" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Bar: Pojok Kiri Bawah (Hapus Akun) & Pojok Kanan Bawah (Simpan Perubahan) --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100 dark:border-slate-700/60">
            {{-- Pojok Kiri Bawah: Tombol Hapus Akun dengan Animasi Warna Memenuhi Tombol saat Diarahkan Cursor --}}
            <button type="button"
                    @click="showDeleteModal = true"
                    class="btn-delete-account group">
                
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

            {{-- Pojok Kanan Bawah: Status Alert & Tombol Simpan Perubahan --}}
            <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 3000)"
                        class="text-sm font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ __('Perubahan berhasil disimpan.') }}</span>
                    </p>
                @endif

                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-medium text-sm rounded-lg shadow-sm transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V8l-4-4H8zm0 0v4h8V4M6 20v-6h12v6"/>
                    </svg>
                    {{ __('Simpan Perubahan') }}
                </button>
            </div>
        </div>
    </form>

    {{-- Modal Konfirmasi Hapus Akun --}}
    <div x-show="showDeleteModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.away="showDeleteModal = false"
             class="w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 p-6 space-y-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

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

            <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4 pt-2">
                @csrf
                @method('delete')

                @if(empty($user->keycloak_id))
                    <div>
                        <label for="delete_password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Masukkan kata sandi saat ini untuk konfirmasi:
                        </label>
                        <input type="password"
                               id="delete_password"
                               name="password"
                               required
                               placeholder="Kata sandi saat ini"
                               class="w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:border-red-500 focus:ring-2 focus:ring-red-500/20">
                        @if($errors->userDeletion->has('password'))
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $errors->userDeletion->first('password') }}</p>
                        @endif
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-700/60">
                    <button type="button"
                            @click="showDeleteModal = false"
                            class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Ya, Hapus Akun</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
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
        .dark .btn-delete-account {
            background-color: transparent;
            border-color: #f87171;
            color: #f87171;
        }

        .dark .btn-delete-account:hover {
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
</section>

