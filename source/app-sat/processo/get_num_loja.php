<?php
    include "../config/config.php";
    unset($existe);
    
    $nsat = $_POST['nsat'];

    $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
    $verificaSat = $conexao->prepare("SELECT sat FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$nsat'");
    $verificaSat->execute();
    $fechSAT = $verificaSat->fetchAll();
    $existe = count($fechSAT);
    if($existe >= 1){
        echo $fechSAT[0]["sat"];
    }else{
        echo 0;
    }
?>