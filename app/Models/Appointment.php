<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'medical_file_id',
        'type',
        'status',
        'appointment_date',
        'duration_minutes',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    public function medicalFile()
    {
        return $this->belongsTo(MedicalFile::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function actes()
    {
        return $this->hasMany(Acte::class);
    }

    public function toCalendarEvent()
    {
        return [
            'id' => $this->id,
            'title' => $this->medicalFile->patient->name . ' - ' . $this->type,
            'start' => $this->appointment_date->toIso8601String(),
            'end' => $this->appointment_date->addMinutes($this->duration_minutes)->toIso8601String(),
            'extendedProps' => [
                'medical_file_id' => $this->medicalFile->id,
                'patient_name' => $this->medicalFile->patient->name,
                'type' => $this->type,
                'status' => $this->status,
                'notes' => $this->notes,
                'duration' => $this->duration_minutes,
            ],
            'color' => $this->getStatusColor(),
        ];
    }

    protected function getStatusColor()
    {
        switch ($this->status) {
            case 'confirmed':
                return '#4CAF50'; // Green
            case 'pending':
                return '#FFC107'; // Amber
            case 'cancelled':
                return '#F44336'; // Red
            case 'completed':
                return '#2196F3'; // Blue
            default:
                return '#9E9E9E'; // Grey
        }
    }
}