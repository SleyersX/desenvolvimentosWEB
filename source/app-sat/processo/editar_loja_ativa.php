<?php
    require_once("../security/seguranca.php");
	protegePagina();
	session_start();
	require_once("../security/connect.php");
	
    $token = $_SESSION['tokenLogonDashSAT'];
    $idShop = filter_input(INPUT_POST,'id', FILTER_SANITIZE_NUMBER_INT);
    $shop = filter_input(INPUT_POST, 'shop', FILTER_SANITIZE_STRING);
    $shop = str_pad($shop, 5, "0", STR_PAD_LEFT);
    $dtSend = filter_input(INPUT_POST, 'date-send-modal', FILTER_SANITIZE_STRING);
    $dtInstall = filter_input(INPUT_POST, 'date-install-modal', FILTER_SANITIZE_STRING);
	$data = date('Y-m-d H:i:s'); 
    
    $send = array_reverse(explode("/", $dtSend));
    $send = implode("-", $send);

    $install = array_reverse(explode("/", $dtInstall));
    $install = implode("-", $install);

    //echo $send;
    //Verifica se a loja já existe
    $sqlExistShop  = "SELECT COUNT(id) AS total_registros FROM tb_install_monitor_sat WHERE id = '$idShop'";
    $queryExistShop = mysqli_query($conn,$sqlExistShop);
    $rowExistShop = mysqli_fetch_assoc($queryExistShop);

    if($rowExistShop["total_registros"] >= 1){

        $updateActiveShop = "UPDATE tb_install_monitor_sat SET date_install = '$install', date_send = '$send' WHERE id = '$idShop'";
        $queryUptdActShop = mysqli_query($conn,$updateActiveShop);

        if (mysqli_affected_rows($conn)) {
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Ativar Loja';
            $msgLog = 'Loja ['.$shop.']:['.$send.']:['.$install.'], foi atualizada com os novos dados.';
            if ($_SESSION['usuarioIDDashSAT'] != 0) {
                insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Loja atualizada com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Loja atualizada com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Ativar Loja';
            $msgLog = 'Nenhuma alteração realizada para a loja ['.$idShop.']:['.$shop.']:['.$send.']:['.$install.'].';
            if ($_SESSION['usuarioIDDashSAT'] != 0) {
                insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Nenhuma alteração realizada para a loja!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Nenhuma alteração realizada para a loja!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }
        }
    }else{
        //Grava LOG
        require_once("processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Ativar Loja';
        $msgLog = 'ID não encontrado ['.$idShop.']:['.$shop.']:['.$send.']:['.$install.'].';
        if ($_SESSION['usuarioIDDashSAT'] != 0) {
            insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
        }
        if($_SESSION['usuarioNivelDashSAT'] == 1){
            echo "<script>alert('Erro ao cadastrar loja!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
            echo "<script>alert('Erro ao cadastrar loja!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
        }
    }
?>