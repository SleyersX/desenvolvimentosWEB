<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class MonitoramentoAberturaCaixas extends DataLayer{
    public function __construct()
    {
        parent::__construct("tb_monitoramento_abertura_de_caixas",["shop","pos"],'id',false);
    }
}