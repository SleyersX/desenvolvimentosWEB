<?php
    require_once("../security/seguranca.php");
	protegePagina();
	session_start();
	require_once("../security/connect.php");
	
    $token = $_SESSION['tokenLogonDashSAT'];
    
    if(isset($_POST['dias-cupons-acumulados'])){
        $diasCuponsAcumulados = $_POST['dias-cupons-acumulados'];
        $nDiasCuponsAcumulados = $_POST['n-dias-cupons-acumulados'];
    }else{
        $diasCuponsAcumulados = 0;
        $nDiasCuponsAcumulados = $_POST['n-dias-cupons-acumulados'];
    }
    if(isset($_POST['num-cupons-acumulados'])){
        $cuponsAcumulados = $_POST['num-cupons-acumulados'];
        $nCuponsAcumulados = $_POST['n-cupons-acumulados'];
    }else{
        $cuponsAcumulados = 0;
        $nCuponsAcumulados = $_POST['n-cupons-acumulados'];
    }
    if(isset($_POST['nivel-de-bateria'])){
        $nivelBateria = $_POST['nivel-de-bateria'];
    }else{
        $nivelBateria = 0;
    }
    if(isset($_POST['vencimento-certificado'])){
        $vencimentoCertificado = $_POST['vencimento-certificado'];
        $nVencimentoCertificado = $_POST['n-vencimento-certificado'];
    }else{
        $vencimentoCertificado = 0;
        $nVencimentoCertificado = $_POST['n-vencimento-certificado'];
    }
    if(isset($_POST['dias-comun-sefaz'])){
        $diasComuSefaz = $_POST['dias-comun-sefaz'];
        $nDiasComuSefaz = $_POST['n-dias-comun-sefaz'];
    }else{
        $diasComuSefaz = 0;
        $nDiasComuSefaz = $_POST['n-dias-comun-sefaz'];
    }
    if(isset($_POST['relogio-sat'])){
        $relogioSAT = $_POST['relogio-sat'];
        $nRelogioSAT = $_POST['n-relogio-sat'];
    }else{
        $relogioSAT = 0;
        $nRelogioSAT = $_POST['n-relogio-sat'];
    }
    if(isset($_POST['estado-bloqueio'])){
        $estadoBloqueio = $_POST['estado-bloqueio'];
    }else{
        $estadoBloqueio = 0;
    }
    if(isset($_POST['estado-wan'])){
        $estadoWAN = $_POST['estado-wan'];
    }else{
        $estadoWAN = 0;
    }

    $updateAlertas = "UPDATE tb_dashsat_alertas SET dias_cupons_acumulados='$diasCuponsAcumulados',n_dias_cupons_acumulados='$nDiasCuponsAcumulados',cupons_acumulados='$cuponsAcumulados',numero_cupons_acumulados='$nCuponsAcumulados',nivel_bateria='$nivelBateria',vencimento_certificado='$vencimentoCertificado',dias_vencimento_certificado='$nVencimentoCertificado',comunicacao_sefaz='$diasComuSefaz',dias_sem_comunicarf_sefaz='$nDiasComuSefaz',variacao_relogio='$relogioSAT',variacao_ntp='$nRelogioSAT',estado_operacao_bloqueado='$estadoBloqueio',estado_wan_desligado='$estadoWAN' WHERE id = 1";
    $queryUpdate = mysqli_query($conn,$updateAlertas);

    if(mysqli_affected_rows($conn)){
        //Grava LOG
		require_once("processa_log.php");
		$dataLog = date('Y-m-d H:i:s');
		$appCallLog = 'Alteração Alarmes'; 
		$msgLog = 'Alteração realizada com sucesso.';
		if($_SESSION['usuarioIDDashSAT'] != 0 ){
			insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
        }
        if($_SESSION['usuarioNivelDashSAT'] == 1){
            echo "<script>alert('Alteração realizada com sucesso!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">'; 
        }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
            echo "<script>alert('Alteração realizada com sucesso!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">'; 
        }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
            echo "<script>alert('Alteração realizada com sucesso!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">'; 
        }
    }else{
        //Grava LOG
		require_once("processa_log.php");
		$dataLog = date('Y-m-d H:i:s');
		$appCallLog = 'Alteração Alarmes'; 
		$msgLog = 'Nenhuma alteração realizada.';
		if($_SESSION['usuarioIDDashSAT'] != 0 ){
			insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
        }
        if($_SESSION['usuarioNivelDashSAT'] == 1){
            echo "<script>alert('Nenhuma alteração realizada!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
            echo "<script>alert('Nenhuma alteração realizada!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">'; 
        }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
            echo "<script>alert('Nenhuma alteração realizada!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">'; 
        }
    }

?>