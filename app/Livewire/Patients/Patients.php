<?php

namespace App\Livewire\Patients;

use Flux\Flux;
use Carbon\Carbon;
use App\Models\Patient;
use Livewire\Component;
use App\Models\MedicalFile;
use App\Helpers\FlashHelper;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Patients extends Component
{
    use WithPagination;

    public $patientId;
    public $patient_full_name;
    public $cin;
    public $phone;
    public $email;
    public $gender = '';
    public $birth_date;
    public $address;
    public $insurance_type = '';

    // Medical file properties
    public $selectedPatientId;
    public $chronic_diseases;
    public $current_medications;
    public $allergies;
    public $notes;

    // is for data filer
    public bool $isEdit = false;
    public bool $isDeleteConfirmationOpen = false;
    public $genderFilter = '';
    public $search = '';
    public $perPage = 8;
    public $page = 1;

    // is for select patient card and display detail informations of patient
    public $selectedPatient = null;
    public $currentTab = 'informations';

    protected $rules = [
        'patient_full_name' => 'required|string|max:255',
        'cin' => 'nullable|string|max:20|unique:patients,cin',
        'phone' => ['required', 'regex:/^(\+212|0)[5-7][0-9]{8}$/'],
        'email' => 'nullable|email',
        'gender' => 'required|in:H,F',
        'birth_date' => 'required|date',
        'address' => 'nullable|string',
        'insurance_type' => 'required|in:CNSS,CNOPS,privé,aucun'
    ];

    protected $medicalFileRules = [
        'selectedPatientId' => 'required|exists:patients,id',
        'chronic_diseases' => 'nullable|string',
        'current_medications' => 'nullable|string',
        'allergies' => 'nullable|string',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'patient_full_name.required' => 'Le nom complet est obligatoire.',
        'phone.required' => 'Le téléphone est obligatoire.',
        'gender.required' => 'Le Genre est obligatoire.',
        'phone.regex' => 'Le numéro de téléphone doit être au format marocain valide (ex: +212XXXXXXXXX ou 0XXXXXXXXX).',
        'email.email' => 'Veuillez entrer une adresse email valide.',
        'cin.unique' => 'Ce CIN existe déjà pour un autre patient.',
        'birth_date.required' => 'La date de naissance est obligatoire.',
        'selectedPatientId.required' => 'Veuillez sélectionner un patient.',
        'selectedPatientId.exists' => 'Le patient sélectionné n\'existe pas.',
    ];

    // In your Patients component render method, update the query to include medical file relation:

    
    public function render()
    {
        $query = Patient::with('medicalFile')
            ->when($this->genderFilter, function ($query) {
                $query->where('gender', $this->genderFilter);
            })
            ->orderBy('created_at', 'desc');

        $patients = $query->get();

        if ($this->search) {
            $search = mb_strtolower(trim($this->search));
            $patients = $patients->filter(function ($patient) use ($search) {
                return (
                    (mb_stripos($patient->patient_full_name, $search) !== false) ||
                    (mb_stripos($patient->cin, $search) !== false) ||
                    (mb_stripos($patient->phone, $search) !== false) ||
                    (mb_stripos($patient->insurance_type, $search) !== false) ||
                    (mb_stripos($patient->email, $search) !== false)
                );
            });
        }

        $displayedPatients = $patients->slice(0, $this->perPage * $this->page);
        $hasMore = $patients->count() > $displayedPatients->count();

        return view('livewire.patients.patients', [
            'patients' => $displayedPatients,
            'hasMore' => $hasMore
        ]);
    }

    public function loadMore()
    {
        $this->page++;
    }

    // Add this method to handle patient selection
    public function selectPatient($patientId)
    {
        $this->selectedPatientId = $patientId;
        $this->selectedPatient = null; // Clear while loading
        
        try {
            $this->selectedPatient = Patient::with('medicalFile')->find($patientId);
            $this->currentTab = 'informations';
        } catch (\Exception $e) {
            FlashHelper::danger('Failed to load patient details');
        } 
    }
    
    public function create()
    {
        $this->reset();
        $this->isEdit = false;
        Flux::modal('create-patient')->show();
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        
        $this->patientId = $id;
        $this->patient_full_name = $patient->patient_full_name;
        $this->cin = $patient->cin;
        $this->phone = $patient->phone;
        $this->email = $patient->email;
        $this->gender = $patient->gender;
        $this->birth_date = $patient->birth_date ? Carbon::parse($patient->birth_date)->format('Y-m-d') : null;
        $this->address = $patient->address;
        $this->insurance_type = $patient->insurance_type;

        $this->isEdit = true;
        Flux::modal('edit-patient')->show();
    }

    public function store()
    {
        $this->validate();

        Patient::create([
            'patient_full_name' => $this->patient_full_name,
            'cin' => $this->cin,
            'phone' => $this->phone,
            'email' => $this->email,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date, // Do not encrypt
            'address' => $this->address,
            'insurance_type' => $this->insurance_type,
            'status' => 'new',
            'created_by' => Auth::user()->name,
        ]);

        Flux::modal('create-patient')->close();
        $this->reset();

        FlashHelper::success('Patient ajouté avec succès');
    }

    public function update()
    {
        $this->validate([
            'patient_full_name' => 'required|string|max:255',
            'cin' => 'nullable|string|max:20|unique:patients,cin,'.$this->patientId,
            'phone' => ['required', 'regex:/^(\+212|0)[5-7][0-9]{8}$/'],
            'email' => 'nullable|email',
            'gender' => 'required|in:H,F',
            'birth_date' => 'required|date',
            'address' => 'nullable|string',
            'insurance_type' => 'required|in:CNSS,CNOPS,privé,aucun'
        ]);

        $patient = Patient::findOrFail($this->patientId);
        $patient->update([
            'patient_full_name' => $this->patient_full_name,
            'cin' => $this->cin,
            'phone' => $this->phone,
            'email' => $this->email,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date, // Do not encrypt
            'address' => $this->address,
            'insurance_type' => $this->insurance_type,
            'updated_by' => Auth::user()->name,
        ]);

        Flux::modal('edit-patient')->close();
        $this->reset();

        FlashHelper::success('Patient mis à jour avec succès');
    }

    public function confirmDelete($id)
    {
        $this->patientId = $id;
        $this->isDeleteConfirmationOpen = true;
        Flux::modal('delete-confirmation-patient')->show();
    }

    public function delete()
    {
        Patient::findOrFail($this->patientId)->delete();
        $this->isDeleteConfirmationOpen = false;
        Flux::modal('delete-confirmation-patient')->close();
        FlashHelper::success('Patient supprimé avec succès');

        // Reset selection after deletion
        $this->selectedPatientId = null;
        $this->selectedPatient = null;
    }

    // Medical File Methods
    public function confirmCreateMedicalFile($patientId)
    {
        $patient = Patient::with('medicalFile')->find($patientId);
        
        if ($patient->medicalFile) {
            FlashHelper::warning('Ce patient a déjà un dossier médical.');
            return;
        }

        $this->selectedPatientId = $patientId;
        $this->selectedPatient = $patient;
        $this->resetMedicalFileForm();
        Flux::modal('confirm-create-medical-file')->show();
    }

    public function createMedicalFileForPatient()
    {
        if (!$this->selectedPatient) {
            FlashHelper::warning('Veuillez sélectionner un patient.');
            return;
        }

        if ($this->selectedPatient->medicalFile) {
            FlashHelper::warning('Ce patient a déjà un dossier médical.');
            return;
        }
        
        // Close confirmation modal and open create form
        Flux::modal('confirm-create-medical-file')->close();
        Flux::modal('create-medical-file-modal-patient')->show();
    }

    public function storeMedicalFile()
    {
        $this->validate($this->medicalFileRules, [
            'selectedPatientId.required' => 'Veuillez sélectionner un patient.',
            'selectedPatientId.exists' => 'Le patient sélectionné n\'existe pas.',
        ]);

        // Check again if patient already has a medical file
        $existingMedicalFile = MedicalFile::where('patient_id', $this->selectedPatientId)->first();
        
        if ($existingMedicalFile) {
            FlashHelper::warning('Ce patient a déjà un dossier médical.');
            return;
        }

        MedicalFile::create([
            'patient_id' => $this->selectedPatientId,
            'chronic_diseases' => $this->chronic_diseases,
            'current_medications' => $this->current_medications,
            'allergies' => $this->allergies,
            'notes' => $this->notes,
            'created_by' => Auth::user()->name,
        ]);

        // Update patient status to 'active'
        $patient = Patient::find($this->selectedPatientId);
        if ($patient) {
            $patient->status = 'active';
            $patient->save();
        }

        Flux::modal('create-medical-file-from-patient')->close();
        Flux::modal('confirm-create-medical-file')->close();

        $this->resetMedicalFileForm();

        FlashHelper::success('Dossier médical créé avec succès pour le patient sélectionné');
    }

    private function resetMedicalFileForm()
    {
        $this->chronic_diseases = '';
        $this->current_medications = '';
        $this->allergies = '';
        $this->notes = '';
    }

    public function getSelectedPatientProperty()
    {
        if ($this->selectedPatientId) {
            return Patient::find($this->selectedPatientId);
        }
        return null;
    }
}