<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'proceso_id',
        'valor',
        'forma_pago',
        'fecha_pago',
        'observaciones'
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function comprobantes()
    {
        return $this->hasMany(ComprobantePago::class);
    }
}
