<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");

    $token = $_SESSION['tokenLogonDashSAT'];
    
    $arquivo = $_FILES["file"]["tmp_name"];
    $nome = $_FILES["file"]["name"];

    $ext = explode(".", $nome);
    $extensao = end($ext);

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    if($extensao != 'csv' && $extensao != null){
        //Grava LOG
        require_once("processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Import CSV Ativar Lojas'; 
        $msgLog = 'Extensão inválida, arquivo deve ser CSV ['.$extensao.'].';
        if($_SESSION['usuarioIDDashSAT'] != 0 ){
            insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
        }
       // echo "<script>alert('Extensão inválida, arquivo deve ser CSV [".$extensao."]');</script>";
       // echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
       $_SESSION['msg'] = "<div class='alert alert-warning'>Extensão inválida, arquivo deve ser CSV [".$extensao."]</div>";
    }elseif($extensao == 'csv'){
        $obejto = fopen($arquivo, 'r');

        while(($dados = fgetcsv($obejto, 1000, ";")) !== FALSE){
            $loja = $dados[0];
            $loja = str_pad($loja, 5, "0", STR_PAD_LEFT);
            $dtSend = $dados[1];
            $dtInstall = $dados[2];
            $active = $dados[3];
            
            $send = array_reverse(explode("/", $dtSend));
            $send = implode("-", $send);

            $install = array_reverse(explode("/", $dtInstall));
            $install = implode("-", $install);

            if(($dados[0] != '') && ($dados[0] != 'loja') && ($dados[1] != 'data_envio') && ($dados[2] != 'data_instalacao') && ($dados[3] != 'active') && (is_numeric($dados[0])) && validateDate($dados[1], 'd/m/Y') && validateDate($dados[2], 'd/m/Y') && (is_numeric($active))){
                $sqlExistLojaCSV = "SELECT COUNT(id) AS total_registros FROM tb_install_monitor_sat WHERE shop = '$loja'";
                $queryExistLojaCSV = mysqli_query($conn,$sqlExistLojaCSV);
                $rowExistLojaCSV = mysqli_fetch_assoc($queryExistLojaCSV);
                
                if($rowExistLojaCSV["total_registros"] == 0){
                    $insertCSV = "INSERT INTO tb_install_monitor_sat (shop, date_install, date_send, ativo) VALUES ('$loja','$install','$send', '$active')";
                    $queryCSV = mysqli_query($conn,$insertCSV);
                    //Grava LOG
                    require_once("processa_import_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Import CSV Ativar Lojas'; 
                    $msgLog = 'Insert dados ['.$loja.']:['.$install.']:['.$send.']:['.$active.'], realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_IV($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                }else{
                    $updateCSV = "UPDATE tb_install_monitor_sat SET date_install = '$install', date_send = '$send', ativo = '$active' WHERE shop = '$loja'";
                    $queryUpdCSV = mysqli_query($conn,$updateCSV);
                    //Grava LOG
                    require_once("processa_import_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Import CSV Ativar Lojas'; 
                    $msgLog = 'Update dados ['.$loja.']:['.$install.']:['.$send.']:['.$active.'], realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_IV($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                }
            }
        }
        if (mysqli_insert_id($conn)) {
            //echo "<script>alert('Dados inseridos com sucesso');</script>";
            //echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Import CSV Ativar Lojas'; 
            $msgLog = 'Import ativar lojas por CSV, realizado com sucesso.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            $_SESSION['msg'] = "<div class='alert alert-success'>Dados inseridos com sucesso!</div>";
        }elseif(mysqli_affected_rows($conn)){
            //echo "<script>alert('Dados inseridos com sucesso');</script>";
            //echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Import CSV Ativar Lojas'; 
            $msgLog = 'Import ativar lojas por CSV, realizado com sucesso.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            $_SESSION['msg'] = "<div class='alert alert-success'>Dados atualizados com sucesso!</div>";
        }else{
            //echo "<script>alert('Erro durante o processo de importação, erro de estrutura CSV');</script>";
            //echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Import CSV Ativar Lojas'; 
            $msgLog = 'Erro import ativar lojas por CSV.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            $_SESSION['msg'] = "<div class='alert alert-info'>Nenhuma alteração realizada!</div>";
        }
    }


?>