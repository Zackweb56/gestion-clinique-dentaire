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
                        placeholder="Rechercher un service..."
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
                <x-custom-tooltip text="Créer un nouveau service">
                @can('créer service')
                    <flux:button variant="primary" wire:click="create" class="inline-flex items-center gap-2">
                        <i class="fas fa-plus me-2"></i>
                        {{ __('Nouveau service') }}
                    </flux:button>
                @endcan
                </x-custom-tooltip>
            </div>
        </div>
        
        {{-- Services Table --}}
        <div class="mt-6 overflow-x-auto">
            <table class="w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Nom
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Prix
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Durée (min)
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200 dark:divide-neutral-700 dark:bg-neutral-900">
                    @forelse ($services as $index => $service)
                        <tr class="transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-white">
                                {{ $service->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ number_format($service->price, 2) }} MAD
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ $service->duration_minutes }} min
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                <x-custom-tooltip text="{{ $service->description }}">
                                    {{ Str::limit($service->description, 15) ?? '--' }}
                                </x-custom-tooltip>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center">
                                    <button 
                                        wire:click="toggleStatus({{ $service->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="toggleStatus({{ $service->id }})"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 {{ $service->is_active ? 'bg-teal-600' : 'bg-neutral-200 dark:bg-neutral-700' }}"
                                        role="switch"
                                        aria-checked="{{ $service->is_active ? 'true' : 'false' }}"
                                    >
                                        <span 
                                            aria-hidden="true" 
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $service->is_active ? 'translate-x-5' : 'translate-x-0' }}"
                                        ></span>
                                    </button>
                                    <span class="ml-2 text-sm {{ $service->is_active ? 'text-teal-600 dark:text-teal-400' : 'text-neutral-500 dark:text-neutral-400' }}">
                                        {{ $service->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                <div class="flex space-x-2">
                                    @can('modifier service')
                                    <x-custom-tooltip text="Modifier le service">
                                    <button 
                                        class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors inline-flex items-center gap-2"
                                        wire:click="edit({{ $service->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="edit({{ $service->id }})"
                                    >
                                        <i class="fas fa-pen"></i>
                                        <span wire:loading wire:target="edit({{ $service->id }})" class="inline-flex items-center">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </button>
                                    </x-custom-tooltip>
                                    @endcan
                                    @can('supprimer service')
                                    <x-custom-tooltip text="Supprimer le service">
                                    <button 
                                        class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors inline-flex items-center gap-2"
                                        wire:click="confirmDelete({{ $service->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="confirmDelete({{ $service->id }})"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                        <span wire:loading wire:target="confirmDelete({{ $service->id }})" class="inline-flex items-center">
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
                                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Aucun service trouvé</p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Commencez par créer votre premier service</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    
        <div class="mt-4">
            {{ $services->links() }}
        </div>
    
    </div>

    {{-- créer service Modal --}}
    <flux:modal name="create-service" class="md:w-[700px]">
        <form wire:submit.prevent="store">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Nouveau service') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Remplissez les informations du service') }}
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <flux:input 
                        label="Nom du service *" 
                        wire:model.defer="name" 
                        placeholder="Entrez le nom du service"
                        error="{{ $errors->first('name') }}"
                        {{-- required --}}
                    />

                    <flux:textarea 
                        label="Description" 
                        wire:model.defer="description" 
                        error="{{ $errors->first('description') }}"
                        placeholder="Description détaillée du service"
                        rows="3"
                    />

                    <flux:input 
                        type="number"
                        step="0.01"
                        label="Prix (MAD) *" 
                        placeholder="Entrez le prix du service"
                        wire:model.defer="price" 
                        error="{{ $errors->first('price') }}"
                        {{-- required --}}
                    />

                    <flux:input 
                        type="number"
                        min="1"
                        max="1440"
                        step="1"
                        label="Durée (minutes) *"
                        placeholder="Ex: 30"
                        wire:model.defer="duration_minutes"
                        error="{{ $errors->first('duration_minutes') }}"
                    />

                    <flux:textarea 
                        label="Notes" 
                        wire:model.defer="notes" 
                        error="{{ $errors->first('notes') }}"
                        placeholder="Notes supplémentaires"
                        rows="3"
                    />
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button type="submit" variant="primary">
                        {{ __('Enregistrer') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- modifier service Modal --}}
    <flux:modal name="edit-service" class="md:w-[700px]">
        <form wire:submit.prevent="update">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        {{ __('Modifier le service') }}
                    </flux:heading>
                    <flux:text class="mt-2">
                        {{ __('Mettez à jour les informations du service') }}
                    </flux:text>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <flux:input 
                        label="Nom du service" 
                        wire:model.defer="name" 
                        error="{{ $errors->first('name') }}"
                        {{-- required --}}
                    />

                    <flux:textarea 
                        label="Description" 
                        wire:model.defer="description" 
                        error="{{ $errors->first('description') }}"
                        placeholder="Description détaillée du service"
                        rows="3"
                    />

                    <flux:input 
                        type="number"
                        step="0.01"
                        label="Prix (MAD)" 
                        wire:model.defer="price" 
                        error="{{ $errors->first('price') }}"
                        {{-- required --}}
                    />

                    <flux:input 
                        type="number"
                        min="1"
                        max="1440"
                        step="1"
                        label="Durée (minutes)"
                        placeholder="Ex: 30"
                        wire:model.defer="duration_minutes"
                        error="{{ $errors->first('duration_minutes') }}"
                    />

                    <flux:textarea 
                        label="Notes" 
                        wire:model.defer="notes" 
                        error="{{ $errors->first('notes') }}"
                        placeholder="Notes supplémentaires"
                        rows="3"
                    />
                </div>

                <div class="flex justify-end space-x-3">
                    <flux:button type="submit" variant="primary">
                        {{ __('Mettre à jour') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    {{-- Delete Confirmation Modal --}}
    <flux:modal name="delete-confirmation-service" class="md:w-[500px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Supprimer le service') }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ __('Êtes-vous sûr de vouloir supprimer ce service ? Cette action est irréversible.') }}
                </flux:text>
            </div>
            <div class="flex justify-end space-x-3">
                <flux:button 
                    type="button" 
                    variant="danger" 
                    wire:click="delete"
                >
                    {{ __('Oui, supprimer') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
