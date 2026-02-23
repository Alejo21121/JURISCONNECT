<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();

            $table->string('tipo_proceso', 100);
            $table->string('numero_radicado', 62)->unique();

            $table->string('demandante', 255);
            $table->string('demandado', 255);
            $table->text('descripcion');

            $table->enum('estado', [
                'Pendiente',
                'Radicado',
                'Admisión',
                'Traslado',
                'Audiencia',
                'Fallo favorable',
                'Fallo desfavorable',
                'Apelación',
                'Ejecutoria',
                'Pago en trámite',
                'Conciliado',
                'Archivado',
                'Reabierto'
            ])->default('Pendiente');

            /* FECHA DE VENCIMIENTO */
            $table->date('fecha_vencimiento')->nullable(); 

            /* PAGOS */
            $table->boolean('requiere_pago')->default(false);
            $table->decimal('valor_estimado', 12, 2)->nullable();

            /* RELACIONES */
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('lawyer_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('lawyer_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos');
    }
};