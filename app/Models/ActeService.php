<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActeService extends Model
{
    protected $fillable = [
        'acte_id',
        'service_id',
        'tooth_number',
        'libelle',
        'price',
        'notes',
    ];

    public function acte()
    {
        return $this->belongsTo(Acte::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
