<?php
    include "../config/config.php";

    $iddash = filter_input(INPUT_GET, 'iddash', FILTER_SANITIZE_STRING);
    $strdash = filter_input(INPUT_GET, 'strdash', FILTER_SANITIZE_STRING);

    switch ($iddash) {
        case '1'://Modelo S@T
            $sqlSatDadosGerais = "SELECT sat, loja, caixa, ip, disco_usado, status_wan, data_hora_comun_sefaz, layout, firmware, data_fim_ativacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE '$strdash'";
            $msg = "relação_modelo_" . $strdash;
            break;
        case '2'://Firmware S@T
            $sqlSatDadosGerais = "SELECT sat, loja, caixa, ip, disco_usado, status_wan, data_hora_comun_sefaz, layout, firmware, data_fim_ativacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE firmware LIKE '$strdash'";
            $msg = "relação_firmware_" . $strdash;
            break;
        case '3'://Layout S@T
            $sqlSatDadosGerais = "SELECT sat, loja, caixa, ip, disco_usado, status_wan, data_hora_comun_sefaz, layout, firmware, data_fim_ativacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE layout LIKE '$strdash'";
            $msg = "relação_layout " . $strdash;
            break;
        case '4'://Expiração do Certificado Digital
            $sqlSatDadosGerais = "SELECT sat, loja, caixa, ip, disco_usado, status_wan, data_hora_comun_sefaz, layout, firmware, data_fim_ativacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE YEAR(data_fim_ativacao) LIKE '$strdash'";
            $msg = "relação_modelo_certificado_expirando_" . $strdash;
            break;
        default:
            $sqlSatDadosGerais = "SELECT sat, loja, caixa, ip, disco_usado, status_wan, data_hora_comun_sefaz, layout, firmware, data_fim_ativacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ."";
            $msg = "relação_todos_sats";
            break;
    }
    header( 'Content-type: application/csv' );
    header( 'Content-Disposition: attachment; filename=export_'.$msg.'.csv' );   
    header( 'Content-Transfer-Encoding: binary' );
    header( 'Pragma: no-cache');

    $pdo = new PDO( 'mysql:host=database;dbname=srvremoto', 'root', 'diabrasil' );
    $stmt = $pdo->prepare($sqlSatDadosGerais);   
    $stmt->execute();
    $results = $stmt->fetchAll( PDO::FETCH_ASSOC );

    $out = fopen( 'php://output', 'w' );
    foreach ( $results as $result ) 
    {
        fputcsv( $out, $result );
    }
    fclose( $out );

    //Grava LOG
    require_once("../processo/processa_log.php");
    $dataLog = date('Y-m-d H:i:s');
    $appCallLog = 'Export'; 
    $msgLog = 'Export SATs Dashboard [export_'.$msg.'.csv], realizado com sucesso.';
    if($_SESSION['usuarioIDDashSAT'] != 0 ){
        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
    }
?>