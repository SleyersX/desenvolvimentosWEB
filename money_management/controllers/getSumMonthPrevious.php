<?php
    use models\DB;
    require_once "../models/connection.php";
    $db = new DB();

    if(!isset($_GET["monthPrevious"]) || !isset($_GET["monthActual"])){
        $monthPrevious = date('m', strtotime('-1 month'));
        $monthActual = date('m');
    }else{
        $monthPrevious = $_GET["monthPrevious"];
        $monthActual = $_GET["monthActual"];
    }

    // Carrega Saldo Mês Anterior

    $sql="SELECT SUM(valor) AS total FROM cnt_receitas_por_mes WHERE mes_lancamento LIKE $monthPrevious";
    $stmt = $db->conn->prepare($sql);
    $stmt->execute();
    $response=$stmt->fetchAll();
    $valor_1= floatval( $response[0]["total"]);

    $sql="SELECT SUM(valor) AS total FROM cnt_despesas_por_mes WHERE mes_lancamento LIKE $monthPrevious";
    $stmt = $db->conn->prepare($sql);
    $stmt->execute();
    $response=$stmt->fetchAll();
    $valor_2= floatval( $response[0]["total"]);

    $sql="SELECT SUM(valor) AS total FROM cnt_parcelamento WHERE mes_pagamento LIKE $monthPrevious AND status LIKE 'PAGO' AND n_parcelas > 1";
    $stmt = $db->conn->prepare($sql);
    $stmt->execute();
    $response=$stmt->fetchAll();
    $valor_3= floatval( $response[0]["total"]);

    $saldoAnterior=($valor_1 - ($valor_2+$valor_3));

    // Carrega Saldo Mês Atual

    $sql="SELECT SUM(valor) AS total FROM cnt_receitas_por_mes WHERE mes_lancamento LIKE $monthActual";
    $stmt = $db->conn->prepare($sql);
    $stmt->execute();
    $response=$stmt->fetchAll();
    $valor_1= floatval( $response[0]["total"]);

    $sql="SELECT SUM(valor) AS total FROM cnt_despesas_por_mes WHERE mes_lancamento LIKE $monthActual";
    $stmt = $db->conn->prepare($sql);
    $stmt->execute();
    $response=$stmt->fetchAll();
    $valor_2= floatval( $response[0]["total"]);

    $saldoMesAtual=($valor_1-$valor_2);

    // Carrega Soma Receitas

    $sql="SELECT * FROM cnt_soma_receitas";
    $stmt = $db->conn->prepare($sql);
    $stmt->execute();
    $response=$stmt->fetchAll();
    $somaReceitasAno= floatval( $response[0]["total"]);

    // Carrega Despesas Ano

    $sql="SELECT * FROM cnt_soma_despesas";
    $stmt = $db->conn->prepare($sql);
    $stmt->execute();
    $response=$stmt->fetchAll();
    $valor_1= floatval( $response[0]["total"]);

    $sql="SELECT * FROM cnt_soma_despesas_parcelamento";
    $stmt = $db->conn->prepare($sql);
    $stmt->execute();
    $response=$stmt->fetchAll();
    $valor_2= floatval( $response[0]["total"]);

    $somaDespesasAno=($valor_1+$valor_2);

    // Carregar Acumulado Ano
    $saldoAcumuladoAno=($somaReceitasAno-$somaDespesasAno);

    $saldos = array(
        "saldoMesAnterior"=>number_format($saldoAnterior,2,'.',''),
        "saldoMesAtual"=>number_format($saldoMesAtual,2,'.',''),
        "somaReceitasAno"=>number_format($somaReceitasAno,2,'.',''),
        "somaDespesasAno"=>number_format($somaDespesasAno,2,'.',''),
        "saldoAcumuladoAno"=>number_format($saldoAcumuladoAno,2,'.',''),
        "mesAtual" => $monthActual,
        "mesAnterior" => $monthPrevious
    );

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($saldos);



