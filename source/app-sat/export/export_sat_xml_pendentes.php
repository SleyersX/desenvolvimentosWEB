<?php
    include "../config/config.php";

    $errxml = filter_input(INPUT_GET, 'errxml', FILTER_SANITIZE_STRING);
    header( 'Content-type: application/csv' );
    header( 'Content-Disposition: attachment; filename=export_'.$errxml.'.csv' );   
    header( 'Content-Transfer-Encoding: binary' );
    header( 'Pragma: no-cache');

    $pdo = new PDO( 'mysql:host=database;dbname=srvremoto', 'root', 'diabrasil' );
    $stmt = $pdo->prepare( 'SELECT sat, loja, caixa, ipsat, disco_usado, iploja FROM '. DATA_CONFIG_BD["cn_tab_disk_used"] .';' );   
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
    $msgLog = 'Export SATs XMLs pendentes de envio a SEFAZ [export_'.$errxml.'.csv], realizado com sucesso.';
    if($_SESSION['usuarioIDDashSAT'] != 0 ){
        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
    }
?>