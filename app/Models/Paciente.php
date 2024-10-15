<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'nome',
        'nascimento',
        'codigo',
    ];

    public function internacoes()
    {
        return $this->hasMany(Internacao::class, 'paciente_id');
    }
}
