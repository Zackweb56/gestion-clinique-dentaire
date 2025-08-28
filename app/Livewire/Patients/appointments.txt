<?php

namespace App\Livewire\Patients;

use Flux\Flux;
use App\Models\Acte;
use App\Models\Invoice;
use App\Models\Service;
use Livewire\Component;
use App\Models\Appointment;
use App\Models\MedicalFile;
use App\Helpers\FlashHelper;
use App\Models\Consultation;
use App\Models\DentalProfile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Appointments extends Component
{
    public $appointmentId;
    public $medical_file_id;
    public $type;
    public $status;
    public $appointment_date;
    public $duration_minutes;
    public $notes;

    public $details_patient_name;
    public $details_appointment_date;
    public $details_type;
    public $details_status;
    public $details_notes;
    public $details_duration_minutes;
    public $details_responsable;

    public $appointments = [];

    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public bool $showCreateConsultationModal = false;
    public bool $showCreateActeModal = false;
    
    public $consultationAppointmentId;
    public $consultation_patient_id;
    public $consultation_date;
    public $consultation_notes;
    public $consultation_symptoms;
    public $consultation_diagnosis;
    public $consultation_acte_plan;
    
    // Acte creation properties
    public $acteAppointmentId;
    public $acte_patient_id;
    public $acte_date;
    public $acte_service_id;
    public $acte_description;
    public $acte_price;
    public $acte_tooth_number;
    public $acte_services = [];

    protected $rules = [
        'medical_file_id' => 'required|exists:medical_files,id',
        'type' => 'required|in:consultation,suivi,acte',
        'status' => 'required|in:pending,confirmed,in_progress,done,canceled,no_show',
        'appointment_date' => 'required|date',
        'duration_minutes' => 'nullable|integer|min:1',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'medical_file_id.required' => 'Le dossier médical est requis.',
        'medical_file_id.exists' => 'Le dossier médical sélectionné est invalide.',
        'type.required' => 'Le type de rendez-vous est requis.',
        'type.in' => 'Le type de rendez-vous sélectionné est invalide.',
        'status.required' => 'Le statut est requis.',
        'status.in' => 'Le statut sélectionné est invalide.',
        'appointment_date.required' => 'La date du rendez-vous est requise.',
        'appointment_date.date' => 'La date du rendez-vous doit être une date valide.',
        'duration_minutes.integer' => 'La durée doit être un nombre entier.',
        'duration_minutes.min' => 'La durée doit être au moins 1 minute.',
        'notes.string' => 'Les notes doivent être une chaîne de caractères.',
    ];

    public function mount()
    {
        $this->loadAppointments();
    }

    public function loadAppointments()
    {
        $statusColors = [
            'pending' => '#6b7280',      // gray-500
            'confirmed' => '#2563eb',    // blue-600
            'in_progress' => '#f59e42',  // orange-400
            'done' => '#22c55e',         // green-500
            'canceled' => '#ef4444',     // red-500
            'no_show' => '#991b1b',      // red-900
        ];

        $this->appointments = Appointment::with('medicalFile.patient')
            ->get()
            ->map(function ($appointment) use ($statusColors) {
                $patientName = $appointment->medicalFile && $appointment->medicalFile->patient ? $appointment->medicalFile->patient->patient_full_name : '';
                $patientEmail = $appointment->medicalFile && $appointment->medicalFile->patient ? $appointment->medicalFile->patient->email : '';
                $patientPhone = $appointment->medicalFile && $appointment->medicalFile->patient ? $appointment->medicalFile->patient->phone : '';
                $duration = (isset($appointment->duration_minutes) && is_numeric($appointment->duration_minutes) && $appointment->duration_minutes !== '') ? (int)$appointment->duration_minutes : 30;
                
                // Ensure the appointment date is in local timezone for display
                $appointmentDate = $appointment->appointment_date->setTimezone('Africa/Casablanca');
                
                return [
                    'id' => $appointment->id,
                    'title' => trim($patientName . ' - ' . __($appointment->type)), // patient name and type
                    'start' => $appointmentDate->toISOString(),
                    'end' => $appointmentDate->copy()->addMinutes((int)$duration)->toISOString(),
                    'status' => $appointment->status,
                    'notes' => $appointment->notes,
                    'backgroundColor' => $statusColors[$appointment->status] ?? '#6b7280',
                    'textColor' => '#fff',
                    'patient_name' => $patientName,
                    'patient_email' => $patientEmail,
                    'patient_phone' => $patientPhone,
                    'type' => $appointment->type,
                    'duration_minutes' => $duration,
                    'created_by' => $appointment->created_by,
                    'medical_file_file_number' => $appointment->medicalFile ? $appointment->medicalFile->file_number : '',
                    'medical_file_patient_id' => $appointment->medicalFile && $appointment->medicalFile->patient ? $appointment->medicalFile->patient->id : null,
                    'has_consultation' => $appointment->consultations()->exists(),
                    'has_acte' => $appointment->actes()->exists(),
                ];
            });
        $this->dispatch('refreshCalendar', appointments: $this->appointments);
    }

    public function resetForm()
    {
        $this->reset(['appointmentId', 'medical_file_id', 'type', 'status', 'appointment_date', 'duration_minutes', 'notes']);
    }

    public function prepareCreate()
    {
        $this->resetForm();
        $this->showCreateModal = true;
        $this->showCreateConsultationModal = false;
        $this->showCreateActeModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;

        $this->appointment_date = now()->setTimezone('Africa/Casablanca')->format('Y-m-d\TH:i');
        $this->dispatch('close-appointment-popup');
        $this->loadAppointments();

    }

    public function store()
    {
        $this->validate();

        // Parse the appointment date in the local timezone (Morocco)
        $appointmentDate = Carbon::parse($this->appointment_date, 'Africa/Casablanca');
        
        // Check for overlapping appointments
        $start = $appointmentDate->format('Y-m-d H:i:s');
        $duration = (int)($this->duration_minutes ?? 30);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $start)->addMinutes($duration)->format('Y-m-d H:i:s');

        $overlap = Appointment::whereRaw('appointment_date < ?', [$end])
            ->whereRaw('DATE_ADD(appointment_date, INTERVAL IFNULL(duration_minutes, 30) MINUTE) > ?', [$start])
            ->exists();
        if ($overlap) {
            $this->addError('appointment_date', 'Un rendez-vous existe déjà à ce créneau pour ce dossier médical.');
            return;
        }

        Appointment::create([
            'medical_file_id' => $this->medical_file_id,
            'type' => $this->type,
            'status' => $this->status,
            'appointment_date' => $appointmentDate,
            'duration_minutes' => $this->duration_minutes,
            'notes' => $this->notes,
            'created_by' => Auth::user()->name,
            'updated_by' => Auth::user()->name,
        ]);
        
        $this->showCreateModal = false;
        $this->resetForm();
        $this->loadAppointments();
        FlashHelper::success('Rendez-vous créé avec succès');
    }

    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);
        $this->appointmentId = $appointment->id;
        $this->medical_file_id = $appointment->medical_file_id;
        $this->type = $appointment->type;
        $this->status = $appointment->status;
        $this->appointment_date = $appointment->appointment_date->setTimezone('Africa/Casablanca')->format('Y-m-d\\TH:i');
        $this->duration_minutes = $appointment->duration_minutes;
        $this->notes = $appointment->notes;
        $this->loadAppointments();
        $this->dispatch('close-appointment-popup');

        $this->showCreateModal = false;
        $this->showEditModal = true;
        $this->showDeleteModal = false;
    }

    public function update()
    {
        $this->validate();

        $appointment = Appointment::findOrFail($this->appointmentId);

        // Parse the appointment date in the local timezone (Morocco)
        $appointmentDate = Carbon::parse($this->appointment_date, 'Africa/Casablanca');

        // Check for overlapping appointments (exclude current)
        $start = $appointmentDate->format('Y-m-d H:i:s');
        $duration = (int)($this->duration_minutes ?? 30);
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $start)->addMinutes($duration)->format('Y-m-d H:i:s');
        $overlap = Appointment::where('id', '!=', $this->appointmentId)
            ->whereRaw('appointment_date < ?', [$end])
            ->whereRaw('DATE_ADD(appointment_date, INTERVAL IFNULL(duration_minutes, 30) MINUTE) > ?', [$start])
            ->exists();

        if ($overlap) {
            $this->addError('appointment_date', 'Un rendez-vous existe déjà à ce créneau pour ce dossier médical.');
            return;
        }

        $appointment->update([
            'type' => $this->type,
            'status' => $this->status,
            'appointment_date' => $appointmentDate,
            'duration_minutes' => $this->duration_minutes,
            'notes' => $this->notes,
            'updated_by' => Auth::user()->name,
        ]);

        $this->showEditModal = false;
        $this->resetForm();
        $this->loadAppointments();
        FlashHelper::success('Rendez-vous mis à jour avec succès');
    }

    public function confirmDelete($id)
    {
        $this->appointmentId = $id;
        $this->loadAppointments();
        $this->dispatch('close-appointment-popup');

        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = true;

    }

    public function delete()
    {
        Appointment::findOrFail($this->appointmentId)->delete();
        $this->showDeleteModal = false;
        $this->resetForm();
        $this->loadAppointments();
        FlashHelper::success('Rendez-vous supprimé avec succès');
    }

    public function quickUpdateStatus($appointmentId, $newStatus)
    {
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            return $this->dispatchBrowserEvent('notify', [
                'type' => 'error',
                'message' => 'Rendez-vous introuvable.'
            ]);
        }

        $appointment->status = $newStatus;
        $appointment->save();

        $this->showCreateConsultationModal = false;
        $this->showCreateActeModal = false;
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        
        // show a notification
        FlashHelper::success('Statut du rendez-vous mis à jour avec succès.');
        
        // refresh the calendar
        $this->loadAppointments();
    }

    public function openCreateConsultationModal($appointmentId)
    {
        $appointment = Appointment::with('medicalFile.patient')->findOrFail($appointmentId);
        $this->consultationAppointmentId = $appointment->id;
        $this->consultation_patient_id = $appointment->medicalFile ? $appointment->medicalFile->patient->id : null;
        $this->consultation_date = $appointment->appointment_date->setTimezone('Africa/Casablanca')->format('Y-m-d\TH:i');
        $this->consultation_notes = $appointment->notes;
        $this->consultation_symptoms = '';
        $this->consultation_diagnosis = '';
        $this->consultation_acte_plan = '';

        $this->showCreateConsultationModal = true;
        $this->showCreateModal = false;
        $this->showCreateActeModal = false;

        $this->dispatch('close-appointment-popup');
        $this->loadAppointments();
    }

    public function createConsultation()
    {
        // Validate only the editable fields
        $this->validate([
            'consultation_symptoms' => 'nullable|string',
            'consultation_diagnosis' => 'nullable|string',
            'consultation_acte_plan' => 'nullable|string',
            'consultation_notes' => 'nullable|string',
        ]);

        Consultation::create([
            'medical_file_id' => $this->consultation_patient_id ? MedicalFile::where('patient_id', $this->consultation_patient_id)->value('id') : null,
            'appointment_id' => $this->consultationAppointmentId,
            'consultation_date' => $this->consultation_date,
            'symptoms' => $this->consultation_symptoms,
            'diagnosis' => $this->consultation_diagnosis,
            'acte_plan' => $this->consultation_acte_plan,
            'notes' => $this->consultation_notes,
            'status' => 'pending',
            'responsable' => Auth::user()->name,
        ]);

        $this->showCreateModal = false;
        $this->showCreateActeModal = false;
        $this->showCreateConsultationModal = false;

        $this->consultationAppointmentId = null;
        $this->consultation_patient_id = null;
        $this->consultation_date = null;
        $this->consultation_notes = null;
        $this->consultation_symptoms = null;
        $this->consultation_diagnosis = null;
        $this->consultation_acte_plan = null;

        FlashHelper::success('Consultation créée avec succès');
        $this->dispatch('consultationCreated');
        $this->loadAppointments();

    }

    public function openCreateActeModal($appointmentId)
    {
        $this->resetCreateActeModal();
        $appointment = Appointment::with('medicalFile.patient')->findOrFail($appointmentId);
        $this->acteAppointmentId = $appointment->id;
        $this->acte_patient_id = $appointment->medicalFile ? $appointment->medicalFile->patient->id : null;
        $this->acte_date = $appointment->appointment_date->setTimezone('Africa/Casablanca')->format('Y-m-d\TH:i');
        $this->showCreateModal = false;
        $this->showCreateConsultationModal = false;
        $this->showCreateActeModal = true;
        $this->dispatch('reset-acte-services');
        $this->dispatch('close-appointment-popup');
        $this->loadAppointments();
    }

    public function createActe()
    {
        $this->validate([
            'acte_services' => 'required|array|min:1',
            'acte_services.*.service_id' => 'required|exists:services,id',
            'acte_services.*.price' => 'required|numeric|min:0',
            'acte_services.*.tooth_number' => 'nullable|string',
            'acte_services.*.libelle' => 'nullable|string',
            'acte_services.*.notes' => 'nullable|string',
            'acte_date' => 'required|date',
        ]);

        $acte = Acte::create([
            'medical_file_id' => $this->acte_patient_id ? MedicalFile::where('patient_id', $this->acte_patient_id)->value('id') : null,
            'appointment_id' => $this->acteAppointmentId,
            'acte_date' => $this->acte_date,
            'status' => 'planned',
            'payment_status' => 'non payé',
        ]);

        foreach ($this->acte_services as $service) {
            $acte->acteServices()->create([
                'service_id' => $service['service_id'],
                'price' => $service['price'],
                'tooth_number' => (isset($service['tooth_number']) && $service['tooth_number'] !== '') ? $service['tooth_number'] : null,
                'libelle' => (isset($service['libelle']) && $service['libelle'] !== '') ? $service['libelle'] : null,
                'notes' => (isset($service['notes']) && $service['notes'] !== '') ? $service['notes'] : null,
            ]);
        }

        // --- INVOICE CREATION LOGIC ---
        $baseAmount = $acte->acteServices->sum('price');
        $dentalProfile = DentalProfile::first();
        $tvaRate = null;
        $tvaAmount = null;
        $totalAmount = $baseAmount;
        if ($dentalProfile && $dentalProfile->TVA !== null && $dentalProfile->TVA !== '') {
            $tvaRate = is_numeric($dentalProfile->TVA) ? ($dentalProfile->TVA / 100) : 0;
            $tvaAmount = $baseAmount * $tvaRate;
            $totalAmount = $baseAmount + $tvaAmount;
        }
        Invoice::create([
            'acte_id' => $acte->id,
            'base_amount' => $baseAmount,
            'tva_rate' => $tvaRate ?? 0,
            'tva_amount' => $tvaAmount ?? 0,
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'status' => 'impayé',
        ]);
        // --- END INVOICE CREATION ---

        $this->showCreateModal = false;
        $this->showCreateConsultationModal = false;
        $this->showCreateActeModal = false;
        $this->resetCreateActeModal();
        FlashHelper::success('Acte créé avec succès');
        $this->dispatch('acteCreated');
        $this->loadAppointments();
    }

    public function render()
    {
        return view('livewire.patients.appointments', [
            'appointments' => $this->appointments,
            'medicalFiles' => MedicalFile::with('patient')->get(),
            'services' => Service::where('is_active', 1)->orderBy('name')->get(),
            'details_patient_name' => $this->details_patient_name,
            'details_appointment_date' => $this->details_appointment_date,
            'details_type' => $this->details_type,
            'details_status' => $this->details_status,
            'details_notes' => $this->details_notes,
            'details_duration_minutes' => $this->details_duration_minutes,
            'details_responsable' => $this->details_responsable,
        ]);
    }

    private function resetCreateActeModal()
    {
        $this->acteAppointmentId = null;
        $this->acte_patient_id = null;
        $this->acte_date = null;
        $this->acte_services = [
            ['service_id' => '', 'price' => '', 'tooth_number' => '', 'libelle' => '', 'notes' => '']
        ];
    }
}