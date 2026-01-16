<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PagoDocumento;
use App\Models\Proceso;


class Pago extends Model
{
    protected $fillable = [
        'proceso_id',
        'valor_pagado',
        'forma_pago',
        'fecha_pago',
        'observaciones'
    ];

    public function documentos()
    {
        return $this->hasMany(PagoDocumento::class);
    }


    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }
}
