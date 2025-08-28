<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'acte_id',
        'base_amount',
        'tva_rate',
        'tva_amount',
        'total_amount',
        'paid_amount',
        'status',
        'invoice_number',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->invoice_number)) {
                $date = now()->format('dmy');
                $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
                $number = 'FAC-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
                while (self::where('invoice_number', $number)->exists()) {
                    $count++;
                    $number = 'FAC-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
                }
                $model->invoice_number = $number;
            }
        });
    }

    public function acte()
    {
        return $this->belongsTo(Acte::class);
    }
}