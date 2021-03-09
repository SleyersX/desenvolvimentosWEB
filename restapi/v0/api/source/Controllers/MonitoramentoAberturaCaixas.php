<?php

namespace Source\Controllers;

use PDOException;
use Source\Models\MonitoramentoAberturaCaixas;
use Source\Models\Connection;
use Source\Models\Validations;

require "../../vendor/autoload.php";
require "../Config.php";

switch ($_SERVER['REQUEST_METHOD']){
    case "GET":
        if(!filter_input(INPUT_GET,"shop") || !filter_input(INPUT_GET,"token")){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Nenhum dado informado!"));
            exit;
        }

        $shop =filter_input(INPUT_GET,"shop");
        $token = filter_input(INPUT_GET,"token");
        $pos = filter_input(INPUT_GET,'pos');

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
        $monitoramentoAberturaCaixas = new MonitoramentoAberturaCaixas();
        if($monitoramentoAberturaCaixas->find("shop = :numShop AND pos = :numPos","numShop=$shop&numPos=$pos")->Count()>0){
            $return = array();
            foreach($monitoramentoAberturaCaixas->find("shop = :numShop AND pos = :numPos","numShop=$shop&numPos=$pos")->fetch(true) as $shops){
                //Tratamento dos dados vindos do banco
                array_push($return,$shops->data());
            }
            echo json_encode(array("response"=>$return));
        }else{
            echo json_encode(array("response"=>"Loja nao cadastrada no banco de dados!"));
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
        $pos = filter_input(INPUT_GET,'pos');
        $dataAberura = filter_input(INPUT_GET,'dataAbertura');
        $horaAbertura = filter_input(INPUT_GET,'horaAbertura');
        $matricula = filter_input(INPUT_GET,'matricula');
        $valorAbertura = filter_input(INPUT_GET,'valorAbertura');
        $data = date("Y-m-d", strtotime($dataAberura));
        $hora = date("H:i",strtotime($horaAbertura));

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
        

        if($matricula == '00000000'){
            //Validamos se existe a loja e o caixa cadastrado no banco de dados
            $exists = Connection::getInstanceI()->prepare("SELECT * FROM tb_monitoramento_abertura_de_caixas WHERE shop = '$shop' AND pos = '$pos' AND matricula = '$matricula' AND data_abertura = '$data'");
            $exists->execute();
            $ret = $exists->fetchAll();
            var_dump($ret);
            $contar = count($ret);
            if ($contar>=1) {
                $maxId = Connection::getInstanceI()->prepare("SELECT MAX(id) AS maxid FROM tb_monitoramento_abertura_de_caixas WHERE shop = '$shop' AND pos = '$pos' AND matricula = '$matricula' AND  data_abertura = '$data'");
                $maxId->execute();
                $id = $maxId->fetchAll();
                $shops = (new MonitoramentoAberturaCaixas())->findById($id[0]->maxid);
                $shops->data_abertura = date("Y-m-d", strtotime($dataAberura));
                $shops->hora_abertura = $horaAbertura;
                $shops->matricula = $matricula;
                $shops->valor_abertura = $valorAbertura;
                $shops->data_atualizacao = date('Y-m-d H:i:s');
                $shops->save();

                if($shops->fail()){
                    header("HTTP/1.1 500 Internal Server Error");
                    echo json_encode(array("response"=>$shops->fail()->getMessage()));
                    exit;
                }

                header("HTTP/1.1 201 Created");
                echo json_encode(array("response"=>"Loja atualizada com sucesso!"));
                exit;
            }else{
                $shops = new MonitoramentoAberturaCaixas();
                $shops->shop = $shop;
                $shops->pos = $pos;
                $shops->data_abertura = date("Y-m-d", strtotime($dataAberura));
                $shops->hora_abertura = $horaAbertura;
                $shops->matricula = $matricula;
                $shops->valor_abertura = $valorAbertura;
                $shops->data_atualizacao = date('Y-m-d H:i:s');
                $shops->save();

                if($shops->fail()){
                    header("HTTP/1.1 500 Internal Server Error");
                    echo json_encode(array("response"=>$shops->fail()->getMessage()));
                    exit;
                }

                header("HTTP/1.1 201 Created");
                echo json_encode(array("response"=>"Loja inserida com sucesso!"));
                exit;
            }
        }else{
            //Validamos se existe a loja e o caixa cadastrado no banco de dados
            $exists = Connection::getInstanceI()->prepare("SELECT * FROM tb_monitoramento_abertura_de_caixas WHERE shop = '$shop' AND pos = '$pos' AND matricula = '$matricula' AND data_abertura = '$data' AND hora_abertura = '$hora'");
            $exists->execute();
            $ret = $exists->fetchAll();
            var_dump($ret);
            $contar = count($ret);
            if ($contar>=1) {
                $maxId = Connection::getInstanceI()->prepare("SELECT MAX(id) AS maxid FROM tb_monitoramento_abertura_de_caixas WHERE shop = '$shop' AND pos = '$pos' AND matricula = '$matricula' AND  data_abertura = '$data' AND hora_abertura = '$hora'");
                $maxId->execute();
                $id = $maxId->fetchAll();
                $shops = (new MonitoramentoAberturaCaixas())->findById($id[0]->maxid);
                $shops->data_abertura = date("Y-m-d", strtotime($dataAberura));
                $shops->hora_abertura = $horaAbertura;
                $shops->matricula = $matricula;
                $shops->valor_abertura = $valorAbertura;
                $shops->data_atualizacao = date('Y-m-d H:i:s');
                $shops->save();

                if($shops->fail()){
                    header("HTTP/1.1 500 Internal Server Error");
                    echo json_encode(array("response"=>$shops->fail()->getMessage()));
                    exit;
                }

                header("HTTP/1.1 201 Created");
                echo json_encode(array("response"=>"Loja atualizada com sucesso!"));
                exit;
            }else{
                $shops = new MonitoramentoAberturaCaixas();
                $shops->shop = $shop;
                $shops->pos = $pos;
                $shops->data_abertura = date("Y-m-d", strtotime($dataAberura));
                $shops->hora_abertura = $horaAbertura;
                $shops->matricula = $matricula;
                $shops->valor_abertura = $valorAbertura;
                $shops->data_atualizacao = date('Y-m-d H:i:s');
                $shops->save();

                if($shops->fail()){
                    header("HTTP/1.1 500 Internal Server Error");
                    echo json_encode(array("response"=>$shops->fail()->getMessage()));
                    exit;
                }

                header("HTTP/1.1 201 Created");
                echo json_encode(array("response"=>"Loja inserida com sucesso!"));
                exit;
            }
        }
        
        break;
    default:
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(array("response"=>"Metodo nao previsto na API"));
        break;
}
