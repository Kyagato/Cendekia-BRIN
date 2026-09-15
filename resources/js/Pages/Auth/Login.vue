<template>
  <div class="min-h-screen bg-[#0f172a] flex items-center justify-center p-4 sm:p-6 lg:p-8 font-sans">
    <div class="w-full max-w-5xl bg-slate-900 rounded-xl shadow-2xl overflow-hidden border border-slate-800">
      <div class="grid grid-cols-1 lg:grid-cols-2">

        <!-- Left Branding Column -->
        <div class="hidden lg:flex flex-col justify-between bg-[#1e3a8a] p-10 relative overflow-hidden min-h-[550px]">

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
            <h3 class="text-xl font-bold text-white tracking-tight">Sistem Manajemen Pengetahuan</h3>
            <p class="text-xs text-blue-100 max-w-sm mx-auto leading-relaxed">Platform digital terpadu untuk mengelola, berbagi, dan menemukan aset pengetahuan pemerintahan strategis.</p>
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
                <span class="text-slate-400 hidden sm:inline">Belum punya akun?</span>
                <Link href="/register" class="px-3 py-1.5 border border-blue-500/40 text-blue-400 font-semibold tracking-wide rounded-lg hover:bg-[#2563eb] hover:text-white transition">
                  Daftar
                </Link>
              </div>
            </div>

            <div class="mb-6">
              <h1 class="text-2xl sm:text-3xl font-bold text-white mb-1.5 tracking-tight">Selamat Datang!</h1>
              <p class="text-slate-400 text-xs sm:text-sm">Masuk ke akun MojoPedia Anda</p>
            </div>

            <!-- Flash Error Notification -->
            <div v-if="$page.props.flash?.error" class="mb-4 p-3 bg-red-500/10 border border-red-500/30 text-red-400 text-xs rounded-lg flex items-center gap-2.5 shadow-sm">
              <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span>{{ $page.props.flash.error }}</span>
            </div>

            <!-- Flash Success Notification -->
            <div v-if="$page.props.flash?.success" class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs rounded-lg flex items-center gap-2.5 shadow-sm">
              <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span>{{ $page.props.flash.success }}</span>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
              <div>
                <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Alamat Email</label>
                <input 
                  id="email" 
                  v-model="form.email" 
                  type="email" 
                  required 
                  autofocus 
                  placeholder="nama@gmail.com"
                  class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#2563eb] focus:border-transparent text-sm transition"
                />
                <span v-if="form.errors.email" class="text-rose-400 text-xs mt-1 block">{{ form.errors.email }}</span>
              </div>

              <div>
                <label for="password" class="block text-xs font-semibold text-slate-300 mb-1.5">Kata Sandi</label>
                <div class="relative">
                  <input 
                    id="password" 
                    v-model="form.password" 
                    :type="showPassword ? 'text' : 'password'" 
                    required 
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi Anda"
                    class="w-full px-3.5 py-2.5 pr-11 bg-slate-950 border border-slate-700 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#2563eb] focus:border-transparent text-sm transition"
                  />
                  <button 
                    type="button" 
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-200 transition"
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
                <span v-if="form.errors.password" class="text-rose-400 text-xs mt-1 block">{{ form.errors.password }}</span>
              </div>

              <div class="flex items-center justify-between text-xs pt-1">
                <label class="inline-flex items-center gap-2 text-slate-400 cursor-pointer">
                  <input type="checkbox" v-model="form.remember" class="rounded border-slate-700 bg-slate-950 text-[#2563eb] focus:ring-0">
                  <span>Ingat saya</span>
                </label>
                <Link href="/forgot-password" class="text-blue-400 hover:underline">
                  Lupa kata sandi?
                </Link>
              </div>

              <button 
                type="submit" 
                :disabled="form.processing"
                class="w-full py-2.5 mt-2 bg-[#2563eb] hover:bg-[#1d4ed8] active:bg-[#1e40af] text-white font-semibold rounded-lg shadow-sm transition text-sm flex items-center justify-center gap-2"
              >
                <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ form.processing ? 'Memproses...' : 'Masuk' }}</span>
              </button>

              <!-- Divider -->
              <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-slate-800"></div>
                <span class="flex-shrink mx-3 text-slate-500 text-xs uppercase font-medium">Atau</span>
                <div class="flex-grow border-t border-slate-800"></div>
              </div>

              <!-- Tombol Keycloak SSO -->
              <a 
                href="/auth/keycloak/redirect" 
                class="w-full py-2.5 bg-slate-950 hover:bg-slate-800 border border-slate-700 hover:border-blue-500 text-slate-200 font-semibold rounded-lg shadow-sm transition text-xs sm:text-sm flex items-center justify-center gap-2.5 group"
              >
                <svg class="w-4 h-4 text-blue-400 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Masuk dengan Keycloak SSO</span>
              </a>
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
