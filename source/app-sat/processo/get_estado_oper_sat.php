<?php
    include "../config/config.php";
    unset($existe);

    $nsat = $_POST['nsat'];
    //echo "\n$nsat";
    $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
    $verificaSat = $conexao->prepare("SELECT estado_operacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$nsat'");
    $verificaSat->execute();
    $fechSAT = $verificaSat->fetchAll();
    //var_dump($fechSAT);
    $existe = $fechSAT[0]["estado_operacao"];
    if($existe == 1 || $existe == 2 || $existe == 3){
        echo 0;
    }else{
        echo 1;
    }

?>