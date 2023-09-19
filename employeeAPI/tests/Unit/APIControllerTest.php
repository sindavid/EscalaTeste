<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\HoursReleased;
use App\Models\Employees;

class APIControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetEmployees()
    {
        //Criando registros falsos
        Employees::factory()->count(11)->create();

        //Enviando requisição
        $response = $this->get('/api/employees');

        //Validando statusCode
        $response->assertStatus(200);
    }

    public function testGetValueByMonthError()
    {
        //Criando registros falsos
        Employees::factory()->count(11)->create();

        //Enviando requisição
        $response = $this->get('/value/DAVID12345/12');

        //Validando statusCode
        $response->assertStatus(404);
    }
}
