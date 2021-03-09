<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class MonitoramentoRecepcaoAlbarans extends DataLayer{
    public function __construct()
    {
        parent::__construct("tb_recepcao_albarans",["shop","data_recepcao","numero_albaran","tipo_pedido","cod_complementario","codigo","qntd_unid_recebida","qntd_kilo_recebida","qntd_pedida"],'id',false);
    }
}