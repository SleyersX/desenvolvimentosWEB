<?php
    $tempo = 0;
    $conexao = new PDO('mysql:host=database;dbname=srvremoto',"root","8wFml6golmmbuKPv");
    $verificaTempo = $conexao->prepare("SELECT * FROM tb_sessoes_login_dashsat WHERE id = '".$_POST['id']."'");
    $verificaTempo->execute();
    $fech = $verificaTempo->fetchAll();
    $conta = count($fech);
    if($conta >= 1){
        $soma = $fech[0]["tempo_inativo"] + 1;
        $updateTempo = $conexao->prepare("UPDATE tb_sessoes_login_dashsat SET tempo_inativo = '".$soma."', tempo_final = '.$soma.' WHERE id = '".$_POST['id']."'");
        $updateTempo->execute();
        if($fech[0]["tempo_inativo"] >= 90){
            echo "1";
        }
    }
?>