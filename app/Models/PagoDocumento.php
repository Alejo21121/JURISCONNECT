<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoDocumento extends Model
{
    use HasFactory;

    protected $table = 'pago_documentos';

    protected $fillable = [
        'pago_id',
        'nombre',
        'ruta',
        'tipo',
        'tamano',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
