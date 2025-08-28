<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profil du Cabinet Dentaire')" :subheading="__('Mettez à jour les informations de votre cabinet dentaire')">
        <form wire:submit.prevent="save" class="my-6 w-full space-y-6">
            <div>
                <div class="flex items-center mb-1">
                    <label for="clinic_name" class="mr-2">{{ __('Nom du cabinet') }}</label>
                    <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                </div>
                <flux:input wire:model="clinic_name" id="clinic_name" type="text" required autofocus autocomplete="organization" />
            </div>
            <div>
                <div class="flex items-center mb-1">
                    <label for="ICE" class="mr-2">{{ __('ICE') }}</label>
                    <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                </div>
                <flux:input wire:model="ICE" id="ICE" type="text" required />
                <span class="text-xs text-gray-400">{{ __('Identifiant Commun de l’Entreprise (obligatoire pour la facturation)') }}</span>
            </div>
            <div>
                <div class="flex items-center mb-1">
                    <label for="IF" class="mr-2">{{ __('IF') }}</label>
                    <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                </div>
                <flux:input wire:model="IF" id="IF" type="text" required />
                <span class="text-xs text-gray-400">{{ __('Identifiant Fiscal (numéro d’imposition de l’entreprise)') }}</span>
            </div>
            <div>
                <div class="flex items-center mb-1">
                    <label for="TVA" class="mr-2">{{ __('TVA') }}</label>
                </div>
                <flux:input wire:model="TVA" id="TVA" type="text" />
                <span class="text-xs text-gray-400">{{ __('Taux de TVA appliqué sur les factures (ex : 20 pour 20%)') }}</span>
            </div>
            <div>
                <div class="flex items-center mb-1">
                    <label for="address" class="mr-2">{{ __('Adresse') }}</label>
                    <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                </div>
                <flux:input wire:model="address" id="address" type="text" required autocomplete="street-address" />
            </div>
            <div>
                <div class="flex items-center mb-1">
                    <label for="city" class="mr-2">{{ __('Ville') }}</label>
                    <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                </div>
                <flux:input wire:model="city" id="city" type="text" required autocomplete="address-level2" />
            </div>
            <div>
                <div class="flex items-center mb-1">
                    <label for="phone_01" class="mr-2">{{ __('Téléphone 01') }}</label>
                    <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                </div>
                <flux:input wire:model="phone_01" id="phone_01" type="text" required autocomplete="tel" />
            </div>
            <div>
                <div class="flex items-center mb-1">
                    <label for="phone_02" class="mr-2">{{ __('Téléphone 02') }}</label>
                </div>
                <flux:input wire:model="phone_02" id="phone_02" type="text" autocomplete="tel" />
            </div>
            <div>
                <div class="flex items-center mb-1">
                    <label for="email" class="mr-2">{{ __('E-mail') }}</label>
                    <span class="text-xs text-red-500">{{ __('Requis') }}</span>
                </div>
                <flux:input wire:model="email" id="email" type="email" required autocomplete="email" />
            </div>
            <div>
                <label class="block mb-1" for="logo">{{ __('Logo') }}</label>
                <div class="flex items-center gap-3">
                    <label for="logo" class="inline-block px-4 py-2 bg-neutral-800 text-gray-100 rounded cursor-pointer hover:bg-neutral-700 dark:bg-neutral-700 dark:text-gray-200">
                        {{ __('Choisir un fichier') }}
                    </label>
                    <input type="file" id="logo" wire:model="logo" class="hidden" />
                    <div wire:loading wire:target="logo" class="animate-spin h-5 w-5 ml-2">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    @if ($logo)
                        <img src="{{ $logo->temporaryUrl() }}" alt="Logo" class="h-10 w-10 object-contain rounded border border-gray-400 bg-white dark:bg-neutral-900" />
                        <button type="button" wire:click="$set('logo', null)" class="ml-2 text-red-500 hover:text-red-600 cursor-pointer">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @elseif (!empty($existingLogo))
                        <img src="{{ asset('storage/' . $existingLogo) }}" alt="Logo" class="h-10 w-10 object-contain rounded border border-gray-400 bg-white dark:bg-neutral-900" />
                        <button type="button" wire:click="deleteLogo" class="ml-2 text-red-500 hover:text-red-600 cursor-pointer">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @endif
                </div>
            </div>
            <div>
                <label class="block mb-1" for="signature">{{ __('Signature') }}</label>
                <div class="flex items-center gap-3">
                    <label for="signature" class="inline-block px-4 py-2 bg-neutral-800 text-gray-100 rounded cursor-pointer hover:bg-neutral-700 dark:bg-neutral-700 dark:text-gray-200">
                        {{ __('Choisir un fichier') }}
                    </label>
                    <input type="file" id="signature" wire:model="signature" class="hidden" />
                    <div wire:loading wire:target="signature" class="animate-spin h-5 w-5 ml-2">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    @if ($signature)
                        <img src="{{ $signature->temporaryUrl() }}" alt="Signature" class="h-10 w-10 object-contain rounded border border-gray-400 bg-white dark:bg-neutral-900" />
                        <button type="button" wire:click="$set('signature', null)" class="ml-2 text-red-500 hover:text-red-600 cursor-pointer">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @elseif (!empty($existingSignature))
                        <img src="{{ asset('storage/' . $existingSignature) }}" alt="Signature" class="h-10 w-10 object-contain rounded border border-gray-400 bg-white dark:bg-neutral-900" />
                        <button type="button" wire:click="deleteSignature" class="ml-2 text-red-500 hover:text-red-600 cursor-pointer">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Enregistrer') }}</flux:button>
                </div>
                @if (session('success'))
                    <x-action-message class="me-3" on="saved">
                        {{ session('success') }}
                    </x-action-message>
                @endif
            </div>
        </form>
    </x-settings.layout>
</section>
<script>
    window.addEventListener('navigate-to', event => {
        if (window.Livewire && window.Livewire.navigate) {
            window.Livewire.navigate(event.detail.url);
        } else {
            window.location.href = event.detail.url;
        }
    });
</script>
