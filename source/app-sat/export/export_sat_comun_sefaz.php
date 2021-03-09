<?php
    include "../config/config.php";

    $errsefaz = filter_input(INPUT_GET, 'errsefaz', FILTER_SANITIZE_STRING);
    header( 'Content-type: application/csv' );
    header( 'Content-Disposition: attachment; filename=export_'.$errsefaz.'.csv' );   
    header( 'Content-Transfer-Encoding: binary' );
    header( 'Pragma: no-cache');

    $pdo = new PDO( 'mysql:host=localhost;dbname=srvremoto', 'root', 'diabrasil' );
    $stmt = $pdo->prepare( 'SELECT sat, loja, caixa, ip, disco_usado, status_wan, data_hora_comun_sefaz, iploja FROM '. DATA_CONFIG_BD["cn_tab_comun_sefaz"] .';' );   
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
    $msgLog = 'Export SATs comunicação com a SEFAZ [export_'.$errsefaz.'.csv], realizado com sucesso.';
    if($_SESSION['usuarioIDDashSAT'] != 0 ){
        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
    }
?>