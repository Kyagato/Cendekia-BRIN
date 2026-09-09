<template>
  <div class="min-h-screen bg-slate-900 flex items-center justify-center p-4 sm:p-6 lg:p-8 font-sans">
    <div class="w-full max-w-5xl bg-slate-800 rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
      <div class="grid grid-cols-1 lg:grid-cols-2">

        <!-- Left Branding Column -->
        <div class="hidden lg:flex flex-col justify-between bg-slate-950 p-10 relative overflow-hidden min-h-[550px]">
          <div class="relative z-10">
            <Link href="/" class="inline-flex items-center gap-2">
              <span class="text-2xl font-bold bg-gradient-to-r from-red-500 via-rose-400 to-amber-400 bg-clip-text text-transparent">MojoPedia</span>
            </Link>
          </div>

          <div class="relative z-10 text-center space-y-4">
            <div class="w-20 h-20 bg-red-600/20 text-red-500 rounded-full flex items-center justify-center mx-auto border border-red-500/30 shadow-lg">
              <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
            <h3 class="text-xl font-bold text-white">Pusat Manajemen Pengetahuan BRIN</h3>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">Platform terpadu untuk mengelola, berbagi, dan menemukan pengetahuan strategis.</p>
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
                <span class="text-slate-400 hidden sm:inline">Belum punya akun?</span>
                <Link href="/register" class="px-4 py-1.5 border border-red-500 text-red-400 font-bold uppercase tracking-wider rounded-full hover:bg-red-600 hover:text-white transition">
                  Daftar
                </Link>
              </div>
            </div>

            <div class="mb-6">
              <h1 class="text-3xl font-bold text-white mb-2">Selamat Datang!</h1>
              <p class="text-slate-400 text-sm">Masuk ke akun MojoPedia Anda</p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
              <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Email</label>
                <input 
                  id="email" 
                  v-model="form.email" 
                  type="email" 
                  required 
                  autofocus 
                  autocomplete="username"
                  placeholder="nama@brin.go.id"
                  class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 focus:border-red-500 focus:outline-none text-sm transition"
                />
                <span v-if="form.errors.email" class="text-red-400 text-xs mt-1 block">{{ form.errors.email }}</span>
              </div>

              <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                  <input 
                    id="password" 
                    v-model="form.password" 
                    :type="showPassword ? 'text' : 'password'" 
                    required 
                    autocomplete="current-password"
                    placeholder="Masukkan password Anda"
                    class="w-full px-4 py-3 pr-12 bg-slate-900 border border-slate-700 rounded-xl text-slate-100 placeholder-slate-500 focus:border-red-500 focus:outline-none text-sm transition"
                  />
                  <button 
                    type="button" 
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-slate-200 transition"
                    tabindex="-1"
                  >
                    <!-- Eye icon (show) -->
                    <svg v-if="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <!-- Eye-off icon (hide) -->
                    <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                  </button>
                </div>
                <span v-if="form.errors.password" class="text-red-400 text-xs mt-1 block">{{ form.errors.password }}</span>
              </div>

              <div class="flex items-center justify-between text-xs">
                <label class="inline-flex items-center gap-2 text-slate-400 cursor-pointer">
                  <input type="checkbox" v-model="form.remember" class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-0">
                  <span>Ingat saya</span>
                </label>
                <Link href="/forgot-password" class="text-red-400 hover:underline">
                  Lupa password?
                </Link>
              </div>

              <button 
                type="submit" 
                :disabled="form.processing"
                class="w-full py-3.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold rounded-xl shadow-lg transition uppercase tracking-wider text-xs flex items-center justify-center gap-2"
              >
                <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ form.processing ? 'Memproses...' : 'Masuk' }}</span>
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';

const showPassword = ref(false);

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  });
};
</script>
