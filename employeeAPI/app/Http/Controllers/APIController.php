<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use App\Models\HoursReleased;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Exception;


class APIController extends Controller
{

    public function importEmployees()
    {
        $client = new Client();
        $apiURL = "https://63zs5guqxkzp3oxyxtzmdwrypa0bvonh.lambda-url.sa-east-1.on.aws/";
        $resp = $client->get($apiURL);

        if ($resp->getStatusCode() != 200) {
            return response(["Houve um erro ao consumir API de Employees", 503]);
        }
        error_log($resp->getStatusCode());
        $emps = json_decode($resp->getBody(), true);

        //Definindo regras para a validação dos dados recebidos
        $regras = [
            '*.id' => 'required|integer',
            '*.funcionario' => 'required|string',
            '*.matricula' => 'required|string',
            '*.tipo' => 'required|in:CLT,PJ',
            '*.data_admissao' => 'required|date_format:d/m/Y',
        ];

        $validacao = Validator::make($emps, $regras);

        if ($validacao->fails()) {
            return response()->json(['message' => "Erro ao validar dados retornados da API," . $validacao->errors()], 404);
        }
        error_log(json_encode($emps));

        $stringErrors = "";
        foreach ($emps as $emp) {
            try {
                (new EmployeesController)->store($emp);
            } catch (QueryException $error) {
                if ($error->errorInfo[1] === 1062) {
                    $stringErrors .= "Chave duplicada, funcionario ID " . $emp['id'] . ". \n";
                } else {
                    return response()->json(['message' => "Erro ao executar importação, " . $error->getMessage()], 500);
                }
            }
        }
        if ($stringErrors != "") {
            return response()->json(['message' => "Importação finalizada, funcionarios com erro: " . $stringErrors], 200);
        }
        return response()->json(['message' => "Importação finalizada com sucesso"], 200);
    }

    public function getEmployees(Request $req, $pages = 1)
    {
        //Busca funcionarios de acordo com a paginação
        $emps = (new EmployeesController)->listEmployees($pages);

        //Formatando response para json
        $response = [
            'total' => $emps->total(),
            'per_page' => $emps->perPage(),
            'current_page' => $emps->currentPage(),
            'data' => $emps->items()
        ];

        return response()->json($response, 200);
    }

    public function storeHours(Request $req, $matricula) {
        //Validação do conteudo da request
        $conteudo = $req->validate([
            'year' => 'required|integer',
            'total_hours' => 'required|numeric',
            'month' => 'required|integer',
        ]);

        $emp = Employees::where('matricula', $matricula)->first();
        if (!$emp) {
            return response()->json(['message' => "Funcionario de matricula ".$matricula." não encontrado"], 404);
        }

        $hours = new HoursReleased;
        $hours->total_hours = $conteudo['total_hours'];
        $hours->month = $conteudo['month'];
        $hours->year = $conteudo['year'];

        //Relação Employee x Hours
        $hours->employees()->associate($emp);
        $hours->save();

        return response()->json(['message' => "Horas lançadas com sucesso"], 200);
    }

    public function updateValue(Request $req, $matricula) {
        $hourValue = $req->input('hour_value');
        if ($hourValue == "") {
            return response()->json(['message' => "Valor hora não informado corretamente"], 400);
        }

        try {
            $emps = (new EmployeesController)->updateHourValue($matricula, $hourValue);
        }catch (QueryException $error) {
            return response()->json(['message' => "Erro ao atualizar valor hora, " . $error->getMessage()], 500);
        }
        return response()->json(['message' => "Valor hora atualizado com sucesso"], 200);
    }

    public function getValueByMonth($matricula, $mes) {
        try {
            $emp = Employees::where("matricula", $matricula)->first();
            if (!$emp) {
                return response()->json(['message' => "Funcionario de matricula ".$matricula." não encontrado"], 404);
            }

            $hours = HoursReleased::where('id_employee', $emp->id)->where('month', $mes)->get();
            if (!$hours) {
                return response()->json(['message' => "Não existe horas lançadas para o funcionario de matricula ".$matricula.", no mês ".$mes], 404);
            }

            $horasTotais = 0;
            $valorTotal = 0;

            foreach ($hours as $hour) {
                $valorTotal += $hour->total_hours * $emp->valor_hora;
                $horasTotais += $hour->total_hours;
            }

            return response()->json([
                'name' => $emp->nome,
                'registry' => $emp->matricula,
                'total_value' => $valorTotal,
                'total_hours' => $horasTotais
            ]);
        } catch (Exception $error) {
            return response()->json(['message' => "Erro ao executar calculo de horas totais,  ".$error->getMessage()], 500);
        }
    }
}
