<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_estados_proceso', function (Blueprint $table) {
            $table->id();

            // Relación con proceso
            $table->foreignId('proceso_id')
                ->constrained('procesos')
                ->onDelete('cascade');

            // Estado del proceso en ese momento
            $table->string('estado', 100);

            // Observación opcional
            $table->text('observacion')->nullable();

            // Usuario que hizo el cambio (abogado / asistente)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // equivalente a set null

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_estados_proceso');
    }
};
