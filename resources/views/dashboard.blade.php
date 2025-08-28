<x-layouts.app :title="__('Dashboard')">
    <div class="relative mb-6 w-full max-w-7xl mx-auto">
        <div class=" mb-3 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- left side-->
            <div class="w-full md:w-auto">
                <flux:heading size="xl" level="1" class="text-center md:text-left">
                    {{ __('Tableau de Bord') }}
                </flux:heading>
                <flux:subheading size="lg" class="mb-6 md:mb-0">{{ __('Vue d\'ensemble de votre clinique dentaire') }}</flux:subheading>
            </div>
    
            <!-- right side-->
            <div class="w-full md:w-auto flex justify-center md:justify-end items-center gap-4">
                <!-- Date & Time Badge -->
                @php
                    \Carbon\Carbon::setLocale('fr');
                    $now = \Carbon\Carbon::now();
                    $date = $now->translatedFormat('l j F Y'); // jeudi 7 août 2025
                    $time = $now->format('H:i'); // 24h format
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-neutral-800 text-neutral-100 shadow-md border border-neutral-700">
                    <i class="fa fa-calendar mr-1"></i> {{ $date }} &nbsp; <i class="fa fa-clock ml-2 mr-1"></i> {{ $time }}
                </span>
            </div>
        </div>
    
        <flux:separator variant="subtle" />
    </div>

    <!-- display dashboard component -->
    @livewire('dashboard.dashboard')
</x-layouts.app>
