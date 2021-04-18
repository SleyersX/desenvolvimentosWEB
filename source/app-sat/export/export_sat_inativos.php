<?php
    include "../config/config.php";

    header( 'Content-type: application/csv' );
    header( 'Content-Disposition: attachment; filename=export_sat_inativos.csv' );   
    header( 'Content-Transfer-Encoding: binary' );
    header( 'Pragma: no-cache');

    $pdo = new PDO( 'mysql:host=database;dbname=srvremoto', 'root', 'diabrasil' );
    $stmt = $pdo->prepare( 'SELECT sat, loja, caixa, ip, mask, gw, dns_1, dns_2, mac, firmware, layout, disco_usado, data_ativacao, data_fim_ativacao, data_atualizacao, modelo_sat, status_wan, data_hora_comun_sefaz  FROM '. DATA_CONFIG_BD["cn_tab_sat_inativos"] .'' );   
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
    $msgLog = 'Export SATs [export_sats_inativos.csv], realizado com sucesso.';
    if($_SESSION['usuarioIDDashSAT'] != 0 ){
        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
    }
?>