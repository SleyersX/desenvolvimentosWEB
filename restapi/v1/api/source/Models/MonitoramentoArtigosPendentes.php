<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class MonitoramentoArtigosPendentes extends DataLayer{
    public function __construct()
    {
        parent::__construct("tb_pedidos_pendentes",["shop","codigo"],'id',false);
    }
}