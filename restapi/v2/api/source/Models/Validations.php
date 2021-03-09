<?php

namespace Source\Models;

use DateTime;

final class Validations{
    public static function validationString(string $token){
        return strlen($token)>=3 && !is_numeric($token) && $token == '7c6ce378b1ef0a29180dd36a0d436a91';
    }

    public static function validationSat(string $sat){
        return strlen($sat)>=9 && is_numeric($sat);
    }

    public static function validationStore(string $store){
        return strlen($store)>=5 && is_numeric($store);
    }

    public static function validationPOS(string $pos){
        return strlen($pos)>=2 && is_numeric($pos);
    }

    public static function validationTypeLan(string $typeLan){
        return strlen($typeLan)>=1 && !is_numeric($typeLan);
    }

    public static function validationIP(string $ip){
        return !filter_input(FILTER_VALIDATE_IP,$ip);
    }

    public static function validationMAC(string $mac){
        return !filter_input(FILTER_VALIDATE_MAC, $mac);
    }

    public static function validationMask(string $mask){
        return !filter_input(FILTER_VALIDATE_IP,$mask);
    }

    public static function validationGW(string $gw){
        return !filter_input(FILTER_VALIDATE_IP,$gw);
    }

    public static function validationDNSPrimary(string $dnsPrimary){
        return !filter_input(FILTER_VALIDATE_IP,$dnsPrimary);
    }

    public static function validationDNSSecundary(string $dnsSecundary){
        return !filter_input(FILTER_VALIDATE_IP,$dnsSecundary);
    }

    public static function validationStatusWAN(string $statusWAN){
        return strlen($statusWAN)>=1 && !is_numeric($statusWAN);
    }

    public static function validationDisk(string $disk){
        return strlen($disk)>=1 && !is_numeric($disk);
    }

    public static function validationUsedDisk(string $usedDisk){
        return strlen($usedDisk)>=1 && !is_numeric($usedDisk);
    }

    public static function validationFirmware(string $firmware){
        return strlen($firmware)>=8 && !is_numeric($firmware);
    }

    public static function validationLayout(string $layout){
        return strlen($layout)>=2;
    }

    public static function validationLastCFe(string $lastCFe){
        return strlen($lastCFe)>=44 && is_numeric($lastCFe);
    }

    public static function validationPrimaryCFeMemory(string $primaryCFeMemory){
        return strlen($primaryCFeMemory)>=44 && is_numeric($primaryCFeMemory);
    }

    public static function validationLastCFeMemory(string $lastCFeMemory){
        return strlen($lastCFeMemory)>=44 && is_numeric($lastCFeMemory);
    }

    public static function validationDateBroadcastSEFAZ(string $dateBroadcastSEFAZ){
        return strlen($dateBroadcastSEFAZ)>=14 && is_numeric($dateBroadcastSEFAZ);
    }

    public static function validationDateComunicationSEFAZ(string $dateComunSEFAZ){
        return strlen($dateComunSEFAZ)>=14 && is_numeric($dateComunSEFAZ);
    }

    public static function validationDateActive(string $dateActive){
        return strlen($dateActive)>=8 && is_numeric($dateActive);
    }

    public static function validationDateEndActive(string $dateEndActive){
        return strlen($dateEndActive)>=8 && is_numeric($dateEndActive);
    }

    public static function validationOperationStatus(string $operationStatus){
        return filter_input(FILTER_VALIDATE_INT,$operationStatus);
    }

    public static function validationStatus(string $status){
        return !is_numeric($status);
    }

    public static function validationModelSat(string $modelSat){
        return !is_numeric($modelSat);
    }

    public static function validationDateRegister(string $dateRegister){
        return  strlen($dateRegister)>=8  && is_numeric($dateRegister);
    }

    public static function validationUpdateDate(string $updateDate){
        return  strlen($updateDate)>=8  && is_numeric($updateDate);
    }

    public static function validationNumberOrder($albaran){
        return  strlen($albaran)==8  && is_numeric($albaran);
    }

    public static function validationCodigoProducto($codigo){
        return  strlen($codigo)<=6  && is_numeric($codigo);
    }

    public static function validationDateReception($dateReception, $format = 'Y-m-d'){
        
        $d = DateTime::createFromFormat($format,$dateReception);
        return $d && $d->format($format) == $dateReception;

    }

    public static function validationDate($date, $format = 'Y-m-d'){
        
        $d = DateTime::createFromFormat($format,$date);
        return $d && $d->format($format) == $date;

    }

}