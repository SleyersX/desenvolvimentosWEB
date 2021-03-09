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
        $data = json_decode(file_get_contents("php://input"),false);
       
        if(!$data){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Nenhum dado informado!"));
            exit; 
        }
        
        $errors = array();
        if(!Validations::validationString($data->token)){
            array_push($errors,"Token");
        }
        if(!Validations::validationStore($data->shop)){
            array_push($errors,"Shop");
        }
        if(!empty($data->date_filter)){
            if(!Validations::validationDate($data->date_filter)){
                array_push($errors,"Date Filter");
            }
        }
        
        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Ha campos invalidos no formulario!","fields"=>$errors));
            exit;
        }

        if (!empty($data->codigo) && empty($data->date_filter) || !isset($data->codigo) && empty($data->date_filter)) {
            header("HTTP/1.1 200 OK");
            $monitoramentoArtigosPendentes = new MonitoramentoArtigosPendentes();
            if ($monitoramentoArtigosPendentes->find("shop = :numShop AND codigo = :numArtigo", "numShop=$data->shop&numArtigo=$data->codigo")->Count()>0) {
                $return = array();
                foreach ($monitoramentoArtigosPendentes->find("shop = :numShop AND codigo = :numArtigo", "numShop=$data->shop&numArtigo=$data->codigo")->fetch(true) as $dados) {
                    //Tratamento dos dados vindos do banco
                    array_push($return, $dados->data());
                }
                echo json_encode(array("response"=>$return));
            } else {
                echo json_encode(array("response"=>"Loja ['$data->shop'] e artigo ['$data->codigo'] nao encontrados no banco de dados!"));
            }
        }elseif(!empty($data->pendente) || !isset($data->pendente) && $data->pendente == 1){
            if(empty($data->date_filter)){
                header("HTTP/1.1 200 OK");
                $monitoramentoArtigosPendentes = new MonitoramentoArtigosPendentes();
                if ($monitoramentoArtigosPendentes->find("shop = :numShop AND qntd_pendente >= 1", "numShop=$data->shop")->Count()>0) {
                    $return = array();
                    foreach ($monitoramentoArtigosPendentes->find("shop = :numShop AND qntd_pendente >= 1","numShop=$data->shop")->fetch(true) as $dados) {
                        //Tratamento dos dados vindos do banco
                        array_push($return, $dados->data());
                    }
                    echo json_encode(array("response"=>$return));
                } else {
                    echo json_encode(array("response"=>"Loja ['$data->shop'] sem artigos pendentes de servir!"));
                }
            }else{
                header("HTTP/1.1 200 OK");
                $monitoramentoArtigosPendentes = new MonitoramentoArtigosPendentes();
                if ($monitoramentoArtigosPendentes->find("shop = :numShop AND qntd_pendente >= 1 AND DATE_FORMAT(data_inclusao, '%Y-%m-%d') = :dateFilter ", "numShop=$data->shop&dateFilter=$data->date_filter")->Count()>0) {
                    $return = array();
                    foreach ($monitoramentoArtigosPendentes->find("shop = :numShop AND qntd_pendente >= 1 AND DATE_FORMAT(data_inclusao, '%Y-%m-%d') = :dateFilter ","numShop=$data->shop&dateFilter=$data->date_filter")->fetch(true) as $dados) {
                        //Tratamento dos dados vindos do banco
                        array_push($return, $dados->data());
                    }
                    echo json_encode(array("response"=>$return));
                } else {
                    echo json_encode(array("response"=>"Loja ['$data->shop'] sem artigos pendentes de servir!"));
                }
            }
        }elseif(!empty($data->date_filter) || !isset($data->date_filter)){
            if(empty($data->codigo)){
                header("HTTP/1.1 200 OK");
                $monitoramentoArtigosPendentes = new MonitoramentoArtigosPendentes();
                if ($monitoramentoArtigosPendentes->find("shop = :numShop AND DATE_FORMAT(data_inclusao, '%Y-%m-%d') = :dateFilter ", "numShop=$data->shop&dateFilter=$data->date_filter")->Count()>0) {
                    $return = array();
                    foreach ($monitoramentoArtigosPendentes->find("shop = :numShop AND DATE_FORMAT(data_inclusao, '%Y-%m-%d') = :dateFilter ", "numShop=$data->shop&dateFilter=$data->date_filter")->fetch(true) as $dados) {
                        //Tratamento dos dados vindos do banco
                        array_push($return, $dados->data());
                    }
                    echo json_encode(array("response"=>$return));
                } else {
                    echo json_encode(array("response"=>"Loja ['$data->shop'] sem artigos pendentes de servir!"));
                }
            }else{
                header("HTTP/1.1 200 OK");
                $monitoramentoArtigosPendentes = new MonitoramentoArtigosPendentes();
                if ($monitoramentoArtigosPendentes->find("shop = :numShop AND DATE_FORMAT(data_inclusao, '%Y-%m-%d') = :dateFilter AND codigo = :numArtigo", "numShop=$data->shop&dateFilter=$data->date_filter&numArtigo=$data->codigo")->Count()>0) {
                    $return = array();
                    foreach ($monitoramentoArtigosPendentes->find("shop = :numShop AND DATE_FORMAT(data_inclusao, '%Y-%m-%d') = :dateFilter AND codigo = :numArtigo", "numShop=$data->shop&dateFilter=$data->date_filter&numArtigo=$data->codigo")->fetch(true) as $dados) {
                        //Tratamento dos dados vindos do banco
                        array_push($return, $dados->data());
                    }
                    echo json_encode(array("response"=>$return));
                } else {
                    echo json_encode(array("response"=>"Loja ['$data->shop'] sem artigos pendentes de servir!"));
                }
            }
            
        }else{
            header("HTTP/1.1 200 OK");
            echo json_encode(array("response"=>"Código vazio ou inexistente!"));
        }
        break;

    case "PUT":
        $data = json_decode(file_get_contents("php://input"),false);
       
        if(!$data){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Nenhum dado informado!"));
            exit; 
        }
        
        $dataCheck = date('Y-m-d');

        $errors = array();
        if(!Validations::validationString($data->token)){
            array_push($errors,"Token");
        }
        if(!Validations::validationStore($data->shop)){
            array_push($errors,"Shop");
        }
        
        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Ha campos invalidos no formulario!","fields"=>$errors));
            exit;
        }

        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Ha campos invalidos no formulario!","fields"=>$errors));
            exit;
        }

        $exists = Connection::getInstanceI()->prepare("SELECT * FROM tb_pedidos_pendentes WHERE shop = '$data->shop' AND codigo = '$data->codigo' AND DATE_FORMAT(data_inclusao, '%Y-%m-%d') = '$dataCheck'");
        $exists->execute();
        $ret = $exists->fetchAll();
        var_dump($ret);
        $contar = count($ret);
        if ($contar>=1) {
            $maxId = Connection::getInstanceI()->prepare("SELECT MAX(id) AS maxid, qntd_pendente, qntd_pendente_kilo, stock, stock_kilo FROM tb_pedidos_pendentes WHERE shop = '$data->shop' AND codigo = '$data->codigo' AND DATE_FORMAT(data_inclusao, '%Y-%m-%d') = '$dataCheck'");
            $maxId->execute();
            $query = $maxId->fetchAll();
            $dados = (new MonitoramentoArtigosPendentes())->findById($query[0]->maxid);
            if($tipo == 0){
                $dados->descricao = $data->descricao;
                $dados->tipo_tratamento = $data->tipo;
                $dados->qntd_pendente = $data->pendente;
                $dados->qntd_pendente_old = $query[0]->qntd_pendente;
                $dados->stock = $data->stock;
                $dados->stock_old = $query[0]->stock;
                $dados->qntd_pendente_kilo = 0;
                $dados->qntd_pendente_kilo_old = 0;
                $dados->stock_kilo = 0;
                $dados->stock_kilo_old = 0;
                $dados->data_atualizacao = date('Y-m-d H:i:s');
                $dados->save();
            }elseif($tipo == 1) {
                $dados->descricao = $data->descricao;
                $dados->tipo_tratamento = $data->tipo;
                $dados->qntd_pendente = 0;
                $dados->qntd_pendente_old = 0;
                $dados->stock = 0;
                $dados->stock_old = 0;
                $dados->qntd_pendente_kilo = $data->pendenteKilo;
                $dados->qntd_pendente_kilo_old = $query[0]->qntd_pendente_kilo;
                $dados->stock_kilo = $data->stock_kilo;
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
                $dados->shop = $data->shop;
                $dados->data_inclusao = date('Y-m-d H:i:s');
                $dados->codigo = $data->codigo;
                $dados->descricao = $data->descricao;
                $dados->tipo_tratamento = $data->tipo;
                $dados->qntd_pendente = $data->pendente;
                $dados->qntd_pendente_old = 0;
                $dados->stock = $data->stock;
                $dados->stock_old = 0;
                $dados->qntd_pendente_kilo = 0;
                $dados->qntd_pendente_kilo_old = 0;
                $dados->stock_kilo = 0;
                $dados->stock_kilo_old = 0;
                $dados->data_atualizacao = date('Y-m-d H:i:s');
                $dados->save();
            }elseif($tipo == 1) {
                $dados->shop = $data->shop;
                $dados->data_inclusao = date('Y-m-d H:i:s');
                $dados->codigo = $data->codigo;
                $dados->descricao = $data->descricao;
                $dados->tipo_tratamento = $data->tipo;
                $dados->qntd_pendente = 0;
                $dados->qntd_pendente_old = 0;
                $dados->stock = 0;
                $dados->stock_old = 0;
                $dados->qntd_pendente_kilo = $data->pendenteKilo;
                $dados->qntd_pendente_kilo_old = 0;
                $dados->stock_kilo = $data->stock_kilo;
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
