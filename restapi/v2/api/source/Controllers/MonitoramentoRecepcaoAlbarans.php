<?php

namespace Source\Models;

use PDOException;
use Source\Models\MonitoramentoRecepcaoAlbarans;
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
        
        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Ha campos invalidos no formulario!","fields"=>$errors));
            exit;
        }

        switch ($data->metodo) {
            case '1':
                // Data recepcao
                if(!empty($data->dataRecepcao)){
                    if(!Validations::validationDateReception($data->dataRecepcao,'Y-m-d')){
                        header("HTTP/1.1 400 Bad Request");
                        echo json_encode(array("response"=>"Formato inválido ou não reconhecido como data ['$data->dataRecepcao']."));
                    }else{
                        header("HTTP/1.1 200 OK");
                        $monitoramentoRecepcaoAlbaran = new MonitoramentoRecepcaoAlbarans();
                        if($monitoramentoRecepcaoAlbaran->find("shop = :numShop AND data_recepcao = :dtRecepcao","numShop=$data->shop&dtRecepcao=$data->dataRecepcao")->Count()>0){
                            $return = array();
                            foreach ($monitoramentoRecepcaoAlbaran->find("shop = :numShop AND data_recepcao = :dtRecepcao","numShop=$data->shop&dtRecepcao=$data->dataRecepcao")->fetch(true) as $dados) {
                                array_push($return,$dados->data());
                            }
                            echo json_encode(array("response"=>$return));

                        }else{
                            echo json_encode(array("response"=>"Loja ['$data->shop'] e data recepção ['$data->dataRecepcao'] não encontrados no banco de dados!"));
                        }
                    }
                }else{
                    header("HTTP/1.1 400 Bad Request");
                    echo json_encode(array("response"=>"Metodo ['$data->metodo'], requer ['dataRecepcao']."));
                    exit;
                }
                break;
            case '2':
                // Numero albaran
                if(!empty($data->albaran)){
                    if(!Validations::validationNumberOrder($data->albaran)){
                        header("HTTP/1.1 400 Bad Request");
                        echo json_encode(array("response"=>"Formato inválido ou não reconhecido para o numero de albaran ['$data->albaran']."));
                    }else{
                        header("HTTP/1.1 200 OK");
                        $monitoramentoRecepcaoAlbaran = new MonitoramentoRecepcaoAlbarans();
                        if($monitoramentoRecepcaoAlbaran->find("shop = :numShop AND numero_albaran = :numAlbaran","numShop=$data->shop&numAlbaran=$data->albaran")->Count()>0){
                            $return = array();
                            foreach ($monitoramentoRecepcaoAlbaran->find("shop = :numShop AND numero_albaran = :numAlbaran","numShop=$data->shop&numAlbaran=$data->albaran")->fetch(true) as $dados) {
                                array_push($return,$dados->data());
                            }
                            echo json_encode(array("response"=>$return));

                        }else{
                            echo json_encode(array("response"=>"Loja ['$data->shop'] e número de albaran ['$data->albaran'] não encontrados no banco de dados!"));
                        }
                    }
                }else{
                    header("HTTP/1.1 400 Bad Request");
                    echo json_encode(array("response"=>"Metodo ['$data->metodo'], requer ['albaran']."));
                    exit;
                }
                break;
            case '3':
                // Codigo produto
                if(!empty($data->codigo)){
                    if(!Validations::validationCodigoProducto($data->codigo)){
                        header("HTTP/1.1 400 Bad Request");
                        echo json_encode(array("response"=>"Formato inválido ou não reconhecido para o codigo artigo ['$data->codigo']."));
                    }else{
                        header("HTTP/1.1 200 OK");
                        $monitoramentoRecepcaoAlbaran = new MonitoramentoRecepcaoAlbarans();
                        if($monitoramentoRecepcaoAlbaran->find("shop = :numShop AND codigo = :numCodigo","numShop=$data->shop&numCodigo=$data->codigo")->Count()>0){
                            $return = array();
                            foreach ($monitoramentoRecepcaoAlbaran->find("shop = :numShop AND codigo = :numCodigo","numShop=$data->shop&numCodigo=$data->codigo")->fetch(true) as $dados) {
                                array_push($return,$dados->data());
                            }
                            echo json_encode(array("response"=>$return));

                        }else{
                            echo json_encode(array("response"=>"Loja ['$data->shop'] e codigo artigo ['$data->codigo'] não encontrados no banco de dados!"));
                        }
                    }
                }else{
                    header("HTTP/1.1 400 Bad Request");
                    echo json_encode(array("response"=>"Metodo ['$data->metodo'], requer ['codigo']."));
                    exit;
                }
                break;
            default:
                header("HTTP/1.1 405 Method Not Allowed");
                echo json_encode(array("response"=>"Metodo não esperado!"));
                break;
        }
        break;
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
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(array("response"=>"Metodo nao previsto na API"));
        break;
}