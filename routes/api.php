<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CensoController;

Route::get('', function (){ echo "API DESAFIO ON"; });

Route::post('/upload-csv', [CensoController::class, 'uploadCSV']);
Route::post('/salvar-censo', [CensoController::class, 'salvarCenso']);

Route::get('/censo/temporarios', [CensoController::class, 'getTemporarios']);
Route::post('/censo/limpa-invalidos', [CensoController::class, 'limpaInvalidos']);
Route::post('/censo/limpa-tudo', [CensoController::class, 'limpaTudo']);
Route::post('/censo/confirmar-cadastro', [CensoController::class, 'confirmarCadastro']);

Route::get('/pacientes', [CensoController::class, 'listarPacientes']);
Route::get('/paciente/{id}/internacoes', [CensoController::class, 'listarInternacoes']);

