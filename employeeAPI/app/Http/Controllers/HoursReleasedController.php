<?php

namespace App\Http\Controllers;

use App\Models\HoursReleased;
use Illuminate\Http\Request;

class HoursReleasedController extends Controller
{
    public function store(mixed $emp){
        HoursReleased::created([
            'year' => $emp['year'],
            'month' => $emp['month'],
            'total_hours' => $emp['total_hours'],
            'value' => $emp['value'],
            'id' => $emp['id']
        ]);

        return "Sucesso";
    }
}
