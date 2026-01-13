<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobantePago extends Model
{
    protected $fillable = [
        'pago_id',
        'nombre',
        'ruta',
        'tipo'
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
