<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pago_documentos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pago_id');

            $table->string('nombre');
            $table->string('ruta');
            $table->string('tipo', 50)->nullable(); // pdf, jpg, png
            $table->integer('tamano')->nullable(); // bytes

            $table->foreign('pago_id')
                ->references('id')
                ->on('pagos')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pago_documentos');
    }
};

