<?php

namespace Source\Models;

use PDOException;
use Source\Models\MonitoramentoRecepcaoAlbarans;
use Source\Models\Connection;
use Source\Models\Validations;

require "../../vendor/autoload.php";
require "../Config.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case "PUT":
        unset($exists);
        
        $shop =filter_input(INPUT_GET,"shop");
        $token = filter_input(INPUT_GET,"token");
        $dataAlbaran = filter_input(INPUT_GET,'dataAlbaran');
        $albaran = filter_input(INPUT_GET,"albaran");
        $tipoAlbaran = filter_input(INPUT_GET,"tipoAlbaran");
        $codComplementario = filter_input(INPUT_GET,"codComplementario");
        $codigo = filter_input(INPUT_GET,"artigo");
        $qntdUndArtigo = filter_input(INPUT_GET,"qntdUndArtigo");
        $qntdKGArtigo = filter_input(INPUT_GET,"qntdKGArtigo");
        $qntdPedidaArtigo = filter_input(INPUT_GET,"qntdPedidaArtigo");

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

        $exists = Connection::getInstanceI()->prepare("SELECT * FROM tb_recepcao_albarans WHERE shop = '$shop' AND numero_albaran = '$albaran' AND codigo = '$codigo'");
        $exists->execute();
        $ret = $exists->fetchAll();
        $contar = count($ret);
        if ($contar>=1){
            header("HTTP/1.1 200 OK");
            echo json_encode(array("response"=>"Loja ['$shop']:['$albaran']:['$codigo'], já existem no banco de dados!"));
            exit;
        }else{
            $data = new MonitoramentoRecepcaoAlbarans();
            $data->shop = $shop;
            $data->data_recepcao = $dataAlbaran;
            $data->numero_albaran = $albaran;
            $data->tipo_pedido = $tipoAlbaran;
            $data->cod_complementario = $codComplementario;
            $data->codigo = $codigo;
            $data->qntd_unid_recebida = $qntdUndArtigo;
            $data->qntd_kilo_recebida = $qntdKGArtigo / 1000;
            $data->qntd_pedida = $qntdPedidaArtigo;
            $data->save();

            if($data->fail()){
                var_dump($data);
                header("HTTP/1.1 500 Internal Server Error");
                echo json_encode(array("response"=>$data->fail()->getMessage()));
                exit;
            }

            header("HTTP/1.1 201 Created");
            echo json_encode(array("response"=>"Loja ['$shop']:['$albaran']:['$codigo'], inserida com sucesso no banco de dados!"));
            exit;
        }
        break;
    default:
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(array("response"=>"Metodo nao previsto na API"));
        break;
}