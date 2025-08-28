@php
    $servicePrices = collect($services)->mapWithKeys(function($service) {
        return [$service->id => $service->price];
    });
@endphp

<div>
    @include('components.flash_message')

    <div class="rounded-xl p-6 bg-neutral-900 shadow mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div class="flex flex-col">
                <div class="flex items-center space-x-4">
                    <flux:heading size="xl" level="1">
                        Agenda des rendez-vous
                    </flux:heading>     
                </div>
                <!-- Status Color Legend -->
                <div class="flex flex-wrap items-center gap-3 mt-2">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full" style="background:#6b7280"></span>
                        <span class="text-xs text-neutral-300">En attente</span>
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full" style="background:#2563eb"></span>
                        <span class="text-xs text-neutral-300">Confirmé</span>
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full" style="background:#f59e42"></span>
                        <span class="text-xs text-neutral-300">En cours</span>
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full" style="background:#22c55e"></span>
                        <span class="text-xs text-neutral-300">Terminé</span>
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full" style="background:#ef4444"></span>
                        <span class="text-xs text-neutral-300">Annulé</span>
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full" style="background:#991b1b"></span>
                        <span class="text-xs text-neutral-300">Absent</span>
                    </span>
                </div>
            </div>
            <div>
                <x-custom-tooltip text="Créer un nouveau rendez-vous">  
                    <button type="button" id="create-appointment-btn" onclick="handleCreateModalOpen()" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-teal-600 hover:bg-teal-700 text-white font-medium">
                        <i class="fas fa-plus me-1"></i>
                        Nouveau rendez-vous
                        <span class="spinner hidden ml-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </x-custom-tooltip>
            </div>
        </div>
        <flux:separator variant="subtle" />

        <div class="mt-6">
            <div id="appointments-calendar" wire:ignore></div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div id="create-appointment-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 transition-opacity duration-200 {{ $showCreateModal ? '' : 'hidden' }}">
        <div class="bg-neutral-800 rounded-xl p-6 w-full max-w-xl md:w-[700px] border border-neutral-600 relative">
            <button type="button" onclick="closeModal('create-appointment-modal')" class="absolute top-4 right-4 py-1 px-2 rounded-md text-neutral-500 hover:bg-neutral-600 hover:text-white text-sm transition-colors">
                <i class="fas fa-close"></i>
            </button>
            <form wire:submit.prevent="store">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-md text-neutral-100">Nouveau rendez-vous</h2>
                        <p class="mt-2 text-sm text-neutral-300">Remplissez les informations du rendez-vous</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        @if($medicalFiles->isEmpty())
                            <div class="bg-yellow-100 text-yellow-800 rounded-md px-4 py-2 text-sm mb-2">
                                Aucun dossier médical n'est disponible. Veuillez d'abord créer un dossier médical pour le patient avant de planifier un rendez-vous.
                            </div>
                        @endif
                        <label class="block">
                            <span class="text-neutral-200 text-sm">Patient <span class="text-xs text-red-600">Requis</span></span>
                            <select wire:model="medical_file_id" class="mt-1 block w-full rounded-lg bg-neutral-700 border text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all @error('medical_file_id') border-red-500 focus:border-red-500 @else border-neutral-600 dark:border-neutral-600 @enderror" @if($medicalFiles->isEmpty()) disabled @endif>
                                <option value="">Sélectionner un patient...</option>
                                @foreach($medicalFiles as $file)
                                    <option value="{{ $file->id }}">
                                        {{ $file->patient->patient_full_name }} - Dossier N° ({{ $file->file_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('medical_file_id') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="block">
                                <span class="text-neutral-200 text-sm">Type de rendez-vous <span class="text-xs text-red-600">Requis</span></span>
                                <select wire:model="type" class="mt-1 block w-full rounded-lg bg-neutral-700 border text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all @error('type') border-red-500 focus:border-red-500 @else border-neutral-600 dark:border-neutral-600 @enderror">
                                    <option value="">Choisir le type...</option>
                                    <option value="consultation">Consultation</option>
                                    <option value="suivi">Suivi</option>
                                    <option value="acte">Acte</option>
                                </select>
                                @error('type') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </label>
                            <label class="block">
                                <span class="text-neutral-200 text-sm">Statut <span class="text-xs text-red-600">Requis</span></span>
                                <select wire:model="status" class="mt-1 block w-full rounded-lg bg-neutral-700 border text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all @error('status') border-red-500 focus:border-red-500 @else border-neutral-600 dark:border-neutral-600 @enderror">
                                    <option value="">Choisir le statut...</option>
                                    <option value="pending">En attente</option>
                                    <option value="confirmed">Confirmé</option>
                                    <option value="in_progress">En cours</option>
                                    <option value="done">Terminé</option>
                                    <option value="canceled">Annulé</option>
                                    <option value="no_show">Absent</option>
                                </select>
                                @error('status') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </label>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="block">
                                <span class="text-neutral-200 text-sm">Date et heure du rendez-vous <span class="text-xs text-red-600">Requis</span></span>
                                <input type="datetime-local" wire:model="appointment_date" class="mt-1 block w-full rounded-lg bg-neutral-700 border text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all @error('appointment_date') border-red-500 focus:border-red-500 @else border-neutral-600 dark:border-neutral-600 @enderror" />
                                @error('appointment_date') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </label>
                            <label class="block">
                                <span class="text-neutral-200 text-sm">Durée (minutes)</span>
                                <input type="number" min="1" wire:model="duration_minutes" class="mt-1 block w-full rounded-lg bg-neutral-700 border text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all @error('duration_minutes') border-red-500 focus:border-red-500 @else border-neutral-600 dark:border-neutral-600 @enderror" />
                                @error('duration_minutes') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </label>
                        </div>
                        <label class="block">
                            <span class="text-neutral-200 text-sm">Notes</span>
                            <textarea rows="3" wire:model="notes" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all"></textarea>
                            @error('notes') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </label>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 focus:ring-2 focus:ring-teal-400 text-white text-sm transition-all flex items-center gap-2" @if($medicalFiles->isEmpty()) disabled @endif wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="store">Enregistrer</span>
                            <span wire:loading wire:target="store" class="spinner ml-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                        <button type="button" onclick="closeModal('create-appointment-modal')" class="px-5 py-2.5 rounded-lg bg-neutral-700 hover:bg-neutral-600 text-neutral-200 font-medium transition-all">Annuler</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="edit-appointment-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 transition-opacity duration-200 {{ $showEditModal ? '' : 'hidden' }}">
        <div class="bg-neutral-800 rounded-xl p-6 w-full max-w-xl md:w-[700px] border border-neutral-600 relative">
            <button type="button" onclick="closeModal('edit-appointment-modal')" class="absolute top-4 right-4 py-1 px-2 rounded-md text-neutral-500 hover:bg-neutral-600 hover:text-white text-sm transition-colors">
                <i class="fas fa-close"></i>
            </button>
            <form wire:submit.prevent="update">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-md text-neutral-100">Modifier le rendez-vous</h2>
                        <p class="mt-2 text-sm text-neutral-300">Modifiez les informations du rendez-vous</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <label class="block">
                            <span class="text-neutral-200 text-sm">Patient <span class="text-xs text-red-600">Requis</span></span>
                            <select wire:model="medical_file_id" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all @error('medical_file_id') border-red-500 focus:border-red-500 @else border-neutral-600 dark:border-neutral-600 @enderror">
                                <option value="">Sélectionner un patient...</option>
                                @foreach($medicalFiles as $file)
                                    <option value="{{ $file->id }}">
                                        {{ $file->patient->patient_full_name }} - Dossier N° ({{ $file->file_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('medical_file_id') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="block">
                                <span class="text-neutral-200 text-sm">Type de rendez-vous <span class="text-xs text-red-600">Requis</span></span>
                                <select wire:model="type" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all @error('type') border-red-500 focus:border-red-500 @else border-neutral-600 dark:border-neutral-600 @enderror">
                                    <option value="">Choisir le type...</option>
                                    <option value="consultation">Consultation</option>
                                    <option value="suivi">Suivi</option>
                                    <option value="acte">acte</option>
                                </select>
                                @error('type') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </label>
                            <label class="block">
                                <span class="text-neutral-200 text-sm">Statut <span class="text-xs text-red-600">Requis</span></span>
                                <select wire:model="status" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all @error('status') border-red-500 focus:border-red-500 @else border-neutral-600 dark:border-neutral-600 @enderror">
                                    <option value="">Choisir le statut...</option>
                                    <option value="pending">En attente</option>
                                    <option value="confirmed">Confirmé</option>
                                    <option value="in_progress">En cours</option>
                                    <option value="done">Terminé</option>
                                    <option value="canceled">Annulé</option>
                                    <option value="no_show">Absent</option>
                                </select>
                                @error('status') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </label>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="block">
                                <span class="text-neutral-200 text-sm">Date et heure du rendez-vous <span class="text-xs text-red-600">Requis</span></span>
                                <input type="datetime-local" wire:model="appointment_date" class="mt-1 block w-full rounded-lg bg-neutral-700 border text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all @error('appointment_date') border-red-500 focus:border-red-500 @else border-neutral-600 dark:border-neutral-600 @enderror" />
                                @error('appointment_date') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </label>
                            <label class="block">
                                <span class="text-neutral-200 text-sm">Durée (minutes)</span>
                                <input type="number" min="1" wire:model="duration_minutes" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all" />
                                @error('duration_minutes') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </label>
                        </div>
                        <label class="block">
                            <span class="text-neutral-200 text-sm">Notes</span>
                            <textarea rows="3" wire:model="notes" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-all"></textarea>
                            @error('notes') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </label>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 focus:ring-2 focus:ring-teal-400 text-white text-sm transition-all flex items-center gap-2" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="update">Mettre à jour</span>
                            <span wire:loading wire:target="update" class="spinner ml-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                        <button type="button" onclick="closeModal('edit-appointment-modal')" class="px-5 py-2.5 rounded-lg bg-neutral-700 hover:bg-neutral-600 text-neutral-200 font-medium transition-all">Annuler</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="delete-appointment-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 transition-opacity duration-200 {{ $showDeleteModal ? '' : 'hidden' }}">
        <div class="bg-neutral-800 rounded-xl p-6 w-full max-w-md border border-neutral-600 relative">
            <button type="button" onclick="closeModal('delete-appointment-modal')" class="absolute top-4 right-4 py-1 px-2 rounded-md text-neutral-500 hover:bg-neutral-600 hover:text-white text-sm transition-colors">
                <i class="fas fa-close"></i>
            </button>
            <div class="space-y-6">
                <div>
                    <h2 class="text-md text-neutral-100">Supprimer le rendez-vous</h2>
                    <p class="mt-2 text-sm text-neutral-300">Êtes-vous sûr de vouloir supprimer ce rendez-vous ? Cette action est irréversible.</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="Livewire.first().call('delete')" class="px-5 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-all flex items-center gap-2" wire:loading.attr="disabled" id="delete-appointment-btn">
                        <span wire:loading.remove wire:target="delete">Oui, supprimer</span>
                        <span wire:loading wire:target="delete" class="spinner ml-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                    <button type="button" onclick="closeModal('delete-appointment-modal')" class="px-5 py-2.5 rounded-lg bg-neutral-700 hover:bg-neutral-600 text-neutral-200 font-medium transition-all">Annuler</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Appointment Popup -->
    <div id="appointment-popup"
        style="display:none; position:absolute; z-index:9999; min-width:350px; max-width:450px;"
        class="bg-neutral-900 text-neutral-100 dark:bg-neutral-800 dark:text-neutral-100 rounded-xl shadow-xl p-4 border border-neutral-700 dark:border-neutral-600 transition-colors duration-200">
        <div id="popup-content"></div>
        <div class="flex justify-end space-x-2 mt-3">
            <!-- Créer consultation button, only for consultation type -->
            <x-custom-tooltip text="Créer une consultation">
                <button 
                    type="button"
                    class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors inline-flex items-center gap-2"
                    id="popup-create-consultation"
                    style="display:none;"
                >
                    <i class="fas fa-stethoscope"></i>
                    <span class="spinner hidden ml-2">
                        <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </x-custom-tooltip>
            <!-- Disabled consultation button when consultation exists -->
            <x-custom-tooltip text="Une consultation existe déjà pour ce rendez-vous">
                <button 
                    type="button"
                    class="px-3 py-2 text-xs font-medium rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 opacity-50 cursor-not-allowed transition-colors inline-flex items-center"
                    id="popup-create-consultation-disabled"
                    style="display:none;"
                    disabled
                >
                    <i class="fas fa-stethoscope"></i>
                </button>
            </x-custom-tooltip>
            <!-- Créer acte button, only for acte type -->
            <x-custom-tooltip text="Créer un acte">
                <button 
                    type="button"
                    class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors inline-flex items-center"
                    id="popup-create-acte"
                    style="display:none;"
                >
                    <i class="fas fa-tooth"></i>
                    <span class="spinner hidden ml-2">
                        <svg class="animate-spin h-4 w-4 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </x-custom-tooltip>
            <!-- Disabled acte button when acte exists -->
            <x-custom-tooltip text="Un acte existe déjà pour ce rendez-vous">
                <button 
                    type="button"
                    class="px-3 py-2 text-xs font-medium rounded-md bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 opacity-50 cursor-not-allowed transition-colors inline-flex items-center gap-2"
                    id="popup-create-acte-disabled"
                    style="display:none;"
                    disabled
                >
                    <i class="fas fa-tooth"></i>
                </button>
            </x-custom-tooltip>
            @can('modifier rendez-vous')
            <x-custom-tooltip text="Modifier le rdv">
                <button 
                    type="button"
                    class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors inline-flex items-center gap-2"
                    id="popup-edit"
                >
                    <i class="fas fa-pen"></i>
                    <span class="spinner hidden ml-2"><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                </button>
            </x-custom-tooltip>
            @endcan
            @can('supprimer rendez-vous')
            <x-custom-tooltip text="Supprimer le rdv">
                <button 
                    type="button"
                    class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center gap-2"
                    id="popup-delete"
                >
                    <i class="fas fa-trash-alt"></i>
                    <span class="spinner hidden ml-2"><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                </button>
            </x-custom-tooltip>
            @endcan
            <x-custom-tooltip text="Fermer">
            <button class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-neutral-50 dark:bg-neutral-900/30 text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-900/50 transition-colors inline-flex items-center gap-2"
                id="popup-close">
                <i class="fas fa-close"></i>
            </button>
            </x-custom-tooltip>
        </div>
    </div>

    {{-- créer consultation Modal --}}
    <div id="create-consultation-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 transition-opacity duration-200 {{ $showCreateConsultationModal ? '' : 'hidden' }}">
        <div class="bg-neutral-800 rounded-xl p-6 w-full max-w-xl md:w-[700px] border border-neutral-600 relative max-h-[80vh] overflow-y-auto">
            <button type="button" onclick="closeModal('create-consultation-modal')" class="absolute top-4 right-4 py-1 px-2 rounded-md text-neutral-500 hover:bg-neutral-600 hover:text-white text-sm transition-colors">
                <i class="fas fa-close"></i>
            </button>
            <form wire:submit.prevent="createConsultation">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-md text-neutral-100">Nouvelle consultation</h2>
                        <p class="mt-2 text-sm text-neutral-300">Remplissez les informations de la consultation</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <label class="block">
                            <span class="text-neutral-200 text-sm">Patient <span class="text-xs text-red-600">*</span></span>
                            <input type="text" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2" value="{{ optional(App\Models\Patient::find($consultation_patient_id))->patient_full_name }}" readonly />
                        </label>
                        <label class="block">
                            <span class="text-neutral-200 text-sm">Date de consultation <span class="text-xs text-red-600">*</span></span>
                            <input type="datetime-local" wire:model="consultation_date" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2" />
                        </label>
                        <label class="block">
                            <span class="text-neutral-200 text-sm">Symptômes</span>
                            <textarea rows="2" wire:model="consultation_symptoms" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2"></textarea>
                        </label>
                        <label class="block">
                            <span class="text-neutral-200 text-sm">Diagnostic</span>
                            <textarea rows="2" wire:model="consultation_diagnosis" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2"></textarea>
                        </label>
                        <label class="block">
                            <span class="text-neutral-200 text-sm">Plan d'acte</span>
                            <textarea rows="2" wire:model="consultation_acte_plan" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2"></textarea>
                        </label>
                        <label class="block">
                            <span class="text-neutral-200 text-sm">Notes</span>
                            <textarea rows="2" wire:model="consultation_notes" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2"></textarea>
                        </label>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-teal-600 hover:bg-teal-700 focus:ring-2 focus:ring-teal-400 text-white text-sm transition-all flex items-center gap-2" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createConsultation">Enregistrer</span>
                            <span wire:loading wire:target="createConsultation" class="spinner ml-2"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                        </button>
                        <button type="button" onclick="closeModal('create-consultation-modal')" class="px-5 py-2.5 rounded-lg bg-neutral-700 hover:bg-neutral-600 text-neutral-200 font-medium transition-all">Annuler</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- créer acte Modal --}}
    <div id="create-acte-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 transition-opacity duration-200 {{ $showCreateActeModal ? '' : 'hidden' }}">
        <div class="bg-neutral-800 rounded-xl p-6 w-full max-w-2xl md:w-[900px] border border-neutral-600 relative max-h-[80vh] overflow-y-auto">
            <button type="button" onclick="closeModal('create-acte-modal')" class="absolute top-4 right-4 py-1 px-2 rounded-md text-neutral-500 hover:bg-neutral-600 hover:text-white text-sm transition-colors">
                <i class="fas fa-close"></i>
            </button>
            <form wire:submit.prevent="createActe">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-md text-neutral-100">Nouvel acte</h2>
                        <p class="mt-2 text-sm text-neutral-300">Remplissez les informations de l'acte</p>
                    </div>
                    <!-- Patient and Date fields at the top -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                        <label class="block col-span-1">
                            <span class="text-neutral-200 text-sm">Patient <span class="text-xs text-red-600">Requis</span></span>
                            <input type="text" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2" value="{{ optional(App\Models\Patient::find($acte_patient_id))->patient_full_name }}" readonly />
                        </label>
                        <label class="block col-span-1">
                            <span class="text-neutral-200 text-sm">Date de l'acte <span class="text-xs text-red-600">Requis</span></span>
                            <input type="datetime-local" wire:model="acte_date" class="mt-1 block w-full rounded-lg bg-neutral-700 border border-neutral-600 text-sm text-neutral-300 px-4 py-2" />
                            @error('acte_date') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                        </label>
                    </div>
                    <!-- Info message for services section -->
                    <div class="mb-2">
                        <div class="flex items-center gap-2 bg-blue-900/30 border border-blue-700 text-blue-300 rounded-md px-3 py-2 text-sm">
                            <i class="fas fa-info-circle"></i>
                            <span>Section des services : Ajoutez un ou plusieurs services à cet acte en utilisant le bouton <b>+ Ajouter un service</b> ci-dessous.</span>
                        </div>
                    </div>
                    <!-- Services repeater -->
                    <div
                        x-data="{
                            servicePrices: {{ $servicePrices->toJson() }},
                            services: [],
                            init() {
                                // أول مرة أو عند إعادة فتح المودال
                                this.resetServices();

                                // كل مرة Livewire يرسل reset-acte-services
                                window.addEventListener('reset-acte-services', () => {
                                    this.resetServices();
                                });
                            },
                            resetServices() {
                                this.services = [{service_id: '', price: '', tooth_number: '', libelle: '', notes: ''}];
                                $wire.set('acte_services', this.services);
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
                    >
                        <template x-for="(service, idx) in services" :key="idx">
                            <div class="border border-neutral-600 rounded-lg p-4 mb-4 relative bg-neutral-900">
                                <x-custom-tooltip text="Supprimer ce service" placement="top">
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
                                <span wire:loading.remove wire:target="createActe">Enregistrer</span>
                                <span wire:loading wire:target="createActe" class="spinner ml-2"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                            </button>
                            <button type="button" onclick="closeModal('create-acte-modal')" class="px-5 py-2.5 rounded-lg bg-neutral-700 hover:bg-neutral-600 text-neutral-200 font-medium transition-all">Annuler</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @script

        <script>
            let calendar = null;

            function renderAppointmentsCalendar(appointments = null) {
                var calendarEl = document.getElementById('appointments-calendar');
                if (!calendarEl) return;

                // Use the passed-in appointments, or fallback to the DOM attribute
                if (!appointments) {
                    try {
                        appointments = JSON.parse(calendarEl.getAttribute('data-appointments'));
                    } catch (e) {
                        appointments = [];
                    }
                }

                if (calendar) {
                    calendar.destroy();
                    calendar = null;
                }

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    buttonText: {
                        today: 'Aujourd\'hui',
                        month: 'Mois',
                        week: 'Semaine',
                        day: 'Jour',
                        list: 'Liste'
                    },
                    views: {
                        timeGridWeek: {
                            titleFormat: { year: 'numeric', month: 'long', day: 'numeric' },
                            dayHeaderFormat: { 
                                weekday: 'short', 
                                day: '2-digit', 
                                month: '2-digit', 
                                separator: '/', 
                                omitCommas: true 
                            },
                            slotLabelFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                                meridiem: false
                            }
                        },
                        timeGridDay: {
                            titleFormat: { year: 'numeric', month: 'long', day: 'numeric' },
                            slotLabelFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false,
                                meridiem: false
                            },
                        }
                    },
                    locale: 'fr',
                    firstDay: 1, // Monday as first day of week
                    events: appointments,
                    slotMinTime: '08:00:00', // Start at 8:00
                    slotMaxTime: '21:00:00', // End at 21:00 (9pm)
                    slotDuration: '00:30:00', // 30 minute slots
                    slotLabelInterval: '00:30:00', // 30 minute labels
                    allDayText: 'Toute la journée',
                    slotLabelFormat: function(info) {
                        const h = info.date.getHours().toString().padStart(2, '0');
                        const m = info.date.getMinutes().toString().padStart(2, '0');
                        return `${h}h${m}`;
                    },
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false,
                        meridiem: false
                    },
                    eventMinHeight: 24,
                    eventContent: function(arg) {
                        let timeText = arg.timeText.replace(':', ' h'); // Replace : with h
                        let [patientName, typeText] = arg.event.title.split(' - ');
                        let bgColor = arg.event.backgroundColor || arg.event.extendedProps.backgroundColor || '#2563eb';
                        let textColor = arg.event.textColor || arg.event.extendedProps.textColor || '#fff';

                        let contentHtml = `
                            <div style="cursor:pointer;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;width:100%;background:${bgColor};color:${textColor};border-radius:6px;padding:2px 4px;text-align:center;">
                                <span style="font-size:10px;font-weight:400;opacity:0.8;">${timeText}</span>
                                <span style="font-size:11px;font-weight:bold;white-space:normal;margin-top:-5px;">${patientName}</span>
                                <span style='font-size:10px;font-weight:normal;font-style:italic;margin-top:-5px;'>- ${typeText}</span>
                            </div>
                        `;
                        return { html: contentHtml };
                    },
                    eventClick: function(info) {
                        info.jsEvent.preventDefault();
                        const appointment = appointments.find(a => a.id == info.event.id);
                        
                        // Format date and time
                        const startDate = new Date(info.event.start);
                        const endDate = new Date(info.event.end);
                        const dateStr = startDate.toLocaleDateString('fr-FR', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        });
                        const timeStr = startDate.toLocaleTimeString('fr-FR', { 
                            hour: '2-digit', 
                            minute: '2-digit' 
                        });
                        const endTimeStr = endDate.toLocaleTimeString('fr-FR', { 
                            hour: '2-digit', 
                            minute: '2-digit' 
                        });
                        
                        // Format status
                        const statusMap = {
                            'pending': 'En attente',
                            'confirmed': 'Confirmé',
                            'in_progress': 'En cours',
                            'done': 'Terminé',
                            'canceled': 'Annulé',
                            'no_show': 'Absent'
                        };
                        
                        // Format type
                        const typeMap = {
                            'consultation': 'Consultation',
                            'suivi': 'Suivi',
                            'acte': 'Acte',
                        };
                        
                        let content = `
                            <div class="space-y-3">
                                <div class="border-b border-neutral-700 pb-2">
                                    <h3 class="text-sm font-semibold text-white mb-1">
                                        ${appointment.patient_name || info.event.title.split(' - ')[0]}
                                    ${appointment.medical_file_file_number && appointment.medical_file_patient_id ? `<a href='/patients/${appointment.medical_file_patient_id}' target='_blank' class='text-xs text-teal-400 underline'>(Dossier N°: ${appointment.medical_file_file_number})</a>` : ''}
                                    </h3>
                                    <div class="text-sm text-neutral-300">${typeMap[appointment.type] || appointment.type}</div>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-alt text-blue-400 w-4"></i>
                                        <span class="text-sm">${dateStr}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock text-green-400 w-4"></i>
                                        <span class="text-sm">${timeStr} - ${endTimeStr} (${appointment.duration_minutes} min)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-info-circle text-yellow-400 w-4"></i>
                                        <span class="text-sm">${statusMap[appointment.status] || appointment.status}</span>
                                    </div>
                                    ${appointment.patient_phone ? `
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-phone text-purple-400 w-4"></i>
                                        <span class="text-sm">${appointment.patient_phone}</span>
                                    </div>
                                    ` : ''}
                                    ${appointment.patient_email ? `
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-envelope text-red-400 w-4"></i>
                                        <span class="text-sm">${appointment.patient_email}</span>
                                    </div>
                                    ` : ''}
                                    ${appointment.notes ? `
                                    <div class="flex items-start gap-2">
                                        <i class="fas fa-sticky-note text-orange-400 w-4 mt-0.5"></i>
                                        <span class="text-sm">${appointment.notes}</span>
                                    </div>
                                    ` : ''}
                                    ${appointment.created_by ? `
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user text-cyan-400 w-4"></i>
                                        <span class="text-sm">Créé par: ${appointment.created_by}</span>
                                    </div>
                                    ` : ''}
                                </div>
                                
                                ${appointment.patient_phone || appointment.patient_email ? `
                                <div class="border-t border-neutral-700 pt-3 flex gap-2 items-center">
                                    ${appointment.patient_phone ? `
                                    <x-custom-tooltip text="Envoyer un message WhatsApp">
                                        <a href="https://wa.me/${appointment.patient_phone.replace(/\s/g, '')}?text=${encodeURIComponent('🏥 *Cabinet Dentaire*\n\nBonjour *' + (appointment.patient_name || '') + '*,\n\nNous vous rappelons votre *' + (typeMap[appointment.type] || appointment.type) + '* prévu :\n📅 *Date :* ' + startDate.toLocaleDateString('fr-FR') + '\n🕐 *Heure :* ' + timeStr + '\n⏱️ *Durée :* ' + appointment.duration_minutes + ' minutes\n\n📍 *Lieu :* Cabinet Dentaire\n\n⚠️ *Important :*\n• Merci de confirmer votre présence\n• Arrivez 10 minutes avant l\'heure prévue\n• Apportez vos documents médicaux si nécessaire\n\n📞 Pour toute question : contactez-nous\n\nCordialement,\nVotre équipe médicale 🦷')}")" 
                                           target="_blank" 
                                           class="inline-flex items-center gap-2 px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    </x-custom-tooltip>
                                    <x-custom-tooltip text="Appeler le patient">
                                        <a href="tel:${appointment.patient_phone.replace(/\s/g, '')}"
                                           class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-md transition-colors">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                    </x-custom-tooltip>
                                    ` : ''}
                                    ${appointment.patient_email ? `
                                    <x-custom-tooltip text="Envoyer un email">
                                        <a href="mailto:${appointment.patient_email}?subject=${encodeURIComponent('Rappel de rendez-vous - Cabinet Dentaire')}&body=${encodeURIComponent('Bonjour ' + (appointment.patient_name || '') + ',\n\nNous vous rappelons votre rendez-vous de ' + (typeMap[appointment.type] || appointment.type) + ' prévu le ' + startDate.toLocaleDateString('fr-FR') + ' à ' + timeStr + '.\n\nCordialement,\nVotre équipe médicale 🦷')}" 
                                           class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </x-custom-tooltip>
                                    ` : ''}
                                </div>
                                ` : ''}
                                <div class="my-3"><hr class="border-neutral-700"></div>
                                <div class="flex items-center gap-2">
                                    <x-custom-tooltip text="Changer le statut du rendez-vous">
                                        <select id="quick-status-select" class="px-2 py-1 rounded-md bg-neutral-800 text-neutral-100 border border-neutral-600 focus:outline-none focus:ring-2 focus:ring-teal-500 text-xs">
                                            <option value="pending" ${appointment.status === 'pending' ? 'selected' : ''}>En attente</option>
                                            <option value="confirmed" ${appointment.status === 'confirmed' ? 'selected' : ''}>Confirmé</option>
                                            <option value="in_progress" ${appointment.status === 'in_progress' ? 'selected' : ''}>En cours</option>
                                            <option value="done" ${appointment.status === 'done' ? 'selected' : ''}>Terminé</option>
                                            <option value="canceled" ${appointment.status === 'canceled' ? 'selected' : ''}>Annulé</option>
                                            <option value="no_show" ${appointment.status === 'no_show' ? 'selected' : ''}>Absent</option>
                                        </select>
                                    </x-custom-tooltip>
                                    <button id="quick-status-confirm" class="px-3 py-2 rounded-md bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium flex items-center gap-2 transition-colors">
                                        <span>Confirmer</span>
                                        <span id="quick-status-spinner" class="hidden ml-1"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                                    </button>
                                </div>
                            </div>
                        `;
                        document.getElementById('popup-content').innerHTML = content;
                        const popup = document.getElementById('appointment-popup');
                        popup.style.display = 'block';
                        popup.style.left = info.jsEvent.pageX + 10 + 'px';
                        popup.style.top = info.jsEvent.pageY + 10 + 'px';
                        document.getElementById('popup-edit').onclick = function() {
                            this.querySelector('.spinner').classList.remove('hidden');
                            document.getElementById('popup-delete').querySelector('.spinner').classList.add('hidden');
                            Livewire.first().call('edit', info.event.id).then(() => {
                                openModal('edit-appointment-modal');
                            });
                        };
                        document.getElementById('popup-delete').onclick = function() {
                            this.querySelector('.spinner').classList.remove('hidden');
                            document.getElementById('popup-edit').querySelector('.spinner').classList.add('hidden');
                            Livewire.first().call('confirmDelete', info.event.id).then(() => {
                                openModal('delete-appointment-modal');
                            });
                        };
                        // Show 'Créer consultation' button only for consultation type AND confirmed status
                        const btnConsult = document.getElementById('popup-create-consultation');
                        const btnConsultDisabled = document.getElementById('popup-create-consultation-disabled');
                        if (appointment.type === 'consultation' && appointment.status === 'confirmed' && !appointment.has_consultation) {
                            btnConsult.style.display = 'inline-flex';
                            btnConsultDisabled.style.display = 'none';
                            btnConsult.disabled = false;
                            btnConsult.classList.remove('opacity-50', 'cursor-not-allowed');
                            btnConsult.classList.add('hover:bg-emerald-100', 'dark:hover:bg-emerald-900/50');
                            btnConsult.onclick = function() {
                                btnConsult.querySelector('.spinner').classList.remove('hidden');
                                btnConsult.disabled = true;
                                Livewire.first().call('openCreateConsultationModal', appointment.id).then(() => {
                                    btnConsult.querySelector('.spinner').classList.add('hidden');
                                    btnConsult.disabled = false;
                                });
                            };
                        } else if (appointment.type === 'consultation' && appointment.status === 'confirmed' && appointment.has_consultation) {
                            // Show disabled button when consultation exists
                            btnConsult.style.display = 'none';
                            btnConsultDisabled.style.display = 'inline-flex';
                        } else {
                            btnConsult.style.display = 'none';
                            btnConsultDisabled.style.display = 'none';
                        }
                        
                        // Show 'Créer acte' button only for acte type AND confirmed status
                        const btnActe = document.getElementById('popup-create-acte');
                        const btnActeDisabled = document.getElementById('popup-create-acte-disabled');
                        if (appointment.type === 'acte' && appointment.status === 'confirmed' && !appointment.has_acte) {
                            btnActe.style.display = 'inline-flex';
                            btnActeDisabled.style.display = 'none';
                            btnActe.disabled = false;
                            btnActe.classList.remove('opacity-50', 'cursor-not-allowed');
                            btnActe.classList.add('hover:bg-purple-100', 'dark:hover:bg-purple-900/50');
                            btnActe.onclick = function() {
                                btnActe.querySelector('.spinner').classList.remove('hidden');
                                btnActe.disabled = true;
                                Livewire.first().call('openCreateActeModal', appointment.id).then(() => {
                                    btnActe.querySelector('.spinner').classList.add('hidden');
                                    btnActe.disabled = false;
                                });
                            };
                        } else if (appointment.type === 'acte' && appointment.status === 'confirmed' && appointment.has_acte) {
                            // Show disabled button when acte exists
                            btnActe.style.display = 'none';
                            btnActeDisabled.style.display = 'inline-flex';
                        } else {
                            btnActe.style.display = 'none';
                            btnActeDisabled.style.display = 'none';
                        }
                        document.getElementById('popup-close').onclick = function() {
                            popup.style.display = 'none';
                        };
                        document.getElementById('quick-status-confirm').onclick = function() {
                            const spinner = document.getElementById('quick-status-spinner');
                            spinner.classList.remove('hidden');
                            const statusToUpdate = document.getElementById('quick-status-select').value;
                            Livewire.first().call('quickUpdateStatus', appointment.id, statusToUpdate)
                                .then(() => {
                                    spinner.classList.add('hidden');
                                })
                                .catch(() => {
                                    spinner.classList.add('hidden');
                                });
                        };
                    }
                });
                
                calendar.render();
            }

            // Also, update window.appointments when Livewire updates
            Livewire.on('refreshCalendar', (data) => {
                // If data is { appointments: [...] }, extract the array
                const appointments = Array.isArray(data) ? data : (data.appointments ?? []);
                const calendarEl = document.getElementById('appointments-calendar');
                if (calendarEl) {
                    calendarEl.setAttribute('data-appointments', JSON.stringify(appointments));
                    // console.log('Appointments data:', appointments);
                }
                setTimeout(() => {
                    renderAppointmentsCalendar(appointments);
                }, 100);
            });
       
        </script>

        <script>
            // Listen for Livewire event to close the popup after modal is shown
            window.addEventListener('close-appointment-popup', function() {
                document.getElementById('appointment-popup').style.display = 'none';
                document.getElementById('popup-edit').querySelector('.spinner').classList.add('hidden');
                document.getElementById('popup-delete').querySelector('.spinner').classList.add('hidden');
            });

            // Listen for Livewire event to close modals only on success
            Livewire.on('appointmentSaved', function() {
                closeModal('create-appointment-modal');
                closeModal('edit-appointment-modal');
            });
        </script>



        <script>
            Livewire.on('consultationCreated', () => {
                if (window.Livewire && typeof Livewire.navigate === 'function') {
                    Livewire.navigate('/consultations');
                } else {
                    window.location.href = '/consultations';
                }
            });
            
            Livewire.on('acteCreated', () => {
                if (window.Livewire && typeof Livewire.navigate === 'function') {
                    Livewire.navigate('/actes');
                } else {
                    window.location.href = '/actes';
                }
            });
        </script>
    @endscript

    <script>
        // Global functions for modal handling
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }
        
        function handleCreateModalOpen() {
            const btn = document.getElementById('create-appointment-btn');
            const spinner = btn.querySelector('.spinner');
            spinner.classList.remove('hidden');
            Livewire.first().call('prepareCreate').then(() => {
                spinner.classList.add('hidden');
                openModal('create-appointment-modal');
            });
        }
    </script>

</div>

