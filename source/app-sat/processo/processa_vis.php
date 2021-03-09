<?php
	session_start();
	require_once("../security/connect.php");
	date_default_timezone_set("America/Sao_Paulo");
	setlocale(LC_ALL, 'pt_BR');
	
	if(isset($_POST['contar'])){

		require_once("../security/seguranca.php");
		//Obter a data atual
		$data['atual'] = date('Y-m-d H:i:s');
		$user = $_SESSION['usuarioNomeDashSAT'];
		$id_user = $_SESSION['usuarioIDDashSAT'];
		$login = $_SESSION['usuarioLoginDashSAT'];
		//Diminuir 1 minuto, contar usuário no site no último minuto
		//$data['online'] = strtotime($data['atual'] . " - 1 minutes");
		
		//Diminuir 20 segundos 
		$data['online'] = strtotime($data['atual'] . " - 20 seconds");
		$data['online'] = date("Y-m-d H:i:s",$data['online']);
		if ((isset($_SESSION['visitanteDashSat'])) AND (!empty($_SESSION['visitanteDashSat']))) {
			if($id_user != 0 ){
				$result_up_visita = "UPDATE tb_visitas_dahshsat SET data_final = '" . $data['atual'] . "' WHERE id = '".$_SESSION['visitanteDashSat']."' AND login  = '".$login."' ";
				$resultado_up_visitas = mysqli_query($conn, $result_up_visita);
				if(mysqli_affected_rows($conn) == 0 ){
					$result_visitas = "INSERT INTO tb_visitas_dahshsat (data_inicio, data_final, id_usuario,usuario, login)VALUES ( '".$data['atual']."', '".$data['atual']."', '".$id_user."','".$user."', '".$login."')";
					$resultado_visitas = mysqli_query($conn, $result_visitas);
					$_SESSION['visitanteDashSat'] = mysqli_insert_id($conn);
				}
			}			
		}else{
			if($id_user != 0 ){
				//Salvar no banco de dados
				$result_visitas = "INSERT INTO tb_visitas_dahshsat (data_inicio, data_final, id_usuario,usuario, login)VALUES ( '".$data['atual']."', '".$data['atual']."', '".$id_user."','".$user."', '".$login."')";
				$resultado_visitas = mysqli_query($conn, $result_visitas);
				$_SESSION['visitanteDashSat'] = mysqli_insert_id($conn);
			}
		}
		
		//Pesquisar os ultimos usuarios online nos 20 segundo
		$result_qnt_visitas = "SELECT count(id) as online FROM tb_visitas_dahshsat WHERE data_final >= '" . $data['online'] . "'";
		$resultado_qnt_visitas = mysqli_query($conn, $result_qnt_visitas);
		$row_qnt_visitas = mysqli_fetch_assoc($resultado_qnt_visitas);
		//echo $data['atual']."|".$data['atual']."'|'".$id_user."'|'".$user."'|'".$login;
	}
?>