<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NaturalPerson extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'natural_persons';

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
        'renda_mensal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Estados civis válidos
     */
    public const ESTADOS_CIVIS = [
        'solteiro' => 'Solteiro(a)',
        'casado' => 'Casado(a)',
        'divorciado' => 'Divorciado(a)',
        'viuvo' => 'Viúvo(a)',
        'uniao_estavel' => 'União Estável'
    ];

    /**
     * Relação com endereços
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Relação com contatos
     */
    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    /**
     * Retorna o endereço principal (primeiro cadastrado)
     */
    public function mainAddress()
    {
        return $this->addresses()->where('tipo', 'residencial')->first();
    }

    /**
     * Retorna o contato principal (primeiro cadastrado)
     */
    public function mainContact()
    {
        return $this->contacts()->first();
    }

    /**
     * Formata o CPF com pontuação
     */
    public function getFormattedCpfAttribute(): string
    {
        $cpf = preg_replace('/[^0-9]/', '', $this->cpf);
        return substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }

    /**
     * Formata a renda mensal como moeda
     */
    public function getFormattedRendaMensalAttribute(): string
    {
        return 'R$ ' . number_format($this->renda_mensal, 2, ',', '.');
    }

    /**
     * Retorna a idade calculada
     */
    public function getIdadeAttribute(): int
    {
        return $this->data_nascimento->age;
    }

    /**
     * Formata a data de nascimento
     */
    public function getFormattedDataNascimentoAttribute(): string
    {
        return $this->data_nascimento->format('d/m/Y');
    }

    /**
     * Formata a data de emissão do RG
     */
    public function getFormattedDataEmissaoRgAttribute(): string
    {
        return $this->data_emissao_rg->format('d/m/Y');
    }

    /**
     * Retorna o estado civil formatado
     */
    public function getEstadoCivilFormatadoAttribute(): string
    {
        return self::ESTADOS_CIVIS[$this->estado_civil] ?? $this->estado_civil;
    }

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Remove espaços extras e capitaliza nomes
        static::saving(function ($model) {
            $model->nome = ucwords(mb_strtolower(trim($model->nome)));
            if ($model->nome_social) {
                $model->nome_social = ucwords(mb_strtolower(trim($model->nome_social)));
            }
            if ($model->mae) {
                $model->mae = ucwords(mb_strtolower(trim($model->mae)));
            }
            if ($model->pai) {
                $model->pai = ucwords(mb_strtolower(trim($model->pai)));
            }
            if ($model->conjuge) {
                $model->conjuge = ucwords(mb_strtolower(trim($model->conjuge)));
            }

            // Remove pontuação do CPF
            $model->cpf = preg_replace('/[^0-9]/', '', $model->cpf);

            // Converte órgão expedidor para maiúsculas
            $model->orgao_expedidor = mb_strtoupper(trim($model->orgao_expedidor));

            // Converte UF para maiúsculas
            $model->uf_expedidor = mb_strtoupper(trim($model->uf_expedidor));
        });
    }

    /**
     * Escopo para busca por CPF
     */
    public function scopeByCpf($query, $cpf)
    {
        return $query->where('cpf', preg_replace('/[^0-9]/', '', $cpf));
    }

    /**
     * Escopo para busca por nome
     */
    public function scopeByName($query, $name)
    {
        return $query->where('nome', 'ilike', "%{$name}%")
                    ->orWhere('nome_social', 'ilike', "%{$name}%");
    }
}
