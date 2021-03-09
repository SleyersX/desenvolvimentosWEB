<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class MonitorCaixas extends DataLayer{
    public function __construct()
    {
        parent::__construct("tb_monitoramento_abertura_de_caixas",["shop","pos","data_abertura","hora_abertura","matricula","valor_abertura"],'id',false);
    }
}