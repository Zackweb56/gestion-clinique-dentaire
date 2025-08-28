<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use App\Models\MedicalFile;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\acte;
use Livewire\Component;
use Livewire\WithPagination;

class PatientDetails extends Component
{
    use WithPagination;

    public $patient;
    public $medicalFile;
    public $activeTab = 'appointments';
    
    protected $queryString = ['activeTab'];

    public function mount($patientId)
    {
        $this->patient = Patient::with(['medicalFile.appointments', 'medicalFile.consultations', 'medicalFile.actes'])
            ->findOrFail($patientId);
        
        $this->medicalFile = $this->patient->medicalFile;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getAppointmentsProperty()
    {
        return $this->medicalFile ? $this->medicalFile->appointments()->orderBy('appointment_date', 'desc')->get() : collect();
    }

    public function getConsultationsProperty()
    {
        return $this->medicalFile ? $this->medicalFile->consultations()->orderBy('consultation_date', 'desc')->get() : collect();
    }

    public function getactesProperty()
    {
        return $this->medicalFile ? $this->medicalFile->actes()->orderBy('acte_date', 'desc')->get() : collect();
    }

    public function getAge()
    {
        if ($this->patient->birth_date) {
            return \Carbon\Carbon::parse($this->patient->birth_date)->age;
        }
        return null;
    }

    public function getInsuranceTypeLabel($type)
    {
        $types = [
            'CNSS' => 'CNSS',
            'CNOPS' => 'CNOPS',
            'privé' => 'Assurance Privée',
            'aucun' => 'Aucune assurance'
        ];
        
        return $types[$type] ?? $type;
    }

    public function getStatusLabel($status)
    {
        $statuses = [
            'active' => 'Actif',
            'new' => 'Nouveau'
        ];
        
        return $statuses[$status] ?? $status;
    }

    public function getGenderLabel($gender)
    {
        $genders = [
            'H' => 'Homme',
            'F' => 'Femme'
        ];
        
        return $genders[$gender] ?? $gender;
    }

    public function render()
    {
        return view('livewire.patients.patient-details', [
            'patient' => $this->patient,
            'medicalFile' => $this->medicalFile,
            'appointments' => $this->appointments,
            'consultations' => $this->consultations,
            'actes' => $this->actes,
        ]);
    }
} 