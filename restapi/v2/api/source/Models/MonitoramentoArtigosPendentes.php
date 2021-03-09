<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class MonitoramentoArtigosPendentes extends DataLayer{
    public function __construct()
    {
        parent::__construct("tb_pedidos_pendentes",["shop","data_inclusao","codigo","descricao","tipo_tratamento","qntd_pendente","qntd_pendente_old","qntd_pendente_kilo","qntd_pendente_kilo_old","stock","stock_old","stock_kilo","stock_kilo_old","data_atualizacao"],'id',false);
    }
}