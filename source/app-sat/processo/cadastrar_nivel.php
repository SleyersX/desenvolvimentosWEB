<?php
	require_once("../security/seguranca.php");
	protegePagina();
	session_start();
	require_once("../security/connect.php");
	
	$token = $_SESSION['tokenLogonDashSAT'];
	$nivel = filter_input(INPUT_POST, 'nivel', FILTER_SANITIZE_STRING);
	$descNivel = filter_input(INPUT_POST, 'desc-nivel', FILTER_SANITIZE_STRING);
	$data = date('Y-m-d H:i:s'); 
    
    $sqlNewNivel = "INSERT INTO tb_niveis_dashsat (nivel, descricao, data_criacao) VALUES ('$nivel', '$descNivel','$data')";
	$queryNewNivel = mysqli_query($conn, $sqlNewNivel);
	if(mysqli_insert_id($conn)){
		//Grava LOG
		require_once("processa_log.php");
		$dataLog = date('Y-m-d H:i:s');
		$appCallLog = 'Cadastro Nivel'; 
		$msgLog = 'Cadastro nível ['.$nivel.']:['.$descNivel.'], realizado com sucesso.';
		if($_SESSION['usuarioIDDashSAT'] != 0 ){
			insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
		}
		if($_SESSION['usuarioNivelDashSAT'] == 1){
			echo "<script>alert('Cadastro realizado com sucesso!');</script>";
			echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
		}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
			echo "<script>alert('Cadastro realizado com sucesso!');</script>";
			echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
		}
	}else{
		//Grava LOG
		require_once("processa_log.php");
		$dataLog = date('Y-m-d H:i:s');
		$appCallLog = 'Cadastro Nivel'; 
		$msgLog = 'Cadastro nível ['.$nivel.']:['.$descNivel.'], erro ao realizar cadastro.';
		if($_SESSION['usuarioIDDashSAT'] != 0 ){
			insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
		}	
		if($_SESSION['usuarioNivelDashSAT'] == 1){
			echo "<script>alert('Erro cadastrar nivel!');</script>";
			echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
		}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
			echo "<script>alert('Erro cadastrar nivel!');</script>";
			echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
		}
	}

?>