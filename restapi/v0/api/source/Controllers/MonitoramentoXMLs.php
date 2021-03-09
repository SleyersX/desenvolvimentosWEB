<?php

namespace Source\Controllers;

use PDOException;
use Source\Models\MonitoramentoXMLs;
use Source\Models\Connection;
use Source\Models\Validations;

require "../../vendor/autoload.php";
require "../Config.php";

switch($_SERVER["REQUEST_METHOD"]){
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
        $monitoramentoXMLs = new MonitoramentoXMLs();
        if($monitoramentoXMLs->find("shop = :numShop AND pos = :numPos","numShop=$shop&numPos=$pos")->Count()>0){
            $return = array();
            foreach($monitoramentoXMLs->find("shop = :numShop AND pos = :numPos","numShop=$shop&numPos=$pos")->fetch(true) as $shop){
                //Tratamento dos dados vindos do banco
                array_push($return,$shop->data());
            }
            echo json_encode(array("response"=>$return));
        }else{
            echo json_encode(array("response"=>"Loja nao cadastrada no banco de dados!"));
        }
    break;
    case "PUT":
        unset($exists);
        
        $shop =filter_input(INPUT_GET,"shop");
        $token = filter_input(INPUT_GET,"token");
        $pos = filter_input(INPUT_GET,'pos');
        $rejected = filter_input(INPUT_GET,"rejected");
        $answ = filter_input(INPUT_GET,"answ");
        $send = filter_input(INPUT_GET,"send");

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

        //Validamos se existe a loja e o caixa cadastrado no banco de dados 
        $exists = Connection::getInstanceI()->prepare("SELECT * FROM tb_xml_pendentes WHERE shop = '$shop' and pos = '$pos'");
        $exists->execute();
        $ret = $exists->fetchAll();
        $contar = count($ret);
        if ($contar>=1) {
            $maxId = Connection::getInstanceI()->prepare("SELECT MAX(id) AS maxid FROM tb_xml_pendentes WHERE shop = '$shop' AND pos = '$pos'");
            $maxId->execute();
            $id = $maxId->fetchAll();
            $shops = (new MonitoramentoXMLs())->findById($id[0]->maxid);
            $shops->xml_rejected = $rejected;
            $shops->xml_answ = $answ;
            $shops->xml_send = $send;
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
            $shops = new MonitoramentoXMLs();
            $shops->shop = $shop;
            $shops->pos = $pos;
            $shops->xml_rejected = $rejected;
            $shops->xml_answ = $answ;
            $shops->xml_send = $send;
            $shops->data_atualizacao = date('Y-m-d H:i:s');
            $shops->save();

            if($shops->fail()){
                var_dump($shops);
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(array("response"=>$shops->fail()->getMessage()));
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