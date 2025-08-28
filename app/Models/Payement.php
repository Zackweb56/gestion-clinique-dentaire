<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payement extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'paid_at',
        'notes',
        'created_by',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
