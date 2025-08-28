@php
    $servicePrices = collect($services)->mapWithKeys(function($service) {
        return [$service->id => $service->price];
    });
@endphp

<div>
    @include('components.flash_message')

    <div class="w-full space-y-4 rounded-xl p-6 bg-neutral-900 shadow mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
            <h2 class="text-xl font-bold mb-0">Actes Actifs</h2>
            <div class="flex items-center space-x-4 w-full md:w-auto">
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-neutral-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="block w-full pl-10 pr-10 py-2 border border-neutral-300 dark:border-neutral-600 rounded-md leading-5 bg-white dark:bg-neutral-700 placeholder-neutral-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent sm:text-sm"
                        placeholder="Rechercher un acte..."
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <div wire:loading wire:target="search" class="animate-spin h-5 w-5 text-teal-500">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <select wire:model.live="statusActeFilter" class="block w-full md:w-40 py-2 px-3 border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent sm:text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En cours</option>
                        <option value="planned">Planifié</option>
                        <option value="completed">Terminé</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <div wire:loading wire:target="statusActeFilter" class="animate-spin h-5 w-5 text-teal-500">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <flux:separator variant="subtle" />

        <div
            x-data="{
                observe() {
                    let observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                $wire.loadMore();
                            }
                        });
                    }, { threshold: 1.0 });
                    observer.observe(this.$refs.sentinel);
                }
            }"
            x-init="observe()"
            class="overflow-y-auto"
            style="max-height: 450px;"
        >
            @forelse($actes as $acte)
                <div class="bg-neutral-800 rounded-xl p-5 shadow flex flex-col gap-2 relative mb-4 hover:shadow-lg transition-shadow">
                    <!-- Header: Patient & Acte Info -->
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center gap-3">
                            <!-- Patient Initials Circle -->
                            <span class="relative flex h-10 w-10 shrink-0 overflow-hidden rounded-lg">
                                <span class="flex h-full w-full items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-800">
                                    {{ $acte->medicalFile->patient->initials() ?? '--' }}
                                </span>
                            </span>
                            <div>
                                <span class="font-semibold text-lg text-neutral-200 block">{{ $acte->medicalFile->patient->patient_full_name ?? '--' }}</span>
                                <a href="{{ route('patients.details', $acte->medicalFile->patient->id) }}" class="text-teal-400 block underline hover:text-teal-300 -mt-1" style="font-size: 10px;" wire:navigate>
                                    Dossier N° {{ $acte->medicalFile->file_number }}
                                </a>
                                <span class="block text-neutral-400" style="font-size: 10px;">Acte N° {{ $acte->acte_number }}</span>
                            </div>
                        </div>
                        <div class="flex flex-1 justify-center items-center gap-2">
                            <span class="px-3 py-1 text-xs font-medium rounded-md bg-neutral-700 text-neutral-200 flex items-center">
                                <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($acte->acte_date)->format('d/m/Y') }}
                            </span>
                            <div x-data="{ open: false }" class="relative">
                                <button
                                    class="cursor-pointer px-3 py-1 text-xs font-medium rounded-md focus:outline-none transition-colors flex items-center @if($acte->status === 'completed') bg-teal-900/30 text-teal-400 @elseif($acte->status === 'planned') bg-yellow-900/30 text-yellow-400 @else bg-blue-900/30 text-blue-400 @endif"
                                    @click="open = !open"
                                    type="button"
                                >
                                    @if($acte->status === 'completed')
                                        <i class="fa-solid fa-check-circle mr-1"></i>
                                    @elseif($acte->status === 'planned')
                                        <i class="fa-solid fa-calendar-check mr-1"></i>
                                    @else
                                        <i class="fa-solid fa-spinner mr-1"></i>
                                    @endif
                                    <span>
                                        {{ $acte->status === 'completed' ? 'Terminé' : ($acte->status === 'planned' ? 'Planifié' : 'En cours') }}
                                    </span>
                                    <i class="fa fa-chevron-down ml-1 text-xs"></i>
                                </button>
                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    class="absolute z-20 mt-2 w-36 rounded-md shadow-lg bg-neutral-800 border border-neutral-700 py-1"
                                    style="display: none;"
                                >
                                    <div wire:loading wire:target="updateStatus">
                                        <div class="flex items-center justify-center py-2">
                                            <svg class="animate-spin h-5 w-5 text-teal-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-teal-400 text-xs">Mise à jour...</span>
                                        </div>
                                    </div>
                                    <button
                                        wire:click="updateStatus({{ $acte->id }}, 'planned')"
                                        class="block w-full text-left px-4 py-2 text-xs hover:bg-yellow-900/40 text-yellow-400"
                                        @click="open = false"
                                        wire:loading.attr="disabled"
                                        wire:target="updateStatus"
                                    >
                                        <span wire:loading wire:target="updateStatus({{ $acte->id }}, 'planned')">
                                            <svg class="animate-spin h-4 w-4 inline mr-1 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </span>
                                        <i class="fa-solid fa-calendar-check mr-1"></i> Planifié
                                    </button>
                                    <button
                                        wire:click="updateStatus({{ $acte->id }}, 'pending')"
                                        class="block w-full text-left px-4 py-2 text-xs hover:bg-blue-900/40 text-blue-400"
                                        @click="open = false"
                                        wire:loading.attr="disabled"
                                        wire:target="updateStatus"
                                    >
                                        <span wire:loading wire:target="updateStatus({{ $acte->id }}, 'pending')">
                                            <svg class="animate-spin h-4 w-4 inline mr-1 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </span>
                                        <i class="fa-solid fa-spinner mr-1"></i> En cours
                                    </button>
                                    <button
                                        wire:click="updateStatus({{ $acte->id }}, 'completed')"
                                        class="block w-full text-left px-4 py-2 text-xs hover:bg-teal-900/40 text-teal-400"
                                        @click="open = false"
                                        wire:loading.attr="disabled"
                                        wire:target="updateStatus"
                                    >
                                        <span wire:loading wire:target="updateStatus({{ $acte->id }}, 'completed')">
                                            <svg class="animate-spin h-4 w-4 inline mr-1 text-teal-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </span>
                                        <i class="fa-solid fa-check-circle mr-1"></i> Terminé
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="px-3 py-1 text-lg font-bold rounded-full bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors inline-flex items-center">{{ number_format($acte->acteServices->sum('price'), 2) }} MAD</span>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($acte->description)
                        <div class="text-xs text-neutral-400 mb-2">{{ $acte->description }}</div>
                    @endif

                    <!-- Services Table -->
                    <div class="overflow-x-auto max-h-48 scrollbar-thin scrollbar-thumb-teal-700 scrollbar-track-neutral-800" style="max-height: 12rem;">
                        <table class="min-w-full text-xs text-left">
                            <thead>
                                <tr class="text-neutral-400">
                                    <th class="px-2 py-1">Service</th>
                                    <th class="px-2 py-1">Prix</th>
                                    <th class="px-2 py-1">Dent</th>
                                    <th class="px-2 py-1">Libellé</th>
                                    <th class="px-2 py-1">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($acte->acteServices as $service)
                                    <tr class="border-t border-neutral-700">
                                        <td class="px-2 py-1">{{ $service->service->name ?? '--' }}</td>
                                        <td class="px-2 py-1">{{ number_format($service->price, 2) }} MAD</td>
                                        <td class="px-2 py-1">{{ $service->tooth_number ?? '-' }}</td>
                                        <td class="px-2 py-1">{{ $service->libelle ?? '-' }}</td>
                                        <td class="px-2 py-1">{{ $service->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Actions (edit/delete) -->
                    <div class="flex gap-2 justify-end mt-2">
                        @can('modifier acte')
                        <x-custom-tooltip text="Modifier le acte">
                            <button 
                                class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors inline-flex items-center gap-2"
                                wire:click="edit({{ $acte->id }})"
                                wire:loading.attr="disabled"
                                wire:target="edit({{ $acte->id }})"
                            >
                                <i class="fas fa-pen"></i>
                                <span wire:loading wire:target="edit({{ $acte->id }})" class="inline-flex items-center">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </x-custom-tooltip>
                        @endcan
                        @can('supprimer acte')
                        <x-custom-tooltip text="Supprimer le acte">
                            <button 
                                class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center gap-2"
                                wire:click="confirmDelete({{ $acte->id }})"
                                wire:loading.attr="disabled"
                                wire:target="confirmDelete({{ $acte->id }})"
                            >
                                <i class="fas fa-trash-alt"></i>
                                <span wire:loading wire:target="confirmDelete({{ $acte->id }})" class="inline-flex items-center">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </x-custom-tooltip>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-16 h-16 rounded-full bg-neutral-700 flex items-center justify-center mb-4">
                        <i class="fas fa-tooth text-3xl text-teal-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-200 mb-2">Aucun acte trouvé</h3>
                    <p class="text-sm text-neutral-400 mb-4">Commencez par créer votre premier acte pour suivre les interventions et paiements de vos patients.</p>
                </div>
            @endforelse
            <div x-ref="sentinel"></div>
            @if($hasMore ?? false)
                <div class="flex justify-center py-4">
                    <span class="text-neutral-400">Chargement...</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="edit-acte-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 transition-opacity duration-200 {{ $showEditModal ? '' : 'hidden' }}">
        <div class="bg-neutral-800 rounded-xl p-6 w-full max-w-2xl md:w-[900px] border border-neutral-600 relative max-h-[80vh] overflow-y-auto">
            <button type="button" onclick="closeModal('edit-acte-modal')" class="absolute top-4 right-4 py-1 px-2 rounded-md text-neutral-500 hover:bg-neutral-600 hover:text-white text-sm transition-colors">
                <i class="fas fa-close"></i>
            </button>
            <form wire:submit.prevent="update">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-md text-neutral-100">Modifier l'acte</h2>
                        <p class="mt-2 text-sm text-neutral-300">Modifiez les informations de l'acte</p>
                    </div>
                    <!-- Patient and Date fields at the top -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                        <label class="block col-span-1">
                            <span class="text-neutral-200 text-sm">Patient <span class="text-xs text-red-600">Requis</span></span>
                            <select wire:model="medical_file_id" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all">
                                <option value="">Sélectionner un patient...</option>
                                @foreach($medicalFiles as $file)
                                    <option value="{{ $file->id }}">
                                        {{ $file->patient->patient_full_name }} - Dossier N° ({{ $file->file_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('medical_file_id') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </label>
                        <label class="block col-span-1">
                            <span class="text-neutral-200 text-sm">Date de l'acte <span class="text-xs text-red-600">Requis</span></span>
                            <input type="datetime-local" wire:model="acte_date" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all" />
                            @error('acte_date') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <!-- Info message for services section -->
                    <div class="mb-2">
                        <div class="flex items-center gap-2 bg-blue-900/30 border border-blue-700 text-blue-300 rounded-md px-3 py-2 text-sm">
                            <i class="fas fa-info-circle"></i>
                            <span>Section des services : Modifiez les services de cet acte en utilisant le bouton <b>+ Ajouter un service</b> ci-dessous.</span>
                        </div>
                    </div>
                    <!-- Services repeater -->
                    <div
                        x-data="{
                            servicePrices: {{ $servicePrices->toJson() }},
                            services: [],
                            init() {
                                // Get services from Livewire and initialize
                                const services = $wire.get('acte_services') || [{service_id: '', price: '', tooth_number: '', libelle: '', notes: ''}];
                                console.log('Init services:', services);
                                this.services = services;
                            },
                            loadServices() {
                                // Force reload services from Livewire
                                const services = $wire.get('acte_services') || [{service_id: '', price: '', tooth_number: '', libelle: '', notes: ''}];
                                console.log('Loading services:', services);
                                this.services = services;
                            },
                            addService() {
                                this.services.push({service_id: '', price: '', tooth_number: '', libelle: '', notes: ''});
                            },
                            removeService(idx) {
                                if (this.services.length > 1) this.services.splice(idx, 1);
                            },
                            updatePrice(idx) {
                                let sid = this.services[idx].service_id;
                                if (this.servicePrices[sid] !== undefined) {
                                    this.services[idx].price = this.servicePrices[sid];
                                }
                            },
                            syncToLivewire() {
                                $wire.set('acte_services', this.services);
                            }
                        }"
                        x-init="init()"
                        wire:ignore
                        x-ref="servicesContainer"
                    >
                        <template x-for="(service, idx) in services" :key="idx">
                            <div class="border border-neutral-600 rounded-lg p-4 mb-4 relative bg-neutral-900">
                                <x-custom-tooltip text="Supprimer ce service">
                                    <button type="button" @click="removeService(idx)" class="cursor-pointer absolute -top-2 right-1 px-3 py-2 text-xs font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center" x-show="services.length > 1">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </x-custom-tooltip>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                                    <label class="block col-span-1">
                                        <span class="text-neutral-200 text-sm">Service <span class="text-xs text-red-600">Requis</span></span>
                                        <select
                                            class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2"
                                            x-model="service.service_id"
                                            @change="updatePrice(idx)"
                                            :name="'acte_services['+idx+'][service_id]'"
                                            required
                                        >
                                            <option value="">Sélectionner un service...</option>
                                            @foreach($services as $srv)
                                                <option value="{{ $srv->id }}">{{ $srv->name }} - {{ number_format($srv->price, 2) }} MAD</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="block col-span-1">
                                        <span class="text-neutral-200 text-sm">Libellé</span>
                                        <input type="text" x-model="service.libelle" :name="'acte_services['+idx+'][libelle]'" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2" />
                                    </label>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-2">
                                    <label class="block col-span-1">
                                        <span class="text-neutral-200 text-sm">Prix (MAD) <span class="text-xs text-red-600">Requis</span></span>
                                        <input type="number" step="0.01" x-model="service.price" :name="'acte_services['+idx+'][price]'" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2" required />
                                    </label>
                                    <label class="block col-span-1">
                                        <span class="text-neutral-200 text-sm">Numéro de dent</span>
                                        <input type="text" x-model="service.tooth_number" :name="'acte_services['+idx+'][tooth_number]'" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2" />
                                    </label>
                                    <label class="block col-span-2">
                                        <span class="text-neutral-200 text-sm">Notes</span>
                                        <input type="text" x-model="service.notes" :name="'acte_services['+idx+'][notes]'" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2" />
                                    </label>
                                </div>
                            </div>
                        </template>
                        <div class="flex justify-end mt-2">
                            <x-custom-tooltip text="Ajouter un service">
                                <button type="button" @click="addService" class="px-4 py-2 rounded bg-violet-700 hover:bg-violet-800 text-white">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </x-custom-tooltip>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="submit" class="px-5 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 focus:ring-2 focus:ring-teal-400 text-white text-sm transition-all flex items-center gap-2" wire:loading.attr="disabled" @click="syncToLivewire()">
                                <span wire:loading.remove wire:target="update">Mettre à jour</span>
                                <span wire:loading wire:target="update" class="spinner ml-2"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                            </button>
                            <button type="button" onclick="closeModal('edit-acte-modal')" class="px-5 py-2.5 rounded-lg bg-neutral-700 hover:bg-neutral-600 text-neutral-200 font-medium transition-all">Annuler</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="delete-acte-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 transition-opacity duration-200 {{ $showDeleteModal ? '' : 'hidden' }}">
        <div class="bg-neutral-800 rounded-xl p-6 w-full max-w-md border border-neutral-600 relative">
            <button type="button" onclick="closeModal('delete-acte-modal')" class="absolute top-4 right-4 py-1 px-2 rounded-md text-neutral-500 hover:bg-neutral-600 hover:text-white text-sm transition-colors">
                <i class="fas fa-close"></i>
            </button>
            <div class="space-y-6">
                <div>
                    <h2 class="text-md text-neutral-100">Supprimer l'acte</h2>
                    <p class="mt-2 text-sm text-neutral-300">Êtes-vous sûr de vouloir supprimer cet acte ? Cette action est irréversible.</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="Livewire.first().call('delete')" class="px-5 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-all flex items-center gap-2" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="delete">Oui, supprimer</span>
                        <span wire:loading wire:target="delete" class="spinner ml-2"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                    </button>
                    <button type="button" onclick="closeModal('delete-acte-modal')" class="px-5 py-2.5 rounded-lg bg-neutral-700 hover:bg-neutral-600 text-neutral-200 font-medium transition-all">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            function closeModal(id) {
                document.getElementById(id).classList.add('hidden');
            }

            // Listen for when edit modal is shown to reload services
            Livewire.on('servicesLoaded', () => {
                // Use a timeout to ensure the modal is fully rendered
                setTimeout(() => {
                    const alpineComponent = document.querySelector('[x-ref="servicesContainer"]');
                    if (alpineComponent && alpineComponent._x_dataStack && alpineComponent._x_dataStack[0]) {
                        alpineComponent._x_dataStack[0].loadServices();
                    }
                }, 100);
            });

        </script>
    @endscript

</div>
