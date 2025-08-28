<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalFile extends Model
{
    protected $fillable = [
        'patient_id',
        'chronic_diseases',
        'current_medications',
        'allergies',
        'notes',
        'created_by',
        'updated_by',
    ];

    // relation with patient
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // relation with consultations
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    // relation with actes
    public function actes()
    {
        return $this->hasMany(acte::class);
    }

    // relation with appointments
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->file_number)) {
                $date = now()->format('ymd');
                do {
                    $random = random_int(100, 999);
                    $fileNumber = 'DM' . $random . '-' . $date;
                } while (self::where('file_number', $fileNumber)->exists());
                $model->file_number = $fileNumber;
            }
        });
    }
}