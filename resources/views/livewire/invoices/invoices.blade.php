
<div>
    @include('components.flash_message')

    {{-- Summary Cards Section --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-neutral-900 rounded-xl shadow p-5 flex items-center gap-4">
            <div class="flex-shrink-0 bg-teal-600/20 rounded-full p-3">
                <i class="fas fa-file-invoice text-teal-400 text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-neutral-400">Montant Total</div>
                <div class="text-lg font-bold text-neutral-100">
                    {{ number_format($invoices->sum('total_amount'), 2) }} MAD
                </div>
            </div>
        </div>
        <div class="bg-neutral-900 rounded-xl shadow p-5 flex items-center gap-4">
            <div class="flex-shrink-0 bg-green-600/20 rounded-full p-3">
                <i class="fas fa-money-bill-wave text-green-400 text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-neutral-400">Total Payé</div>
                <div class="text-lg font-bold text-green-400">
                    {{ number_format($invoices->where('status', 'payé')->sum('paid_amount'), 2) }} MAD
                </div>
            </div>
        </div>
        <div class="bg-neutral-900 rounded-xl shadow p-5 flex items-center gap-4">
            <div class="flex-shrink-0 bg-yellow-600/20 rounded-full p-3">
                <i class="fas fa-hourglass-half text-yellow-400 text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-neutral-400">Total Partiel</div>
                <div class="text-lg font-bold text-yellow-400">
                    {{ number_format($invoices->where('status', 'partiel')->sum('total_amount'), 2) }} MAD
                </div>
            </div>
        </div>
        <div class="bg-neutral-900 rounded-xl shadow p-5 flex items-center gap-4">
            <div class="flex-shrink-0 bg-red-600/20 rounded-full p-3">
                <i class="fas fa-times-circle text-red-400 text-xl"></i>
            </div>
            <div>
                <div class="text-xs text-neutral-400">Total Impayé</div>
                <div class="text-lg font-bold text-red-400">
                    {{ number_format($invoices->where('status', 'impayé')->sum('total_amount'), 2) }} MAD
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl p-6 bg-neutral-900 shadow mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">
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
                        placeholder="Rechercher une facture..."
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
                    <select wire:model.live="statusFilter" class="block w-full md:w-40 py-2 px-3 border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-700 text-neutral-700 dark:text-neutral-200 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:border-transparent sm:text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="impayé">Impayé</option>
                        <option value="partiel">Partiel</option>
                        <option value="payé">Payé</option>
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
        <flux:separator variant="subtle" />

        <div class="mt-6 overflow-x-auto">
            <table class="w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">
                            <span class="flex items-center gap-1">
                                Acte / Facture
                            </span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">TVA</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Total TTC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Payé</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Reste</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-neutral-500 dark:text-neutral-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-neutral-200 dark:divide-neutral-700 dark:bg-neutral-900">
                    @forelse ($invoices as $index => $invoice)
                        <tr class="transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <span class="relative flex h-10 w-10 shrink-0 overflow-hidden rounded-lg">
                                        <span class="flex h-full w-full items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-800">
                                            {{ $invoice->acte->medicalFile->patient->initials() ?? '--' }}
                                        </span>
                                    </span>
                                    <div>
                                        <span class="font-semibold text-md text-neutral-200 block">{{ $invoice->acte->medicalFile->patient->patient_full_name ?? '--' }}</span>
                                        <a href="{{ route('patients.details', $invoice->acte->medicalFile->patient->id) }}" class="text-teal-400 block underline hover:text-teal-300 -mt-1" style="font-size: 10px;" wire:navigate>
                                            Dossier N° {{ $invoice->acte->medicalFile->file_number }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                <div class="flex flex-col gap-1">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-tooth text-teal-400"></i>
                                        <span style="font-size: 11px;">{{ $invoice->acte->acte_number ?? $invoice->acte_id }}</span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-file-invoice text-blue-400"></i>
                                        <span style="font-size: 11px;">{{ $invoice->invoice_number ?? '--' }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                <div class="flex flex-col items-start gap-1">
                                    <x-custom-tooltip text="Montant de paiement">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-money-bill-wave text-green-500"></i>
                                            <span style="font-size: 11px;">{{ number_format($invoice->base_amount, 2) }} MAD</span>
                                        </span>
                                    </x-custom-tooltip>
                                    <x-custom-tooltip text="Date de facture">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-calendar-alt text-blue-500"></i>
                                            <span style="font-size: 11px;">{{ $invoice->created_at ? $invoice->created_at->format('d/m/Y H:i') : '--' }}</span>
                                        </span>
                                    </x-custom-tooltip>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ number_format($invoice->tva_amount, 2) }} MAD ({{ $invoice->tva_rate * 100 }}%)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                {{ number_format($invoice->total_amount, 2) }} MAD
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($invoice->paid_amount ?? 0, 2) }} MAD</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                @if(($invoice->total_amount - ($invoice->paid_amount ?? 0)) > 0)
                                    <span class="text-red-600 dark:text-red-400 font-semibold">{{ number_format($invoice->total_amount - ($invoice->paid_amount ?? 0), 2) }} MAD</span>
                                @else
                                    <span class="text-green-600 dark:text-green-400 font-semibold">0.00 MAD</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $invoice->status === 'payé' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                                       ($invoice->status === 'partiel' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                       'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                    {{ ucfirst($invoice->status ?? 'impayé') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-500 dark:text-neutral-400">
                                <div class="flex space-x-2">
                                    <x-custom-tooltip text="Créer un paiement">
                                        @php
                                            $acteStatus = $invoice->acte->status ?? null;
                                            $isActeCompleted = $acteStatus === 'completed';
                                            $isInvoicePaid = $invoice->status === 'payé';
                                            $shouldDisable = !$isActeCompleted || $isInvoicePaid;
                                        @endphp
                                        <button 
                                            class="px-3 py-2 text-xs font-medium rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors inline-flex items-center gap-2 @if($shouldDisable) cursor-not-allowed opacity-60 @else cursor-pointer @endif"
                                            wire:click="openPaymentModal({{ $invoice->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="openPaymentModal({{ $invoice->id }})"
                                            @if($shouldDisable) disabled style="cursor: not-allowed;" @endif
                                        >
                                            <span wire:loading.remove wire:target="openPaymentModal({{ $invoice->id }})">
                                                <i class="fas fa-credit-card"></i> Paiement
                                            </span>
                                            <span wire:loading wire:target="openPaymentModal({{ $invoice->id }})" class="inline-flex items-center">
                                                <svg class="animate-spin h-4 w-4 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </x-custom-tooltip>
                                    <x-custom-tooltip text="Télécharger PDF">
                                        <button 
                                            class="cursor-pointer px-3 py-2 text-xs font-medium rounded-md bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 hover:bg-teal-100 dark:hover:bg-teal-900/50 transition-colors inline-flex items-center gap-2"
                                            wire:click="downloadPdf({{ $invoice->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="downloadPdf({{ $invoice->id }})"
                                        >
                                            <span wire:loading.remove wire:target="downloadPdf({{ $invoice->id }})"><i class="fas fa-file-pdf"></i> PDF</span>
                                            <span wire:loading wire:target="downloadPdf({{ $invoice->id }})" class="inline-flex items-center">
                                                <svg class="animate-spin h-4 w-4 text-teal-500 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </x-custom-tooltip>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-8">
                                <div class="flex flex-col items-center justify-center text-center">
                                    <svg class="w-12 h-12 text-neutral-400 dark:text-neutral-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Aucune facture trouvée</p>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-1">Commencez par créer votre première facture</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>

</div>
