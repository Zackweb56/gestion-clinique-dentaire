<?php

namespace App\Livewire\Services;

use Flux\Flux;
use App\Models\Service;
use Livewire\Component;
use App\Helpers\FlashHelper;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Services extends Component
{
    use WithPagination;

    public $serviceId;
    public $name;
    public $description;
    public $price;
    public $duration_minutes;
    public $notes;

    public string $search = '';
    public bool $isEdit = false;
    public bool $isDeleteConfirmationOpen = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'duration_minutes' => 'required|integer|min:1|max:1440',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'name.required' => 'Le nom du service est obligatoire.',
        'name.string' => 'Le nom du service doit être une chaîne de caractères.',
        'name.max' => 'Le nom du service ne peut pas dépasser 255 caractères.',
        'description.string' => 'La description doit être une chaîne de caractères.',
        'price.required' => 'Le prix est obligatoire.',
        'price.numeric' => 'Le prix doit être un nombre.',
        'price.min' => 'Le prix ne peut pas être négatif.',
        'duration_minutes.required' => 'La durée est obligatoire.',
        'duration_minutes.integer' => 'La durée doit être un nombre entier (minutes).',
        'duration_minutes.min' => 'La durée doit être au moins 1 minute.',
        'duration_minutes.max' => 'La durée ne peut pas dépasser 1440 minutes.',
        'notes.string' => 'Les notes doivent être une chaîne de caractères.',
    ];

    public function render()
    {
        $services = Service::when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('description', 'like', '%'.$this->search.'%')
                  ->orWhere('price', 'like', '%'.$this->search.'%')
                  ->orWhere('notes', 'like', '%'.$this->search.'%');
        })
        ->orderBy('name')
        ->paginate(5);

        return view('livewire.services.services', [
            'services' => $services,
        ]);
    }

    public function create()
    {
        $this->reset();
        $this->isEdit = false;
        Flux::modal('create-service')->show();
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        
        $this->serviceId = $id;
        $this->name = $service->name;
        $this->description = $service->description;
        $this->price = $service->price;
        $this->duration_minutes = $service->duration_minutes;
        $this->notes = $service->notes;

        $this->isEdit = true;
        Flux::modal('edit-service')->show();
    }

    public function store()
    {
        $this->validate();

        Service::create([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_minutes' => $this->duration_minutes,
            'is_active' => true, // Default to true for new services
            'notes' => $this->notes,
        ]);

        Flux::modal('create-service')->close();
        $this->reset();

        FlashHelper::success('Service créé avec succès');
    }

    public function update()
    {
        $this->validate();

        $service = Service::findOrFail($this->serviceId);
        $service->update([
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration_minutes' => $this->duration_minutes,
            'notes' => $this->notes,
        ]);

        Flux::modal('edit-service')->close();
        $this->reset();

        FlashHelper::success('Service mis à jour avec succès');
    }

    public function toggleStatus($id)
    {
        $service = Service::findOrFail($id);
        $service->update([
            'is_active' => !$service->is_active
        ]);

        FlashHelper::success('Statut du service mis à jour avec succès');
    }

    public function confirmDelete($id)
    {
        $this->serviceId = $id;
        $this->isDeleteConfirmationOpen = true;
        Flux::modal('delete-confirmation-service')->show();
    }

    public function delete()
    {
        Service::findOrFail($this->serviceId)->delete();
        $this->isDeleteConfirmationOpen = false;
        Flux::modal('delete-confirmation-service')->close();
        FlashHelper::danger('Service supprimé avec succès');
    }
}