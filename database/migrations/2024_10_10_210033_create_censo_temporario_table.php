<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void {
        Schema::create('censo_temporario', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->date('nascimento');
            $table->string('codigo');
            $table->string('guia')->nullable();
            $table->date('entrada')->nullable();
            $table->date('saida')->nullable();
            $table->boolean('valido')->default(false);
            $table->text('mensagem_erro')->nullable(); // Detalhes dos problemas encontrados
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('censo_temporario');
    }
};
