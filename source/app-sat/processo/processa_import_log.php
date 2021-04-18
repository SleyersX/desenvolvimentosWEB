<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    
    /**
    * Função fpara gravar os Logs do usuário do Sistema
    *
    * @param int    $idUserLog - ID usuário registrado no Log
    * @param string $nomeUserLog - Usuário registrado no Log
    * @param string $loginUserLog - Login do usuário registrado no Log
    * @param string $appLog - Aplicação que executou o Log
    * @param string $dataLog - Data do Log
    * @param string $logDados - Dados do Log
    *
    * @return bool - Se o Log foi gravado (true/false)
    */
    function insert_log_IV($idUserLog,$nomeUserLog,$loginUserLog,$appLog,$dataLog,$logDados){
        $user="root";
        $passwd="diabrasil";
        $host="database";
        $banco="srvremoto";
        $conn= mysqli_connect($host,$user,$passwd,$banco);

        $sqlInsertLog = "INSERT INTO tb_log_import_dashsat (id_user, nome_user, login_user, aplicacao, data_log, log_dados) VALUES ('$idUserLog','$nomeUserLog','$loginUserLog','$appLog','$dataLog','$logDados')";
        $queryInsertLog = mysqli_query($conn,$sqlInsertLog);
        
        if(mysqli_insert_id($conn)){
            return false;
        }else{
            return true;
        }
    }
?>