<?php
    include "../config/config.php";

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
    $str = filter_input(INPUT_GET, 'str', FILTER_SANITIZE_STRING);
    $param = filter_input(INPUT_GET, 'param', FILTER_SANITIZE_STRING);

    switch ($id) {
        case '1':// Top 10 sem comunicar
            $sqlSatDadosGerais = "SELECT sat, loja, caixa, n_dias, n_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_comun_sefaz"] ." WHERE caixa != '01' AND n_cfes_memoria > 0 AND n_dias > ". $param ." ORDER BY n_dias  DESC LIMIT 10";
            break;
        case '2':// Top 10 sem transmitir
            $sqlSatDadosGerais = "SELECT sat,loja, caixa, n_dias, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_transm_sefaz"] ." WHERE caixa != '01' AND numeros_cfes_memoria > 0 AND n_dias > ". $param ." ORDER BY numeros_cfes_memoria DESC LIMIT 10";
            break;
        default:
            break;
    }
    header( 'Content-type: application/csv' );
    header( 'Content-Disposition: attachment; filename=export_'.$str.'.csv' );   
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
    $msgLog = 'Export Lojas especifica [export_'.$str.'.csv], realizado com sucesso.';
    if($_SESSION['usuarioidSAT'] != 0 ){
        insert_log_I($_SESSION['usuarioidSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
    }
?>