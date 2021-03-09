<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Sats extends DataLayer{
    public function __construct()
    {
        //parent::__construct("tb_hml_sat",["retorno_status_operacional","msg_status_operacional","aviso_sefaz","msg_aviso_sefaz","sat","loja","caixa","tipo_lan","ip","mac","mask","gw","dns_1","dns_2","status_wan","nivel_bateria", "disco", "disco_usado","data_hora_atual","firmware","layout","ultimo_cfe","primeiro_cfe_memoria","ultimo_cfe_memoria","data_hora_transm_sefaz","data_hora_comun_sefaz","data_ativacao","data_fim_ativacao","estado_operacao","status","data_inclusao","data_atualizacao",],'id',false);
        parent::__construct("tb_hml_sat",["sat","loja","caixa","tipo_lan","ip","mac","mask","gw","dns_1","dns_2","status_wan","nivel_bateria", "disco", "disco_usado","data_hora_atual","firmware","layout","status"],'id',false);
    }
}