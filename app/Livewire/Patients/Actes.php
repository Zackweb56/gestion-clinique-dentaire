<?php

namespace App\Livewire\Patients;

use Flux\Flux;
use App\Models\Acte;
use App\Models\Service;
use Livewire\Component;
use App\Models\MedicalFile;
use App\Helpers\FlashHelper;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Actes extends Component
{
    use WithPagination;

    public $acteId;
    public $medical_file_id;
    public $service_id;
    public $acte_date;
    public $description;
    public $price;
    public $status = 'pending';
    public $tooth_number;
    public $payment_status = 'non payé';
    public $acte_services = [];

    // Modal states
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $editingActeId = null;

    public string $search = '';
    public bool $isEdit = false;
    public bool $isDeleteConfirmationOpen = false;


    public $perPage = 5;
    public $page = 1;

    // Filter for status
    public $statusActeFilter = '';

    protected $rules = [
        'medical_file_id' => 'required|exists:medical_files,id',
        'acte_date' => 'required|date',
        'acte_services' => 'required|array|min:1',
        'acte_services.*.service_id' => 'required|exists:services,id',
        'acte_services.*.price' => 'required|numeric|min:0',
        'acte_services.*.tooth_number' => 'nullable|string',
        'acte_services.*.libelle' => 'nullable|string',
        'acte_services.*.notes' => 'nullable|string',
    ];

    protected $messages = [
        'medical_file_id.required' => 'Le dossier médical est obligatoire.',
        'medical_file_id.exists' => 'Dossier médical invalide.',
        'acte_date.required' => 'La date du acte est obligatoire.',
        'acte_date.date' => 'Date du acte invalide.',
        'acte_services.required' => 'Au moins un service est requis.',
        'acte_services.array' => 'Les services doivent être un tableau.',
        'acte_services.min' => 'Au moins un service est requis.',
        'acte_services.*.service_id.required' => 'Le service est obligatoire.',
        'acte_services.*.service_id.exists' => 'Service invalide.',
        'acte_services.*.price.required' => 'Le prix est obligatoire.',
        'acte_services.*.price.numeric' => 'Le prix doit être un nombre.',
        'acte_services.*.price.min' => 'Le prix ne peut pas être négatif.',
    ];

    public function render()
    {
        $query = Acte::with(['medicalFile', 'acteServices.service'])
            ->orderByDesc('created_at');

        if ($this->statusActeFilter) {
            $query->where('status', $this->statusActeFilter);
        }

        if ($this->search) {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('acte_number', 'like', "%{$search}%") // Acte number
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('medicalFile', function ($q2) use ($search) {
                      $q2->where('id', 'like', "%{$search}%")
                          ->orWhere('file_number', 'like', "%{$search}%")
                          ->orWhereHas('patient', function ($q3) use ($search) {
                              $q3->where('patient_full_name', 'like', "%{$search}%");
                          });
                  });
            });
        }

        $allActes = $query->get();
        $displayedActes = $allActes->slice(0, $this->perPage * $this->page);
        $hasMore = $allActes->count() > $displayedActes->count();

        $medicalFiles = MedicalFile::orderBy('id', 'desc')->get();
        $services = Service::where('is_active', 1)->orderBy('name')->get();

        return view('livewire.patients.actes', [
            'actes' => $displayedActes,
            'medicalFiles' => $medicalFiles,
            'services' => $services,
            'hasMore' => $hasMore,
        ]);
    }

    public function create()
    {
        $this->resetExcept(['search']);
        $this->isEdit = false;
        Flux::modal('create-acte')->show();
    }

    public function edit($id)
    {
        $acte = Acte::with(['medicalFile', 'acteServices'])->findOrFail($id);
        
        $this->editingActeId = $id;
        $this->medical_file_id = $acte->medical_file_id;
        $this->acte_date = $acte->acte_date ? Carbon::parse($acte->acte_date)->format('Y-m-d\TH:i') : '';
        
        // Convert acte services to the format expected by the form
        $this->acte_services = $acte->acteServices->map(function($acteService) {
            return [
                'service_id' => $acteService->service_id,
                'price' => $acteService->price,
                'tooth_number' => $acteService->tooth_number,
                'libelle' => $acteService->libelle,
                'notes' => $acteService->notes,
            ];
        })->toArray();
        
        $this->showEditModal = true;
        $this->dispatch('servicesLoaded');
    }

    public function update()
    {
        $this->validate();

        $acte = Acte::findOrFail($this->editingActeId);
        
        $acte->update([
            'medical_file_id' => $this->medical_file_id,
            'acte_date' => $this->acte_date,
        ]);

        // Delete existing acte services
        $acte->acteServices()->delete();

        // Create new acte services
        foreach ($this->acte_services as $service) {
            $acte->acteServices()->create([
                'service_id' => $service['service_id'],
                'price' => $service['price'],
                'tooth_number' => (isset($service['tooth_number']) && $service['tooth_number'] !== '') ? $service['tooth_number'] : null,
                'libelle' => (isset($service['libelle']) && $service['libelle'] !== '') ? $service['libelle'] : null,
                'notes' => (isset($service['notes']) && $service['notes'] !== '') ? $service['notes'] : null,
            ]);
        }

        $this->resetEditForm();
        $this->showEditModal = false;
        
        FlashHelper::success('Acte mis à jour avec succès');
    }

    public function confirmDelete($id)
    {
        $this->editingActeId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $acte = Acte::findOrFail($this->editingActeId);
        
        // Delete associated acte services first
        $acte->acteServices()->delete();
        
        // Delete the acte
        $acte->delete();
        
        $this->editingActeId = null;
        $this->showDeleteModal = false;
        
        FlashHelper::success('Acte supprimé avec succès');
    }

    public function toggleStatus($id)
    {
        $acte = Acte::findOrFail($id);
        $acte->update([
            'status' => $acte->status === 'completed' ? 'pending' : 'completed',
        ]);
        FlashHelper::success('Statut du acte mis à jour avec succès');
    }

    public function updateStatus($id, $status)
    {
        $acte = Acte::findOrFail($id);
        $acte->status = $status;
        $acte->save();
        FlashHelper::success('Statut de l\'acte mis à jour avec succès');
        $this->resetPage();
    }

    public function loadMore()
    {
        $this->page++;
    }

    private function resetEditForm()
    {
        $this->editingActeId = null;
        $this->medical_file_id = '';
        $this->acte_date = '';
        $this->acte_services = [
            ['service_id' => '', 'price' => '', 'tooth_number' => '', 'libelle' => '', 'notes' => '']
        ];
    }
}