<?php

namespace App\Livewire\Settings;

use App\Helpers\FlashHelper;
use Livewire\Component;
use App\Models\DentalProfile as DentalProfileModel;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class DentalProfile extends Component
{
    use WithFileUploads;

    public $clinic_name, $ICE, $IF, $TVA, $address, $city, $phone_01, $phone_02, $email, $logo, $signature;
    public $profileId;
    public $existingLogo, $existingSignature;

    public function mount()
    {
        $profile = DentalProfileModel::first();
        if ($profile) {
            $this->profileId = $profile->id;
            $this->clinic_name = $profile->clinic_name;
            $this->ICE = $profile->ICE;
            $this->IF = $profile->IF;
            $this->TVA = $profile->TVA;
            $this->address = $profile->address;
            $this->city = $profile->city;
            $this->phone_01 = $profile->phone_01;
            $this->phone_02 = $profile->phone_02;
            $this->email = $profile->email;
            $this->existingLogo = $profile->logo;
            $this->existingSignature = $profile->signature;
        }
    }

    public function deleteLogo()
    {
        if ($this->existingLogo && Storage::disk('public')->exists($this->existingLogo)) {
            Storage::disk('public')->delete($this->existingLogo);
        }
        $this->logo = null;
        $this->existingLogo = null;
        if ($this->profileId) {
            DentalProfileModel::where('id', $this->profileId)->update(['logo' => null]);
        }
    }

    public function deleteSignature()
    {
        if ($this->existingSignature && Storage::disk('public')->exists($this->existingSignature)) {
            Storage::disk('public')->delete($this->existingSignature);
        }
        $this->signature = null;
        $this->existingSignature = null;
        if ($this->profileId) {
            DentalProfileModel::where('id', $this->profileId)->update(['signature' => null]);
        }
    }

    public function save()
    {
        $data = $this->validate([
            'clinic_name' => 'required|string',
            'ICE' => 'required|string',
            'IF' => 'required|string',
            'TVA' => 'nullable|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'phone_01' => 'required|string',
            'phone_02' => 'nullable|string',
            'email' => 'required|email',
            'logo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        if ($this->logo) {
            $data['logo'] = $this->logo->store('logos', 'public');
        } else if ($this->existingLogo) {
            $data['logo'] = $this->existingLogo;
        } else {
            $data['logo'] = null;
        }
        if ($this->signature) {
            $data['signature'] = $this->signature->store('signatures', 'public');
        } else if ($this->existingSignature) {
            $data['signature'] = $this->existingSignature;
        } else {
            $data['signature'] = null;
        }

        DentalProfileModel::updateOrCreate(['id' => $this->profileId], $data);
        FlashHelper::success('Profil du cabinet mis à jour avec succès.');
        $this->dispatch('navigate-to', url: route('settings.dental_profile'));
    }

    public function render()
    {
        return view('livewire.settings.dental-profile');
    }
}