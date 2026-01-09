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
        Schema::create('proceso_documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proceso_id');
            $table->string('nombre');
            $table->string('ruta');
            $table->timestamps();

            $table->foreign('proceso_id')->references('id')->on('procesos')->onDelete('cascade');
        });
    }
};