@php
    $profile = \App\Models\DentalProfile::first();
@endphp
@if($profile)
    <div class="flex items-center">
        @if($profile->logo)
            <img src="{{ asset('storage/' . $profile->logo) }}" alt="Logo du cabinet" class="h-10 w-10 mr-2 object-contain mb-1" />
        @else
            <div class="flex aspect-square size-15 items-center justify-center rounded-md text-accent-foreground">
                <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
            </div>
        @endif
        <div class="ms-2 grid flex-1 text-start text-sm">
            <span class="mb-0.5 truncate leading-tight font-semibold">{{ $profile->clinic_name }}</span>
            <span class="text-gray-400 leading-tight" style="font-size: 10px;">Système de gestion dentaire</span>
        </div>
    </div>
@else
    <div class="flex aspect-square size-15 items-center justify-center rounded-md text-accent-foreground">
        <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
    </div>
    <div class="ms-1 grid flex-1 text-start text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">{{ config('app.name') }}</span>
        <span class="text-gray-400 leading-tight" style="font-size: 10px;">Système de gestion dentaire</span>
    </div>
@endif
