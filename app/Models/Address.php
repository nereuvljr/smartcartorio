<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'tipo'
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
