<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employees extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nome',
        'matricula',
        'tipo',
        'data_admissao',
        'valor_hora'
    ];

    //Relação com horas lançadas
    public function HoursRelesead(): HasMany {
        return $this->hasMany(HoursReleased::class);
    }
}
