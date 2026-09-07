<template>
  <div class="min-h-screen bg-slate-900 flex items-center justify-center p-4 sm:p-6 lg:p-8 font-sans">
    <div class="w-full max-w-5xl bg-slate-800 rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
      <div class="grid grid-cols-1 lg:grid-cols-2">

        <!-- Left Branding Column -->
        <div class="hidden lg:flex flex-col justify-between bg-slate-950 p-10 relative overflow-hidden min-h-[600px]">
          <div class="relative z-10">
            <Link href="/" class="inline-flex items-center gap-2">
              <span class="text-2xl font-bold bg-gradient-to-r from-red-500 via-rose-400 to-amber-400 bg-clip-text text-transparent">MojoPedia</span>
            </Link>
          </div>

          <div class="relative z-10 text-center space-y-4">
            <div class="w-20 h-20 bg-red-600/20 text-red-500 rounded-full flex items-center justify-center mx-auto border border-red-500/30 shadow-lg">
              <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
            </div>
            <h3 class="text-xl font-bold text-white">Bergabung Bersama Kami</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">Daftarkan akun Anda untuk berkolaborasi dan mengakses repositori pengetahuan BRIN.</p>
          </div>

          <div class="relative z-10 text-xs text-slate-500">
            &copy; 2026 MojoPedia — Badan Riset dan Inovasi Nasional.
          </div>
        </div>

        <!-- Right Form Column -->
        <div class="flex flex-col p-8 sm:p-10 lg:p-12 justify-between">
          <div>
            <div class="flex items-center justify-between mb-8">
              <Link href="/" class="lg:hidden inline-flex items-center gap-2">
                <span class="text-xl font-bold text-red-500">MojoPedia</span>
              </Link>
              <div class="flex items-center gap-3 ml-auto text-xs">
                <span class="text-slate-400 hidden sm:inline">Sudah punya akun?</span>
                <Link href="/login" class="px-4 py-1.5 border border-red-500 text-red-400 font-bold uppercase tracking-wider rounded-full hover:bg-red-600 hover:text-white transition">
                  Masuk
                </Link>
              </div>
            </div>

            <div class="mb-6">
              <h1 class="text-3xl font-bold text-white mb-2">Buat Akun Baru</h1>
              <p class="text-slate-400 text-sm">Daftarkan diri Anda di MojoPedia</p>
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
                  class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 focus:border-red-500 focus:outline-none text-sm transition"
                />
                <span v-if="form.errors.name" class="text-red-400 text-xs mt-1 block">{{ form.errors.name }}</span>
              </div>

              <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Email</label>
                <input 
                  id="email" 
                  v-model="form.email" 
                  type="email" 
                  required 
                  autocomplete="username"
                  placeholder="nama@brin.go.id"
                  class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 focus:border-red-500 focus:outline-none text-sm transition"
                />
                <span v-if="form.errors.email" class="text-red-400 text-xs mt-1 block">{{ form.errors.email }}</span>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5">Password</label>
                  <input 
                    id="password" 
                    v-model="form.password" 
                    type="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 focus:border-red-500 focus:outline-none text-sm transition"
                  />
                  <span v-if="form.errors.password" class="text-red-400 text-xs mt-1 block">{{ form.errors.password }}</span>
                </div>

                <div>
                  <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 mb-1.5">Konfirmasi Password</label>
                  <input 
                    id="password_confirmation" 
                    v-model="form.password_confirmation" 
                    type="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                    class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 focus:border-red-500 focus:outline-none text-sm transition"
                  />
                  <span v-if="form.errors.password_confirmation" class="text-red-400 text-xs mt-1 block">{{ form.errors.password_confirmation }}</span>
                </div>
              </div>

              <button 
                type="submit" 
                :disabled="form.processing"
                class="w-full py-3.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-xl shadow-lg transition uppercase tracking-wider text-xs flex items-center justify-center gap-2 mt-4"
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
