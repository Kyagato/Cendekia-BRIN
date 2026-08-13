<section>
    <header>
        <h2 class="text-lg font-medium text-slate-800 dark:text-slate-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')
        
        <div class="flex items-center gap-4 mb-6">
            <div class="w-20 h-20 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center font-bold text-xl overflow-hidden shrink-0 ring-4 ring-primary-500/20">
                @if($user->foto_profil)
                    <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="flex-1" x-data="{ fileName: '' }">
                <x-input-label for="foto_profil" :value="__('Foto Profil')" class="mb-1" />
                <div class="flex items-center gap-3">
                    <label class="cursor-pointer inline-flex items-center px-4 py-2 bg-primary-50 dark:bg-slate-700 text-primary-700 dark:text-slate-200 text-sm font-semibold rounded-md border-0 hover:bg-primary-100 dark:hover:bg-slate-600 transition">
                        Ubah Foto Profil
                        <input type="file" id="foto_profil" name="foto_profil" class="hidden" accept="image/*" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" />
                    </label>
                    <span x-text="fileName || 'Belum ada foto yang dipilih'" class="text-sm text-slate-500 dark:text-slate-400"></span>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('foto_profil')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-slate-800 dark:text-slate-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        
        <div>
            <x-input-label for="role" :value="__('Role')" />
            @if(in_array($user->role, ['Super Admin', 'Admin Pusat', 'Admin IPPD']))
                <select id="role" name="role" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                    <option value="Super Admin" {{ $user->role == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="Admin Pusat" {{ $user->role == 'Admin Pusat' ? 'selected' : '' }}>Admin Pusat</option>
                    <option value="Admin IPPD" {{ $user->role == 'Admin IPPD' ? 'selected' : '' }}>Admin IPPD</option>
                    <option value="Kreator Pengetahuan" {{ $user->role == 'Kreator Pengetahuan' ? 'selected' : '' }}>Kreator Pengetahuan</option>
                    <option value="Analisis Pengetahuan" {{ $user->role == 'Analisis Pengetahuan' ? 'selected' : '' }}>Analisis Pengetahuan</option>
                    <option value="Karyawan BRIN" {{ $user->role == 'Karyawan BRIN' ? 'selected' : '' }}>Karyawan BRIN</option>
                    <option value="Publik" {{ $user->role == 'Publik' ? 'selected' : '' }}>Publik</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('role')" />
            @else
                <div class="mt-1 p-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-md text-slate-700 dark:text-slate-300">
                    {{ $user->role ?? 'User' }}
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-600 dark:text-slate-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
