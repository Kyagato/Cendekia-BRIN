<template>
  <div class="min-h-screen bg-[#0f172a] flex items-center justify-center p-4 sm:p-6 lg:p-8 font-sans">
    <div class="w-full max-w-5xl bg-slate-900 rounded-xl shadow-2xl overflow-hidden border border-slate-800">
      <div class="grid grid-cols-1 lg:grid-cols-2">

        <!-- Left Branding Column -->
        <div class="hidden lg:flex flex-col justify-between bg-[#1e3a8a] p-10 relative overflow-hidden min-h-[600px]">

          <div class="relative z-10">
            <Link href="/" class="inline-flex items-center gap-3 group">
              <div class="w-10 h-10 flex items-center justify-center">
                <img src="/images/logo-mojopedia.png" alt="Logo MojoPedia" class="w-full h-full object-contain drop-shadow-sm" />
              </div>
              <span class="text-xl font-extrabold tracking-tight text-white">Mojo<span class="text-[#93c5fd]">Pedia</span></span>
            </Link>
          </div>

          <div class="relative z-10 text-center space-y-4">
            <div class="w-20 h-20 flex items-center justify-center mx-auto drop-shadow-md">
              <img src="/images/logo-mojopedia.png" alt="Logo MojoPedia" class="w-full h-full object-contain" />
            </div>
            <h3 class="text-xl font-bold text-white tracking-tight">Bergabung dengan Ekosistem Pengetahuan</h3>
            <p class="text-xs text-blue-100 max-w-sm mx-auto leading-relaxed">Daftarkan akun Anda untuk berkontribusi, berdiskusi, dan mengakses seluruh arsip riset dan kajian strategis.</p>
          </div>

          <div class="relative z-10 text-[11px] text-blue-200">
            &copy; 2026 MojoPedia. Digital Governance System.
          </div>
        </div>

        <!-- Right Form Column -->
        <div class="flex flex-col p-8 sm:p-10 lg:p-12 justify-between bg-slate-900">
          <div>
            <div class="flex items-center justify-between mb-8">
              <Link href="/" class="lg:hidden inline-flex items-center gap-2">
                <span class="text-xl font-bold text-white">Mojo<span class="text-[#2563eb]">Pedia</span></span>
              </Link>
              <div class="flex items-center gap-3 ml-auto text-xs">
                <span class="text-slate-400 hidden sm:inline">Sudah punya akun?</span>
                <Link href="/login" class="px-3 py-1.5 border border-blue-500/40 text-blue-400 font-semibold tracking-wide rounded-lg hover:bg-[#2563eb] hover:text-white transition">
                  Masuk
                </Link>
              </div>
            </div>

            <div class="mb-6">
              <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1.5 tracking-tight">Buat Akun Baru</h1>
              <p class="text-slate-400 text-xs sm:text-sm">Daftarkan diri Anda di portal MojoPedia</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
              <div>
                <label for="name" class="block text-xs font-semibold text-slate-300 mb-1.5">Nama Lengkap</label>
                <input 
                  id="name" 
                  v-model="form.name" 
                  type="text" 
                  required 
                  autofocus 
                  autocomplete="name"
                  placeholder="Masukkan nama lengkap Anda"
                  class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#2563eb] focus:border-transparent text-sm transition"
                />
                <span v-if="form.errors.name" class="text-rose-400 text-xs mt-1 block">{{ form.errors.name }}</span>
              </div>

              <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Email</label>
                <input 
                  id="email" 
                  v-model="form.email" 
                  type="email" 
                  required 
                  autocomplete="username"
                  placeholder="nama@gmail.com"
                  class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#2563eb] focus:border-transparent text-sm transition"
                />
                <span v-if="form.errors.email" class="text-rose-400 text-xs mt-1 block">{{ form.errors.email }}</span>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5">Kata Sandi</label>
                  <input 
                    id="password" 
                    v-model="form.password" 
                    type="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#2563eb] focus:border-transparent text-sm transition"
                  />
                  <span v-if="form.errors.password" class="text-rose-400 text-xs mt-1 block">{{ form.errors.password }}</span>
                </div>

                <div>
                  <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 mb-1.5">Konfirmasi Kata Sandi</label>
                  <input 
                    id="password_confirmation" 
                    v-model="form.password_confirmation" 
                    type="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi"
                    class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#2563eb] focus:border-transparent text-sm transition"
                  />
                  <span v-if="form.errors.password_confirmation" class="text-rose-400 text-xs mt-1 block">{{ form.errors.password_confirmation }}</span>
                </div>
              </div>

              <button 
                type="submit" 
                :disabled="form.processing"
                class="w-full py-2.5 mt-4 bg-[#2563eb] hover:bg-[#1d4ed8] active:bg-[#1e40af] text-white font-semibold rounded-lg shadow-sm transition text-sm flex items-center justify-center gap-2"
              >
                <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ form.processing ? 'Memproses...' : 'Daftar Sekarang' }}</span>
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post('/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>
