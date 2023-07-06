<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registro extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atributos que devem ser tratados como datas.
     *
     * @var array<string, string, string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Atributos que podem ser preenchidos em massa.
     *
     * @var array<string, string>
     */
    protected $fillable = [
        'nome',
        'valor'
    ];
}
