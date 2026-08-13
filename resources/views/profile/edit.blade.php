@extends('layouts.admin')

@section('title', 'Pengaturan Profil')

@section('breadcrumbs')
    <li>
        <svg class="w-4 h-4 text-slate-400 mx-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </li>
    <li class="text-slate-800 font-semibold">Pengaturan Profil</li>
@endsection

@section('content')
    <div class="space-y-6 max-w-4xl">
        <div class="p-4 sm:p-8 bg-white border border-slate-200 shadow-sm rounded-xl">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white border border-slate-200 shadow-sm rounded-xl">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white border border-slate-200 shadow-sm rounded-xl">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
