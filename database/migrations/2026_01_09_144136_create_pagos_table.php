<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proceso_id')
                ->constrained('procesos')
                ->onDelete('cascade');

            $table->decimal('valor', 15, 2);
            $table->string('forma_pago', 100);
            $table->date('fecha_pago');
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pagos');
    }
};