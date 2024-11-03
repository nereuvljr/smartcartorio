<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('natural_persons', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('nome_social')->nullable();
            $table->string('cpf', 11)->unique();
            $table->string('rg');
            $table->string('orgao_expedidor');
            $table->string('uf_expedidor', 2);
            $table->date('data_emissao_rg');
            $table->date('data_nascimento');
            $table->string('naturalidade');
            $table->string('nacionalidade')->default('Brasileira');
            $table->enum('estado_civil', ['solteiro', 'casado', 'divorciado', 'viuvo', 'uniao_estavel']);
            $table->string('conjuge')->nullable();
            $table->string('mae');
            $table->string('pai')->nullable();
            $table->string('profissao');
            $table->decimal('renda_mensal', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('natural_persons');
    }
};
