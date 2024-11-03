<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NaturalPerson extends Model
{
    use SoftDeletes;

    protected $table = 'natural_persons'; // <- Especifica o nome da tabela

    protected $fillable = [
        'nome',
        'nome_social',
        'cpf',
        'rg',
        'orgao_expedidor',
        'uf_expedidor',
        'data_emissao_rg',
        'data_nascimento',
        'naturalidade',
        'nacionalidade',
        'estado_civil',
        'conjuge',
        'mae',
        'pai',
        'profissao',
        'renda_mensal'
    ];

    protected $casts = [
        'data_emissao_rg' => 'date',
        'data_nascimento' => 'date',
        'renda_mensal' => 'decimal:2'
    ];

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }
}
