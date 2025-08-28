<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.18/index.global.min.js'></script>
        
        @fluxAppearance
        @livewireStyles
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Statistiques')" class="grid">
                    <flux:navlist.item :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        <i class="fas fa-tachometer-alt flux-sidebar-icon"></i>
                        {{ __('Tableau de bord') }}
                    </flux:navlist.item>
                </flux:navlist.group>

                @canany(['voir rôles', 'voir utilisateurs'])
                    <flux:navlist.group :heading="__('Gestion des utilisateurs')" class="grid">

                        <flux:navlist.item :href="route('personnels')" :current="request()->routeIs('personnels')" wire:navigate>
                            <i class="fas fa-user-group flux-sidebar-icon"></i>
                            {{ __('Personnels') }}
                        </flux:navlist.item>
                        
                    </flux:navlist.group>
                @endcanany

                @canany(['voir patients', 'voir dossiers-médicaux','voir consultations','voir actes'])
                    <flux:navlist.group :heading="__('Gestion des patients')" class="grid">
                        @can('voir patients')
                            <flux:navlist.item :href="route('patients')" :current="request()->routeIs('patients')" wire:navigate>
                                <i class="fas fa-users flux-sidebar-icon"></i>
                                {{ __('Liste des Patients') }}
                            </flux:navlist.item>
                        @endcan
                        @can('voir dossiers-médicaux')
                            <flux:navlist.item :href="route('medicalFiles')" :current="request()->routeIs('medicalFiles')" wire:navigate>
                                <i class="fas fa-hospital-user flux-sidebar-icon"></i>
                                {{ __('Dossiers Médicaux') }}
                            </flux:navlist.item>
                        @endcan
                        @can('voir rendez-vous')
                            <flux:navlist.item :href="route('appointments')" :current="request()->routeIs('appointments')" wire:navigate>
                                <i class="fas fa-calendar-check flux-sidebar-icon"></i>
                                {{ __('Rendez-vous') }}
                            </flux:navlist.item>
                        @endcan
                        @can('voir consultations')
                            <flux:navlist.item :href="route('consultations')" :current="request()->routeIs('consultations')" wire:navigate>
                                <i class="fas fa-stethoscope flux-sidebar-icon"></i>
                                {{ __('Consultations') }}
                            </flux:navlist.item>
                        @endcan
                        @can('voir actes')
                            <flux:navlist.item :href="route('actes')" :current="request()->routeIs('actes')" wire:navigate>
                                <i class="fas fa-tooth flux-sidebar-icon"></i>
                                {{ __('Actes') }}
                            </flux:navlist.item>
                        @endcan
                    </flux:navlist.group>
                @endcanany

                {{-- @can('voir services') --}}
                <flux:navlist.group :heading="__('Gestions des Paiements')" class="grid">
                    <flux:navlist.item :href="route('invoices')" :current="request()->routeIs('invoices')" wire:navigate>
                        <i class="fas fa-file-invoice flux-sidebar-icon"></i>
                        {{ __('Liste des factures') }}
                    </flux:navlist.item>
                    <flux:navlist.item :href="route('payements')" :current="request()->routeIs('payements')" wire:navigate>
                        <i class="fas fa-dollar-sign flux-sidebar-icon"></i>
                        {{ __('Liste des paiements') }}
                    </flux:navlist.item>
                </flux:navlist.group>
                {{-- @endcan --}}

                @can('voir services')
                <flux:navlist.group :heading="__('Gestions des Services')" class="grid">
                    <flux:navlist.item :href="route('services')" :current="request()->routeIs('services')" wire:navigate>
                        <i class="fas fa-clipboard-list flux-sidebar-icon"></i>
                        {{ __('Liste des services') }}
                    </flux:navlist.item>
                </flux:navlist.group>
                @endcan
                
            </flux:navlist>
        </flux:sidebar>

        @include('components.layouts.app.header')

        {{ $slot }}

        @fluxScripts
        @livewireScripts
        @stack('scripts')

    </body>
</html>