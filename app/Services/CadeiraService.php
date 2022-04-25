<?php

namespace App\Services;

use App\Models\Cadeira;
use App\Models\Anoletivo;
use App\Models\Inscricaoucs;
use Illuminate\Support\Facades\DB;

class CadeiraService
{
    public function getInformacoesCadeirasForAdmin($cadeira){
        $anoletivo = Anoletivo::where("ativo", 1)->first();
        if(empty($anoletivo)){
            return ['msg' => "Error",'code' => 404];
        }
        $subQuery = "(select i.idTurno from inscricao i join turno t on t.id = i.idTurno where i.idTurno IN 
        (SELECT t.id from turno t WHERE t.idCadeira = ". $cadeira->id ." and t.idAnoletivo = ". $anoletivo->id .") and i.idUtilizador = utilizador.id) as idTurno";
        $alunos = Inscricaoucs::where('idCadeira',$cadeira->id)->where('idAnoletivo', $anoletivo->id)->where('estado',1)
            ->join('utilizador', 'utilizador.id', '=', 'inscricaoucs.idUtilizador')
            ->select('utilizador.id','utilizador.nome','utilizador.login','inscricaoucs.nrinscricoes',DB::raw($subQuery))->get();
        $totalNaorepetentes = 0;
        $totalRepetentes = 0;
        $totalinscritosTurnos = 0;
        foreach ($alunos as $aluno) {
            if(!is_null($aluno->idTurno)){
                $totalinscritosTurnos++;
            }
            if($aluno->nrinscricoes == 1){
                $totalNaorepetentes++;
            }else{
                $totalRepetentes++;
            }
        }
        $send = ["totalinscritos" => $totalNaorepetentes+$totalRepetentes,"totalrepetentes" => $totalRepetentes,
                "totalnaorepetentes" => $totalNaorepetentes, "totalnaoinscritos" => ($totalNaorepetentes+$totalRepetentes)-$totalinscritosTurnos, "alunos" => $alunos];

        return ['msg' => $send,'code' => 200];
    }
}