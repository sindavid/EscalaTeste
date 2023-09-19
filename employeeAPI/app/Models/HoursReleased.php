<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoursReleased extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'total_hours'
    ];


    //Relação com funcionarios
    public function employees(): BelongsTo {
        return $this->belongsTo(Employees::class, 'id_employee');
    }
}
