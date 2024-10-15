<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Paciente;
use App\Models\Internacao;
use App\Models\CensoTemporario;

class CensoController extends Controller
{
    public function uploadCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if (($handle = fopen($request->file('csv_file'), 'r')) !== false) {
            fgetcsv($handle, 1000, ',');

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {

                $nascimento = \Carbon\Carbon::createFromFormat('d/m/Y', $data[1])->format('Y-m-d');
                $entrada = \Carbon\Carbon::createFromFormat('d/m/Y', $data[4])->format('Y-m-d');
                $saida = \Carbon\Carbon::createFromFormat('d/m/Y', $data[5])->format('Y-m-d');

                $valido = $this->validarLinha($data);
                CensoTemporario::create([
                    'nome' => $data[0],
                    'nascimento' => $nascimento,
                    'codigo' => $data[2],
                    'guia' => $data[3],
                    'entrada' => $entrada,
                    'saida' => $saida,
                    'valido' => $valido['valido'],
                    'mensagem_erro' => $valido['mensagem'],
                ]);
            }
            fclose($handle);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Arquivo CSV carregado com sucesso!'
        ], 200);
    }

    private function validarLinha($data)
    {
        // RN02-01
        $pacienteExistente = Paciente::where('nome', $data[0])
                                      ->where('nascimento', $data[1])
                                      ->first();
        if ($pacienteExistente && $pacienteExistente->codigo !== $data[2]) {
            return ['valido' => false, 'mensagem' => 'Código divergente para o paciente'];
        }

        // RN02-02
        $internacaoExistente = Internacao::where('guia', $data[3])->first();
        if ($internacaoExistente) {
            return ['valido' => false, 'mensagem' => 'Guia de internação já cadastrada'];
        }

        // RN02-03
        if (strtotime($data[4]) < strtotime($data[1])) {
            return ['valido' => false, 'mensagem' => 'Data de entrada anterior à data de nascimento'];
        }

        // RN02-04
        if ($data[5] && strtotime($data[5]) <= strtotime($data[4])) {
            return ['valido' => false, 'mensagem' => 'Data de saída não pode ser anterior ou igual à data de entrada'];
        }

        // RN02-05
        $conflitoInternacao = Internacao::where('paciente_id', $pacienteExistente->id ?? null)
            ->where(function ($query) use ($data) {
                $query->whereBetween('entrada', [$data[4], $data[5]])
                      ->orWhereBetween('saida', [$data[4], $data[5]]);
            })->first();
        if ($conflitoInternacao) {
            return ['valido' => false, 'mensagem' => 'Conflito com período de internação'];
        }

        return ['valido' => true, 'mensagem' => ''];
    }

    public function getTemporarios()
    {
        $temporarios = CensoTemporario::all();

        return response()->json([
            'status' => 'success',
            'data' => $temporarios
        ], 200);
    }

    public function listarPacientes(){
        $pacientes = Paciente::all();

        return response()->json([
            'status' => 'success',
            'data' => $pacientes
        ], 200);
    }

    public function listarInternacoes(int $id)
    {
        $paciente = Paciente::find($id);

        if (!$paciente) {
            return response()->json([
                'status' => 'error',
                'message' => 'Paciente não encontrado'
            ], 404);
        }

        $internacoes = Internacao::where('paciente_id', $paciente->id)->get();

        $response = $internacoes->map(function ($internacao) {
            return [
                'guia' => $internacao->guia,
                'entrada' => $internacao->entrada,
                'saida' => $internacao->saida,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'paciente' => [
                    'nome' => $paciente->nome,
                    'nascimento' => $paciente->nascimento,
                    'codigo' => $paciente->codigo,
                ],
                'internacoes' => $response
            ]
        ], 200);
    }

    public function limpaInvalidos()
    {
        CensoTemporario::where('valido', 0)->delete();

        return response()->json([
            'status' => 'success',
            'data' => 'Inválidos excluidos limpa com sucesso'
        ], 200);
    }
    public function limpaTudo()
    {
        CensoTemporario::truncate();

        return response()->json([
            'status' => 'success',
            'data' => 'Tabela temporária limpa com sucesso'
        ], 200);
    }

    public function confirmarCadastro()
    {
        $linhasValidas = CensoTemporario::where('valido', true)->get();

        $novosPacientes = 0;
        $novasInternacoes = 0;

        foreach ($linhasValidas as $linha) {
            $paciente = Paciente::firstOrCreate([
                'nome' => $linha->nome,
                'nascimento' => $linha->nascimento,
            ], [
                'codigo' => $linha->codigo
            ]);

            if ($paciente->wasRecentlyCreated) {
                $novosPacientes++;
            }

            $internacaoExistente = Internacao::where('guia', $linha->guia)->exists();

            if (!$internacaoExistente) {
                Internacao::create([
                    'paciente_id' => $paciente->id,
                    'guia' => $linha->guia,
                    'entrada' => $linha->entrada,
                    'saida' => $linha->saida
                ]);

                $novasInternacoes++;
            }
        }

        CensoTemporario::truncate();

        return response()->json([
            'message' => 'Censo confirmado com sucesso.',
            'novos_pacientes' => $novosPacientes,
            'novas_internacoes' => $novasInternacoes,
        ]);
    }

}
