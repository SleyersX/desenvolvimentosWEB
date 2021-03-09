<?php
    require_once("../security/seguranca.php");
	protegePagina();
	session_start();
	require_once("../security/connect.php");
	
	$token = $_SESSION['tokenLogonDashSAT'];
    $shop = filter_input(INPUT_POST, 'num-shop', FILTER_SANITIZE_STRING);
    $shop = str_pad($shop, 5, "0", STR_PAD_LEFT);
    $dtSend = filter_input(INPUT_POST, 'date-send', FILTER_SANITIZE_STRING);
    $dtInstall = filter_input(INPUT_POST, 'date-install', FILTER_SANITIZE_STRING);
    $active = filter_input(INPUT_POST, 'cb_active', FILTER_SANITIZE_STRING);
    $stShop = filter_input(INPUT_POST,'cb_status_loja', FILTER_SANITIZE_STRING);
	$data = date('Y-m-d H:i:s'); 
    
    $send = array_reverse(explode("/", $dtSend));
    $send = implode("-", $send);

    $install = array_reverse(explode("/", $dtInstall));
    $install = implode("-", $install);

    //echo $send;
    //Verifica se a loja já existe
    $sqlExistShop  = "SELECT COUNT(id) AS total_registros FROM tb_install_monitor_sat WHERE shop = '$shop'";
    $queryExistShop = mysqli_query($conn,$sqlExistShop);
    $rowExistShop = mysqli_fetch_assoc($queryExistShop);

    if($rowExistShop["total_registros"] >= 1){

        if($stShop == 1){
            $sqlExistShopSAT  = "SELECT COUNT(id) AS total_registros FROM tb_hml_sat WHERE loja = '$shop'";
            $queryExistShopSAT = mysqli_query($conn,$sqlExistShopSAT);
            $rowExistShopSAT = mysqli_fetch_assoc($queryExistShopSAT);
            if($rowExistShopSAT >= 1){
                $updateInactiveShop = "UPDATE tb_hml_sat SET status = 'Inativo', data_atualizacao = '$data' WHERE loja = '$shop'";
                $queryInactiveShop = mysqli_query($conn,$updateInactiveShop);
                if (mysqli_affected_rows($conn)) {
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Inativar Loja';
                    $msgLog = 'Inativar loja ['.$shop.']:['.$send.']:['.$install.']:['.$active.']:['.$stShop.'],já existe e foi marcado todos os SATs como inativo.';
                    if ($_SESSION['usuarioIDDashSAT'] != 0) {
                        insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                    }
                }else{
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Inativar Loja';
                    $msgLog = 'Erro ao inativar loja ['.$shop.']:['.$send.']:['.$install.']:['.$active.']:['.$stShop.'].';
                    if ($_SESSION['usuarioIDDashSAT'] != 0) {
                        insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                    }
                }        
            }
        }

        $updateActiveShop = "UPDATE tb_install_monitor_sat SET date_install = '$install', date_send = '$send', ativo = '$active', status_shop = '$stShop' WHERE shop = '$shop'";
        $queryUptdActShop = mysqli_query($conn,$updateActiveShop);

        if (mysqli_affected_rows($conn)) {
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Ativar Loja';
            $msgLog = 'Ativar loja ['.$shop.']:['.$send.']:['.$install.']:['.$active.']:['.$stShop.'],já existe e foi atualizada com os novos dados.';
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
            $msgLog = 'Erro ao ativar loja ['.$shop.']:['.$send.']:['.$install.']:['.$active.']:['.$stShop.'].';
            if ($_SESSION['usuarioIDDashSAT'] != 0) {
                insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Erro ao atualizar dados!');</script>";
			    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Erro ao atualizar dados!');</script>";
			    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }
        }
    }else{
        $sqlActiveShop = "INSERT INTO tb_install_monitor_sat (shop, date_install, date_send, ativo, status_shop) VALUES ('$shop', '$install', '$send', '$active', '$stShop')";
        $queryActiveShop = mysqli_query($conn,$sqlActiveShop);

        if(mysqli_insert_id($conn)){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Ativar Loja';
            $msgLog = 'Ativar loja ['.$shop.']:['.$send.']:['.$install.']:['.$active.']:['.$stShop.'],cadastrada com sucesso.';
            if ($_SESSION['usuarioIDDashSAT'] != 0) {
                insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Loja cadastrada com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Loja cadastrada com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Ativar Loja';
            $msgLog = 'Erro ao ativar loja ['.$shop.']:['.$send.']:['.$install.']:['.$active.']:['.$stShop.'].';
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
    }
?>