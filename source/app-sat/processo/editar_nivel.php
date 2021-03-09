<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");
	
	$token = $_SESSION['tokenLogonDashSAT'];
	
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
	$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
	$data = date('Y-m-d H:i:s'); 
		
	if(!empty($id)){
		if(empty($nome)){
			//Grava LOG
			require_once("processa_log.php");
			$dataLog = date('Y-m-d H:i:s');
			$appCallLog = 'Editar Nivel'; 
			$msgLog = 'Dados ['.$id.']:['.$nome.'], erro ao realizar atualização.Revise os dados.';
			if($_SESSION['usuarioIDDashSAT'] != 0 ){
				insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
			}
			if($_SESSION['usuarioNivelDashSAT'] == 1){
				echo "<script>alert('Erro ao atualizar dados do nível revise os dados!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
			}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
				echo "<script>alert('Erro ao atualizar dados do nível revise os dados!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
			}
        }
        $updateNivel = "UPDATE tb_niveis_dashsat SET descricao = '$nome', data_modificacao = NOW() WHERE id = '$id'";
        $queryUpdate = mysqli_query($conn,$updateNivel);
        if(mysqli_affected_rows($conn)){
            //Grava LOG
			require_once("processa_log.php");
			$dataLog = date('Y-m-d H:i:s');
			$appCallLog = 'Editar Nivel'; 
			$msgLog = 'Dados ['.$id.']:['.$nome.'], atualizados com sucesso.';
			if($_SESSION['usuarioIDDashSAT'] != 0 ){
				insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
			}
			if($_SESSION['usuarioNivelDashSAT'] == 1){
				echo "<script>alert('Dados atualizados com sucesso!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
			}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
				echo "<script>alert('Dados atualizados com sucesso!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
			}
        }else{
            //Grava LOG
			require_once("processa_log.php");
			$dataLog = date('Y-m-d H:i:s');
			$appCallLog = 'Editar Nivel'; 
			$msgLog = 'Dados ['.$id.']:['.$nome.'], nenhuma alteração necessária.';
			if($_SESSION['usuarioIDDashSAT'] != 0 ){
				insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
			}
			if($_SESSION['usuarioNivelDashSAT'] == 1){
				echo "<script>alert('Nenhuma alteração necessária!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
			}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
				echo "<script>alert('Nenhuma alteração necessária!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
			}
        }
	}else{
		//Grava LOG
		require_once("processa_log.php");
		$dataLog = date('Y-m-d H:i:s');
		$appCallLog = 'Editar Nivel'; 
		$msgLog = 'Necessário selecionar um nível!';
		if($_SESSION['usuarioIDDashSAT'] != 0 ){
			insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
		}	
		$_SESSION['msg'] = "<p style='color:red;'>Necessário selecionar um nível</p>";
		if($_SESSION['usuarioNivelDashSAT'] == 1){
			echo "<script>alert('Necessário selecionar um nível!');</script>";
			echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
		}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
			echo "<script>alert('Necessário selecionar um nível!');</script>";
			echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
		}
	}

?>
