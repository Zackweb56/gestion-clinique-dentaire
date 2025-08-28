<?php

use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\DentalProfile;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    // Dental Clinic Profile Route
    Route::get('settings/dental-clinic-profile', DentalProfile::class)->name('settings.dental_profile');

    // Users | Roles & Permissions Routes
    Route::view('personnels', 'users.personnels_management')->name('personnels')->middleware('can:voir utilisateurs');

    // Ptients & Medical files Routes
    Route::view('patients', 'patients.patients_management')->name('patients')->middleware('can:voir patients');
    Route::get('patients/{patientId}', App\Livewire\Patients\PatientDetails::class)->name('patients.details')->middleware('can:voir patients');
    Route::view('medicalFiles', 'patients.medical_files_management')->name('medicalFiles')->middleware('can:voir dossiers-médicaux');
    Route::view('appointments', 'patients.appointments_management')->name('appointments')->middleware('can:voir rendez-vous');
    Route::get('appointments/{appointment}/whatsapp-message', [App\Livewire\Patients\Appointments::class, 'getWhatsAppMessage'])->name('appointments.whatsapp-message')->middleware('can:voir rendez-vous');
    Route::view('consultations', 'patients.consultations_management')->name('consultations')->middleware('can:voir consultations');
    Route::view('actes', 'patients.actes_management')->name('actes')->middleware('can:voir actes');
    
    // Services Routes
    Route::view('services', 'services.services_management')->name('services')->middleware('can:voir services');
    
    Route::view('invoices', 'invoices.invoices_management')->name('invoices')->middleware('can:voir factures');
    Route::view('payements', 'payements.payements_management')->name('payements');
});

require __DIR__.'/auth.php';