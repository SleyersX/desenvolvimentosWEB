<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Sats extends DataLayer{
    public function __construct()
    {
        parent::__construct("tb_sat",["sat","loja","caixa","tipo_lan","ip","mac","mask","gw","dns_1","dns_2","status_wan", "disco", "disco_usado", "firmware","layout"],'id',false);
    }
}
