<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proceso_id');

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

            $table->foreign('proceso_id')
                ->references('id')
                ->on('procesos')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};
