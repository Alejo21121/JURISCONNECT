<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HistorialEstadoProceso;
use App\Models\Pago;


class Proceso extends Model
{
    use HasFactory;

    protected $table = 'procesos';

    protected $attributes = [
        'estado' => 'pendiente', // Valor por defecto
    ];

    protected $fillable = [
        'tipo_proceso',
        'numero_radicado',
        'demandante',
        'demandado',
        'descripcion',
        'estado',
        'user_id',
        'lawyer_id',
        'fecha_vencimiento', // 👈 AGREGAR
        // 👇 PAGO
        'requiere_pago',
        'valor_estimado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'created_at' => 'datetime:d-m-Y',
        'fecha_vencimiento' => 'date', // 👈 AGREGAR
    ];

    /**
     * Relación con el usuario que creó el proceso
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el abogado asignado
     */
    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('estado', $status);
    }

    /**
     * Scope para buscar procesos
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('numero_radicado', 'like', "%{$term}%")
                ->orWhere('demandante', 'like', "%{$term}%")
                ->orWhere('demandado', 'like', "%{$term}%")
                ->orWhere('tipo_proceso', 'like', "%{$term}%");
        });
    }

    public function documentos()
    {
        return $this->hasMany(ProcesoDocumento::class);
    }

    /**
     * Relación: Un proceso tiene un concepto jurídico
     */
    public function concepto()
    {
        return $this->hasOne(ConceptoJuridico::class, 'proceso_id');
    }

    public function conceptos()
    {
        return $this->hasMany(ConceptoJuridico::class);
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstadoProceso::class);
    }


    public function historial()
    {
        return $this->hasMany(
            HistorialEstadoProceso::class,
            'proceso_id'
        )->orderBy('created_at', 'desc');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class);
    }

    public function cuotas()
    {
        return $this->hasMany(Cuota::class);
    }
}
