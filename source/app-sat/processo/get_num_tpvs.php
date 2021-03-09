<?php
    include "../config/config.php";
    unset($existe);
    
    $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
    if((strlen($_POST['consulta']) <= 5) && ($_POST['consulta'] <= 9999)){
        $nLoja = $_POST['consulta'];
        $verifica = $conexao->prepare("SELECT n_tpvs_setvari FROM ". DATA_CONFIG_BD["tab_group_lojas"] ." WHERE loja = '$nLoja'");
        $verifica->execute();
        $fech = $verifica->fetchAll();
        //var_dump($fechSAT);
        $existe = $fech[0]["n_tpvs_setvari"];
        if($existe >= 1){
            echo $fech[0]["n_tpvs_setvari"];
        }else{
            echo 0;
        }
    }else{
        $nSat = $_POST['consulta'];
        $verifica = $conexao->prepare("SELECT caixa FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$nSat'");
        $verifica->execute();
        $fech = $verifica->fetchAll();
        //var_dump($fechSAT);
        $existe = $fech[0]["caixa"];
        if($existe >= 1){
            echo $fech[0]["caixa"];
        }else{
            echo 0;
        }
    }

?>