<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Cendekia BRIN') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-800">
                    <h3 class="text-2xl font-bold mb-2 text-primary-700">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mt-4">
                        <p class="text-slate-700">Informasi Akun Anda:</p>
                        <ul class="list-disc list-inside mt-2 text-sm text-slate-600">
                            <li><strong>Hak Akses (Role):</strong> {{ Auth::user()->role }}</li>
                            <li><strong>Instansi Asal:</strong> {{ Auth::user()->instansi }}</li>
                            <li><strong>Email:</strong> {{ Auth::user()->email }}</li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <p class="mb-4">Mulai kelola dan jelajahi repositori pengetahuan institusi sekarang.</p>
                        <a href="{{ route('knowledge.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Buka Repositori Pengetahuan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>