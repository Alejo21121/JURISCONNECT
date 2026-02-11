<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            // Relación con proceso
            $table->unsignedBigInteger('proceso_id');

            // Datos del pago
            $table->decimal('valor_pagado', 12, 2);
            $table->date('fecha_pago');

            $table->enum('forma_pago', [
                'Efectivo',
                'Transferencia',
                'Consignación',
                'Tarjeta',
                'Otro'
            ])->default('Transferencia');

            $table->text('observaciones')->nullable();

            // 🔐 Validación del abogado
            $table->enum('estado', [
                'Pendiente',
                'Aprobado',
                'Rechazado'
            ])->default('Pendiente');

            $table->text('motivo_rechazo')->nullable(); // 👈 NUEVO

            $table->unsignedBigInteger('validado_por')->nullable();
            $table->timestamp('fecha_validacion')->nullable();

            // Relaciones
            $table->foreign('proceso_id')
                ->references('id')
                ->on('procesos')
                ->onDelete('cascade');

            $table->foreign('validado_por')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
