<?php

namespace Source\Controllers;

use PDOException;
use Source\Models\Sats;
use Source\Models\Validations;
use Source\Models\Connection;

require "../../vendor/autoload.php";
require "../Config.php";

switch($_SERVER["REQUEST_METHOD"]){
    case "POST":
        //Array INPUT_GET para validação se algum dado na URL esta vazio
        $arrfilters = array(
            $sat => filter_input(INPUT_GET,"sat"),
            $token => filter_input(INPUT_GET,"token"),
            $store => filter_input(INPUT_GET,"store"),
            $pos => filter_input(INPUT_GET,"pos"),
            $typeLan => filter_input(INPUT_GET,"typeLan"),
            $ip => filter_input(INPUT_GET,"ip"),
            $mac => filter_input(INPUT_GET,"mac"),
            $mask => filter_input(INPUT_GET,"mask"),
            $gw => filter_input(INPUT_GET,"gw"),
            $dnsPrimary => filter_input(INPUT_GET,"dnsPrimary"),
            $dnsSecundary => filter_input(INPUT_GET,"dnsSecundary"),
            $statusWAN => filter_input(INPUT_GET,"statusWAN"),
            $disk => filter_input(INPUT_GET,"disk"),
            $usedDisk => filter_input(INPUT_GET,"usedDisk"),
            $firmware => filter_input(INPUT_GET,"firmware"),
            $layout => filter_input(INPUT_GET,"layout")
        );

        $filters = filter_input_array(INPUT_GET,$arrfilters);
        foreach($arrfilters as $filter){
            if(!$filter){
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(array("response"=>"Nenhum dado informado!"));
                exit; 
            }
        }

        $sat = filter_input(INPUT_GET,"sat");
        $token = filter_input(INPUT_GET,"token");
        $store = filter_input(INPUT_GET,"store");
        $pos = filter_input(INPUT_GET,"pos");
        $typeLan = filter_input(INPUT_GET,"typeLan");
        $ip = filter_input(INPUT_GET,"ip");
        $mac = filter_input(INPUT_GET,"mac");
        $mask = filter_input(INPUT_GET,"mask");
        $gw = filter_input(INPUT_GET,"gw");
        $dnsPrimary = filter_input(INPUT_GET,"dnsPrimary");
        $dnsSecundary = filter_input(INPUT_GET,"dnsSecundary");
        $statusWAN = filter_input(INPUT_GET,"statusWAN");
        $disk = filter_input(INPUT_GET,"disk");
        $usedDisk = filter_input(INPUT_GET,"usedDisk");
        $firmware = filter_input(INPUT_GET,"firmware");
        $layout = filter_input(INPUT_GET,"layout");

        $errors = array();
        if(!Validations::validationString($token)){
            array_push($errors,"Token");
        }
        if(!Validations::validationSat($sat)){
            array_push($errors,"SAT");
        }
        if(!Validations::validationStore($store)){
            array_push($errors,"Store");
        }
        if(!Validations::validationPOS($pos)){
            array_push($errors,"POS");
        }
        if(!Validations::validationTypeLan($typeLan)){
            array_push($errors,"Type LAN");
        }
        if(!Validations::validationIP($ip)){
            array_push($errors,"IP");
        }
        if(!Validations::validationMAC($mac)){
            array_push($errors,"MAC");
        }
        if(!Validations::validationMask($mask)){
            array_push($errors,"MASK");
        }
        if(!Validations::validationGW($gw)){
            array_push($errors,"Gateway");
        }
        if(!Validations::validationDNSPrimary($dnsPrimary)){
            array_push($errors,"DNS Primary");
        }
        if(!Validations::validationDNSSecundary($dnsSecundary)){
            array_push($errors,"DNS Secundary");
        }
        if(!Validations::validationStatusWAN($statusWAN)){
            array_push($errors,"Status WAN");
        }
        if(!Validations::validationDisk($disk)){
            array_push($errors,"Disk");
        }
        if(!Validations::validationUsedDisk($usedDisk)){
            array_push($errors,"Used Disk");
        }
        if(!Validations::validationFirmware($firmware)){
            array_push($errors,"Firmware");
        }
        if(!Validations::validationLayout($layout)){
            array_push($errors,"Layout");
        }
        
        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Ha campos invalidos no formulario!","fields"=>$errors));
            exit;
        }

        $sats = (new Sats())->find("sat = :satNum","satNum=$sat")->fetch();
        if($sats){
            header("HTTP/1.1 200 OK");
            echo json_encode(array("response"=>"SAT ja cadastrado no banco de dados !"));
            exit;
        }
        $sats = new Sats();
        $sats->sat = $sat;
        $sats->loja = $store;
        $sats->caixa = $pos;
        $sats->tipo_lan = $typeLan;
        $sats->ip = $ip;
        $sats->mac = $mac;
        $sats->mask = $mask;
        $sats->gw = $gw;
        $sats->dns_1 = $dnsPrimary;
        $sats->dns_2 = $dnsSecundary;
        $sats->status_wan = $statusWAN;
        $sats->disco = $disk;
        $sats->disco_usado = $usedDisk;
        $sats->firmware = $firmware;
        $sats->layout = $layout;
        $sats->status = 'Ativo';
        $sats->save();

        if($sats->fail()){
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(array("response"=>$sats->fail()->getMessage()));
            exit;
        }

        header("HTTP/1.1 201 Created");
        echo json_encode(array("response"=>"SAT cadastrado com sucesso!"));

    break;
    case "GET":
        if(!filter_input(INPUT_GET,"sat") || !filter_input(INPUT_GET,"token")){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Nenhum dado informado!"));
            exit; 
        }

        $sat =filter_input(INPUT_GET,"sat");
        $token = filter_input(INPUT_GET,"token");
        
        $errors = array();
        if(!Validations::validationString($token)){
            array_push($errors,"Token");
        }
        if(!Validations::validationSat($sat)){
            array_push($errors,"SAT");
        }

        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Há campos inválidos no formulário!","fields"=>$errors));
            exit;
        }

        header("HTTP/1.1 200 OK");
        $sats = new Sats();
        if($sats->find("sat = :satNum","satNum=$sat")->Count()>0){
            $return = array();
            foreach($sats->find("sat = :satNum","satNum=$sat")->fetch(true) as $sat){
                //Tratamento dos dados vindos do banco
                array_push($return,$sat->data());
            }
            echo json_encode(array("response"=>$return));
        }else{
            echo json_encode(array("response"=>"Nenhum sat cadastrado no banco de dados!"));
        }
    break;
    case "PUT":
        unset($exists);
        
        $token = filter_input(INPUT_GET,"token");
        $returnStateOper = filter_input(INPUT_GET,"returnStateOper");
        $msgStateOper = filter_input(INPUT_GET,"msgStateOper");
        $avisoSefaz = filter_input(INPUT_GET,"avisoSefaz");
        $msgAvisoSefaz = filter_input(INPUT_GET,"msgAvisoSefaz");
        $sat = filter_input(INPUT_GET,"sat");
        $store = filter_input(INPUT_GET,"store");
        $pos = filter_input(INPUT_GET,"pos");
        $typeLan = filter_input(INPUT_GET,"typeLan");
        $ip = filter_input(INPUT_GET,"ip");
        $mac = filter_input(INPUT_GET,"mac");
        $mask = filter_input(INPUT_GET,"mask");
        $gw = filter_input(INPUT_GET,"gw");
        $dnsPrimary = filter_input(INPUT_GET,"dnsPrimary");
        $dnsSecundary = filter_input(INPUT_GET,"dnsSecundary");
        $statusWAN = filter_input(INPUT_GET,"statusWAN");
        $nivelBatery = filter_input(INPUT_GET,"nivelBatery");
        $disk = filter_input(INPUT_GET,"disk");
        $usedDisk = filter_input(INPUT_GET,"usedDisk");
        $dateHAtual = filter_input(INPUT_GET,"dateHAtual");
        $firmware = filter_input(INPUT_GET,"firmware");
        $layout = filter_input(INPUT_GET,"layout");
        $lastCFeEmis = filter_input(INPUT_GET,"lastCFeEmis");
        $primaryCFeMemory = filter_input(INPUT_GET,"primaryCFeMemory");
        $lastCFeMemory = filter_input(INPUT_GET,"lastCFeMemory");
        $cfesEmitidos = filter_input(INPUT_GET,"cfesEmitidos");
        $cfesMemory = filter_input(INPUT_GET,"cfesMemory");
        $dateHRTransm = filter_input(INPUT_GET,"dateHRTransm");
        $dateHRComuni = filter_input(INPUT_GET,"dateHRComuni");
        $certEmisao = filter_input(INPUT_GET,"certEmisao");
        $certVencimento = filter_input(INPUT_GET,"certVencimento");
        $estadoOperacao = filter_input(INPUT_GET,"estadoOperacao");
        $satEmFalha = filter_input(INPUT_GET,"satEmFalha");
        $modeloSat="";

        $errors = array();
        if(!Validations::validationString($token)){
            array_push($errors,"Token");
        }
        if(!Validations::validationSat($sat)){
            array_push($errors,"SAT");
        }
        if(!Validations::validationStore($store)){
            array_push($errors,"Store");
        }
        if(!Validations::validationPOS($pos)){
            array_push($errors,"POS");
        }
        if(!Validations::validationTypeLan($typeLan)){
            array_push($errors,"Type LAN");
        }
        if(!Validations::validationIP($ip)){
            array_push($errors,"IP");
        }
        if(!Validations::validationMAC($mac)){
            array_push($errors,"MAC");
        }
        if(!Validations::validationMask($mask)){
            array_push($errors,"MASK");
        }
        if(!Validations::validationGW($gw)){
            array_push($errors,"Gateway");
        }
        if(!Validations::validationDNSPrimary($dnsPrimary)){
            array_push($errors,"DNS Primary");
        }
        if(!Validations::validationDNSSecundary($dnsSecundary)){
            array_push($errors,"DNS Secundary");
        }
        if(!Validations::validationStatusWAN($statusWAN)){
            array_push($errors,"Status WAN");
        }
        if(!Validations::validationDisk($disk)){
            array_push($errors,"Disk");
        }
        if(!Validations::validationUsedDisk($usedDisk)){
            array_push($errors,"Used Disk");
        }
        if(!Validations::validationFirmware($firmware)){
            array_push($errors,"Firmware");
        }
        if(!Validations::validationLayout($layout)){
            array_push($errors,"Layout");
        }
        
        if(count($errors)>0){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"Ha campos invalidos no formulario!","fields"=>$errors));
            exit;
        }
        
        if($firmware == "01.00.00"){
            $modeloSat="SAT-2.0";
        }elseif($firmware == "01.01.01"){
            $modeloSat="SAT-2.0";
        }elseif($firmware == "01.01.00" && $layout == "00.08"){
            $modeloSat="SAT-2.0";
        }elseif($firmware == "01.01.00" && $layout == "00.07" || $layout == "00.06"){
            $modeloSat="SAT-1.0";
        }elseif($firmware == "01.02.00"){
            $modeloSat="SAT-1.0";
        }elseif($firmware == "01.03.00"){
            $modeloSat="SAT-1.0";
        }elseif($firmware == "01.04.00"){
            $modeloSat="SAT-1.0";
        }elseif($firmware == "01.04.01"){
            $modeloSat="SAT-1.0";
        }elseif($firmware == "01.05.00"){
            $modeloSat="SAT-1.0";
        }elseif($firmware == "01.06.00"){
            $modeloSat="SAT-1.0";
        }elseif($firmware == "01.06.01"){
            $modeloSat="SAT-1.0";
        }


        //Validamos se existe a loja e o caixa cadastrado no banco de dados 
        $exists = Connection::getInstanceI()->prepare("SELECT * FROM tb_hml_sat WHERE sat = '$sat'");
        $exists->execute();
        $ret = $exists->fetchAll();
        $contar = count($ret);
        if($contar>=1){
            $inativo = Connection::getInstanceI()->prepare("UPDATE tb_hml_sat SET status = 'Inativo' WHERE sat = '$sat'");
            $inativo->execute();
            if($inativo->rowCount() > 0) {
                $existPos = Connection::getInstanceI()->prepare("SELECT id FROM tb_hml_sat WHERE Loja = '$store' AND caixa = '$pos'");
                $existPos->execute();
                //var_dump($existPos->fetch());
                $countPos = $existPos->rowCount();
                if($countPos == 0){
                    $sats = new Sats();
                    $sats->retorno_status_operacional = $returnStateOper;
                    $sats->msg_status_operacional = $msgStateOper;
                    $sats->aviso_sefaz = $avisoSefaz;
                    $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                    $sats->sat = $sat;
                    $sats->loja = $store;
                    $sats->caixa = $pos;
                    $sats->tipo_lan = $typeLan;
                    $sats->ip = $ip;
                    $sats->mac = $mac;
                    $sats->mask = $mask;
                    $sats->gw = $gw;
                    $sats->dns_1 = $dnsPrimary;
                    $sats->dns_2 = $dnsSecundary;
                    $sats->status_wan = $statusWAN;
                    $sats->nivel_bateria = $nivelBatery;
                    $sats->disco = $disk;
                    $sats->disco_usado = $usedDisk;
                    $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                    $sats->firmware = $firmware;
                    $sats->layout = $layout;
                    $sats->ultimo_cfe = $lastCFeEmis;
                    $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                    $sats->ultimo_cfe_memoria = $lastCFeMemory;
                    $sats->numero_cfes_emitidos = $cfesEmitidos;
                    $sats->numeros_cfes_memoria = $cfesMemory;
                    $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                    $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                    $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                    $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                    $sats->estado_operacao = $estadoOperacao;
                    $sats->falha = $satEmFalha;
                    $sats->status = 'Ativo';
                    $sats->modelo_sat = $modeloSat;
                    $sats->data_inclusao = date('Y-m-d H:i:s');
                    $sats->data_atualizacao = date('Y-m-d H:i:s');
                    $sats->save();

                    if($sats->fail()){
                        header("HTTP/1.1 500 Internal Server Error");
                        echo json_encode(array("response"=>$sats->fail()->getMessage()));
                        exit;
                    }
        
                    header("HTTP/1.1 201 Created");
                    echo json_encode(array("response"=>"SAT cadastrado com sucesso!"));
                    exit;
                }elseif($countPos == 1){
                    $nBDSat = Connection::getInstanceI()->prepare("SELECT sat FROM tb_hml_sat WHERE Loja = '$store' AND caixa = '$pos'");
                    $nBDSat->execute();
                    $nSat = $nBDSat->fetchAll();
                    if($nSat[0]->sat == $sat){
                        $maxId = Connection::getInstanceI()->prepare("SELECT MAX(id) AS maxid FROM tb_hml_sat WHERE Loja = '$store' AND caixa = '$pos' AND sat = '$sat'");
                        $maxId->execute();
                        $id = $maxId->fetchAll();
                        $sats = (new Sats())->findById($id[0]->maxid);
                        $sats->retorno_status_operacional = $returnStateOper;
                        $sats->msg_status_operacional = $msgStateOper;
                        $sats->aviso_sefaz = $avisoSefaz;
                        $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                        $sats->sat = $sat;
                        $sats->loja = $store;
                        $sats->caixa = $pos;
                        $sats->tipo_lan = $typeLan;
                        $sats->ip = $ip;
                        $sats->mac = $mac;
                        $sats->mask = $mask;
                        $sats->gw = $gw;
                        $sats->dns_1 = $dnsPrimary;
                        $sats->dns_2 = $dnsSecundary;
                        $sats->status_wan = $statusWAN;
                        $sats->nivel_bateria = $nivelBatery;
                        $sats->disco = $disk;
                        $sats->disco_usado = $usedDisk;
                        $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                        $sats->firmware = $firmware;
                        $sats->layout = $layout;
                        $sats->ultimo_cfe = $lastCFeEmis;
                        $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                        $sats->ultimo_cfe_memoria = $lastCFeMemory;
                        $sats->numero_cfes_emitidos = $cfesEmitidos;
                        $sats->numeros_cfes_memoria = $cfesMemory;
                        $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                        $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                        $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                        $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                        $sats->estado_operacao = $estadoOperacao;
                        $sats->falha = $satEmFalha;
                        $sats->status = 'Ativo';
                        $sats->modelo_sat = $modeloSat;
                        $sats->data_atualizacao = date('Y-m-d H:i:s');
                        $sats->save();

                        if($sats->fail()){
                            header("HTTP/1.1 500 Internal Server Error");
                            echo json_encode(array("response"=>$sats->fail()->getMessage()));
                            exit;
                        }
            
                        header("HTTP/1.1 201 Created");
                        echo json_encode(array("response"=>"SAT atualizado com sucesso!"));
                        exit;
                    }else{
                        $updInativo = Connection::getInstanceI()->prepare("UPDATE tb_hml_sat SET status = 'Inativo' WHERE sat = '$sat'");
                        $updInativo->execute();
                        $sats = new Sats();
                        $sats->retorno_status_operacional = $returnStateOper;
                        $sats->msg_status_operacional = $msgStateOper;
                        $sats->aviso_sefaz = $avisoSefaz;
                        $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                        $sats->sat = $sat;
                        $sats->loja = $store;
                        $sats->caixa = $pos;
                        $sats->tipo_lan = $typeLan;
                        $sats->ip = $ip;
                        $sats->mac = $mac;
                        $sats->mask = $mask;
                        $sats->gw = $gw;
                        $sats->dns_1 = $dnsPrimary;
                        $sats->dns_2 = $dnsSecundary;
                        $sats->status_wan = $statusWAN;
                        $sats->nivel_bateria = $nivelBatery;
                        $sats->disco = $disk;
                        $sats->disco_usado = $usedDisk;
                        $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                        $sats->firmware = $firmware;
                        $sats->layout = $layout;
                        $sats->ultimo_cfe = $lastCFeEmis;
                        $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                        $sats->ultimo_cfe_memoria = $lastCFeMemory;
                        $sats->numero_cfes_emitidos = $cfesEmitidos;
                        $sats->numeros_cfes_memoria = $cfesMemory;
                        $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                        $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                        $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                        $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                        $sats->estado_operacao = $estadoOperacao;
                        $sats->falha = $satEmFalha;
                        $sats->status = 'Ativo';
                        $sats->modelo_sat = $modeloSat;
                        $sats->data_inclusao = date('Y-m-d H:i:s');
                        $sats->data_atualizacao = date('Y-m-d H:i:s');
                        //var_dump($sats);
                        $sats->save();

                        if($sats->fail()){
                            header("HTTP/1.1 500 Internal Server Error");
                            echo json_encode(array("response"=>$sats->fail()->getMessage()));
                            exit;
                        }
            
                        header("HTTP/1.1 201 Created");
                        echo json_encode(array("response"=>"SAT cadastrado com sucesso!"));
                        exit;
                    }
                }elseif($countPos >= 2){
                    $inativo = Connection::getInstanceI()->prepare("UPDATE tb_hml_sat SET status = 'Inativo' WHERE Loja = '$store' AND caixa = '$pos'");
                    $inativo->execute();
                    $maxId = Connection::getInstanceI()->prepare("SELECT MAX(id) AS maxid FROM tb_hml_sat WHERE Loja = '$store' AND caixa = '$pos'");
                    $maxId->execute();
                    $id = $maxId->fetchAll();
                    $nBDSat = Connection::getInstanceI()->prepare("SELECT sat FROM tb_hml_sat WHERE id = ".$id[0]->maxid."");
                    $nBDSat->execute();
                    $nSat = $nBDSat->fetchAll();
                    if($nSat[0]->sat == $sat){
                        $sats = (new Sats())->findById($id[0]->maxid);
                        $sats->retorno_status_operacional = $returnStateOper;
                        $sats->msg_status_operacional = $msgStateOper;
                        $sats->aviso_sefaz = $avisoSefaz;
                        $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                        $sats->sat = $sat;
                        $sats->loja = $store;
                        $sats->caixa = $pos;
                        $sats->tipo_lan = $typeLan;
                        $sats->ip = $ip;
                        $sats->mac = $mac;
                        $sats->mask = $mask;
                        $sats->gw = $gw;
                        $sats->dns_1 = $dnsPrimary;
                        $sats->dns_2 = $dnsSecundary;
                        $sats->status_wan = $statusWAN;
                        $sats->nivel_bateria = $nivelBatery;
                        $sats->disco = $disk;
                        $sats->disco_usado = $usedDisk;
                        $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                        $sats->firmware = $firmware;
                        $sats->layout = $layout;
                        $sats->ultimo_cfe = $lastCFeEmis;
                        $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                        $sats->ultimo_cfe_memoria = $lastCFeMemory;
                        $sats->numero_cfes_emitidos = $cfesEmitidos;
                        $sats->numeros_cfes_memoria = $cfesMemory;
                        $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                        $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                        $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                        $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                        $sats->estado_operacao = $estadoOperacao;
                        $sats->falha = $satEmFalha;
                        $sats->status = 'Ativo';
                        $sats->modelo_sat = $modeloSat;
                        $sats->data_atualizacao = date('Y-m-d H:i:s');
                        //var_dump($sats);
                        $sats->save();

                        if($sats->fail()){
                            header("HTTP/1.1 500 Internal Server Error");
                            echo json_encode(array("response"=>$sats->fail()->getMessage()));
                            exit;
                        }
            
                        header("HTTP/1.1 201 Created");
                        echo json_encode(array("response"=>"SAT atualizado com sucesso!"));
                        exit;
                    }else{
                        $inativoPos = Connection::getInstanceI()->prepare("UPDATE tb_hml_sat SET status = 'Inativo' WHERE Loja = '$store' AND caixa = '$pos'");
                        $inativoPos->execute();
                        $updInativo = Connection::getInstanceI()->prepare("UPDATE tb_hml_sat SET status = 'Inativo' WHERE sat = '$sat'");
                        $updInativo->execute();
                        $sats = new Sats();
                        $sats->retorno_status_operacional = $returnStateOper;
                        $sats->msg_status_operacional = $msgStateOper;
                        $sats->aviso_sefaz = $avisoSefaz;
                        $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                        $sats->sat = $sat;
                        $sats->loja = $store;
                        $sats->caixa = $pos;
                        $sats->tipo_lan = $typeLan;
                        $sats->ip = $ip;
                        $sats->mac = $mac;
                        $sats->mask = $mask;
                        $sats->gw = $gw;
                        $sats->dns_1 = $dnsPrimary;
                        $sats->dns_2 = $dnsSecundary;
                        $sats->status_wan = $statusWAN;
                        $sats->nivel_bateria = $nivelBatery;
                        $sats->disco = $disk;
                        $sats->disco_usado = $usedDisk;
                        $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                        $sats->firmware = $firmware;
                        $sats->layout = $layout;
                        $sats->ultimo_cfe = $lastCFeEmis;
                        $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                        $sats->ultimo_cfe_memoria = $lastCFeMemory;
                        $sats->numero_cfes_emitidos = $cfesEmitidos;
                        $sats->numeros_cfes_memoria = $cfesMemory;
                        $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                        $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                        $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                        $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                        $sats->estado_operacao = $estadoOperacao;
                        $sats->falha = $satEmFalha;
                        $sats->status = 'Ativo';
                        $sats->modelo_sat = $modeloSat;
                        $sats->data_inclusao = date('Y-m-d H:i:s');
                        $sats->data_atualizacao = date('Y-m-d H:i:s');
                        //var_dump($sats);
                        $sats->save();

                        if($sats->fail()){
                            header("HTTP/1.1 500 Internal Server Error");
                            echo json_encode(array("response"=>$sats->fail()->getMessage()));
                            exit;
                        }
            
                        header("HTTP/1.1 201 Created");
                        echo json_encode(array("response"=>"SAT cadastrado com sucesso!"));
                        exit;
                    }
                }else{
                    header("HTTP/1.1 200 OK");
                    echo json_encode(array("response"=>"Saiu por aqui!"));
                    exit;
                }               
            }else{
                $existPos = Connection::getInstanceI()->prepare("SELECT id FROM tb_hml_sat WHERE Loja = '$store' AND caixa = '$pos'");
                $existPos->execute();
                //var_dump($existPos->fetch());
                $countPos = $existPos->rowCount();
                if($countPos == 0){
                    $sats = new Sats();
                    $sats->retorno_status_operacional = $returnStateOper;
                    $sats->msg_status_operacional = $msgStateOper;
                    $sats->aviso_sefaz = $avisoSefaz;
                    $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                    $sats->sat = $sat;
                    $sats->loja = $store;
                    $sats->caixa = $pos;
                    $sats->tipo_lan = $typeLan;
                    $sats->ip = $ip;
                    $sats->mac = $mac;
                    $sats->mask = $mask;
                    $sats->gw = $gw;
                    $sats->dns_1 = $dnsPrimary;
                    $sats->dns_2 = $dnsSecundary;
                    $sats->status_wan = $statusWAN;
                    $sats->nivel_bateria = $nivelBatery;
                    $sats->disco = $disk;
                    $sats->disco_usado = $usedDisk;
                    $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                    $sats->firmware = $firmware;
                    $sats->layout = $layout;
                    $sats->ultimo_cfe = $lastCFeEmis;
                    $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                    $sats->ultimo_cfe_memoria = $lastCFeMemory;
                    $sats->numero_cfes_emitidos = $cfesEmitidos;
                    $sats->numeros_cfes_memoria = $cfesMemory;
                    $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                    $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                    $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                    $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                    $sats->estado_operacao = $estadoOperacao;
                    $sats->falha = $satEmFalha;
                    $sats->status = 'Ativo';
                    $sats->modelo_sat = $modeloSat;
                    $sats->data_inclusao = date('Y-m-d H:i:s');
                    $sats->data_atualizacao = date('Y-m-d H:i:s');
                    //var_dump($sats);
                    $sats->save();

                    if($sats->fail()){
                        header("HTTP/1.1 500 Internal Server Error");
                        echo json_encode(array("response"=>$sats->fail()->getMessage()));
                        exit;
                    }
        
                    header("HTTP/1.1 201 Created");
                    echo json_encode(array("response"=>"SAT cadastrado com sucesso!"));
                    exit;
                }elseif($countPos == 1){
                    $nBDSat = Connection::getInstanceI()->prepare("SELECT sat FROM tb_hml_sat WHERE Loja = '$store' AND caixa = '$pos'");
                    $nBDSat->execute();
                    $nSat = $nBDSat->fetchAll();
                    if($nSat[0]->sat == $sat){
                        $maxId = Connection::getInstanceI()->prepare("SELECT MAX(id) AS maxid FROM tb_hml_sat WHERE Loja = '$store' AND caixa = '$pos' AND sat = '$sat'");
                        $maxId->execute();
                        $id = $maxId->fetchAll();
                        $sats = (new Sats())->findById($id[0]->maxid);
                        $sats->retorno_status_operacional = $returnStateOper;
                        $sats->msg_status_operacional = $msgStateOper;
                        $sats->aviso_sefaz = $avisoSefaz;
                        $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                        $sats->sat = $sat;
                        $sats->loja = $store;
                        $sats->caixa = $pos;
                        $sats->tipo_lan = $typeLan;
                        $sats->ip = $ip;
                        $sats->mac = $mac;
                        $sats->mask = $mask;
                        $sats->gw = $gw;
                        $sats->dns_1 = $dnsPrimary;
                        $sats->dns_2 = $dnsSecundary;
                        $sats->status_wan = $statusWAN;
                        $sats->nivel_bateria = $nivelBatery;
                        $sats->disco = $disk;
                        $sats->disco_usado = $usedDisk;
                        $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                        $sats->firmware = $firmware;
                        $sats->layout = $layout;
                        $sats->ultimo_cfe = $lastCFeEmis;
                        $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                        $sats->ultimo_cfe_memoria = $lastCFeMemory;
                        $sats->numero_cfes_emitidos = $cfesEmitidos;
                        $sats->numeros_cfes_memoria = $cfesMemory;
                        $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                        $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                        $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                        $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                        $sats->estado_operacao = $estadoOperacao;
                        $sats->falha = $satEmFalha;
                        $sats->status = 'Ativo';
                        $sats->modelo_sat = $modeloSat;
                        $sats->data_atualizacao = date('Y-m-d H:i:s');
                        //var_dump($sats);
                        $sats->save();

                        if($sats->fail()){
                            header("HTTP/1.1 500 Internal Server Error");
                            echo json_encode(array("response"=>$sats->fail()->getMessage()));
                            exit;
                        }
            
                        header("HTTP/1.1 201 Created");
                        echo json_encode(array("response"=>"SAT atualizado com sucesso!"));
                        exit;
                    }else{
                        $inativoPos = Connection::getInstanceI()->prepare("UPDATE tb_hml_sat SET status = 'Inativo' WHERE Loja = '$store' AND caixa = '$pos'");
                        $inativoPos->execute();
                        $updInativo = Connection::getInstanceI()->prepare("UPDATE tb_hml_sat SET status = 'Inativo' WHERE sat = '$sat'");
                        $updInativo->execute();
                        $sats = new Sats();
                        $sats->retorno_status_operacional = $returnStateOper;
                        $sats->msg_status_operacional = $msgStateOper;
                        $sats->aviso_sefaz = $avisoSefaz;
                        $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                        $sats->sat = $sat;
                        $sats->loja = $store;
                        $sats->caixa = $pos;
                        $sats->tipo_lan = $typeLan;
                        $sats->ip = $ip;
                        $sats->mac = $mac;
                        $sats->mask = $mask;
                        $sats->gw = $gw;
                        $sats->dns_1 = $dnsPrimary;
                        $sats->dns_2 = $dnsSecundary;
                        $sats->status_wan = $statusWAN;
                        $sats->nivel_bateria = $nivelBatery;
                        $sats->disco = $disk;
                        $sats->disco_usado = $usedDisk;
                        $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                        $sats->firmware = $firmware;
                        $sats->layout = $layout;
                        $sats->ultimo_cfe = $lastCFeEmis;
                        $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                        $sats->ultimo_cfe_memoria = $lastCFeMemory;
                        $sats->numero_cfes_emitidos = $cfesEmitidos;
                        $sats->numeros_cfes_memoria = $cfesMemory;
                        $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                        $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                        $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                        $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                        $sats->estado_operacao = $estadoOperacao;
                        $sats->falha = $satEmFalha;
                        $sats->status = 'Ativo';
                        $sats->modelo_sat = $modeloSat;
                        $sats->data_inclusao = date('Y-m-d H:i:s');
                        $sats->data_atualizacao = date('Y-m-d H:i:s');
                        //var_dump($sats);
                        $sats->save();

                        if($sats->fail()){
                            header("HTTP/1.1 500 Internal Server Error");
                            echo json_encode(array("response"=>$sats->fail()->getMessage()));
                            exit;
                        }
            
                        header("HTTP/1.1 201 Created");
                        echo json_encode(array("response"=>"SAT cadastrado com sucesso!"));
                        exit;
                    }
                }elseif($countPos >= 2){
                    $inativo = Connection::getInstanceI()->prepare("UPDATE tb_hml_sat SET status = 'Inativo' WHERE Loja = '$store' AND caixa = '$pos'");
                    $inativo->execute();
                    $maxId = Connection::getInstanceI()->prepare("SELECT MAX(id) AS maxid FROM tb_hml_sat WHERE Loja = '$store' AND caixa = '$pos'");
                    $maxId->execute();
                    $id = $maxId->fetchAll();
                    $nBDSat = Connection::getInstanceI()->prepare("SELECT sat FROM tb_hml_sat WHERE id = ".$id[0]->maxid."");
                    $nBDSat->execute();
                    $nSat = $nBDSat->fetchAll();
                    if($nSat[0]->sat == $sat){
                        $sats = (new Sats())->findById($id[0]->maxid);
                        $sats->retorno_status_operacional = $returnStateOper;
                        $sats->msg_status_operacional = $msgStateOper;
                        $sats->aviso_sefaz = $avisoSefaz;
                        $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                        $sats->sat = $sat;
                        $sats->loja = $store;
                        $sats->caixa = $pos;
                        $sats->tipo_lan = $typeLan;
                        $sats->ip = $ip;
                        $sats->mac = $mac;
                        $sats->mask = $mask;
                        $sats->gw = $gw;
                        $sats->dns_1 = $dnsPrimary;
                        $sats->dns_2 = $dnsSecundary;
                        $sats->status_wan = $statusWAN;
                        $sats->nivel_bateria = $nivelBatery;
                        $sats->disco = $disk;
                        $sats->disco_usado = $usedDisk;
                        $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                        $sats->firmware = $firmware;
                        $sats->layout = $layout;
                        $sats->ultimo_cfe = $lastCFeEmis;
                        $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                        $sats->ultimo_cfe_memoria = $lastCFeMemory;
                        $sats->numero_cfes_emitidos = $cfesEmitidos;
                        $sats->numeros_cfes_memoria = $cfesMemory;
                        $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                        $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                        $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                        $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                        $sats->estado_operacao = $estadoOperacao;
                        $sats->falha = $satEmFalha;
                        $sats->status = 'Ativo';
                        $sats->modelo_sat = $modeloSat;
                        $sats->data_atualizacao = date('Y-m-d H:i:s');
                        //var_dump($sats);
                        $sats->save();

                        if($sats->fail()){
                            header("HTTP/1.1 500 Internal Server Error");
                            echo json_encode(array("response"=>$sats->fail()->getMessage()));
                            exit;
                        }
            
                        header("HTTP/1.1 201 Created");
                        echo json_encode(array("response"=>"SAT atualizado com sucesso!"));
                        exit;
                    }else{
                        $updInativo = Connection::getInstanceI()->prepare("UPDATE tb_hml_sat SET status = 'Inativo' WHERE sat = '$sat'");
                        $updInativo->execute();
                        $sats = new Sats();
                        $sats->retorno_status_operacional = $returnStateOper;
                        $sats->msg_status_operacional = $msgStateOper;
                        $sats->aviso_sefaz = $avisoSefaz;
                        $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                        $sats->sat = $sat;
                        $sats->loja = $store;
                        $sats->caixa = $pos;
                        $sats->tipo_lan = $typeLan;
                        $sats->ip = $ip;
                        $sats->mac = $mac;
                        $sats->mask = $mask;
                        $sats->gw = $gw;
                        $sats->dns_1 = $dnsPrimary;
                        $sats->dns_2 = $dnsSecundary;
                        $sats->status_wan = $statusWAN;
                        $sats->nivel_bateria = $nivelBatery;
                        $sats->disco = $disk;
                        $sats->disco_usado = $usedDisk;
                        $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                        $sats->firmware = $firmware;
                        $sats->layout = $layout;
                        $sats->ultimo_cfe = $lastCFeEmis;
                        $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                        $sats->ultimo_cfe_memoria = $lastCFeMemory;
                        $sats->numero_cfes_emitidos = $cfesEmitidos;
                        $sats->numeros_cfes_memoria = $cfesMemory;
                        $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                        $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                        $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                        $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                        $sats->estado_operacao = $estadoOperacao;
                        $sats->falha = $satEmFalha;
                        $sats->status = 'Ativo';
                        $sats->modelo_sat = $modeloSat;
                        $sats->data_inclusao = date('Y-m-d H:i:s');
                        $sats->data_atualizacao = date('Y-m-d H:i:s');
                        //var_dump($sats);
                        $sats->save();

                        if($sats->fail()){
                            header("HTTP/1.1 500 Internal Server Error");
                            echo json_encode(array("response"=>$sats->fail()->getMessage()));
                            exit;
                        }
            
                        header("HTTP/1.1 201 Created");
                        echo json_encode(array("response"=>"SAT cadastrado com sucesso!"));
                        exit;
                    }
                }else{
                    header("HTTP/1.1 200 OK");
                    echo json_encode(array("response"=>"Saiu por aqui!"));
                    exit;
                }
            }
        }else{
            $existPos = Connection::getInstanceI()->prepare("SELECT id FROM tb_hml_sat WHERE Loja = '$store' AND caixa = '$pos'");
            $existPos->execute();
            $countPos = $existPos->rowCount();
            if($countPos == 0){
                $sats = new Sats();
                $sats->retorno_status_operacional = $returnStateOper;
                $sats->msg_status_operacional = $msgStateOper;
                $sats->aviso_sefaz = $avisoSefaz;
                $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                $sats->sat = $sat;
                $sats->loja = $store;
                $sats->caixa = $pos;
                $sats->tipo_lan = $typeLan;
                $sats->ip = $ip;
                $sats->mac = $mac;
                $sats->mask = $mask;
                $sats->gw = $gw;
                $sats->dns_1 = $dnsPrimary;
                $sats->dns_2 = $dnsSecundary;
                $sats->status_wan = $statusWAN;
                $sats->nivel_bateria = $nivelBatery;
                $sats->disco = $disk;
                $sats->disco_usado = $usedDisk;
                $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                $sats->firmware = $firmware;
                $sats->layout = $layout;
                $sats->ultimo_cfe = $lastCFeEmis;
                $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                $sats->ultimo_cfe_memoria = $lastCFeMemory;
                $sats->numero_cfes_emitidos = $cfesEmitidos;
                $sats->numeros_cfes_memoria = $cfesMemory;
                $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                $sats->estado_operacao = $estadoOperacao;
                $sats->falha = $satEmFalha;
                $sats->status = 'Ativo';
                $sats->modelo_sat = $modeloSat;
                $sats->data_inclusao = date('Y-m-d H:i:s');
                $sats->data_atualizacao = date('Y-m-d H:i:s');
                //var_dump($sats);
                $sats->save();

                if($sats->fail()){
                    header("HTTP/1.1 500 Internal Server Error");
                    echo json_encode(array("response"=>$sats->fail()->getMessage()));
                    exit;
                }
    
                header("HTTP/1.1 201 Created");
                echo json_encode(array("response"=>"SAT cadastrado com sucesso!"));
                exit;
            }else{
                $inativo = Connection::getInstanceI()->prepare("UPDATE tb_hml_sat SET status = 'Inativo' WHERE Loja = '$store' AND caixa = '$pos'");
                $inativo->execute();
                $sats = new Sats();
                $sats->retorno_status_operacional = $returnStateOper;
                $sats->msg_status_operacional = $msgStateOper;
                $sats->aviso_sefaz = $avisoSefaz;
                $sats->msg_aviso_sefaz = $msgAvisoSefaz;
                $sats->sat = $sat;
                $sats->loja = $store;
                $sats->caixa = $pos;
                $sats->tipo_lan = $typeLan;
                $sats->ip = $ip;
                $sats->mac = $mac;
                $sats->mask = $mask;
                $sats->gw = $gw;
                $sats->dns_1 = $dnsPrimary;
                $sats->dns_2 = $dnsSecundary;
                $sats->status_wan = $statusWAN;
                $sats->nivel_bateria = $nivelBatery;
                $sats->disco = $disk;
                $sats->disco_usado = $usedDisk;
                $sats->data_hora_atual = date("Y-m-d H:i:s", strtotime($dateHAtual));
                $sats->firmware = $firmware;
                $sats->layout = $layout;
                $sats->ultimo_cfe = $lastCFeEmis;
                $sats->primeiro_cfe_memoria = $primaryCFeMemory;
                $sats->ultimo_cfe_memoria = $lastCFeMemory;
                $sats->numero_cfes_emitidos = $cfesEmitidos;
                $sats->numeros_cfes_memoria = $cfesMemory;
                $sats->data_hora_transm_sefaz = date("Y-m-d H:i:s", strtotime($dateHRTransm));
                $sats->data_hora_comun_sefaz = date("Y-m-d H:i:s", strtotime($dateHRComuni));
                $sats->data_ativacao = date("Y-m-d", strtotime($certEmisao));
                $sats->data_fim_ativacao = date("Y-m-d", strtotime($certVencimento));
                $sats->estado_operacao = $estadoOperacao;
                $sats->falha = $satEmFalha;
                $sats->status = 'Ativo';
                $sats->modelo_sat = $modeloSat;
                $sats->data_inclusao = date('Y-m-d H:i:s');
                $sats->data_atualizacao = date('Y-m-d H:i:s');
                //var_dump($sats);
                $sats->save();

                if($sats->fail()){
                    header("HTTP/1.1 500 Internal Server Error");
                    echo json_encode(array("response"=>$sats->fail()->getMessage()));
                    exit;
                }
    
                header("HTTP/1.1 201 Created");
                echo json_encode(array("response"=>"SAT cadastrado com sucesso!"));
                exit;
            }
        }

    break;
    case "DELETE":
        $satNum = filter_input(INPUT_GET,"id");
        if(!$satNum){
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("response"=>"ID nao informado"));
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
