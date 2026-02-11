<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PagoDocumento;
use App\Models\Proceso;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'proceso_id',
        'valor_pagado',
        'forma_pago',
        'fecha_pago',
        'observaciones',
        'estado',              // 👈 AGREGADO
        'motivo_rechazo',      // 👈 AGREGADO
        'validado_por',        // 👈 AGREGADO
        'fecha_validacion',    // 👈 AGREGADO
    ];

    public function documentos()
    {
        return $this->hasMany(PagoDocumento::class);
    }

    public function proceso()
    {
        return $this->belongsTo(Proceso::class, 'proceso_id');
    }

    public function cuota()
    {
        return $this->hasOne(\App\Models\Cuota::class);
    }

    public function validador()
    {
        return $this->belongsTo(\App\Models\User::class, 'validado_por');
    }
}
