<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $fillable = [
        'proceso_id',
        'pago_id',
        'numero_cuota',
        'valor',
        'fecha_vencimiento',
        'estado',
        'fecha_pago'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
