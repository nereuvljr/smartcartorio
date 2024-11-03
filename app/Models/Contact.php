<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'telefone',
        'celular',
        'email',
        'contato_alternativo'
    ];

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}
