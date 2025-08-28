<?php

namespace App\Livewire\Dashboard;

use App\Models\Invoice;
use App\Models\Patient;
use Livewire\Component;
use App\Models\Appointment;

class Dashboard extends Component
{
    public function render()
    {
        // Patients
        $totalPatients = Patient::count();
        $patientsGrowth = '+12% ce mois'; // Example, replace with real calculation

        // Appointments
        $appointmentsToday = Appointment::whereDate('appointment_date', now())->count();
        $appointmentsPending = Appointment::whereDate('appointment_date', now())->where('status', 'en attente')->count();
        $todayAppointments = Appointment::with(['medicalFile'])
            ->whereDate('appointment_date', now())
            ->orderBy('appointment_date')
            ->get();

        // Monthly Revenue
        $monthlyRevenue = Invoice::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        $revenueGrowth = '+8.2% vs mois dernier'; // Example, replace with real calculation

        // Unpaid Invoices
        $unpaidInvoices = Invoice::where('status', 'impayé')->sum('total_amount');
        $unpaidCount = Invoice::where('status', 'impayé')->count();

        // Revenue for last 6 months
        $monthlyRevenues = [];
        $months = [
            'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'
        ];
        $now = now();
        $totalRevenue6Months = 0;
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $amount = Invoice::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_amount');
            $monthlyRevenues[$months[$date->month - 1]] = [
                'amount' => $amount,
                'percent' => $monthlyRevenue > 0 ? round($amount / max($monthlyRevenue,1) * 100) : 0
            ];
            $totalRevenue6Months += $amount;
        }
        $revenue6MonthsGrowth = '+12.5% vs période précédente'; // Example, replace with real calculation

        return view('livewire.dashboard.dashboard', compact(
            'totalPatients',
            'patientsGrowth',
            'appointmentsToday',
            'appointmentsPending',
            'todayAppointments',
            'monthlyRevenue',
            'revenueGrowth',
            'unpaidInvoices',
            'unpaidCount',
            'monthlyRevenues',
            'totalRevenue6Months',
            'revenue6MonthsGrowth'
        ));
    }
}