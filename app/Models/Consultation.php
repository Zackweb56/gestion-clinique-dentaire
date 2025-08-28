<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_file_id',
        'appointment_id',
        'consultation_date',
        'symptoms',
        'diagnosis',
        'acte_plan',
        'notes',
        'status',
        'responsable'
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
    ];

    public function medicalFile()
    {
        return $this->belongsTo(MedicalFile::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->consultation_number)) {
                $date = now()->format('ymd');
                do {
                    $random = random_int(100, 999);
                    $number = 'CNS' . $random . '-' . $date;
                } while (self::where('consultation_number', $number)->exists());
                $model->consultation_number = $number;
            }
        });
    }
}