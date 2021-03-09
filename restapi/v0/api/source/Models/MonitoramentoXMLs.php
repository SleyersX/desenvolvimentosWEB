<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class MonitoramentoXMLs extends DataLayer{
    public function __construct()
    {
        //parent::__construct("tb_xml_pendentes",["shop","pos","xml_rejected","xml_answ","xml_send","data_atualizacao"],'id',false);
        parent::__construct("tb_xml_pendentes",["shop","pos"],'id',false);
    }
}