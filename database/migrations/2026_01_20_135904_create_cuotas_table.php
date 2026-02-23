<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();

            // Relación
            $table->unsignedBigInteger('proceso_id');
            $table->unsignedBigInteger('pago_id')->nullable(); // cuando se pague

            // Datos de la cuota
            $table->integer('numero_cuota');
            $table->decimal('valor', 12, 2);
            $table->date('fecha_vencimiento');

            // Estado
            $table->enum('estado', [
                'Pendiente',
                'Pagada',
                'Vencida'
            ])->default('Pendiente');

            $table->date('fecha_pago')->nullable();

            // Relaciones
            $table->foreign('proceso_id')
                ->references('id')
                ->on('procesos')
                ->onDelete('cascade');

            $table->foreign('pago_id')
                ->references('id')
                ->on('pagos')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cuotas');
    }
};
