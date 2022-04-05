<?php

namespace App\Services;

use App\Models\Logs;
use App\Models\Pedidos;
use App\Models\Aberturas;
use App\Models\Anoletivo;
use App\Models\Pedidosucs;
use App\Models\Inscricaoucs;

class PedidosService
{
    public function save($data){
        $pedido = new Pedidos();
        $pedido->idUtilizador = $data->get('idUtilizador');
        $pedido->idAnoLetivo = $data->get('idAnoletivo');
        $pedido->descricao = $data->get('descricao');
        $pedido->estado = $data->get('estado');
        $pedido->semestre = $data->get('semestre');

        $pedido->save();
        return $pedido;
    }

    public function update($pedido, $data){
        $pedido->descricao = $data->get('descricao');
        $pedido->estado = $data->get('estado');

        $pedido->save();
        return $pedido;
    }


    public function savePedidosUcs($pedidosId, $cadeiraId){
        $pedidoucs = new Pedidosucs();
        $pedidoucs->idCadeira = $cadeiraId;
        $pedidoucs->idPedidos = $pedidosId;

        $pedidoucs->save();
        return $pedidoucs;
    }

    public function editPedidoByAdmin($data, Pedidos $pedido){
        if((!$data->has('pedidosucsAprovadasIds') || !$data->has('pedidosucsReprovadasIds'))){
            return ["msg" => "Têm de ser enviados os aprovados e reprovados mesmo que vazio!", "code" => 400];
        }
        if(sizeof($pedido->pedidosucs) != (sizeof($data->get('pedidosucsAprovadasIds'))+sizeof($data->get('pedidosucsReprovadasIds')))){
            return ["msg" => "Não foram selecionadas todas as ucs!", "code" => 400];
        }
        
        foreach($data->get('pedidosucsAprovadasIds') as $id){
            $pedidoucs = Pedidosucs::where('id',$id)->first();
            $pedidoucs->aceite = 1;
            $pedidoucs->save();

            //$idAnoletivo =  Aberturas::where('idCurso',$pedidoucs->cadeira->id)->select('idAnoletivo')->distinct()->get()->max('idAnoletivo');
            $idAnoletivo =  (Anoletivo::where('anoletivo',Anoletivo::distinct()->get()->max('anoletivo'))->first())->id;

            $inscricaoucs = "a";//verificiar se ja nao existe criado se ja houver n criar
            $inscricaoucs = new Inscricaoucs();
            $inscricaoucs->idUtilizador = $pedido->idUtilizador;
            $inscricaoucs->idCadeira = $pedidoucs->idCadeira;
            $inscricaoucs->nrinscricoes = 1;
            $inscricaoucs->estado = 1;
            $inscricaoucs->idAnoletivo = $idAnoletivo;
            $inscricaoucs->save();
            //inscrever alunos na cadeira
        }
        foreach($data->get('pedidosucsReprovadasIds') as $id){
            //inscrever alunos na cadeira
            $pedidoucs = Pedidosucs::where('id',$id)->first();
            $pedidoucs->aceite = 0;
            $pedidoucs->save();
        }

        if(sizeof($data->get('pedidosucsReprovadasIds')) == 0){
            $pedido->estado = 2;
        }else{
            if(sizeof($data->get('pedidosucsAprovadasIds')) == 0){
                $pedido->estado = 3;
            }else{
                $pedido->estado = 4;
            }
        }
        $pedido->save();

        return ["msg" => "Processo completo!", "code" => 200];
    }


    public function checkifExists($data){
        if($data->get('estado') == 0){
            $pedidos = Pedidos::where('idUtilizador', $data->get('idUtilizador'))->where('idAnoletivo',$data->get('idAnoletivo'))->where('semestre',$data->get('semestre'))->where('estado',0)->first();
            if(!empty($pedidos)){
                return ['response' => 0, 'erro' => 'Já existe uma confirmação'];
            }
            return ['response' => 1];
        }
        

        $pedidos = Pedidos::where('idUtilizador', $data->get('idUtilizador'))->where('idAnoletivo',$data->get('idAnoletivo'))->where('semestre',$data->get('semestre'))->where('estado',1)->get();
        if(!empty($pedidos)){
            foreach($pedidos as $pedido){
                foreach($pedido->pedidosucs as $pedidoucs){
                    if(in_array($pedidoucs->cadeira->id, $data->get('cadeirasIds'))){
                        return ['response' => 0, 'erro' => 'Já têm um pedido pendente para a cadeira: ' . $pedidoucs->cadeira->nome . ' '];
                    }
                }
            }
        }
        return ['response' => 1];
    }
}