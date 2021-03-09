<?php
    $user="root";
    $passwd="diabrasil";
    $host="localhost";
    $banco="srvremoto";

    $conn=mysqli_connect($host,$user,$passwd,$banco) or die ("Erro ao conectar com o banco de dados.");

?>