<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cilindros', function (Blueprint $table) {
            $table->id();
            $table->string('numero_serie', 25)->unique();
            $table->date('fecha_recepcion');
            $table->date('fecha_registro');
            $table->string('url_ficha')->nullable();
            $table->binary('QR_code')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cilindros');
    }
};
