<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class MonitoramentoRecepcaoAlbarans extends DataLayer{
    public function __construct()
    {
        parent::__construct("tb_recepcao_albarans",["shop","numero_albaran"],'id',false);
    }
}