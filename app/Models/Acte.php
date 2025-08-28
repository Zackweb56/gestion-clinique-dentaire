<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acte extends Model
{
    protected $fillable = [
        'medical_file_id',
        'appointment_id',
        'acte_date',
        'payment_status',
        'status',
    ];

    public function medicalFile()
    {
        return $this->belongsTo(MedicalFile::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function acteServices()
    {
        return $this->hasMany(ActeService::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->acte_number)) {
                $date = now()->format('ymd');
                do {
                    $random = random_int(100, 999);
                    $number = 'ACT' . $random . '-' . $date;
                } while (self::where('acte_number', $number)->exists());
                $model->acte_number = $number;
            }
        });
    }

}