<?php
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");

    $token = $_SESSION['tokenLogonDashSAT'];

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $ativo  = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_NUMBER_INT);
    $ntpvs = filter_input(INPUT_GET, 'ntpvs', FILTER_SANITIZE_NUMBER_INT);
    $shop = filter_input(INPUT_GET,'shop',FILTER_SANITIZE_STRING);    
    
    if($ativo == 0){
        if(!empty($id)){
            $sqlDeleteLoja = "DELETE FROM tb_install_monitor_sat WHERE id = '$id'";
            $queryDeleteLoja = mysqli_query($conn,$sqlDeleteLoja);
    
            if(mysqli_affected_rows($conn)){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Delete Loja'; 
                $msgLog = 'Loja ['.$shop.'] excluída com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('Loja excluída com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('Loja excluída com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            }else{
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Delete Loja'; 
                $msgLog = 'Erro ao excluír loja ['.$shop.'] .';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('Erro ao excluír loja!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('Erro ao excluír loja!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            }
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Delete Loja'; 
            $msgLog = 'Loja ['.$shop.'], necessário selecionar uma loja.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Necessário selecionar uma loja!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Necessário selecionar uma loja!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }
        }    
    }else{
        $sqlGetIP = "SELECT ip FROM tb_ip WHERE loja LIKE '$shop'";
        $queryGetIP = mysqli_query($conn,$sqlGetIP);
        $rowGetIP = mysqli_fetch_assoc($queryGetIP);
        $ipShop = $rowGetIP['ip'];
        $sumErro = 0;
        require_once("../api/processo/processa_delete.php");
        for ($i=1; $i <= $ntpvs; $i++) { 
            $return=removeServiceShop(1,$i,$shop,$ipShop);
            $str = explode('|',$return);
            $getErro = end($str);
            $sumErro = $sumErro + $getErro; 
            echo $str[0];   
        }
        if($sumErro == $ntpvs){
            if(!empty($id)){
                $sqlDeleteLoja = "DELETE FROM tb_install_monitor_sat WHERE id = '$id'";
                $queryDeleteLoja = mysqli_query($conn,$sqlDeleteLoja);
        
                if(mysqli_affected_rows($conn)){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Delete Loja'; 
                    $msgLog = 'Loja ['.$shop.'] excluída com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo "<script>alert('Loja excluída com sucesso!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo "<script>alert('Loja excluída com sucesso!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                }else{
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Delete Loja'; 
                    $msgLog = 'Erro ao excluír loja ['.$shop.'] .';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo "<script>alert('Erro ao excluír loja!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo "<script>alert('Erro ao excluír loja!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                }
            }else{
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Delete Loja'; 
                $msgLog = 'Loja ['.$shop.'], necessário selecionar uma loja.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('Necessário selecionar uma loja!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('Necessário selecionar uma loja!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            }
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Delete Loja'; 
            $msgLog = 'Loja ['.$shop.'], foram detectados erros ao remover serviço.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Foram detectados erros ao remover serviços dos PDVs!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Foram detectados erros ao remover serviços dos PDVs!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }
        }
    }
    
?>