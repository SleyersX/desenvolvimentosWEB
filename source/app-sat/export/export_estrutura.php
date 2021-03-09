<?php
    include "../config/config.php";

    header( 'Content-type: application/csv' );
    header( 'Content-Disposition: attachment; filename=export_estrutura.csv' );   
    header( 'Content-Transfer-Encoding: binary' );
    header( 'Pragma: no-cache');

    $pdo = new PDO( 'mysql:host=localhost;dbname=srvremoto', 'root', 'diabrasil' );
    $stmt = $pdo->prepare('SELECT loja, DireFisc, PersFisc, CodiEstaClie, LocaFisc, CodiIden, n_tpvs_setvari FROM '. DATA_CONFIG_BD["cn_tab_list_lojas"] .';' );   
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
    $msgLog = 'Export [export_estrutura.csv], realizado com sucesso.';
    if($_SESSION['usuarioIDDashSAT'] != 0 ){
        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
    }
?>