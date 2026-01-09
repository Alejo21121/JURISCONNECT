<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoDocumento extends Model
{
    protected $fillable = [
        'proceso_id',
        'nombre',
        'ruta',
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }
}