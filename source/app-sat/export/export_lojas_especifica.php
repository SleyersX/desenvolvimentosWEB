<?php
    include "../config/config.php";

    $iddash = filter_input(INPUT_GET, 'iddash', FILTER_SANITIZE_STRING);
    $strdash = filter_input(INPUT_GET, 'strdash', FILTER_SANITIZE_STRING);

    switch ($iddash) {
        case '1'://CR
            $sqlSatDadosGerais = "SELECT loja, direFisc, codiEstaClie, locaFisc, codiIden, centro FROM ". DATA_CONFIG_BD["cn_tab_lojas_sat_complete"] ." WHERE centro LIKE '$strdash'";
            $msg = "relação_" . $strdash;
            break;
        case '2'://Região
            $sqlSatDadosGerais = "SELECT loja, direFisc, codiEstaClie, locaFisc, codiIden, centro FROM ". DATA_CONFIG_BD["cn_tab_lojas_sat_complete"] ." WHERE regiao LIKE '$strdash'";
            $msg = "relação_região_" . $strdash;
            break;
        case '3'://Divisão
            $sqlSatDadosGerais = "SELECT loja, direFisc, codiEstaClie, locaFisc, codiIden, centro FROM ". DATA_CONFIG_BD["cn_tab_lojas_sat_complete"] ." WHERE divisao LIKE '$strdash'";
            $msg = "relação_divisao_ " . $strdash;
            break;
        default:
            $sqlSatDadosGerais =  "SELECT loja, direFisc, codiEstaClie, locaFisc, codiIden, centro FROM ". DATA_CONFIG_BD["cn_tab_lojas_sat_complete"] ."";
            $msg = "relação_todas_";
            break;
    }
    header( 'Content-type: application/csv' );
    header( 'Content-Disposition: attachment; filename=export_'.$msg.'.csv' );   
    header( 'Content-Transfer-Encoding: binary' );
    header( 'Pragma: no-cache');

    $pdo = new PDO( 'mysql:host=localhost;dbname=srvremoto', 'root', 'diabrasil' );
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
    $msgLog = 'Export Lojas especifica [export_'.$msg.'.csv], realizado com sucesso.';
    if($_SESSION['usuarioIDDashSAT'] != 0 ){
        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
    }
?>