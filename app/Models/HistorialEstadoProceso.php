<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEstadoProceso extends Model
{
    use HasFactory;

    protected $table = 'historial_estados_proceso';

    protected $fillable = [
        'proceso_id',
        'estado',
        'observacion',
        'user_id',
    ];

    public function proceso()
    {
        return $this->belongsTo(Proceso::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
