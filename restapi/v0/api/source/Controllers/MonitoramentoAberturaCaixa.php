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
        $monitoramentoAberturaCaixa = new MonitoramentoAberturaCaixas();
        if($monitoramentoAberturaCaixa->find("shop = :numShop AND pos = :numPos","numShop=$shop&numPos=$pos")->Count()>0){
            $return = array();
            foreach($monitoramentoAberturaCaixa->find("shop = :numShop AND pos = :numPos","numShop=$shop&numPos=$pos")->fetch(true) as $shop){
                //Tratamento dos dados vindos do banco
                array_push($return,$shop->data());
            }
            echo json_encode(array("response"=>$return));
        }else{
            echo json_encode(array("response"=>"Loja nao cadastrada no banco de dados!"));
        }
        break;
    case "PUT":
        break;
    default:
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(array("response"=>"Metodo nao previsto na API"));
        break;
}
