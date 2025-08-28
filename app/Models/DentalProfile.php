<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DentalProfile extends Model
{
    protected $fillable = [
        'clinic_name',
        'ICE',
        'IF',
        'TVA',
        'address',
        'city',
        'phone_01',
        'phone_02',
        'email',
        'logo',
        'signature',
    ];
}