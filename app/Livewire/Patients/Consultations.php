<?php

namespace App\Livewire\Patients;

use Flux\Flux;
use App\Models\Consultation;
use App\Models\MedicalFile;
use Livewire\Component;
use App\Helpers\FlashHelper;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Consultations extends Component
{
    use WithPagination;

    public $consultationId;
    public $medical_file_id;
    public $consultation_date;
    public $symptoms;
    public $diagnosis;
    public $acte_plan;
    public $notes;
    public $status;

    public $details_patient_name;
    public $details_patient_cin;
    public $details_consultation_date;
    public $details_symptoms;
    public $details_diagnosis;
    public $details_acte_plan;
    public $details_notes;
    public $details_status;
    public $details_responsable;
    public $details_consultation_number;

    public string $search = '';
    public string $statusFilter = '';
    public bool $isEdit = false;
    public bool $isDeleteConfirmationOpen = false;

    protected $rules = [
        'medical_file_id' => 'required|exists:medical_files,id',
        'consultation_date' => 'required|date',
        'symptoms' => 'nullable|string',
        'diagnosis' => 'nullable|string',
        'acte_plan' => 'nullable|string',
        'notes' => 'nullable|string',
        'status' => 'required|in:pending,in_progress,completed,cancelled',
    ];

    protected $messages = [
        'medical_file_id.required' => 'Le dossier médical est requis.',
        'medical_file_id.exists' => 'Le dossier médical sélectionné est invalide.',
        'consultation_date.required' => 'La date de consultation est requise.',
        'consultation_date.date' => 'La date de consultation doit être une date valide.',
        'symptoms.string' => 'Les symptômes doivent être une chaîne de caractères.',
        'diagnosis.string' => 'Le diagnostic doit être une chaîne de caractères.',
        'acte_plan.string' => 'Le plan de acte doit être une chaîne de caractères.',
        'notes.string' => 'Les notes doivent être une chaîne de caractères.',
        'status.required' => 'Le statut est requis.',
        'status.in' => 'Le statut sélectionné est invalide.',
    ];

    public function mount($medical_file_id = null)
    {
        if ($medical_file_id) {
            $this->medical_file_id = $medical_file_id;
        }
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $consultations = Consultation::with(['medicalFile.patient'])
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->whereHas('medicalFile.patient', function ($subQuery) {
                        $subQuery->where('patients.patient_full_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('symptoms', 'like', '%' . $this->search . '%')
                    ->orWhere('diagnosis', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('consultation_date', 'desc')
            ->paginate(10);

        $medical_files = MedicalFile::with('patient')->get();

        return view('livewire.patients.consultations', [
            'consultations' => $consultations,
            'medical_files' => $medical_files,
        ]);
    }

    public function edit($id)
    {
        $consultation = Consultation::findOrFail($id);
        
        $this->consultationId = $id;
        $this->medical_file_id = $consultation->medical_file_id;
        $this->consultation_date = $consultation->consultation_date->format('Y-m-d\TH:i');
        $this->symptoms = $consultation->symptoms;
        $this->diagnosis = $consultation->diagnosis;
        $this->acte_plan = $consultation->acte_plan;
        $this->notes = $consultation->notes;
        $this->status = $consultation->status;

        $this->isEdit = true;
        Flux::modal('edit-consultation')->show();
    }

    public function update()
    {
        $this->validate();

        $consultation = Consultation::findOrFail($this->consultationId);
        $consultation->update([
            'medical_file_id' => $this->medical_file_id,
            'consultation_date' => $this->consultation_date,
            'symptoms' => $this->symptoms,
            'diagnosis' => $this->diagnosis,
            'acte_plan' => $this->acte_plan,
            'notes' => $this->notes,
            'status' => $this->status,
            'responsable' => Auth::user()->name,
        ]);

        Flux::modal('edit-consultation')->close();
        $this->reset();

        FlashHelper::success('Consultation mise à jour avec succès');
    }

    public function confirmDelete($id)
    {
        $this->consultationId = $id;
        $this->isDeleteConfirmationOpen = true;
        Flux::modal('delete-confirmation-consultation')->show();
    }

    public function delete()
    {
        Consultation::findOrFail($this->consultationId)->delete();
        $this->isDeleteConfirmationOpen = false;
        Flux::modal('delete-confirmation-consultation')->close();
        FlashHelper::danger('Consultation supprimée avec succès');
    }

    /**
     * Show details modal for a consultation
     */
    public function showDetails($id)
    {
        $consultation = Consultation::with('medicalFile.patient')->findOrFail($id);
        $this->details_patient_name = $consultation->medicalFile->patient->patient_full_name ?? '';
        $this->details_patient_cin = $consultation->medicalFile->patient->cin ?? '';
        $this->details_consultation_date = $consultation->consultation_date ? $consultation->consultation_date->format('d/m/Y H:i') : '';
        $this->details_symptoms = $consultation->symptoms;
        $this->details_diagnosis = $consultation->diagnosis;
        $this->details_acte_plan = $consultation->acte_plan;
        $this->details_notes = $consultation->notes;
        $this->details_status = $consultation->status;
        $this->details_responsable = $consultation->responsable;
        $this->details_consultation_number = $consultation->consultation_number;
        Flux::modal('consultation-details')->show();
    }

    private function resetDetails()
    {
        $this->details_patient_name = null;
        $this->details_patient_cin = null;
        $this->details_consultation_date = null;
        $this->details_symptoms = null;
        $this->details_diagnosis = null;
        $this->details_acte_plan = null;
        $this->details_notes = null;
        $this->details_status = null;
        $this->details_responsable = null;
        $this->details_consultation_number = null;
    }
}