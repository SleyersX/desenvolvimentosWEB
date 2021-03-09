<?php
	require_once("../security/seguranca.php");
	protegePagina();
	session_start();
	require_once("../security/connect.php");

	$token = $_SESSION['tokenLogonDashSAT'];

	unset($_SESSION['tempNome']);
	unset($_SESSION['tempEmail']);
	unset($_SESSION['tempLogin']);
	
	$nome = filter_input(INPUT_POST, 'nome-usuario', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'email-usuario', FILTER_SANITIZE_EMAIL);
	$login = filter_input(INPUT_POST, 'nome-login', FILTER_SANITIZE_STRING); 
	$nivel = filter_input(INPUT_POST, 'cb_nivel', FILTER_SANITIZE_STRING);
	$ativo = filter_input(INPUT_POST, 'cb_status', FILTER_SANITIZE_STRING);
	$data = date('Y-m-d H:i:s'); 
	$senha = password_hash('123456', PASSWORD_DEFAULT, ['cost' => 12 ]);
	$avatarDef = '../dist/img/default.png';
	
	if(empty($nivel) || $nivel == 0){
		//Grava LOG
		require_once("processa_log.php");
		$dataLog = date('Y-m-d H:i:s');
		$appCallLog = 'Cadastro Usuário'; 
		$msgLog = 'Cadastro usuário ['.$nome.']:['.$email.']:['.$login.']:['.$nivel.']:['.$avatarDef.']:['.$ativo.'], erro ao realizar cadastro.Revise os dados.';
		if($_SESSION['usuarioIDDashSAT'] != 0 ){
			insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
		}
		$_SESSION['tempNome'] = filter_input(INPUT_POST, 'nome-usuario', FILTER_SANITIZE_STRING);
		$_SESSION['tempEmail'] = filter_input(INPUT_POST, 'email-usuario', FILTER_SANITIZE_EMAIL);
		$_SESSION['tempLogin'] = filter_input(INPUT_POST, 'nome-login', FILTER_SANITIZE_STRING);

		if($_SESSION['usuarioNivelDashSAT'] == 1){
			echo "<script>alert('Erro ao relizar cadastro do usuário revise os dados!');</script>";
			echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
		}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
			echo "<script>alert('Erro ao relizar cadastro do usuário revise os dados!');</script>";
			echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
		}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
			echo "<script>alert('Erro ao relizar cadastro do usuário revise os dados!');</script>";
			echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
		}
	}else{
		if(empty($ativo) || $ativo == 0 ){
			//Grava LOG
			require_once("processa_log.php");
			$dataLog = date('Y-m-d H:i:s');
			$appCallLog = 'Cadastro Usuário'; 
			$msgLog = 'Cadastro usuário ['.$nome.']:['.$email.']:['.$login.']:['.$nivel.']:['.$avatarDef.']:['.$ativo.'], erro ao realizar cadastro.Revise os dados.';
			if($_SESSION['usuarioIDDashSAT'] != 0 ){
				insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
			}
			$_SESSION['tempNome'] = filter_input(INPUT_POST, 'nome-usuario', FILTER_SANITIZE_STRING);
			$_SESSION['tempEmail'] = filter_input(INPUT_POST, 'email-usuario', FILTER_SANITIZE_EMAIL);
			$_SESSION['tempLogin'] = filter_input(INPUT_POST, 'nome-login', FILTER_SANITIZE_STRING);
			if($_SESSION['usuarioNivelDashSAT'] == 1){
				echo "<script>alert('Erro ao relizar cadastro do usuário revise os dados!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
			}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
				echo "<script>alert('Erro ao relizar cadastro do usuário revise os dados!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
			}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
				echo "<script>alert('Erro ao relizar cadastro do usuário revise os dados!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
			}
		}else{
			$sqlExistUserID = "SELECT COUNT(id) AS Ttotal FROM tb_usuarios_dashsat WHERE login LIKE '$login'";
			$queryExistUserID = mysqli_query($conn,$sqlExistUserID);
			$rowExistUserID = mysqli_fetch_assoc($queryExistUserID);
			if($rowExistUserID['Ttotal'] >= 1){
				//Grava LOG
				require_once("processa_log.php");
				$dataLog = date('Y-m-d H:i:s');
				$appCallLog = 'Cadastro Usuário'; 
				$msgLog = 'Cadastro usuário ['.$nome.']:['.$email.']:['.$login.']:['.$nivel.']:['.$avatarDef.']:['.$ativo.'], login já existe no banco de dados.';
				if($_SESSION['usuarioIDDashSAT'] != 0 ){
					insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
				}
				if($_SESSION['usuarioNivelDashSAT'] == 1){
					echo "<script>alert('Erro ao relizar cadastro, login já existe no banco de dados!');</script>";
					echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
				}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
					echo "<script>alert('Erro ao relizar cadastro, login já existe no banco de dados!');</script>";
					echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
				}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
					echo "<script>alert('Erro ao relizar cadastro, login já existe no banco de dados!');</script>";
					echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
				}
			}else{
				$sqlExistUserEmail = "SELECT COUNT(id) AS Ttotal FROM tb_usuarios_dashsat WHERE email LIKE '$email'";
				$queryExistUserEmail = mysqli_query($conn,$sqlExistUser);
				$rowExistUserEmail = mysqli_fetch_assoc($queryExistUser);
				if($rowExistUserEmail['Ttotal'] >= 1){
					//Grava LOG
					require_once("processa_log.php");
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Cadastro Usuário'; 
					$msgLog = 'Cadastro usuário ['.$nome.']:['.$email.']:['.$login.']:['.$nivel.']:['.$avatarDef.']:['.$ativo.'], email já existe no banco de dados.';
					if($_SESSION['usuarioIDDashSAT'] != 0 ){
						insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
					}
					if($_SESSION['usuarioNivelDashSAT'] == 1){
						echo "<script>alert('Erro ao relizar cadastro, e-mail já existe no banco de dados!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
					}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
						echo "<script>alert('Erro ao relizar cadastro, e-mail já existe no banco de dados!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
					}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
						echo "<script>alert('Erro ao relizar cadastro, e-mail já existe no banco de dados!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
					}
				}else{
					$result_usuario = "INSERT INTO tb_usuarios_dashsat (nome, login, email, senha,nivel, ativo, idavatar, avatar, data_criacao, email_verificado) VALUES ('$nome', '$login','$email', '$senha','$nivel','$ativo',0,'$avatarDef','$data',0)";
					$resultado_usuario = mysqli_query($conn, $result_usuario);
					if(mysqli_insert_id($conn)){
						$idnewuser = mysqli_insert_id($conn);

						//Grava LOG
						require_once("processa_log.php");
						$dataLog = date('Y-m-d H:i:s');
						$appCallLog = 'Cadastro Usuário'; 
						$msgLog = 'Cadastro usuário ['.$nome.']:['.$email.']:['.$login.']:['.$nivel.']:['.$avatarDef.']:['.$ativo.'], realizado com sucesso.';
						if($_SESSION['usuarioIDDashSAT'] != 0 ){
							insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
						}
						
						//Enviar E-mail
						require_once("send_email.php");						
						if($_SESSION['usuarioIDDashSAT'] != 0 ){
							send_email_verificacao($idnewuser,$email,$login,$nome);
						}
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
						$appCallLog = 'Cadastro Usuário'; 
						$msgLog = 'Cadastro usuário ['.$nome.']:['.$email.']:['.$login.']:['.$nivel.']:['.$avatarDef.']:['.$ativo.'], erro ao realizar cadastro.Revise os dados.';
						if($_SESSION['usuarioIDDashSAT'] != 0 ){
							insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
						}
						if($_SESSION['usuarioNivelDashSAT'] == 1){
							echo "<script>alert('Erro ao relizar cadastro do usuário revise os dados!');</script>";
							echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
						}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
							echo "<script>alert('Erro ao relizar cadastro do usuário revise os dados!');</script>";
							echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
						}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
							echo "<script>alert('Erro ao relizar cadastro do usuário revise os dados!');</script>";
							echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
						}
						$_SESSION['tempNome'] = filter_input(INPUT_POST, 'nome-usuario', FILTER_SANITIZE_STRING);
						$_SESSION['tempEmail'] = filter_input(INPUT_POST, 'email-usuario', FILTER_SANITIZE_EMAIL);
						$_SESSION['tempLogin'] = filter_input(INPUT_POST, 'nome-login', FILTER_SANITIZE_STRING);
					}
				}
			}
		}
	}

?>
