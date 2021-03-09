<?php
    $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
    $limpaTempo = $conexao->prepare("UPDATE tb_sessoes_login_dashsat SET tempo_inativo = '0' WHERE id = '".$_POST['id']."'");
    $limpaTempo->execute();
?>