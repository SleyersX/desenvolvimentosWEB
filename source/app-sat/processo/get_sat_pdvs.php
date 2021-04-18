<?php
    include "../config/config.php";
    unset($existe);
    
    $shop = $_POST['nshop'];
    $nbox = $_POST['ncaixa'];

    $conexao = new PDO('mysql:host=database;dbname=srvremoto',"root","8wFml6golmmbuKPv");
    $verificaSat = $conexao->prepare("SELECT sat FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' AND caixa = '0$nbox'");
    $verificaSat->execute();
    $fechSAT = $verificaSat->fetchAll();
    $existe = count($fechSAT);
    if($existe >= 1){
        echo $fechSAT[0]["sat"];
    }else{
        echo 0;
    }
?>
