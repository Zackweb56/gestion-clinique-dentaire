<div>
    @include('components.flash_message')

    <div class="rounded-xl p-6 bg-neutral-900 shadow mb-6">
    
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
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
                        placeholder="Rechercher une consultation..."
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
    
                <div class="relative w-full md:w-48">
                    <select
                        wire:model.live="statusFilter"
                        class="block w-full pl-3 pr-10 py-2 border border-neutral-300 dark:border-neutral-600 rounded-md leading-5 bg-white dark:bg-neutral-700 text-neutral-700 dark:text-neutral-300 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent sm:text-sm"
                    >
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="in_progress">En cours</option>
                        <option value="completed">Terminé</option>
                        <option value="cancelled">Annulé</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <div wire:loading wire:target="statusFilter" class="animate-spin h-5 w-5 text-teal-500">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Consultations Table --}}
        <div class="mt-6 overflow-x-auto">
            <table class="w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            # N°
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Date & Heure
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Patient
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Responsable
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200 dark:divide-neutral-700 dark:bg-neutral-900">
                    @forelse ($consultations as $consultation)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $consultation->consultation_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $consultation->consultation_date->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-white">
                                <span class="font-semibold text-neutral-900 dark:text-white">
                                    <div class="p-0 text-sm font-normal">
                                        <div class="flex items-center gap-2 pb-1.5 text-start text-sm">
                                            <span class="relative flex h-10 w-10 shrink-0 overflow-hidden rounded-lg">
                                                <span class="flex h-full w-full items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-800">
                                                    {{ $consultation->medicalFile->patient->initials() }}
                                                </span>
                                            </span>
                                            <div class="grid flex-1 text-start text-sm leading-tight">
                                                <span class="truncate font-semibold">{{ $consultation->medicalFile->patient->patient_full_name }}</span>
                                                <span class="text-teal-600 dark:text-teal-400" style="font-size: 10px;">
                                                    <a href="{{ route('patients.details', $consultation->medicalFile->patient->id) }}" class="underline hover:text-teal-800 dark:hover:text-teal-200" wire:navigate>
                                                        Dossier N° {{ $consultation->medicalFile->file_number }}
                                                    </a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </span>
                            </td>
                            {{-- <td class="px-6 py-4 text-sm text-neutral-500 dark:text-neutral-400">
                                {{ Str::limit($consultation->symptoms, 50) ?: '--' }}
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="py-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-md transition-colors items-center
                                    @if($consultation->status === 'completed') bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 hover:bg-teal-100 dark:hover:bg-teal-900/50
                                    @elseif($consultation->status === 'cancelled') bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50
                                    @elseif($consultation->status === 'in_progress') bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50
                                    @else bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-100 dark:hover:bg-yellow-900/50
                                    @endif
                                ">
                                    @if($consultation->status === 'completed') Terminé
                                    @elseif($consultation->status === 'cancelled') Annulé
                                    @elseif($consultation->status === 'in_progress') En cours
                                    @else En attente
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $consultation->responsable ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                <div class="flex space-x-2">
                                    <!-- Eye icon button for details -->
                                    <x-custom-tooltip text="Voir les détails de la consultation">
                                        <button 
                                            class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-neutral-50 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors inline-flex items-center gap-2"
                                            wire:click="showDetails({{ $consultation->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="showDetails({{ $consultation->id }})"
                                        >
                                        <i class="fas fa-eye"></i>
                                        <span wire:loading wire:target="showDetails({{ $consultation->id }})" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                        </button>
                                    </x-custom-tooltip>
                                    @can('modifier consultation')
                                    <x-custom-tooltip text="Modifier la consultation">
                                        <button 
                                            class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors inline-flex items-center gap-2"
                                            wire:click="edit({{ $consultation->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="edit({{ $consultation->id }})"
                                        >
                                        <i class="fas fa-pen"></i>
                                        <span wire:loading wire:target="edit({{ $consultation->id }})" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                        </button>
                                    </x-custom-tooltip>
                                    @endcan
                                    @can('supprimer consultation')
                                    <x-custom-tooltip text="Supprimer la consultation">
                                        <button 
                                            class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center gap-2"
                                            wire:click="confirmDelete({{ $consultation->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="confirmDelete({{ $consultation->id }})"
                                        >
                                        <i class="fas fa-trash-alt"></i>
                                        <span wire:loading wire:target="confirmDelete({{ $consultation->id }})" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                        </button>
                                    </x-custom-tooltip>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <svg class="w-12 h-12 text-neutral-400 dark:text-neutral-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Aucune consultation trouvée</p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Commencez par créer votre première consultation</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    
        <div class="mt-4">
            {{ $consultations->links() }}
        </div>
    
    </div>

    {{-- modifier consultation Modal --}}
    <flux:modal name="edit-consultation" class="md:w-[700px]">
        <form wire:submit.prevent="update">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Modifier la consultation') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Mettre à jour les détails de la consultation') }}
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <flux:select 
                        label="Dossier médical" 
                        wire:model="medical_file_id"
                        error="{{ $errors->first('medical_file_id') }}"
                    >
                        <option value="">Sélectionner un dossier médical</option>
                        @foreach($medical_files as $medical_file)
                            <option value="{{ $medical_file->id }}">{{ $medical_file->file_number }} ({{ $medical_file->patient->patient_full_name }})</option>
                        @endforeach
                    </flux:select>

                    <flux:input 
                        type="datetime-local"
                        label="Date et heure de consultation" 
                        wire:model="consultation_date"
                        error="{{ $errors->first('consultation_date') }}"
                    />

                    <flux:textarea 
                        label="Symptômes" 
                        wire:model="symptoms"
                        error="{{ $errors->first('symptoms') }}"
                        rows="3"
                    />

                    <flux:textarea 
                        label="Diagnostic" 
                        wire:model="diagnosis"
                        error="{{ $errors->first('diagnosis') }}"
                        rows="3"
                    />

                    <flux:textarea 
                        label="Plan de acte" 
                        wire:model="acte_plan"
                        error="{{ $errors->first('acte_plan') }}"
                        rows="3"
                    />

                    <flux:textarea 
                        label="Notes" 
                        wire:model="notes"
                        error="{{ $errors->first('notes') }}"
                        rows="3"
                    />

                    <flux:select 
                        label="Statut" 
                        wire:model="status"
                        error="{{ $errors->first('status') }}"
                    >
                        <option value="pending">En attente</option>
                        <option value="in_progress">En cours</option>
                        <option value="completed">Terminé</option>
                        <option value="cancelled">Annulé</option>
                    </flux:select>
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button type="submit" variant="primary" title="Mettre à jour la consultation">
                        {{ __('Mettre à jour') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-confirmation-consultation" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Supprimer la consultation') }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ __('Êtes-vous sûr de vouloir supprimer cette consultation ? Cette action est irréversible.') }}
                </flux:text>
            </div>
            <div class="flex justify-end space-x-3">
                <flux:button 
                    type="button" 
                    variant="danger" 
                    wire:click="delete"
                    title="Confirmer la suppression de la consultation"
                >
                    {{ __('Oui, supprimer') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Consultation Details Modal (placeholder) --}}
    <flux:modal name="consultation-details" class="w-full max-w-3xl">
        <div class="space-y-10">
            <flux:heading size="lg">
                {{ __('Détails de la consultation') }}
            </flux:heading>
            <!-- Patient Info Section -->
            <div>
                <h3 class="text-lg font-bold text-teal-700 mb-4 pb-2 border-b border-teal-200 flex items-center gap-2">
                    <i class="fas fa-user-circle"></i> Informations du patient
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user text-teal-500"></i>
                        <span class="font-semibold">Patient :</span>
                        <span class="ml-2">{{ $details_patient_name ?? '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-id-card text-teal-500"></i>
                        <span class="font-semibold">CIN :</span>
                        <span class="ml-2">{{ $details_patient_cin ?? '--' }}</span>
                    </div>
                </div>
            </div>
            <!-- Consultation Info Section -->
            <div>
                <h3 class="text-lg font-bold text-teal-700 mb-4 pb-2 border-b border-teal-200 flex items-center gap-2">
                    <i class="fas fa-stethoscope"></i> Informations de la consultation
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-hashtag text-teal-500"></i>
                        <span class="font-semibold">N° Consultation :</span>
                        <span class="ml-2">{{ $details_consultation_number }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-teal-500"></i>
                        <span class="font-semibold">Date & Heure :</span>
                        <span class="ml-2">{{ $details_consultation_date ?? '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-thermometer-half text-teal-500"></i>
                        <span class="font-semibold">Symptômes :</span>
                        <span class="ml-2">{{ $details_symptoms ?? '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-diagnoses text-teal-500"></i>
                        <span class="font-semibold">Diagnostic :</span>
                        <span class="ml-2">{{ $details_diagnosis ?? '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-notes-medical text-teal-500"></i>
                        <span class="font-semibold">Plan de acte :</span>
                        <span class="ml-2">{{ $details_acte_plan ?? '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-info-circle text-teal-500"></i>
                        <span class="font-semibold">Statut :</span>
                        @php
                            $badgeClass = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full border ';
                            switch($details_status) {
                                case 'completed':
                                    $badgeClass .= 'border-green-600 text-green-600 bg-transparent';
                                    $statusLabel = 'Terminé';
                                    break;
                                case 'cancelled':
                                    $badgeClass .= 'border-red-600 text-red-600 bg-transparent';
                                    $statusLabel = 'Annulé';
                                    break;
                                case 'in_progress':
                                    $badgeClass .= 'border-blue-600 text-blue-600 bg-transparent';
                                    $statusLabel = 'En cours';
                                    break;
                                case 'pending':
                                    $badgeClass .= 'border-yellow-600 text-yellow-600 bg-transparent';
                                    $statusLabel = 'En attente';
                                    break;
                                default:
                                    $badgeClass .= 'border-neutral-400 text-neutral-400 bg-transparent';
                                    $statusLabel = '--';
                            }
                        @endphp
                        <span class="ml-2 {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </div>
                    <div class="flex items-center gap-2 md:col-span-2">
                        <i class="fas fa-sticky-note text-teal-500"></i>
                        <span class="font-semibold">Notes :</span>
                        <span class="ml-2">{{ $details_notes ?? '--' }}</span>
                    </div>
                </div>
            </div>
            <!-- Metadata Section -->
            <div>
                <h3 class="text-lg font-bold text-teal-700 mb-4 pb-2 border-b border-teal-200 flex items-center gap-2">
                    <i class="fas fa-user-shield"></i> Métadonnées
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user text-teal-500"></i>
                        <span class="font-semibold">Responsable :</span>
                        <span class="ml-2">{{ $details_responsable ?? '--' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
