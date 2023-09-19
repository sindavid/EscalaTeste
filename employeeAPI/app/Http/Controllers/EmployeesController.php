<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class EmployeesController extends Controller
{
    public function listEmployees(int $page): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        // Obtém os funcionários com paginação do banco de dados
        $emps = DB::table('employees')->paginate(10, ['*'], 'page', $page);

        return $emps;

    }

    public function store(mixed $emp){
        error_log(json_encode($emp));
        DB::table('employees')->insert([
            'id' => $emp['id'],
            'nome' => $emp['funcionario'],
            'matricula' => $emp['matricula'],
            'tipo' => $emp['tipo'],
            'data_admissao' => $emp['data_admissao']
        ]);

        return "Sucesso";
    }

    public function updateHourValue($matricula, $hourValue){
        DB::table('employees')->where('matricula', $matricula)->update(['valor_hora' => $hourValue]);

        return "Sucesso";
    }
}
