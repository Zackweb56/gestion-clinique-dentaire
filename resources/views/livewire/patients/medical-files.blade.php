<div>
    @include('components.flash_message')

    <div class="rounded-xl p-6 bg-neutral-900 shadow mb-6">
    
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
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
                        placeholder="Rechercher un dossier..."
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
            </div>
    
            <div>
                @can('créer dossier-médical')
                    <x-custom-tooltip text="Créer un nouveau dossier médical">
                        <flux:button variant="primary" wire:click="create">
                            <i class="fa-solid fa-file-medical mr-2"></i>
                            {{ __('Nouveau dossier') }}
                        </flux:button>
                    </x-custom-tooltip>
                @endcan
            </div>
        </div>
        <flux:separator variant="subtle" />
        
        {{-- Medical Files Table --}}
        <div class="mt-6 overflow-x-auto">
            <table class="w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            N° Dossier
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Patient
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Maladies chroniques
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Créé par
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200 dark:divide-neutral-700 dark:bg-neutral-900">
                    @forelse ($medicalFiles as $index => $file)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900 dark:text-white font-semibold">
                                <a href="{{ route('patients.details', $file->patient_id) }}" class="text-teal-600 dark:text-teal-400 font-semibold hover:underline" wire:navigate>
                                    {{ $file->file_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span class="flex h-full w-full items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-800">
                                            {{ $file->patient->initials() }}
                                        </span>
                                    </span>
                                    <span class="truncate">{{ $file->patient->patient_full_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $file->chronic_diseases ? Str::limit($file->chronic_diseases, 30) : '--' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $file->created_by }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                <div class="flex flex-row gap-2 items-end md:items-center">
                                    <x-custom-tooltip text="Voir les détails du dossier médical">
                                        <button 
                                            class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-neutral-50 dark:bg-neutral-800 text-neutral-700 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 transition-colors inline-flex items-center gap-2"
                                            wire:click="showDetails({{ $file->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="showDetails({{ $file->id }})"
                                        >
                                        <i class="fas fa-eye"></i>
                                       
                                        <span wire:loading wire:target="showDetails({{ $file->id }})" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                        </button>
                                    </x-custom-tooltip>
                                    @can('modifier dossier-médical')
                                    <x-custom-tooltip text="Modifier le dossier médical">
                                        <button 
                                            class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors inline-flex items-center gap-2"
                                            wire:click="edit({{ $file->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="edit({{ $file->id }})"
                                        >
                                        <i class="fas fa-pen"></i>
                                        <span wire:loading wire:target="edit({{ $file->id }})" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                        </button>
                                    </x-custom-tooltip>
                                    @endcan
                                    @can('supprimer dossier-médical')
                                    <x-custom-tooltip text="Supprimer le dossier médical">
                                        <button 
                                            class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center gap-2"
                                            wire:click="confirmDelete({{ $file->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="confirmDelete({{ $file->id }})"
                                        >
                                        <i class="fas fa-trash-alt"></i>
                                        
                                        <span wire:loading wire:target="confirmDelete({{ $file->id }})" class="inline-flex items-center">
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
                            <td colspan="7" class="px-6 py-8">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <svg class="w-12 h-12 text-neutral-400 dark:text-neutral-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Aucun dossier médical trouvé</p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Commencez par créer votre premier dossier</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $medicalFiles->links() }}
        </div>

    </div>

    {{-- Create Medical File Modal --}}
    <flux:modal name="create-medical-file" class="md:w-[700px]">
        <form wire:submit.prevent="store">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Nouveau dossier médical') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Remplissez les informations médicales') }}
                    </flux:text>
                </div>

                @if(collect($this->getNewPatients())->isEmpty())
                    <div class="bg-yellow-100 text-yellow-800 rounded-md px-4 py-2 text-sm mb-2">
                        Aucun patient disponible pour créer un dossier médical. Tous les patients ont déjà un dossier ou aucun patient n’a encore été créé. Veuillez d’abord créer un patient sans dossier.
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <div class="flex items-center mb-1">
                            <label for="patient_id" class="mr-2">Patient</label>
                            <span class="text-xs text-red-500">Requis</span>
                        </div>
                        <flux:select 
                            wire:model="patient_id" 
                            error="{{ $errors->first('patient_id') }}"
                            required
                            id="patient_id"
                        >
                            <option value="">Sélectionnez un patient</option>
                            @foreach($this->getNewPatients() as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->patient_full_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>

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
                  
                    <x-custom-tooltip text="Enregistrer le dossier médical">
                        <flux:button type="submit" variant="primary">
                            {{ __('Enregistrer') }}
                        </flux:button>
                    </x-custom-tooltip>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Edit Medical File Modal --}}
    <flux:modal name="edit-medical-file" class="md:w-[700px]">
        <form wire:submit.prevent="update">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Modifier le dossier médical') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Mettez à jour les informations médicales') }}
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <div class="flex items-center mb-1">
                            <label for="patient_id" class="mr-2">Patient</label>
                            <span class="text-xs text-red-500">Requis</span>
                        </div>
                        <flux:select 
                            wire:model.defer="patient_id" 
                            error="{{ $errors->first('patient_id') }}"
                            required
                            id="patient_id"
                        >
                            <option value="">Sélectionnez un patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->patient_full_name }}{{ $patient->cin ? ' (' . $patient->cin . ')' : '' }}</option>
                            @endforeach
                        </flux:select>
                    </div>

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
                   
                    <x-custom-tooltip text="Mettre à jour le dossier médical">
                        <flux:button type="submit" variant="primary">
                            {{ __('Mettre à jour') }}
                        </flux:button>
                    </x-custom-tooltip>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-confirmation-medical-file" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Supprimer le dossier médical') }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ __('Êtes-vous sûr de vouloir supprimer ce dossier médical ? Cette action est irréversible.') }}
                </flux:text>
                @if(isset($medicalFiles) && $medicalFiles->where('id', $medicalFileId ?? null)->first() && $medicalFiles->where('id', $medicalFileId ?? null)->first()->consultations->count() > 0)
                    <div class="mt-4 p-3 rounded bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors text-sm flex items-center gap-2">
                        <span><i class="fas fa-exclamation-triangle text-lg me-2"></i><strong>Attention&nbsp;:</strong> La suppression de ce dossier médical entraînera également la suppression de <b>toutes les consultations associées</b> à ce dossier.</span>
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-3">
              
                <x-custom-tooltip text="Confirmer la suppression du dossier médical">
                    <flux:button 
                        type="button" 
                        variant="danger" 
                        wire:click="delete"
                    >
                        {{ __('Supprimer définitivement') }}
                    </flux:button>
                </x-custom-tooltip>
            </div>
        </div>
    </flux:modal>

    {{-- Medical File Details Modal (placeholder) --}}
    <flux:modal name="medical-file-details" class="w-full max-w-4xl">
        <div class="space-y-10">
            <flux:heading size="lg">
                {{ __('Détails du dossier médical') }}
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
                        <span class="ml-2">{{ $details_patient_name ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-id-card text-teal-500"></i>
                        <span class="font-semibold">CIN :</span>
                        <span class="ml-2">{{ $details_patient_cin ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-phone text-teal-500"></i>
                        <span class="font-semibold">Téléphone :</span>
                        <span class="ml-2">{{ $details_patient_phone ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-envelope text-teal-500"></i>
                        <span class="font-semibold">Email :</span>
                        <span class="ml-2">{{ $details_patient_email ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-venus-mars text-teal-500"></i>
                        <span class="font-semibold">Genre :</span>
                        <span class="ml-2">{{ $details_patient_gender == 'H' ? 'Homme' : ($details_patient_gender == 'F' ? 'Femme' : '--') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-birthday-cake text-teal-500"></i>
                        <span class="font-semibold">Date de naissance :</span>
                        <span class="ml-2">{{ $details_patient_birth_date ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2 md:col-span-2">
                        <i class="fas fa-map-marker-alt text-teal-500"></i>
                        <span class="font-semibold">Adresse :</span>
                        <span class="ml-2">{{ $details_patient_address ?: '--' }}</span>
                    </div>
                </div>
            </div>
            <!-- Medical Info Section -->
            <div>
                <h3 class="text-lg font-bold text-teal-700 mb-4 pb-2 border-b border-teal-200 flex items-center gap-2">
                    <i class="fas fa-notes-medical"></i> Informations médicales
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="flex items-center gap-2 md:col-span-2">
                        <i class="fas fa-hashtag text-teal-500"></i>
                        <span class="font-semibold">N° Dossier :</span>
                        <span class="ml-2">{{ $details_file_number ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2 md:col-span-2">
                        <i class="fas fa-notes-medical text-teal-500"></i>
                        <span class="font-semibold">Maladies chroniques :</span>
                        <span class="ml-2">{{ $details_chronic_diseases ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2 md:col-span-2">
                        <i class="fas fa-pills text-teal-500"></i>
                        <span class="font-semibold">Médicaments actuels :</span>
                        <span class="ml-2">{{ $details_current_medications ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2 md:col-span-2">
                        <i class="fas fa-allergies text-teal-500"></i>
                        <span class="font-semibold">Allergies :</span>
                        <span class="ml-2">{{ $details_allergies ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2 md:col-span-2">
                        <i class="fas fa-sticky-note text-teal-500"></i>
                        <span class="font-semibold">Notes médicales :</span>
                        <span class="ml-2">{{ $details_notes ?: '--' }}</span>
                    </div>
                </div>
            </div>
            <!-- Metadata Section -->
            <div>
                <h3 class="text-lg font-bold text-teal-700 mb-4 pb-2 border-b border-teal-200 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Métadonnées
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-plus text-teal-500"></i>
                        <span class="font-semibold">Créé par :</span>
                        <span class="ml-2">{{ $details_created_by ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-plus text-teal-500"></i>
                        <span class="font-semibold">Créé le :</span>
                        <span class="ml-2">{{ $details_created_at ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-edit text-teal-500"></i>
                        <span class="font-semibold">Modifié par :</span>
                        <span class="ml-2">{{ $details_updated_by ?: '--' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-teal-500"></i>
                        <span class="font-semibold">Modifié le :</span>
                        @if($details_updated_at && $details_updated_at != $details_created_at)
                            <span class="ml-2">{{ $details_updated_at }}</span>
                        @else
                            --
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </flux:modal>

</div>