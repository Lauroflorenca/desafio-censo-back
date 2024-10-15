<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CensoTemporario extends Model
{
    use HasFactory;

    protected $table = 'censo_temporario';

    protected $fillable = [
        'nome',
        'nascimento',
        'codigo',
        'guia',
        'entrada',
        'saida',
        'valido',
        'mensagem_erro'
    ];
}
