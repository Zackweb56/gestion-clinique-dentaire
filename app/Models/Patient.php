<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Crypt;

class Patient extends Model
{

    protected $fillable = [
        'patient_full_name',
        'email',
        'cin',
        'birth_date',
        'insurance_type',
        'gender',
        'phone',
        'address',
        'status',
        'created_by',
        'updated_by',
    ];

    public function medicalFile(): HasOne
    {
        return $this->hasOne(MedicalFile::class);
    }

    /**
     * Get the patients initials
    */
    public function initials(): string
    {
        return Str::of($this->patient_full_name)
            ->explode(' ')
            ->map(fn (string $patient_full_name) => Str::of($patient_full_name)->substr(0, 1))
            ->implode('');
    }

    // Encryption mutators and decryption accessors for sensitive fields
    public function setPatientFullNameAttribute($value)
    {
        $this->attributes['patient_full_name'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getPatientFullNameAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setCinAttribute($value)
    {
        $this->attributes['cin'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getCinAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getPhoneAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getEmailAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }
}