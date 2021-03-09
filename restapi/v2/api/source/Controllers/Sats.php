<?php

namespace Source\Controllers;

use Source\Models\Sats;
use Source\Models\Validations;

require "../../vendor/autoload.php";
require "../Config.php";

switch($_SERVER["REQUEST_METHOD"]){
    case "POST":
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
        if(!Validations::validationSat($data->sat)){
            array_push($errors,"SAT");
        }
        if(!Validations::validationStore($data->store)){
            array_push($errors,"Store");
        }
        if(!Validations::validationPOS($data->pos)){
            array_push($errors,"POS");
        }
        if(!Validations::validationTypeLan($data->typeLan)){
            array_push($errors,"Type LAN");
        }
        if(!Validations::validationIP($data->ip)){
            array_push($errors,"IP");
        }
        if(!Validations::validationMAC($data->mac)){
            array_push($errors,"MAC");
        }
        if(!Validations::validationMask($data->mask)){
            array_push($errors,"MASK");
        }
        if(!Validations::validationGW($data->gw)){
            array_push($errors,"Gateway");
        }
        if(!Validations::validationDNSPrimary($data->dnsPrimary)){
            array_push($errors,"DNS Primary");
        }
        if(!Validations::validationDNSSecundary($data->dnsSecundary)){
            array_push($errors,"DNS Secundary");
        }
        if(!Validations::validationStatusWAN($data->statusWAN)){
            array_push($errors,"Status WAN");
        }
        if(!Validations::validationDisk($data->disk)){
            array_push($errors,"Disk");
        }
        if(!Validations::validationUsedDisk($data->usedDisk)){
            array_push($errors,"Used Disk");
        }
        if(!Validations::validationFirmware($data->firmware)){
            array_push($errors,"Firmware");
        }
        if(!Validations::validationLayout($data->layout)){
            array_push($errors,"Layout");
        }
        
        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Há campos inválidos no formulário!","fields"=>$errors));
            exit;
        }

        var_dump($data);
        $sat = (new Sats())->find("sat = :satNum","satNum=$data->sat")->fetch();
        if($sat){
            header("HTTP/1.1 200 OK");
            echo json_encode(array("response"=>"SAT já cadastrado no banco de dados !"));
            exit;
        }
        $sat = new Sats();
        $sat->sat = $data->sat;
        $sat->loja = $data->store;
        $sat->caixa = $data->pos;
        $sat->tipo_lan = $data->typeLan;
        $sat->ip = $data->ip;
        $sat->mac = $data->mac;
        $sat->mask = $data->mask;
        $sat->gw = $data->gw;
        $sat->dns_1 = $data->dnsPrimary;
        $sat->dns_2 = $data->dnsSecundary;
        $sat->status_wan = $data->statusWAN;
        $sat->disco = $data->disk;
        $sat->disco_usado = $data->usedDisk;
        $sat->firmware = $data->firmware;
        $sat->layout = $data->layout;
        $sat->save();

        if($sat->fail()){
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("response"=>$sat->fail()->getMessage()));
            exit;
        }

        header("HTTP/1.1 201 Created");
        echo json_encode(array("response"=>"SAT cadastrado com sucesso!"));

    break;
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
        if(!Validations::validationSat($data->sat)){
            array_push($errors,"SAT");
        }

        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Há campos inválidos no formulário!","fields"=>$errors));
            exit;
        }

        header("HTTP/1.1 200 OK");
        $sats = new Sats();
        if($sats->find("sat = :satNum","satNum=$data->sat")->Count()>0){
            $return = array();
            foreach($sats->find("sat = :satNum","satNum=$data->sat")->fetch(true) as $sat){
                //Tratamento dos dados vindos do banco
                array_push($return,$sat->data());
            }
            echo json_encode(array("response"=>$return));
        }else{
            echo json_encode(array("response"=>"Nenhum sat cadastrado no banco de dados!"));
        }
    break;
    case "PUT":

        $data = json_decode(file_get_contents("php://input"),false);
        if(!$data){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Nenhum dado informado!"));
            exit;
        }
        //var_dump($data);
        $errors = array();
        if(!Validations::validationString($data->token)){
            array_push($errors,"Token");
        }
        if(!Validations::validationSat($data->sat)){
            array_push($errors,"SAT");
        }
        if(!Validations::validationStore($data->store)){
            array_push($errors,"Store");
        }
        if(!Validations::validationPOS($data->pos)){
            array_push($errors,"POS");
        }
        if(!Validations::validationTypeLan($data->typeLan)){
            array_push($errors,"Type LAN");
        }
        if(!Validations::validationIP($data->ip)){
            array_push($errors,"IP");
        }
        if(!Validations::validationMAC($data->mac)){
            array_push($errors,"MAC");
        }
        if(!Validations::validationMask($data->mask)){
            array_push($errors,"MASK");
        }
        if(!Validations::validationGW($data->gw)){
            array_push($errors,"Gateway");
        }
        if(!Validations::validationDNSPrimary($data->dnsPrimary)){
            array_push($errors,"DNS Primary");
        }
        if(!Validations::validationDNSSecundary($data->dnsSecundary)){
            array_push($errors,"DNS Secundary");
        }
        if(!Validations::validationStatusWAN($data->statusWAN)){
            array_push($errors,"Status WAN");
        }
        if(!Validations::validationDisk($data->disk)){
            array_push($errors,"Disk");
        }
        if(!Validations::validationUsedDisk($data->usedDisk)){
            array_push($errors,"Used Disk");
        }
        if(!Validations::validationFirmware($data->firmware)){
            array_push($errors,"Firmware");
        }
        if(!Validations::validationLayout($data->layout)){
            array_push($errors,"Layout");
        }

        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Há campos inválidos no formulário!","fields"=>$errors));
            exit;
        }

        $sats = (new Sats())->find("sat = :satNum","satNum=$data->sat")->fetch();
        
        if(!$sats){
            header("HTTP/1.1 200 OK");
            echo json_encode(array("response"=>"Nenhum SAT foi localizado com número informado!"));
            exit;
        }
        $sats->sat = $data->sat;
        $sats->loja = $data->store;
        $sats->caixa = $data->pos;
        $sats->tipo_lan = $data->typeLan;
        $sats->ip = $data->ip;
        $sats->mac = $data->mac;
        $sats->mask = $data->mask;
        $sats->gw = $data->gw;
        $sats->dns_1 = $data->dnsPrimary;
        $sats->dns_2 = $data->dnsSecundary;
        $sats->status_wan = $data->statusWAN;
        $sats->disco = $data->disk;
        $sats->disco_usado = $data->usedDisk;
        $sats->firmware = $data->firmware;
        $sats->layout = $data->layout;
        $sats->save();
        if($sats->fail()){
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("response"=>$sats->fail()->getMessage()));
            exit;
        }
        header("HTTP/1.1 201 Created");
        echo json_encode(array("response"=>"SAT atualizado com sucesso!"));
    break;
    case "DELETE":
        $satNum = filter_input(INPUT_GET,"id");
        if(!$satNum){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"ID não informado"));
            exit;
        }

        $data = json_decode(file_get_contents('php://input'),false);
        if(!$data){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Nenhum dado informado!"));
            exit; 
        }

        $errors = array();
        if(!Validations::validationString($data->token)){
            array_push($errors,"Token");
        }

        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Há campos inválidos no formulário!","fields"=>$errors));
            exit;
        }

        $sat = (new Sats())->find("sat = :satNum","satNum=$satNum")->fetch();
        if(!$sat){
            header("HTTP/1.1 200 OK");
            echo json_encode(array("response"=>"Nenhum SAT foi localizado com número informado!"));
            exit;
        }
        $verify = $sat->destroy();
        if($sat->fail()){
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("response"=>$sat->fail()->getMessage()));
            exit;
        }
        if($verify){
            header("HTTP/1.1 200 OK");
            echo json_encode(array("response"=>"SAT removido com sucesso!"));
        }else{
            header("HTTP/1.1 200 OK");
            echo json_encode(array("response"=>"Nenhum SAT pode ser removido!"));
        }
        
    break;
    default:
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(array("response"=>"Método não previsto na API"));
    break;
}
