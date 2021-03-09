<?php
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");

    $token = $_SESSION['tokenLogonDashSAT'];

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_STRING);
    if(!empty($id)){
        
        $sqlExistEmail = "SELECT COUNT(id) AS Ttotal FROM cn_email_alarmes WHERE id = '$id'";
        $queryExistEmail = mysqli_query($conn,$sqlExistEmail);
        $rowExistEmail = mysqli_fetch_assoc($queryExistEmail);
        $countEmail = $rowExistEmail['Ttotal'];

        if($countEmail >= 1){
            $sqlDelEmail = "DELETE FROM tb_email_alarmes WHERE id = '$id'";
            $queryDelEmail = mysqli_query($conn, $sqlDelEmail);
            if(mysqli_affected_rows($conn)){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Delete Email'; 
                $msgLog = 'Email ['.$id.']:['.$email.'], apagado com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('Email apagado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('Email apagado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                    echo "<script>alert('Email apagado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
                }
            }else{
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Delete Email'; 
                $msgLog = 'Email ['.$id.']:['.$email.'], não foi apagado com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('Erro o email não foi apagado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('Erro o email não foi apagado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                    echo "<script>alert('Erro o email não foi apagado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
                }
            }
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Delete Email'; 
            $msgLog = 'Email ['.$id.']:['.$email.'], não foi apagado com sucesso, pois existem usuários ['.$countEmail.'] associados a ele.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('O email selecionado não pode ser deletado!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('O email selecionado não pode ser deletado!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo "<script>alert('O email selecionado não pode ser deletado!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
            }
        }
    }else{
        //Grava LOG
        require_once("processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Delete Email'; 
        $msgLog = 'Email ['.$id.']:['.$email.'], necessário selecionar um email.';
        if($_SESSION['usuarioIDDashSAT'] != 0 ){
            insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
        }
        if($_SESSION['usuarioNivelDashSAT'] == 1){
            echo "<script>alert('Necessário selecionar um email!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
            echo "<script>alert('Necessário selecionar um email!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
            echo "<script>alert('Necessário selecionar um email!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
        }	
    }
?>