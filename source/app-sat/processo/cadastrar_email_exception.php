<?php
	require_once("../security/seguranca.php");
	protegePagina();
	session_start();
    require_once("../security/connect.php");
    require_once("send_email_verificacao_alerta.php");
	
	$token = $_SESSION['tokenLogonDashSAT'];
    $email = filter_input(INPUT_POST, 'email-alarme-exception', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'cb_active_email_exception', FILTER_SANITIZE_STRING);
    
    $existActiveEmail = "SELECT COUNT(id) AS total_registros FROM tb_email_alarmes_exception WHERE email LIKE '$email'";
    $queryExistActiveEmail = mysqli_query($conn,$existActiveEmail);
    $rowExistActiveEmail = mysqli_fetch_assoc($queryExistActiveEmail);

    if($rowExistActiveEmail["total_registros"] >= 1){
        $sqlIdActiveEmail = "SELECT id, email_verificado FROM tb_email_alarmes_exception WHERE email LIKE '$email'";
        $queryIdActiveEmail = mysqli_query($conn,$sqlIdActiveEmail);
        $rowIdActiveEmail = mysqli_fetch_assoc($queryIdActiveEmail);

        $idEmail = $rowIdActiveEmail['id'];

        $updateActiveEmail = "UPDATE tb_email_alarmes_exception SET status_email = '$status' WHERE email LIKE '$email'";
        $queryUpdtActiveEmail = mysqli_query($conn,$updateActiveEmail);
        if(mysqli_affected_rows($conn)){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Email Alertas'; 
            $msgLog = 'Update email ['.$email.']:['.$status.'], realizado com sucesso.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            if($rowIdActiveEmail['email_verificado'] == 0){
                send_email_verificacao_alerta(99,$idEmail,$email,'root','System');
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Cadastro atualizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">'; 
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Cadastro atualizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo "<script>alert('Cadastro atualizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
            }
            
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Email Alertas'; 
            $msgLog = 'Update email ['.$email.']:['.$status.'], nenhuma alteração realizada.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            if($rowIdActiveEmail['email_verificado'] == 0){
                send_email_verificacao_alerta(99,$idEmail,$email,'root','System');
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Update email, nenhuma alteração realizada!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Update email, nenhuma alteração realizada!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo "<script>alert('Update email, nenhuma alteração realizada!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
            }
        }
    }else{
        $sqlNewEmail = "INSERT INTO tb_email_alarmes_exception (email, status_email, email_verificado) VALUES ('$email', '$status',0)";
        $queryNewEmail = mysqli_query($conn, $sqlNewEmail);
        if(mysqli_insert_id($conn)){
            $idEmail = mysqli_insert_id($conn);
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Email Alertas'; 
            $msgLog = 'Cadastro email ['.$email.']:['.$status.'], realizado com sucesso.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            send_email_verificacao_alerta(99,$idEmail,$email,'root','System');
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Cadastro realizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Cadastro realizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo "<script>alert('Cadastro realizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
            }
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Email Alertas'; 
            $msgLog = 'Cadastro email ['.$email.']:['.$status.'], erro ao realizar cadastro.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }	
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Erro ao realizar cadastro!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Erro ao realizar cadastro!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo "<script>alert('Erro ao realizar cadastro!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
            }
        }
    }

?>