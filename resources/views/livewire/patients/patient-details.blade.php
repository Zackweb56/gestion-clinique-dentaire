<div>
    @include('components.flash_message')

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('patients') }}" wire:navigate class="inline-flex items-center text-sm font-medium text-neutral-500 hover:text-teal-600 dark:text-neutral-400 dark:hover:text-teal-400">
                        <i class="fas fa-users mr-2"></i>
                        Patients
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-neutral-400 mx-2"></i>
                        <span class="text-sm font-medium text-neutral-900 dark:text-neutral-200">{{ $patient->patient_full_name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    {{-- Patient Information Card --}}
    <div class="bg-neutral-900 rounded-xl p-6 shadow mb-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white text-2xl font-bold">
                        {{ $patient->initials() }}
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-green-500 border-2 border-white dark:border-neutral-900 flex items-center justify-center">
                        <i class="fas fa-check text-white text-xs"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white mb-1">
                        {{ $patient->patient_full_name }}
                        @if($patient->status)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-2 {{ $patient->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' }}">
                                {{ $this->getStatusLabel($patient->status) }}
                            </span>
                        @endif
                    </h1>
                    <div class="flex items-center mt-2 space-x-2 ml-2">
                        @if($patient->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $patient->phone) }}" target="_blank" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-neutral-800 hover:bg-green-600 text-green-400 hover:text-white shadow transition-colors" title="WhatsApp">
                                <i class="fab fa-whatsapp text-base"></i>
                            </a>
                            <a href="tel:{{ $patient->phone }}" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-neutral-800 hover:bg-teal-600 text-teal-400 hover:text-white shadow transition-colors" title="Appeler">
                                <i class="fas fa-phone text-base"></i>
                            </a>
                        @endif
                        @if($patient->email)
                            <a href="mailto:{{ $patient->email }}" class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-neutral-800 hover:bg-blue-600 text-blue-400 hover:text-white shadow transition-colors" title="Envoyer un email">
                                <i class="fas fa-envelope text-base"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <x-custom-tooltip text="Prochain rendez-vous pour ce patient" placement="left">
            <div class="flex items-center">
                @php
                    $nextAppointment = null;
                    if (isset($medicalFile) && $medicalFile && $medicalFile->appointments) {
                        $nextAppointment = $medicalFile->appointments
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
            </x-custom-tooltip>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Personal Information --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-white border-b border-neutral-700 pb-2">Informations Personnelles</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-birthday-cake text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">Date de naissance</p>
                            <p class="text-white">
                                {{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') : 'Non renseigné' }}
                                @if($this->getAge())
                                    <span class="ml-2 text-xs text-neutral-400">({{ $this->getAge() }} ans)</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-user text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">Genre</p>
                            <p class="text-white">{{ $this->getGenderLabel($patient->gender) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-id-card text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">CIN</p>
                            <p class="text-white">{{ $patient->cin ?? 'Non renseigné' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-shield-alt text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">Type d'assurance</p>
                            <p class="text-white">{{ $patient->insurance_type ? $this->getInsuranceTypeLabel($patient->insurance_type) : 'Non renseigné' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-white border-b border-neutral-700 pb-2">Contact</h3>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-phone text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">Téléphone</p>
                            <p class="text-white">{{ $patient->phone ?? 'Non renseigné' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-envelope text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">Email</p>
                            <p class="text-white">{{ $patient->email ?? 'Non renseigné' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-map-marker-alt text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">Adresse</p>
                            <p class="text-white">{{ $patient->address ?: 'Non renseigné' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Medical Information --}}
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-white border-b border-neutral-700 pb-2">Informations Médicales</h3>
                <div class="space-y-3">
                    @if($medicalFile)
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-hospital-user text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">Dossier médical</p>
                            <p class="text-white">N° {{ $medicalFile->file_number }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-shield-alt text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">Assurance</p>
                            <p class="text-white">{{ $patient->insurance_type ? $this->getInsuranceTypeLabel($patient->insurance_type) : 'Non renseigné' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-user-md text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">Créé par</p>
                            <p class="text-white">{{ $patient->created_by }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-clock text-teal-400 w-5"></i>
                        <div>
                            <p class="text-sm text-neutral-400">Créé le</p>
                            <p class="text-white">{{ $patient->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Section --}}
    <div class="bg-neutral-900 rounded-xl shadow">
        {{-- Tab Navigation --}}
        <div class="border-b border-neutral-700">
            <nav class="flex space-x-8 px-6 overflow-x-auto scrollbar-thin scrollbar-thumb-neutral-600 scrollbar-track-neutral-800" aria-label="Tabs">
                <button wire:click="setActiveTab('appointments')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap {{ $activeTab === 'appointments' ? 'border-teal-500 text-teal-400' : 'border-transparent text-neutral-400 hover:text-neutral-300 hover:border-neutral-300' }}">
                    <i class="fas fa-calendar mr-2"></i>
                    Rendez-vous ({{ $appointments->count() }})
                </button>
                <button wire:click="setActiveTab('consultations')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap {{ $activeTab === 'consultations' ? 'border-teal-500 text-teal-400' : 'border-transparent text-neutral-400 hover:text-neutral-300 hover:border-neutral-300' }}">
                    <i class="fas fa-stethoscope mr-2"></i>
                    Consultations ({{ $consultations->count() }})
                </button>
                <button wire:click="setActiveTab('actes')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap {{ $activeTab === 'actes' ? 'border-teal-500 text-teal-400' : 'border-transparent text-neutral-400 hover:text-neutral-300 hover:border-neutral-300' }}">
                    <i class="fas fa-tooth mr-2"></i>
                    actes ({{ $actes->count() }})
                </button>
                <button wire:click="setActiveTab('factures')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap {{ $activeTab === 'factures' ? 'border-teal-500 text-teal-400' : 'border-transparent text-neutral-400 hover:text-neutral-300 hover:border-neutral-300' }}">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Factures
                </button>
                <button wire:click="setActiveTab('paiements')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors whitespace-nowrap {{ $activeTab === 'paiements' ? 'border-teal-500 text-teal-400' : 'border-transparent text-neutral-400 hover:text-neutral-300 hover:border-neutral-300' }}">
                    <i class="fas fa-credit-card mr-2"></i>
                    Paiements
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-6 max-h-100 overflow-y-auto scrollbar-thin scrollbar-thumb-neutral-600 scrollbar-track-neutral-800">
            {{-- Appointments Tab --}}
            @if($activeTab === 'appointments')
                <div class="space-y-4">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <h3 class="text-lg font-semibold text-white">Rendez-vous</h3>
                        <button class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-medium transition-colors w-full md:w-auto justify-center">
                            <i class="fas fa-plus mr-2"></i>
                            Nouveau rendez-vous
                        </button>
                    </div>
                    @if($appointments->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($appointments->sortByDesc('appointment_date') as $appointment)
                                <div class="bg-gradient-to-br from-neutral-800 to-neutral-900 rounded-xl p-5 border border-neutral-700 shadow hover:shadow-lg transition-shadow flex flex-col h-full">
                                    <div class="flex items-center justify-between mb-2">
                                        @php
                                            $statusLabels = [
                                                'pending' => 'En attente',
                                                'confirmed' => 'Confirmé',
                                                'in_progress' => 'En cours',
                                                'done' => 'Terminé',
                                                'canceled' => 'Annulé',
                                                'no_show' => 'Absent',
                                            ];
                                            $statusText = $statusLabels[$appointment->status] ?? ucfirst($appointment->status);
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold
                                            {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' :
                                               ($appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' :
                                               'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                            <i class="fas fa-circle mr-1 text-[8px]"></i>
                                            {{ $statusText }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ ucfirst($appointment->type) }}
                                        </span>
                                    </div>
                                    @php
                                        \Carbon\Carbon::setLocale('fr');
                                        $start = \Carbon\Carbon::parse($appointment->appointment_date);
                                        $end = $appointment->end_time ? \Carbon\Carbon::parse($appointment->end_time) : null;
                                        $duration = $appointment->duration_minutes ?? ($end ? $start->diffInMinutes($end) : null);
                                    @endphp
                                    <div class="mb-2">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-day text-teal-400 mr-2"></i>
                                            <span class="text-sm text-neutral-200 font-semibold">
                                                {{ $start->translatedFormat('l d F Y') }}
                                            </span>
                                        </div>
                                        @php
                                            $displayDuration = $duration ?? 30;
                                            $displayEnd = (clone $start)->addMinutes($displayDuration);
                                        @endphp
                                        <div class="flex items-center mt-1">
                                            <i class="fas fa-clock text-teal-400 mr-2"></i>
                                            <span class="text-sm text-neutral-300">
                                                {{ $start->format('H:i') }} - {{ $displayEnd->format('H:i') }} ({{ $displayDuration }} min)
                                            </span>
                                        </div>
                                    </div>
                                    @if($appointment->notes)
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-sticky-note text-teal-400 mr-2"></i>
                                            <span class="text-xs text-neutral-400 italic">{{ Str::limit($appointment->notes, 60) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-4xl text-neutral-600 mb-4"></i>
                            <p class="text-neutral-400">Aucun rendez-vous trouvé</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Consultations Tab --}}
            @if($activeTab === 'consultations')
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white">Consultations</h3>
                        <button class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvelle consultation
                        </button>
                    </div>
                    
                    @if($consultations->count() > 0)
                        <div class="space-y-3">
                            @foreach($consultations as $consultation)
                                <div class="bg-neutral-800 rounded-lg p-4 border border-neutral-700">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-white font-medium">Consultation #{{ $consultation->consultation_number }}</h4>
                                            <p class="text-sm text-neutral-400">{{ \Carbon\Carbon::parse($consultation->consultation_date)->format('d/m/Y H:i') }}</p>
                                            @if($consultation->diagnosis)
                                                <p class="text-sm text-neutral-300 mt-1">{{ Str::limit($consultation->diagnosis, 100) }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $consultation->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($consultation->status) }}
                                            </span>
                                            <button class="text-neutral-400 hover:text-white">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-stethoscope text-4xl text-neutral-600 mb-4"></i>
                            <p class="text-neutral-400">Aucune consultation trouvée</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- actes Tab --}}
            @if($activeTab === 'actes')
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white">Actes</h3>
                        <button class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvel acte
                        </button>
                    </div>
                    @if($actes->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                            @foreach($actes->sortByDesc('acte_date') as $acte)
                                <div class="bg-gradient-to-br from-neutral-800 to-neutral-900 rounded-xl p-5 border border-neutral-700 shadow hover:shadow-lg transition-shadow flex flex-col h-full">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-teal-900/30 text-teal-300">
                                            <i class="fas fa-tooth mr-1"></i> Acte N° {{ $acte->acte_number }}
                                        </span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $acte->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' :
                                               ($acte->status === 'planned' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' :
                                               'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                                            {{ ucfirst($acte->status ?? 'en cours') }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col text-start">
                                        <span class="text-base font-semibold text-white">{{ $acte->medicalFile->patient->patient_full_name ?? '--' }}</span>
                                        <span class="text-xs text-blue-300">Dossier N° {{ $acte->medicalFile->file_number ?? '--' }}</span>
                                        <span class="text-xs text-neutral-400 flex items-center gap-1"><i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($acte->acte_date)->format('d/m/Y') }}</span>
                                        <span class="px-4 py-1 text-md font-bold rounded-full bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 hover:bg-violet-100 dark:hover:bg-violet-900/50 transition-colors inline-flex items-center mt-2">
                                            {{ number_format($acte->price ?? ($acte->acteServices->sum('price') ?? 0), 2) }} MAD
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-tooth text-4xl text-neutral-600 mb-4"></i>
                            <p class="text-neutral-400">Aucun acte trouvé</p>
                            <p class="text-sm text-neutral-500 mt-2">Commencez par créer votre premier acte pour ce patient.</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Paiements Tab (Placeholder) --}}
            @if($activeTab === 'paiements')
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white">Paiements</h3>
                        <button class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Nouveau paiement
                        </button>
                    </div>
                    
                    <div class="text-center py-8">
                        <i class="fas fa-credit-card text-4xl text-neutral-600 mb-4"></i>
                        <p class="text-neutral-400">Module de paiements à venir</p>
                        <p class="text-sm text-neutral-500 mt-2">Cette fonctionnalité sera disponible prochainement</p>
                    </div>
                </div>
            @endif


            {{-- Factures Tab (Placeholder) --}}
            @if($activeTab === 'factures')
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-white">Factures</h3>
                        <button class="inline-flex items-center px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvelle facture
                        </button>
                    </div>
                    <div class="text-center py-8">
                        <i class="fas fa-file-invoice text-4xl text-neutral-600 mb-4"></i>
                        <p class="text-neutral-400">Module de factures à venir</p>
                        <p class="text-sm text-neutral-500 mt-2">Cette fonctionnalité sera disponible prochainement</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div> 