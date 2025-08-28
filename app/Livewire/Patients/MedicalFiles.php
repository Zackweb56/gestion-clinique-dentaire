<?php

namespace App\Livewire\Patients;

use Flux\Flux;
use App\Models\Patient;
use Livewire\Component;
use App\Models\MedicalFile;
use App\Helpers\FlashHelper;
use Illuminate\Support\Facades\Auth;

class MedicalFiles extends Component
{
    public $medicalFileId;
    public $patient_id;
    public $chronic_diseases;
    public $current_medications;
    public $allergies;
    public $notes;

    public $new_patient_full_name;
    public $new_cin;
    public $new_phone;
    public $new_email;
    public $new_gender = 'H';
    public $new_birth_date;
    public $new_address;

    public string $search = '';
    public bool $isEdit = false;
    public bool $isDeleteConfirmationOpen = false;

    public $details_file_number;
    public $details_patient_name;
    public $details_patient_cin;
    public $details_patient_phone;
    public $details_patient_email;
    public $details_patient_gender;
    public $details_patient_birth_date;
    public $details_patient_address;
    public $details_chronic_diseases;
    public $details_current_medications;
    public $details_allergies;
    public $details_notes;
    public $details_created_by;
    public $details_created_at;
    public $details_updated_by;
    public $details_updated_at;

    protected $rules = [
        'patient_id' => 'required|exists:patients,id',
        'chronic_diseases' => 'nullable|string',
        'current_medications' => 'nullable|string',
        'allergies' => 'nullable|string',
        'notes' => 'nullable|string',
    ];

    protected $newPatientRules = [
        'new_patient_full_name' => 'required|string|max:255',
        'new_cin' => 'nullable|string|max:20|unique:patients,cin',
        'new_phone' => ['required', 'regex:/^(\+212|0)[5-7][0-9]{8}$/'],
        'new_email' => 'nullable|email',
        'new_gender' => 'required|in:H,F',
        'new_birth_date' => 'required|date',
        'new_address' => 'nullable|string',
    ];

    protected $messages = [
        'new_patient_full_name.required' => 'Le nom complet est obligatoire.',
        'new_phone.required' => 'Le téléphone est obligatoire.',
        'new_gender.required' => 'Le Genre est obligatoire.',
        'new_birth_date.required' => 'La date de naissance est obligatoire.',
        'new_phone.regex' => 'Le numéro de téléphone doit être au format marocain valide (ex: +212XXXXXXXXX ou 0XXXXXXXXX).',
        'new_email.email' => 'Veuillez entrer une adresse email valide.',
        'new_cin.unique' => 'Ce CIN existe déjà pour un autre patient.',
    ];

    public function render()
    {
        $medicalFiles = MedicalFile::with('patient')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('patient', function ($q2) {
                        $q2->where('patient_full_name', 'like', '%'.$this->search.'%')
                          ->orWhere('cin', 'like', '%'.$this->search.'%')
                          ->orWhere('phone', 'like', '%'.$this->search.'%');
                    })
                    ->orWhere('file_number', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $patients = Patient::orderBy('patient_full_name')->get();

        return view('livewire.patients.medical-files', [
            'medicalFiles' => $medicalFiles,
            'patients' => $patients,
        ]);
    }

    public function getNewPatients()
    {
        return Patient::where('status', 'new')->orderBy('patient_full_name')->get();
    }

    public function create()
    {
        // Only reset if we don't have a patient_id (like when coming from patient creation)
        if (!$this->patient_id) {
            $this->resetMedicalFileForm();
        }
        $this->isEdit = false;
        Flux::modal('create-medical-file')->show();
    }

    public function edit($id)
    {
        $medicalFile = MedicalFile::findOrFail($id);
        
        $this->medicalFileId = $id;
        $this->patient_id = $medicalFile->patient_id;
        $this->chronic_diseases = $medicalFile->chronic_diseases;
        $this->current_medications = $medicalFile->current_medications;
        $this->allergies = $medicalFile->allergies;
        $this->notes = $medicalFile->notes;

        $this->isEdit = true;
        Flux::modal('edit-medical-file')->show();

    }

    public function store()
    {
        $this->validate();

        // Prevent duplicate medical files for the same patient
        if (MedicalFile::where('patient_id', $this->patient_id)->exists()) {
            FlashHelper::danger('Ce patient possède déjà un dossier médical.');
            return;
        }

        MedicalFile::create([
            'patient_id' => $this->patient_id,
            'chronic_diseases' => $this->chronic_diseases,
            'current_medications' => $this->current_medications,
            'allergies' => $this->allergies,
            'notes' => $this->notes,
            'created_by' => Auth::user()->name,
        ]);

        // Update patient status to 'active'
        $patient = Patient::find($this->patient_id);
        if ($patient) {
            $patient->status = 'active';
            $patient->save();
        }

        Flux::modal('create-medical-file')->close();

        $this->resetMedicalFileForm();

        FlashHelper::success('Dossier médical créé avec succès');
    }

    public function update()
    {
        $this->validate();

        $medicalFile = MedicalFile::findOrFail($this->medicalFileId);
        $oldPatientId = $medicalFile->patient_id; // Store old patient ID

        $medicalFile->update([
            'patient_id' => $this->patient_id,
            'chronic_diseases' => $this->chronic_diseases,
            'current_medications' => $this->current_medications,
            'allergies' => $this->allergies,
            'notes' => $this->notes,
            'updated_by' => Auth::user()->name,
        ]);

        // Update new patient's status to 'active'
        $newPatient = Patient::find($this->patient_id);
        if ($newPatient) {
            $newPatient->status = 'active';
            $newPatient->save();
        }

        // If the patient was changed, update the old patient's status to 'new' (if they have no other medical file)
        if ($oldPatientId != $this->patient_id) {
            $oldPatient = Patient::find($oldPatientId);
            if ($oldPatient && !MedicalFile::where('patient_id', $oldPatientId)->exists()) {
                $oldPatient->status = 'new';
                $oldPatient->save();
            }
        }

        Flux::modal('edit-medical-file')->close();

        $this->reset();

        FlashHelper::success('Dossier médical mis à jour avec succès');
    }

    public function confirmDelete($id)
    {
        $this->medicalFileId = $id;
        $this->isDeleteConfirmationOpen = true;
        Flux::modal('delete-confirmation-medical-file')->show();
    }

    public function delete()
    {
        try {
            $medicalFile = MedicalFile::findOrFail($this->medicalFileId);
            $patientId = $medicalFile->patient_id;
            
            // Delete all related consultations before deleting the medical file
            $medicalFile->consultations()->delete();
            $medicalFile->delete();
            $this->isDeleteConfirmationOpen = false;

            // Update patient status to 'new'
            $patient = Patient::find($patientId);
            if ($patient) {
                $patient->status = 'new';
                $patient->save();
            }
            
            Flux::modal('delete-confirmation-medical-file')->close();

            FlashHelper::success('Dossier médical supprimé avec succès');
        } catch (\Exception $e) {
            FlashHelper::danger('Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // public function openCreatePatientModalFromMedicalFile()
    // {
    //     $this->resetNewPatientForm();
    //     Flux::modal('create-patient-from-medical-file')->show();
    // }

    // public function storePatientFromMedicalFile()
    // {
    //     $this->validate($this->newPatientRules, $this->messages);
    
    //     $patient = Patient::create([
    //         'patient_full_name' => $this->new_patient_full_name,
    //         'cin' => $this->new_cin ?: null,
    //         'phone' => $this->new_phone,
    //         'email' => $this->new_email ?: null,
    //         'gender' => $this->new_gender,
    //         'birth_date' => $this->new_birth_date,
    //         'address' => $this->new_address ?: null,
    //         'status' => 'new',
    //         'created_by' => Auth::user()->name,
    //     ]);
    
    //     $this->patient_id = $patient->id; // Select the new patient
    //     $this->resetNewPatientForm();
        
    //     // Only close the patient modal, not the medical file modal
    //     $this->dispatch('close-modal', name: 'create-patient-from-medical-file');
        
    //     FlashHelper::success('Patient ajouté avec succès et sélectionné.');
    // }

    // private function resetNewPatientForm()
    // {
    //     $this->new_patient_full_name = '';
    //     $this->new_cin = '';
    //     $this->new_phone = '';
    //     $this->new_email = '';
    //     $this->new_gender = 'H';
    //     $this->new_birth_date = '';
    //     $this->new_address = '';
    // }

    /**
     * Reset only the medical file form fields
     */
    private function resetMedicalFileForm()
    {
        $this->medicalFileId = null;
        // Don't reset patient_id if it's set
        if (!$this->isEdit) {
            $this->patient_id = null;
        }
        $this->chronic_diseases = null;
        $this->current_medications = null;
        $this->allergies = null;
        $this->notes = null;
        $this->isEdit = false;
        $this->isDeleteConfirmationOpen = false;
        $this->resetDetails();
    }
    
    /**
     * Reset details properties
     */
    private function resetDetails()
    {
        $this->details_file_number = null;
        $this->details_patient_name = null;
        $this->details_patient_cin = null;
        $this->details_patient_phone = null;
        $this->details_patient_email = null;
        $this->details_patient_gender = null;
        $this->details_patient_birth_date = null;
        $this->details_patient_address = null;
        $this->details_chronic_diseases = null;
        $this->details_current_medications = null;
        $this->details_allergies = null;
        $this->details_notes = null;
        $this->details_created_by = null;
        $this->details_created_at = null;
        $this->details_updated_by = null;
        $this->details_updated_at = null;
    }

    /**
     * Show details modal for a medical file
     */
    public function showDetails($id)
    {
        $file = MedicalFile::with('patient')->findOrFail($id);
        $this->details_file_number = $file->file_number;
        $this->details_patient_name = $file->patient->patient_full_name ?? '';
        $this->details_patient_cin = $file->patient->cin ?? '';
        $this->details_patient_phone = $file->patient->phone ?? '';
        $this->details_patient_email = $file->patient->email ?? '';
        $this->details_patient_gender = $file->patient->gender ?? '';
        $this->details_patient_birth_date = $file->patient->birth_date ?? '';
        $this->details_patient_address = $file->patient->address ?? '';
        $this->details_chronic_diseases = $file->chronic_diseases;
        $this->details_current_medications = $file->current_medications;
        $this->details_allergies = $file->allergies;
        $this->details_notes = $file->notes;
        $this->details_created_by = $file->created_by;
        $this->details_created_at = $file->created_at;
        $this->details_updated_by = $file->updated_by ?? '';
        $this->details_updated_at = $file->updated_at;
        Flux::modal('medical-file-details')->show();
    }
}