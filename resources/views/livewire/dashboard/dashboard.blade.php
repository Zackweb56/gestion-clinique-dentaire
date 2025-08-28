<div class="min-h-screen bg-neutral-900 rounded-xl shadow p-5 text-neutral-100">
    @include('components.flash_message')

    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Patients -->
        <div class="bg-neutral-800 rounded-xl p-6 flex flex-col items-start relative overflow-hidden">
            <div class="absolute -top-8 -right-8 z-10 select-none pointer-events-none">
                <span class="inline-block p-6 rounded-full bg-blue-700/40" style="transform: rotate(-30deg);">
                    <i class="fa fa-users text-white text-5xl opacity-60"></i>
                </span>
            </div>
            <div class="text-sm font-medium mb-2">Total Patients</div>
            <div class="text-3xl font-bold mb-1">{{ $totalPatients }}</div>
            <div class="text-xs text-green-400">{{ $patientsGrowth }}</div>
        </div>
        <!-- Today's Appointments -->
        <div class="bg-neutral-800 rounded-xl p-6 flex flex-col items-start relative overflow-hidden">
            <div class="absolute -top-8 -right-8 z-10 select-none pointer-events-none">
                <span class="inline-block p-6 rounded-full bg-green-700/40" style="transform: rotate(-30deg);">
                    <i class="fa fa-calendar-check text-white text-5xl opacity-60"></i>
                </span>
            </div>
            <div class="text-sm font-medium mb-2">RDV Aujourd'hui</div>
            <div class="text-3xl font-bold mb-1">{{ $appointmentsToday }}</div>
            <div class="text-xs text-yellow-400">{{ $appointmentsPending }} en attente</div>
        </div>
        <!-- Monthly Revenue -->
        <div class="bg-neutral-800 rounded-xl p-6 flex flex-col items-start relative overflow-hidden">
            <div class="absolute -top-8 -right-8 z-10 select-none pointer-events-none">
                <span class="inline-block p-6 rounded-full bg-cyan-700/40" style="transform: rotate(-30deg);">
                    <i class="fa fa-credit-card text-white text-5xl opacity-60"></i>
                </span>
            </div>
            <div class="text-sm font-medium mb-2">CA Mensuel</div>
            <div class="text-3xl font-bold mb-1">{{ number_format($monthlyRevenue, 0, ',', ' ') }} MAD</div>
            <div class="text-xs text-green-400">{{ $revenueGrowth }}</div>
        </div>
        <!-- Unpaid Invoices -->
        <div class="bg-neutral-800 rounded-xl p-6 flex flex-col items-start relative overflow-hidden">
            <div class="absolute -top-8 -right-8 z-10 select-none pointer-events-none">
                <span class="inline-block p-6 rounded-full bg-pink-700/40" style="transform: rotate(-30deg);">
                    <i class="fa fa-file-invoice text-white text-5xl opacity-60"></i>
                </span>
            </div>
            <div class="text-sm font-medium mb-2">Factures Impayées</div>
            <div class="text-3xl font-bold mb-1">{{ number_format($unpaidInvoices, 0, ',', ' ') }} MAD</div>
            <div class="text-xs text-red-400">{{ $unpaidCount }} factures en retard</div>
        </div>
    </div>

    <!-- Today's Appointments List -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-8">
        <div class="bg-neutral-800 rounded-xl p-6 shadow col-span-12 md:col-span-8">
            <div class="flex items-center mb-4">
                <i class="fa fa-clock text-blue-400 mr-2"></i>
                <span class="text-lg font-semibold">Rendez-vous du jour</span>
            </div>
            <ul>
                @forelse($todayAppointments as $appointment)
                    <li class="mb-3">
                        <div class="flex items-center justify-between bg-neutral-700/40 backdrop-blur-md rounded-xl px-4 py-3 shadow">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <span class="relative flex h-10 w-10 shrink-0 overflow-hidden rounded-lg">
                                    <span class="flex h-full w-full items-center justify-center rounded-full border border-neutral-200 dark:border-neutral-700 text-neutral-600 dark:text-neutral-400 bg-neutral-50 dark:bg-neutral-800">
                                        {{ $appointment->medicalFile->patient->initials() }}
                                    </span>
                                </span>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="truncate font-semibold">{{ $appointment->medicalFile->patient->patient_full_name }}</span>
                                        @if($appointment->type === 'consultation')
                                            <span class="px-2 py-0.5 rounded-full bg-blue-700/20 text-blue-500 text-xs font-semibold flex items-center gap-1 shadow-sm"><i class="fa fa-stethoscope"></i> Consultation</span>
                                        @elseif($appointment->type === 'acte')
                                            <span class="px-2 py-0.5 rounded-full bg-green-700/20 text-green-500 text-xs font-semibold flex items-center gap-1 shadow-sm"><i class="fa fa-tooth"></i> Acte</span>
                                        @elseif($appointment->type === 'suivi')
                                            <span class="px-2 py-0.5 rounded-full bg-yellow-700/20 text-yellow-500 text-xs font-semibold flex items-center gap-1 shadow-sm"><i class="fa fa-sync-alt"></i> Suivi</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('patients.details', $appointment->medicalFile->patient->id) }}" class="block">
                                        <div class="text-xs text-teal-400 hover:underline mt-1">Dossier N°: {{ $appointment->medicalFile->file_number }}</div>
                                    </a>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 min-w-[160px]">
                                @php
                                    // Time formatting
                                    $start = \Carbon\Carbon::parse($appointment->appointment_date);
                                    $end = $appointment->end_time ? \Carbon\Carbon::parse($appointment->end_time) : $start->copy()->addMinutes($appointment->duration ?? 30);
                                    $duration = $start->diffInMinutes($end);
                                    $timeString = $start->translatedFormat('H:i') . ' - ' . $end->translatedFormat('H:i') . ' (' . $duration . 'min)';

                                    // Status translation
                                    $statusTranslations = [
                                        'pending' => 'En attente',
                                        'confirmed' => 'Confirmé',
                                        'in_progress' => 'En cours',
                                        'done' => 'Terminé',
                                        'canceled' => 'Annulé',
                                        'no_show' => 'Absent',
                                    ];
                                    $statusStyles = [
                                        'pending' => 'bg-gray-500/80 text-white',
                                        'confirmed' => 'bg-blue-600/80 text-white',
                                        'in_progress' => 'bg-orange-400/80 text-white',
                                        'done' => 'bg-green-500/80 text-white',
                                        'canceled' => 'bg-red-500/80 text-white',
                                        'no_show' => 'bg-red-900/80 text-white',
                                    ];
                                    $statusIcons = [
                                        'pending' => 'fa-clock',
                                        'confirmed' => 'fa-check-circle',
                                        'in_progress' => 'fa-spinner',
                                        'done' => 'fa-check-double',
                                        'canceled' => 'fa-times-circle',
                                        'no_show' => 'fa-user-slash',
                                    ];
                                @endphp
                                <span class="text-sm font-bold text-neutral-200">
                                    {{ $timeString }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 shadow {{ $statusStyles[$appointment->status] ?? 'bg-gray-500/80 text-white' }}">
                                    <i class="fa {{ $statusIcons[$appointment->status] ?? 'fa-clock' }}"></i>
                                    {{ $statusTranslations[$appointment->status] ?? $appointment->status }}
                                </span>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="text-neutral-400 py-4 text-center">Aucun rendez-vous aujourd'hui.</li>
                @endforelse
            </ul>
        </div>

        <!-- Revenue Chart Placeholder -->
        <div class="bg-neutral-800 rounded-xl p-6 shadow col-span-12 md:col-span-4">
            <div class="flex items-center mb-4">
                <i class="fa fa-chart-line text-cyan-400 mr-2"></i>
                <span class="text-lg font-semibold">Chiffre d'Affaires <small class="text-neutral-400" style="font-size: 11px;">(6 derniers mois)</small></span>
            </div>
            <div class="mt-4">
                @foreach($monthlyRevenues as $month => $value)
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-neutral-300 font-medium">{{ $month }}</span>
                            <span class="text-neutral-100 font-semibold">{{ number_format($value['amount'], 0, ',', ' ') }} MAD</span>
                        </div>
                        <div class="w-full h-3 rounded bg-neutral-700 overflow-hidden">
                            <div class="h-3 rounded"
                                style="width: {{ $value['percent'] }}%; background: linear-gradient(90deg, #008081 0%, #57C3AD 100%); transition: width 0.5s;"></div>
                        </div>
                    </div>
                @endforeach
                <div class="mt-4 w-full bg-neutral-700/40 backdrop-blur-lg rounded-xl p-4">
                    <div class="flex align-items-center justify-between w-full mb-1">
                        <div class="text-neutral-400 text-sm mt-1">Total sur 6 mois</div>
                        <div class="text-md md:text-xl font-extrabold text-white">{{ number_format($totalRevenue6Months, 0, ',', ' ') }} MAD</div>
                    </div>
                    <div class="text-xs text-green-400">{{ $revenue6MonthsGrowth }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-neutral-800 rounded-2xl p-6 shadow flex flex-col items-start transition-all duration-200 hover:bg-blue-900/60 group">
            <div class="flex items-center mb-4">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-700/30 mr-3 group-hover:scale-110 transition-transform duration-200">
                    <i class="fa fa-user-plus text-blue-400 text-xl"></i>
                </span>
                <span class="font-semibold text-lg text-neutral-100">Nouveau Patient</span>
            </div>
            <div class="text-neutral-400 mb-4">Ajouter un patient</div>
            <a href="{{ route('patients') }}" wire:navigate class="px-4 py-2 rounded-lg bg-blue-700 text-blue-100 font-semibold hover:bg-blue-800 transition-all duration-200 w-full text-center">Gestion Patients</a>
        </div>
        <div class="bg-neutral-800 rounded-2xl p-6 shadow flex flex-col items-start transition-all duration-200 hover:bg-green-900/60 group">
            <div class="flex items-center mb-4">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-700/30 mr-3 group-hover:scale-110 transition-transform duration-200">
                    <i class="fa fa-calendar-plus text-green-400 text-xl"></i>
                </span>
                <span class="font-semibold text-lg text-neutral-100">Nouveau RDV</span>
            </div>
            <div class="text-neutral-400 mb-4">Planifier un rendez-vous</div>
            <a href="{{ route('appointments') }}" wire:navigate class="px-4 py-2 rounded-lg bg-green-700 text-green-100 font-semibold hover:bg-green-800 transition-all duration-200 w-full text-center">Gestion RDV</a>
        </div>
        <div class="bg-neutral-800 rounded-2xl p-6 shadow flex flex-col items-start transition-all duration-200 hover:bg-yellow-900/60 group">
            <div class="flex items-center mb-4">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-700/30 mr-3 group-hover:scale-110 transition-transform duration-200">
                    <i class="fa fa-chart-pie text-yellow-400 text-xl"></i>
                </span>
                <span class="font-semibold text-lg text-neutral-100">Statistiques</span>
            </div>
            <div class="text-neutral-400 mb-4">Voir les rapports</div>
            <a href="#" wire:navigate class="px-4 py-2 rounded-lg bg-yellow-700 text-yellow-100 font-semibold hover:bg-yellow-800 transition-all duration-200 w-full text-center">Statistiques</a>
        </div>
    </div>
</div>
