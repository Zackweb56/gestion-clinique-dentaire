<div>
    @include('components.flash_message')

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <!-- Search input removed -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-neutral-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="block w-full pl-10 pr-10 py-2 border border-neutral-300 dark:border-neutral-600 rounded-md leading-5 bg-white dark:bg-neutral-700 placeholder-neutral-500 dark:placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent sm:text-sm"
                    placeholder="{{ __('Rechercher un patient...') }}"
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
            <div class="w-full md:w-40">
                <flux:select wire:model.live="genderFilter">
                    <option value="">Tous les genres</option>
                    <option value="H">Homme</option>
                    <option value="F">Femme</option>
                </flux:select>
            </div>
        </div>

        <div>
            @can('créer patient')
                <x-custom-tooltip text="Créer un nouveau patient">
                    <flux:button variant="primary" wire:click="create" class="inline-flex items-center">
                        <i class="fa-solid fa-user-plus mr-1"></i>
                        {{ __('Nouveau patient') }}
                    </flux:button>
                </x-custom-tooltip>
            @endcan
        </div>
    </div>
    
    {{-- Patients Card display --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6 h-[calc(100vh-180px)] overflow-hidden">
        {{-- Patients List --}}
        <div class="md:col-span-1 p-2 border border-neutral-200 dark:border-none bg-white dark:bg-neutral-900 rounded-lg shadow-sm overflow-hidden h-full flex flex-col">
            <div class="p-4 dark:border-neutral-700">
                <h3 class="text-lg font-semibold text-neutral-800 dark:text-neutral-200">
                    {{ __('Liste des Patients') }}
                </h3>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ count($patients) }} patient(s) trouvé(s)
                </p>
            </div>
            <div class="overflow-y-auto flex-grow">
                @forelse ($patients as $patient)
                    {{-- In the patient card section --}}
                    <div class="relative p-4 m-2 border rounded-md hover:shadow-md cursor-pointer {{ $selectedPatient && $selectedPatient->id == $patient->id ? 'bg-gradient-to-r from-teal-50 to-teal-50 dark:from-teal-900/20 dark:to-teal-900/20 border-teal-200 dark:border-teal-800 shadow-md' : 'border border-neutral-200 dark:border-neutral-700 hover:bg-neutral-50 dark:hover:bg-neutral-700' }}"
                         wire:click="selectPatient({{ $patient->id }})">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-neutral-900 dark:text-white">
                                <div class="p-0 text-sm font-normal">
                                    <div class="flex items-center gap-2 pb-1.5 text-start text-sm">
                                        <span class="relative flex h-10 w-10 shrink-0 overflow-hidden rounded-lg">
                                            <span class="flex h-full w-full items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-800 {{ $selectedPatient && $selectedPatient->id == $patient->id ? 'bg-gradient-to-br from-teal-50 to-teal-50 dark:from-teal-900/30 dark:to-teal-900/30' : '' }}">
                                                {{ $patient->initials() }}
                                            </span>
                                        </span>
                                        <div class="grid flex-1 text-start -mt-1 text-sm leading-tight">
                                            <span class="truncate font-semibold {{ $selectedPatient && $selectedPatient->id == $patient->id ? 'text-teal-600 dark:text-teal-400' : '' }}">{{ $patient->patient_full_name }}</span>
                                            @if($patient->medicalFile)
                                                <a href="{{ route('patients.details', $patient->id) }}" wire:navigate class="text-teal-600 dark:text-teal-400 hover:underline" style="font-size: 11px;">
                                                    Dossier N° {{ $patient->medicalFile->file_number }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </span>
                            <div class="flex items-center space-x-2">
                                <x-custom-tooltip text="Voir les détails du patient">
                                    <a href="{{ route('patients.details', $patient->id) }}" 
                                       class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </x-custom-tooltip>
                            </div>
                        </div>
                        @if($selectedPatientId != $patient->id)
                            <div class="absolute bottom-3 right-3">
                                @if($patient->status == 'active')
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 dark:from-green-900/30 dark:to-emerald-900/30 dark:text-green-400 shadow-sm">Actif</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-blue-100 to-blue-100 text-blue-800 dark:from-blue-900/30 dark:to-blue-900/30 dark:text-blue-400 shadow-sm">Nouveau</span>
                                @endif
                            </div>
                        @endif
                        <div class="mt-2 space-y-1">
                            <p class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center">
                                <i class="fa-solid fa-calendar-days w-4 h-4 mr-1.5 text-neutral-400"></i>
                                Âge : {{ \Carbon\Carbon::parse($patient->birth_date)->age }} ans
                            </p>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center">
                                <i class="fa-solid fa-user w-4 h-4 mr-1.5 text-neutral-400"></i>
                                Genre : 
                                @if ($patient->gender == 'F')
                                    Femme
                                @elseif($patient->gender == 'H')
                                    Homme
                                @endif
                            </p>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center">
                                <i class="fa-solid fa-phone w-4 h-4 mr-1.5 text-neutral-400"></i>
                                {{ $patient->phone }}
                            </p>
                            <p class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center">
                                <i class="fa-solid fa-shield-alt w-4 h-4 mr-1.5 text-neutral-400"></i>
                                {{ $patient->insurance_type == 'CNSS' ? 'CNSS' : ($patient->insurance_type == 'CNOPS' ? 'CNOPS' : ($patient->insurance_type == 'privé' ? 'Assurance Privée' : 'Aucune assurance')) }}
                            </p>
                        </div>

                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center text-center h-full py-20">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-12 h-12 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                                <i class="fa-solid fa-users-slash text-neutral-400 dark:text-neutral-600"></i>
                            </div>
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Aucun patient trouvé</p>
                                <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Commencez par créer votre premier patient</p>
                            </div>
                        </div>
                    </div>
                @endforelse
                @if($hasMore ?? false)
                    <div class="flex justify-center my-4">
                        <button
                            wire:click="loadMore"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-teal-600 text-white rounded shadow hover:bg-teal-700 transition-colors"
                        >
                            <span wire:loading.remove wire:target="loadMore">Charger plus</span>
                            <span wire:loading wire:target="loadMore">
                                <svg class="animate-spin h-5 w-5 inline-block text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Chargement...
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Patient Details / Medical File --}}
        <div class="md:col-span-2 border border-neutral-200 dark:border-none bg-white dark:bg-neutral-900 rounded-lg shadow-sm overflow-hidden h-full flex flex-col relative">
            <div wire:loading wire:target="selectPatient" class="absolute inset-0 flex flex-col items-center justify-center text-center h-full py-50 z-10 bg-white dark:bg-neutral-900">
                <div class="flex flex-col items-center space-y-4">
                    <div class="w-12 h-12 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                        <div class="animate-spin h-8 w-8 text-teal-500">
                            <svg class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <p class="text-lg font-medium text-neutral-700 dark:text-neutral-300">Chargement des détails...</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Veuillez patienter pendant que nous récupérons les informations du patient</p>
                    </div>
                </div>
            </div>

            <div wire:loading.remove wire:target="selectPatient" class="flex flex-col h-full">
                @if ($selectedPatient)
                    <div class="p-6 border-b border-neutral-200 dark:border-neutral-700 flex-shrink-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-neutral-800 dark:text-neutral-200">
                                    {{ __('Dossier de') }} {{ $selectedPatient->patient_full_name }}
                                </h3>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                    Patient #00{{ $selectedPatient->id }} • {{ $selectedPatient->medicalFile ? ($selectedPatient->medicalFile->actes ? count($selectedPatient->medicalFile->actes) : 0) : 0 }} acte(s)
                                </p>
                            </div>
                            <div class="flex flex-row gap-2 items-end md:items-center">
                                @can('modifier patient')
                                <x-custom-tooltip text="Modifier le patient">
                                    <button
                                        class="cursor-pointer px-3 py-1 text-xs font-medium rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors inline-flex items-center gap-2"
                                        wire:click="edit({{ $selectedPatient->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="edit"
                                    >
                                        <i class="fas fa-pen"></i>
                                        <span class="hidden md:inline" wire:loading.remove wire:target="edit">Modifier</span>
                                        <span wire:loading wire:target="edit" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </x-custom-tooltip>
                                @endcan
                                @can('supprimer patient')
                                <x-custom-tooltip text="Supprimer le patient">
                                    <button
                                        class="cursor-pointer px-3 py-1 text-xs font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center gap-2"
                                        wire:click="confirmDelete({{ $selectedPatient->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="confirmDelete"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                        <span class="hidden md:inline" wire:loading.remove wire:target="confirmDelete">Supprimer</span>
                                        <span wire:loading wire:target="confirmDelete" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                </x-custom-tooltip>
                                @endcan
                                @can('créer dossier-médical')
                                    @if(!$selectedPatient->medicalFile)
                                    <x-custom-tooltip text="Créer un dossier médical pour ce patient">
                                        <button
                                            class="cursor-pointer px-3 py-1 text-xs font-medium rounded-md bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 hover:bg-teal-100 dark:hover:bg-teal-900/50 transition-colors inline-flex items-center gap-2"
                                            wire:click="confirmCreateMedicalFile({{ $selectedPatient->id }})"
                                        >
                                            <i class="fa-solid fa-file-medical"></i>
                                            <span class="hidden md:inline">Dossier médical</span>
                                            <span wire:loading wire:target="confirmCreateMedicalFile({{ $selectedPatient->id }})" class="inline-flex items-center">
                                                <svg class="animate-spin h-4 w-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </x-custom-tooltip>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="p-6 flex-grow overflow-y-auto">
                        @if ($selectedPatient)
                            <div class="bg-white dark:bg-neutral-900 rounded-lg p-1 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold text-neutral-800 dark:text-neutral-200 flex items-center gap-2">
                                            {{ $selectedPatient->patient_full_name }}
                                            @if($selectedPatient->status == 'active')
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Actif</span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">Nouveau</span>
                                            @endif
                                        </h3>
                                        @if($selectedPatient->medicalFile)
                                            <a href="{{ route('patients.details', $selectedPatient->id) }}" wire:navigate class="text-xs text-teal-600 dark:text-teal-400 hover:underline mt-1 block">
                                                Dossier N° {{ $selectedPatient->medicalFile->file_number }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 text-neutral-700 dark:text-neutral-300">
                                            <i class="fa-solid fa-phone"></i> {{ $selectedPatient->phone ?: 'Non mentionné' }}
                                        </div>
                                        <div class="flex items-center gap-2 text-neutral-700 dark:text-neutral-300">
                                            <i class="fa-solid fa-envelope"></i> {{ $selectedPatient->email ?: 'Non mentionné' }}
                                        </div>
                                        <div class="flex items-center gap-2 text-neutral-700 dark:text-neutral-300">
                                            <i class="fa-solid fa-calendar-days"></i> Âge: {{ $selectedPatient->birth_date ? \Carbon\Carbon::parse($selectedPatient->birth_date)->age . ' ans' : 'Non mentionné' }}
                                        </div>
                                        <div class="flex items-center gap-2 text-neutral-700 dark:text-neutral-300">
                                            <i class="fa-solid fa-user"></i> Genre: {{ $selectedPatient->gender == 'F' ? 'Femme' : ($selectedPatient->gender == 'H' ? 'Homme' : 'Non mentionné') }}
                                        </div>
                                        <div class="flex items-center gap-2 text-neutral-700 dark:text-neutral-300">
                                            <i class="fa-solid fa-location-dot"></i> {{ $selectedPatient->address ?: 'Non mentionné' }}
                                        </div>
                                        <div class="flex items-center gap-2 text-neutral-700 dark:text-neutral-300">
                                            <i class="fa-solid fa-shield-alt"></i> Assurance: {{ $selectedPatient->insurance_type == 'CNSS' ? 'CNSS' : ($selectedPatient->insurance_type == 'CNOPS' ? 'CNOPS' : ($selectedPatient->insurance_type == 'privé' ? 'Assurance Privée' : 'Aucune assurance')) }}
                                        </div>
                                        <div class="flex items-center gap-2 text-neutral-700 dark:text-neutral-300">
                                            <i class="fa-solid fa-clock"></i> Prochain RDV:
                                            @php
                                                $nextAppointment = null;
                                                if ($selectedPatient->medicalFile && $selectedPatient->medicalFile->appointments) {
                                                    $nextAppointment = $selectedPatient->medicalFile->appointments
                                                        ->where('appointment_date', '>=', now())
                                                        ->sortBy('appointment_date')
                                                        ->first();
                                                }
                                            @endphp
                                            @if($nextAppointment)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-blue-700 to-teal-700 text-white dark:from-blue-900 dark:to-teal-900 dark:text-teal-200 shadow-md ml-2">
                                                    <i class="fa-solid fa-calendar-check mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($nextAppointment->appointment_date)->format('d/m/Y H:i') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-neutral-200 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 ml-2">
                                                    Non mentionné
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($selectedPatient->medicalFile)
                                    <div>
                                        <div class="mb-2">
                                            <span class="font-semibold">Maladies chroniques:</span>
                                            <span>{{ $selectedPatient->medicalFile->chronic_diseases ?: 'Non mentionné' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold">Médicaments actuels:</span>
                                            <span>{{ $selectedPatient->medicalFile->current_medications ?: 'Non mentionné' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-semibold">Allergies:</span>
                                            <span>{{ $selectedPatient->medicalFile->allergies ?: 'Non mentionné' }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <!-- Existing empty state -->
                        @endif
                    </div>
                @else
                    <div class="flex flex-col text-center h-full py-50 flex-grow">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-12 h-12 rounded-full bg-neutral-100 dark:bg-neutral-800 flex items-center justify-center">
                                <i class="fa-solid fa-hand-pointer text-2xl text-neutral-400 dark:text-neutral-600"></i>
                            </div>
                            <div class="space-y-2">
                                <p class="text-lg font-medium text-neutral-700 dark:text-neutral-300">Sélectionnez un patient</p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">Cliquez sur un patient dans la liste pour voir ses détails</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- créer patient Modal --}}
    <flux:modal name="create-patient" class="md:w-[700px]">
        <form wire:submit.prevent="store">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Ajouter un nouveau patient') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Remplissez les informations du patient') }}
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center mb-1">
                            <label for="patient_full_name" class="mr-2">Nom complet</label>
                            <span class="text-xs text-red-500">Requis</span>
                        </div>
                        <flux:input 
                            wire:model.defer="patient_full_name" 
                            placeholder="Nom complet du patient"
                        />
                        @error('patient_full_name')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <flux:input 
                        label="CIN" 
                        wire:model.defer="cin" 
                        error="{{ $errors->first('cin') }}"
                        placeholder="Numéro de carte d'identité"
                    />

                    <div>
                        <div class="flex items-center mb-1">
                            <label for="phone" class="mr-2">Téléphone</label>
                            <span class="text-xs text-red-500">Requis</span>
                        </div>
                        <flux:input 
                            wire:model.defer="phone" 
                            placeholder="Numéro de téléphone"
                        />
                        @error('phone')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <flux:input 
                        label="Email" 
                        wire:model.defer="email" 
                        error="{{ $errors->first('email') }}"
                        placeholder="Adresse email"
                        type="email"
                    />

                    <div>
                        <div class="flex items-center mb-1">
                            <label for="gender" class="mr-2">Genre</label>
                            <span class="text-xs text-red-500">Requis</span>
                        </div>
                        <flux:select 
                            wire:model.defer="gender" 
                            error="{{ $errors->first('gender') }}"
                        >
                            <option value="">-- Sélectionner un genre --</option>
                            <option value="H">Homme</option>
                            <option value="F">Femme</option>
                        </flux:select>
                        @error('gender')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center mb-1">
                            <label for="birth_date" class="mr-2">Date de naissance</label>
                            <span class="text-xs text-red-500">Requis</span>
                        </div>
                        <flux:input 
                            wire:model.defer="birth_date" 
                            type="date"
                        />
                        @error('birth_date')
                            <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 col-span-2">
                        <div>
                            <div class="flex items-center mb-1">
                                <label for="insurance_type" class="mr-2">Type d'assurance</label>
                                <span class="text-xs text-red-500">Requis</span>
                            </div>
                            <flux:select 
                                wire:model.defer="insurance_type" 
                                error="{{ $errors->first('insurance_type') }}"
                            >
                                <option value="">-- Sélectionner un type --</option>
                                <option value="CNSS">CNSS</option>
                                <option value="CNOPS">CNOPS</option>
                                <option value="privé">Assurance Privée</option>
                                <option value="aucun">Aucune assurance</option>
                            </flux:select>
                            @error('insurance_type')
                                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <flux:input 
                            label="Adresse" 
                            wire:model.defer="address" 
                            error="{{ $errors->first('address') }}"
                            placeholder="Adresse complète"
                        />
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button type="submit" variant="primary" title="Enregistrer le patient">
                        {{ __('Enregistrer') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- modifier patient Modal --}}
    <flux:modal name="edit-patient" class="md:w-[700px]">
        <form wire:submit.prevent="update">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Modifier le patient') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Modifiez les informations du patient') }}
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="flex items-center mb-1">
                            <label for="patient_full_name" class="mr-2">Nom complet</label>
                            <span class="text-xs text-red-500">Requis</span>
                        </div>
                        <flux:input 
                            wire:model.defer="patient_full_name" 
                            error="{{ $errors->first('patient_full_name') }}"
                            placeholder="Nom complet du patient"
                        />
                    </div>

                    <flux:input 
                        label="CIN" 
                        wire:model.defer="cin" 
                        error="{{ $errors->first('cin') }}"
                        placeholder="Numéro de carte d'identité"
                    />

                    <div>
                        <div class="flex items-center mb-1">
                            <label for="phone" class="mr-2">Téléphone</label>
                            <span class="text-xs text-red-500">Requis</span>
                        </div>
                        <flux:input 
                            wire:model.defer="phone" 
                            error="{{ $errors->first('phone') }}"
                            placeholder="Numéro de téléphone"
                        />
                    </div>

                    <flux:input 
                        label="Email" 
                        wire:model.defer="email" 
                        error="{{ $errors->first('email') }}"
                        placeholder="Adresse email"
                        type="email"
                    />

                    <div>
                        <div class="flex items-center mb-1">
                            <label for="gender" class="mr-2">Genre</label>
                            <span class="text-xs text-red-500">Requis</span>
                        </div>
                        <flux:select 
                            wire:model.defer="gender" 
                            error="{{ $errors->first('gender') }}"
                        >
                            <option value="">-- Sélectionner un genre --</option>
                            <option value="H">Homme</option>
                            <option value="F">Femme</option>
                        </flux:select>
                    </div>

                    <div>
                        <div class="flex items-center mb-1">
                            <label for="birth_date" class="mr-2">Date de naissance</label>
                            <span class="text-xs text-red-500">Requis</span>
                        </div>
                        <flux:input 
                            wire:model.defer="birth_date" 
                            error="{{ $errors->first('birth_date') }}"
                            type="date"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4 col-span-2">
                        <div>
                            <div class="flex items-center mb-1">
                                <label for="insurance_type" class="mr-2">Type d'assurance</label>
                                <span class="text-xs text-red-500">Requis</span>
                            </div>
                            <flux:select 
                                wire:model.defer="insurance_type" 
                                error="{{ $errors->first('insurance_type') }}"
                            >
                                <option value="" selected>Sélectionner un type</option>
                                <option value="CNSS">CNSS</option>
                                <option value="CNOPS">CNOPS</option>
                                <option value="privé">Assurance Privée</option>
                                <option value="aucun">Aucune assurance</option>
                            </flux:select>
                        </div>
                        <flux:input 
                            label="Adresse" 
                            wire:model.defer="address" 
                            error="{{ $errors->first('address') }}"
                            placeholder="Adresse complète"
                        />
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button type="submit" variant="primary" title="Mettre à jour le patient">
                        {{ __('Mettre à jour') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-confirmation-patient" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Supprimer le patient') }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ __('Êtes-vous sûr de vouloir supprimer ce patient ? Cette action est irréversible.') }}
                </flux:text>
                @if(isset($selectedPatient) && $selectedPatient->medicalFile)
                    <div class="mt-4 p-3 rounded bg-red-100 text-red-800 border border-red-300 text-sm flex items-center gap-2">
                        <span><i class="fas fa-exclamation-triangle text-lg me-2"></i><strong>Attention&nbsp;:</strong> La suppression de ce patient entraînera également la suppression de <b>son dossier médical</b>, <b>tous les rendez-vous (RDV)</b> et <b>toutes les actess associées</b>.</span>
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button 
                    type="button" 
                    variant="danger" 
                    wire:click="delete"
                    title="Confirmer la suppression du patient"
                >
                    {{ __('Supprimer définitivement') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Medical File Confirmation Modal --}}
    <flux:modal name="confirm-create-medical-file" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Créer un dossier médical') }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ __('Voulez-vous créer un dossier médical pour ce patient ?') }}
                </flux:text>
            </div>
            <div class="flex justify-end space-x-3">
                <flux:button 
                    type="button" 
                    variant="primary" 
                    wire:click="createMedicalFileForPatient"
                    title="Confirmer la création du dossier médical"
                >
                    {{ __('Oui, créer le dossier') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Create Medical File Modal from patient --}}
    <flux:modal name="create-medical-file-modal-patient" class="md:w-[700px]">
        <form wire:submit.prevent="storeMedicalFile">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Nouveau dossier médical') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Remplissez les informations médicales') }}
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @if($selectedPatient)
                        <flux:input 
                            label="Patient *" 
                            value="{{ $selectedPatient->patient_full_name }}"
                            readonly
                        />
                        <input type="hidden" wire:model="selectedPatientId" value="{{ $selectedPatient->id }}">
                    @else
                        <div class="p-4 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg">
                            {{ __('Aucun patient sélectionné') }}
                        </div>
                    @endif

                    <flux:textarea 
                        label="Maladies chroniques" 
                        wire:model.defer="chronic_diseases" 
                        error="{{ $errors->first('chronic_diseases') }}"
                        placeholder="Diabète, hypertension, etc."
                        rows="3"
                    />

                    <flux:textarea 
                        label="Médicaments actuels" 
                        wire:model.defer="current_medications" 
                        error="{{ $errors->first('current_medications') }}"
                        placeholder="Liste des médicaments pris régulièrement"
                        rows="3"
                    />

                    <flux:textarea 
                        label="Allergies" 
                        wire:model.defer="allergies" 
                        error="{{ $errors->first('allergies') }}"
                        placeholder="Allergies médicamenteuses ou autres"
                        rows="3"
                    />

                    <flux:textarea 
                        label="Notes médicales" 
                        wire:model.defer="notes" 
                        error="{{ $errors->first('notes') }}"
                        placeholder="Informations supplémentaires"
                        rows="5"
                    />
                </div>

                <div class="flex justify-end space-x-3">
                  
                    <flux:button type="submit" variant="primary" title="Enregistrer le dossier médical">
                        {{ __('Enregistrer') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>


</div>