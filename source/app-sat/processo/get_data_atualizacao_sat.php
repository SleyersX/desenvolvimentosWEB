<?php
    include "../config/config.php";
    unset($existe);

    $nsat = $_POST['nsat'];
    
    $conexao = new PDO('mysql:host=database;dbname=srvremoto',"root","8wFml6golmmbuKPv");
    $verificaSat = $conexao->prepare("SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$nsat' AND DATE_FORMAT(data_atualizacao, '%Y-%m-%d' ) = DATE_FORMAT(NOW(),'%Y-%m-%d')");
    $verificaSat->execute();
    $fechSAT = $verificaSat->fetchAll();
    $existe = count($fechSAT);
    if($existe >= 1){
        echo $fechSAT[0]["total_registros"];;
    }else{
        echo 0;
    }
?>