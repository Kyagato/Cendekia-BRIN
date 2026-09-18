@extends('layouts.admin')

@section('title', 'Pengaturan Profil')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 dark:text-slate-600 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 dark:text-slate-200 font-semibold">Pengaturan Profil</li>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto w-full">
        <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm rounded-xl">
            <div class="w-full">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
    </div>
@endsection
