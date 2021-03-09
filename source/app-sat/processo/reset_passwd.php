<?php
	//session_start();
	require_once("../security/seguranca.php");
	protegePagina();
	require_once("../security/connect.php");

	$token = $_SESSION['tokenLogonDashSAT'];

    if ($_SESSION['usuarioNivelDashSAT'] == 1) {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $login = filter_input(INPUT_GET, 'login-user', FILTER_SANITIZE_STRING);
        $nivel = filter_input(INPUT_GET, 'nivel-user', FILTER_SANITIZE_STRING);
        if (!empty($id)) {
            $padrao = password_hash('123456', PASSWORD_DEFAULT, ['cost' => 12 ]);
            $result_usuario = "UPDATE tb_usuarios_dashsat set senha ='$padrao', data_modificacao=NOW() WHERE id='$id'";
            $resultado_usuario = mysqli_query($conn, $result_usuario);
            if (mysqli_affected_rows($conn)) {
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Reset Passwd';
                $msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha realizado com sucesso.';
                if ($_SESSION['usuarioIDDashSAT'] != 0) {
                    insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                }
                if ($_SESSION['usuarioNivelDashSAT'] == 1) {
                    echo "<script>alert('Reset de senha com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                } elseif ($_SESSION['usuarioNivelDashSAT'] == 2) {
                    echo "<script>alert('Reset de senha com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                } elseif ($_SESSION['usuarioNivelDashSAT'] == 3) {
                    echo "<script>alert('Reset de senha com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
                }
            } else {
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Reset Passwd';
                $msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha, erro no processo.';
                if ($_SESSION['usuarioIDDashSAT'] != 0) {
                    insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                }
                if ($_SESSION['usuarioNivelDashSAT'] == 1) {
                    echo "<script>alert('Erro no processox!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                } elseif ($_SESSION['usuarioNivelDashSAT'] == 2) {
                    echo "<script>alert('Erro no processox!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                } elseif ($_SESSION['usuarioNivelDashSAT'] == 3) {
                    echo "<script>alert('Erro no processox!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
                }
            }
        } else {
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Reset Passwd';
            $msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha, necessário selecionar um usuário.';
            if ($_SESSION['usuarioIDDashSAT'] != 0) {
                insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
            }
            if ($_SESSION['usuarioNivelDashSAT'] == 1) {
                echo "<script>alert('Necessário selecionar um usuário!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            } elseif ($_SESSION['usuarioNivelDashSAT'] == 2) {
                echo "<script>alert('Necessário selecionar um usuário!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            } elseif ($_SESSION['usuarioNivelDashSAT'] == 3) {
                echo "<script>alert('Necessário selecionar um usuário!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
            }
        }
    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
		$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
		$login = filter_input(INPUT_GET, 'login-user', FILTER_SANITIZE_STRING);
		$nivel = filter_input(INPUT_GET, 'nivel-user', FILTER_SANITIZE_STRING);
		$resultado_nivel = "SELECT * FROM tb_usuarios_dashsat WHERE id = '".$id."'";
		$resulta_nivel = mysqli_query($conn, $resultado_nivel);
		$row_result_nivel = mysqli_fetch_assoc($resulta_nivel);
		
		$nivel = $row_result_nivel['nivel'];
		
        if ($nivel == 1) {
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Reset Passwd';
            $msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha.Ops!Usuário não permitido.';
            if ($_SESSION['usuarioIDDashSAT'] != 0) {
                insert_log_I($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
            }
            if ($_SESSION['usuarioNivelDashSAT'] == 1) {
                echo "<script>alert('Ops!Usuário não permitido!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            } elseif ($_SESSION['usuarioNivelDashSAT'] == 2) {
                echo "<script>alert('Ops!Usuário não permitido!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            } elseif ($_SESSION['usuarioNivelDashSAT'] == 3) {
                echo "<script>alert('Ops!Usuário não permitido!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
            }
        }else{
			if(!empty($id)){
				$padrao = password_hash('123456', PASSWORD_DEFAULT, ['cost' => 12 ]);
				$result_usuario = "UPDATE tb_usuarios_dashsat set senha ='$padrao', data_modificacao=NOW() WHERE id='$id'";
				$resultado_usuario = mysqli_query($conn, $result_usuario);
				if(mysqli_affected_rows($conn)){
					//Grava LOG
					require_once("processa_log.php");
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Reset Passwd'; 
					$msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha realizado com sucesso.';
					if($_SESSION['usuarioIDDashSAT'] != 0 ){
						insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
					}
					if($_SESSION['usuarioNivelDashSAT'] == 1){
						echo "<script>alert('Reset de senha com sucesso!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
					}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
						echo "<script>alert('Reset de senha com sucesso!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
					}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
						echo "<script>alert('Reset de senha com sucesso!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
					}
				}else{
					//Grava LOG
					require_once("processa_log.php");
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Reset Passwd'; 
					$msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha, erro no processo.';
					if($_SESSION['usuarioIDDashSAT'] != 0 ){
						insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
					}
					if($_SESSION['usuarioNivelDashSAT'] == 1){
						echo "<script>alert('Erro no processox!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
					}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
						echo "<script>alert('Erro no processox!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
					}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
						echo "<script>alert('Erro no processox!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
					}
				}
			}else{
				//Grava LOG
				require_once("processa_log.php");
				$dataLog = date('Y-m-d H:i:s');
				$appCallLog = 'Reset Passwd'; 
				$msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha, necessário selecionar um usuário.';
				if($_SESSION['usuarioIDDashSAT'] != 0 ){
					insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
				}	
				if($_SESSION['usuarioNivelDashSAT'] == 1){
					echo "<script>alert('Necessário selecionar um usuário!');</script>";
					echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
				}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
					echo "<script>alert('Necessário selecionar um usuário!');</script>";
					echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
				}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
					echo "<script>alert('Necessário selecionar um usuário!');</script>";
					echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
				}
			}
		}
	}else{
		$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
		$login = filter_input(INPUT_GET, 'login-user', FILTER_SANITIZE_STRING);
    	$nivel = filter_input(INPUT_GET, 'nivel-user', FILTER_SANITIZE_STRING);
		$resultado_nivel = "SELECT * FROM tb_usuarios_dashsat WHERE id = '".$id."'";
		$resulta_nivel = mysqli_query($conn, $resultado_nivel);
		$row_result_nivel = mysqli_fetch_assoc($resulta_nivel);
		
		$nivel = $row_result_nivel['nivel'];
		
		if ($nivel == 1 || $nivel == 2){
			//Grava LOG
			require_once("processa_log.php");
			$dataLog = date('Y-m-d H:i:s');
			$appCallLog = 'Reset Passwd'; 
			$msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha.Ops!Usuário não permitido.';
			if($_SESSION['usuarioIDDashSAT'] != 0 ){
				insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
			}
			if($_SESSION['usuarioNivelDashSAT'] == 1){
				echo "<script>alert('Ops!Usuário não permitido!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
			}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
				echo "<script>alert('Ops!Usuário não permitido!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
			}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
				echo "<script>alert('Ops!Usuário não permitido!');</script>";
				echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
			}
		}else{
			if(!empty($id)){
				$padrao = password_hash('123456', PASSWORD_DEFAULT, ['cost' => 12 ]);
				$result_usuario = "UPDATE tb_usuarios_dashsat set senha ='$padrao', data_modificacao=NOW() WHERE id='$id'";
				$resultado_usuario = mysqli_query($conn, $result_usuario);
				if(mysqli_affected_rows($conn)){
					//Grava LOG
					require_once("processa_log.php");
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Reset Passwd'; 
					$msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha realizado com sucesso.';
					if($_SESSION['usuarioIDDashSAT'] != 0 ){
						insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
					}
					if($_SESSION['usuarioNivelDashSAT'] == 1){
						echo "<script>alert('Reset de senha com sucesso!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
					}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
						echo "<script>alert('Reset de senha com sucesso!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
					}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
						echo "<script>alert('Reset de senha com sucesso!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
					}
				}else{
					//Grava LOG
					require_once("processa_log.php");
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Reset Passwd'; 
					$msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha, erro no processo.';
					if($_SESSION['usuarioIDDashSAT'] != 0 ){
						insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
					}
					if($_SESSION['usuarioNivelDashSAT'] == 1){
						echo "<script>alert('Erro no processoZ!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
					}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
						echo "<script>alert('Erro no processoZ!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
					}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
						echo "<script>alert('Erro no processoZ!');</script>";
						echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
					}
				}
			}else{	
				//Grava LOG
				require_once("processa_log.php");
				$dataLog = date('Y-m-d H:i:s');
				$appCallLog = 'Reset Passwd'; 
				$msgLog = 'Reset ['.$id.']:['.$login.']:['.$nivel.'] senha, erro no processo.';
				if($_SESSION['usuarioIDDashSAT'] != 0 ){
					insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
				}
				if($_SESSION['usuarioNivelDashSAT'] == 1){
					echo "<script>alert('Erro no processo!');</script>";
					echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
				}elseif($_SESSION['usuarioNivelDashSAT'] == 2){
					echo "<script>alert('Erro no processo!');</script>";
					echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
				}elseif($_SESSION['usuarioNivelDashSAT'] == 3){
					echo "<script>alert('Erro no processo!');</script>";
					echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
				}
			}
		}
	}
?>