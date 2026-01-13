<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('comprobantes_pago', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pago_id')
                ->constrained('pagos')
                ->onDelete('cascade');

            $table->string('nombre');     // nombre original
            $table->string('ruta');       // ruta storage
            $table->string('tipo', 20);   // pdf | imagen

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comprobantes_pago');
    }
};