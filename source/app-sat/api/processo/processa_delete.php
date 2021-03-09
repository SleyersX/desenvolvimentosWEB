<?php
    session_start();
    require_once("/var/www/html/source/app-sat/security/seguranca.php");
	protegePagina();
    require_once("/var/www/html/source/app-sat/security/connect.php");

    /**
    * Função para remover o serviço de monitoramento nas lojas
    *
    * @param  int    $seletor  - ID para o case
    * @param  int    $npos     - Número de PDVs para executar o laço
    * @param  string $shop     - Número da loja
    * @param  string $ipShop   - IP da loja a remover o serviço
    * @return string $output   - Retornar para interface 
    *
    */

    function removeServiceShop($seletor, $npos,$shop,$ipShop){
        switch ($seletor) {
            case '1':
                $output=shell_exec("/var/www/html/source/app-sat/api/bash/delete.sh remove $shop $ipShop $npos");
                return $output;
                break;
            default:
                break;
        }
    }
?>