<?php

namespace Source\Controllers;

use PDOException;
use Source\Models\MonitoramentoArtigosPendentes;
use Source\Models\Connection;
use Source\Models\Validations;

require "../../vendor/autoload.php";
require "../Config.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case "GET":
        if(!filter_input(INPUT_GET,"shop") || !filter_input(INPUT_GET,"token")){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Nenhum dado informado!"));
            exit;
        }

        $shop =filter_input(INPUT_GET,"shop");
        $token = filter_input(INPUT_GET,"token");
        $codigo = filter_input(INPUT_GET,'codigo');

        $errors = array();
        if(!Validations::validationString($token)){
            array_push($errors,"Token");
        }
        if(!Validations::validationStore($shop)){
            array_push($errors,"Shop");
        }

        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Ha campos invalidos no formulario!","fields"=>$errors));
            exit;
        }

        header("HTTP/1.1 200 OK");
        $monitoramentoArtigosPendentes = new MonitoramentoArtigosPendentes();
        if($monitoramentoArtigosPendentes->find("shop = :numShop AND codigo = :numArtigo","numShop=$shop&numArtigo=$codigo")->Count()>0){
            $return = array();
            foreach($monitoramentoArtigosPendentes->find("shop = :numShop AND codigo = :numArtigo","numShop=$shop&numArtigo=$codigo")->fetch(true) as $shops){
                //Tratamento dos dados vindos do banco
                array_push($return,$shops->data());
            }
            echo json_encode(array("response"=>$return));
        }else{
            echo json_encode(array("response"=>"Loja ['$shop'] e artigo ['$codigo'] nao encontrados no banco de dados!"));
        }
        break;

    case "PUT":
        unset($exists);

        if(!filter_input(INPUT_GET,"shop") || !filter_input(INPUT_GET,"token")){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Nenhum dado informado!"));
            exit;
        }

        $shop =filter_input(INPUT_GET,"shop");
        $token = filter_input(INPUT_GET,"token");
        $codigo = filter_input(INPUT_GET,'codigo');
        $descricao = filter_input(INPUT_GET,'descricao');
        $tipo = filter_input(INPUT_GET,'tipo');
        $pendente = filter_input(INPUT_GET,'pendente');
        $pendenteKilo = filter_input(INPUT_GET,'pendenteKilo');
        $stock = filter_input(INPUT_GET,'stock');
        $stockKilo = filter_input(INPUT_GET,'stockKilo');
        $dataCheck = date('Y-m-d');

        $errors = array();
        if(!Validations::validationString($token)){
            array_push($errors,"Token");
        }
        if(!Validations::validationStore($shop)){
            array_push($errors,"Shop");
        }

        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Ha campos invalidos no formulario!","fields"=>$errors));
            exit;
        }

        $exists = Connection::getInstanceI()->prepare("SELECT * FROM tb_pedidos_pendentes WHERE shop = '$shop' AND codigo = '$codigo' AND DATE_FORMAT(data_inclusao, '%Y-%m-%d') = '$dataCheck'");
        $exists->execute();
        $ret = $exists->fetchAll();
        var_dump($ret);
        $contar = count($ret);
        if ($contar>=1) {
            $maxId = Connection::getInstanceI()->prepare("SELECT MAX(id) AS maxid, qntd_pendente, qntd_pendente_kilo, stock, stock_kilo FROM tb_pedidos_pendentes WHERE shop = '$shop' AND codigo = '$codigo' AND DATE_FORMAT(data_inclusao, '%Y-%m-%d') = '$dataCheck'");
            $maxId->execute();
            $query = $maxId->fetchAll();
            $dados = (new MonitoramentoArtigosPendentes())->findById($query[0]->maxid);
            if($tipo == 0){
                $dados->descricao = $descricao;
                $dados->tipo_tratamento = $tipo;
                $dados->qntd_pendente = $pendente;
                $dados->qntd_pendente_old = $query[0]->qntd_pendente;
                $dados->stock = $stock;
                $dados->stock_old = $query[0]->stock;
                $dados->qntd_pendente_kilo = 0;
                $dados->qntd_pendente_kilo_old = 0;
                $dados->stock_kilo = 0;
                $dados->stock_kilo_old = 0;
                $dados->data_atualizacao = date('Y-m-d H:i:s');
                $dados->save();
            }elseif($tipo == 1) {
                $dados->descricao = $descricao;
                $dados->tipo_tratamento = $tipo;
                $dados->qntd_pendente = 0;
                $dados->qntd_pendente_old = 0;
                $dados->stock = 0;
                $dados->stock_old = 0;
                $dados->qntd_pendente_kilo = $pendenteKilo;
                $dados->qntd_pendente_kilo_old = $query[0]->qntd_pendente_kilo;
                $dados->stock_kilo = $stockKilo;
                $dados->stock_kilo_old = $query[0]->stock_kilo;
                $dados->data_atualizacao = date('Y-m-d H:i:s');
                $dados->save();
            }

            if($dados->fail()){
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(array("response"=>$dados->fail()->getMessage()));
                exit;
            }

            header("HTTP/1.1 201 Created");
            echo json_encode(array("response"=>"Loja atualizada com sucesso!"));
            exit;
        }else{
            $dados = new MonitoramentoArtigosPendentes();
            if($tipo == 0){
                $dados->shop = $shop;
                $dados->data_inclusao = date('Y-m-d H:i:s');
                $dados->codigo = $codigo;
                $dados->descricao = $descricao;
                $dados->tipo_tratamento = $tipo;
                $dados->qntd_pendente = $pendente;
                $dados->qntd_pendente_old = 0;
                $dados->stock = $stock;
                $dados->stock_old = 0;
                $dados->qntd_pendente_kilo = 0;
                $dados->qntd_pendente_kilo_old = 0;
                $dados->stock_kilo = 0;
                $dados->stock_kilo_old = 0;
                $dados->data_atualizacao = date('Y-m-d H:i:s');
                $dados->save();
            }elseif($tipo == 1) {
                $dados->shop = $shop;
                $dados->data_inclusao = date('Y-m-d H:i:s');
                $dados->codigo = $codigo;
                $dados->descricao = $descricao;
                $dados->tipo_tratamento = $tipo;
                $dados->qntd_pendente = 0;
                $dados->qntd_pendente_old = 0;
                $dados->stock = 0;
                $dados->stock_old = 0;
                $dados->qntd_pendente_kilo = $pendenteKilo;
                $dados->qntd_pendente_kilo_old = 0;
                $dados->stock_kilo = $stockKilo;
                $dados->stock_kilo_old = 0;
                $dados->data_atualizacao = date('Y-m-d H:i:s');
                $dados->save();
            }
            
            if($dados->fail()){
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(array("response"=>$dados->fail()->getMessage()));
                exit;
            }

            header("HTTP/1.1 201 Created");
            echo json_encode(array("response"=>"Loja inserida com sucesso!"));
            exit;
        }
        break;

    default:
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(array("response"=>"Metodo nao previsto na API"));
        break;
}
?>
