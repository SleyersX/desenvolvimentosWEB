<?php
/**
* Sistema de segurança com acesso restrito
*
* Usado para restringir o acesso de certas páginas do seu site
*
* @version 2.0
* @package SistemaSeguranca
*/
//  Configurações do Script
// ==============================
$_SG['conectaServidorDashSAT'] = true;       // Abre uma conexão com o servidor MySQL?
$_SG['abreSessaoDashSAT'] = true;            // Inicia a sessão com um session_start()?
$_SG['caseSensitiveDashSAT'] = false;        // Usar case-sensitive?
$_SG['validaSempreDashSAT'] = true;          // Deseja validar o usuário e a senha a cada carregamento de página
$_SG['paginaLoginDashSAT'] = "../index.php"; // Página de login

if ($_SG['conectaServidorDashSAT'] == true) {
    //Conectando no servidor
    require_once("connect.php");
    }
    // Verifica se precisa iniciar a sessão
    if ($_SG['abreSessaoDashSAT'] == true)
      session_start();
    /**
    * Função que valida um usuário e senha
    *
    * @param string $usuariot - O usuário a ser validado
    * @param string $senhat - A senha a ser validada
    *
    * @return bool - Se o usuário foi validado ou não (true/false)
    */
    function validaUsuario($usuariot, $senhat) {
		global $_SG;
		$cS = ($_SG['caseSensitiveDashSAT']) ? 'BINARY' : ''; 
		// Usa a função addslashes para escapar as aspas
		$nusuario = addslashes($usuariot);
		$nsenha = addslashes($senhat);
		//$nlembrete = addcslashes($remembert);
		//echo "<script>alert('Senha1:$nsenha');</script>";
		$user = "root";
		$password = "diabrasil";
		
		// Monta uma consulta SQL (query) para procurar um usuário
		$conn = mysqli_connect('localhost', $user, $password , 'srvremoto')or die ("Erro ao conectar com o banco de dados!");
		//require_once("conecta.php");
		//$sql = "SELECT * FROM tb_usuarios WHERE login='$nusuario' AND senha='$nsenha' LIMIT 1";
		$sql = "SELECT * FROM tb_usuarios_dashsat WHERE login='$nusuario'";
		$query = mysqli_query($conn,$sql);
		$resultado = mysqli_fetch_assoc($query);
		// Verifica se encontrou algum registro
		if (empty($resultado)){
		// Nenhum registro foi encontrado => o usuário é inválido
		return false;
		}else{
			$sql_ = "SELECT * FROM tb_usuarios_dashsat WHERE login='$nusuario'";
			$query_ = mysqli_query($conn,$sql_);
			while($resulta = mysqli_fetch_array($query_))
			{
				$_SESSION['usuarioStatusDashSAT'] = $resulta['ativo'];
				$_SESSION['usuarioLoginDashSAT'] = $nusuario;
				$senha_crypt = $resulta['senha'];
				//echo "<script>alert('$senha_crypt');</script>";
				//echo "<script>alert('Senha2:$nsenha');</script>";
				$check_senha=password_verify($nsenha, $senha_crypt);
				//echo "<script>alert('$check_senha');</script>";
				if($check_senha == true){
					// Definimos dois valores na sessão com os dados do usuário
					$_SESSION['usuarioIDDashSAT'] = $resulta['id']; // Pega o valor da coluna 'id do registro encontrado no MySQL
					$_SESSION['usuarioNomeDashSAT'] = $resulta['nome']; // Pega o valor da coluna 'nome' do registro encontrado no MySQL
					$_SESSION['usuarioNivelDashSAT'] = $resulta['nivel'];
					$_SESSION['usuarioAvatarDashSAT'] = $resulta['avatar'];
					$_SESSION['usuarioIDAvatarDashSAT'] = $resulta['idavatar'];
					$_SESSION['usuarioStatusEmail'] = $resulta['email_verificado'];

					// Verifica a opção se sempre validar o login
					if ($_SG['validaSempreDashSAT'] == true) {
						// Definimos dois valores na sessão com os dados do login
						$_SESSION['usuarioLoginDashSAT'] = $nusuario;
						$_SESSION['usuarioSenhaDashSAT'] = $nsenha;
					}

					$st=$_SESSION['usuarioStatusDashSAT'];
					$randomico = mt_rand(0,999999);
					$nivelLoginToken = $_SESSION['usuarioNivelDashSAT'];
					$id = $_SESSION['usuarioIDDashSAT'];
					$userName = $_SESSION['usuarioNomeDashSAT'];
					$userLogin = $_SESSION['usuarioLoginDashSAT'];
					$dataLog = date('Y-m-d H:i:s');

					$tokenLogon = $randomico.$id.$nivelLoginToken.$userName.$userLogin.$dataLog;
					$tokenLogon = md5($tokenLogon);
					$tokenLogon = $tokenLogon.md5($tokenLogon);
					$_SESSION['tokenLogonDashSAT'] = $tokenLogon;

					if($_SESSION['usuarioStatusDashSAT'] == 1 || $_SESSION['usuarioStatusDashSAT'] == 4){
						$sqlExiteBloqueio = "SELECT COUNT(id) AS existe_registro FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1";
						$queryExisteBloqueio = mysqli_query($conn,$sqlExiteBloqueio);
						$rowExisteBloqueio = mysqli_fetch_assoc($queryExisteBloqueio);
						if($rowExisteBloqueio['existe_registro'] >= 1){
							$sqlMaxIDUsrBloqTemp = "SELECT MAX(id) AS id FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d')";
							$queryMaxIDUsrBloqTemp = mysqli_query($conn,$sqlMaxIDUsrBloqTemp);
							$rowMaxIDUsrBloqTemp = mysqli_fetch_assoc($queryMaxIDUsrBloqTemp);
							$idTempBloqUser = $rowMaxIDUsrBloqTemp['id'];

							$sqlTbBloqTemp = "SELECT id, id_user, nome_user, login_user, count_tentativas, data_bloqueio, tempo_desbloqueio, timestampdiff(MINUTE, data_bloqueio, NOW()) AS tempo_decorrido FROM tb_bloqueio_temporario WHERE id = '$idTempBloqUser'";
							$queryTbBloqTemp = mysqli_query($conn,$sqlTbBloqTemp);
							$rowTbBloqTemp = mysqli_fetch_assoc($queryTbBloqTemp);
							$tentativa = $rowTbBloqTemp['count_tentativas'];
							if($rowTbBloqTemp['tempo_decorrido'] <= $rowTbBloqTemp['tempo_desbloqueio'] && $rowTbBloqTemp['count_tentativas'] >= 5){
								$_SESSION['usuarioBloqDashsat'] = 1;
								$dataLog = date('Y-m-d H:i:s');
								$appCallLog = 'Login Sistema';
								$msgLog = 'Usuário ['.$userLogin.'] bloqueado.';
								insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
							}else if ($rowTbBloqTemp['tempo_decorrido'] > $rowTbBloqTemp['tempo_desbloqueio'] && $rowTbBloqTemp['count_tentativas'] > 5){								
								$sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
								$queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Usuário ['.$userLogin.'] bloqueado.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] bloqueado.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = '0' WHERE id = '$idTempBloqUser'";
								$queryUpdateBloqTemp = mysqli_query($conn,$sqlUpdateBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Registro usuário ['.$userLogin.'], update status bloqueio.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] status bloqueio.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$_SESSION['usuarioBloqDashsat'] = 0;
								$_SESSION['usuarioStatusDashSAT'] = 1;
								$_SESSION['statuslogon'] = 1;
							}else if($rowTbBloqTemp['tempo_decorrido'] <= $rowTbBloqTemp['tempo_desbloqueio'] && $rowTbBloqTemp['count_tentativas'] <= 5){
								$sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
								$queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Usuário ['.$userLogin.'] bloqueado.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] bloqueado.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = '0' WHERE id = '$idTempBloqUser'";
								$queryUpdateBloqTemp = mysqli_query($conn,$sqlUpdateBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Registro usuário ['.$userLogin.'], update status bloqueio.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] status bloqueio.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$_SESSION['usuarioBloqDashsat'] = 0;
								$_SESSION['usuarioStatusDashSAT'] = 1;
								$_SESSION['statuslogon'] = 1;
							}else if($rowTbBloqTemp['tempo_decorrido'] >= $rowTbBloqTemp['tempo_desbloqueio'] && $rowTbBloqTemp['count_tentativas'] <= 5){
								$sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
								$queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Usuário ['.$userLogin.'] bloqueado.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] bloqueado.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = '0' WHERE id = '$idTempBloqUser'";
								$queryUpdateBloqTemp = mysqli_query($conn,$sqlUpdateBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Registro usuário ['.$userLogin.'], update status bloqueio.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] status bloqueio.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$_SESSION['usuarioBloqDashsat'] = 0;
								$_SESSION['usuarioStatusDashSAT'] = 1;
								$_SESSION['statuslogon'] = 1;
							}
						}else{
							$sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
							$queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
							if(mysqli_affected_rows($conn)){
								$dataLog = date('Y-m-d H:i:s');
								$appCallLog = 'Login Sistema';
								$msgLog = 'Usuário ['.$userLogin.'] bloqueado.';
								insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
							}else{
								$dataLog = date('Y-m-d H:i:s');
								$appCallLog = 'Login Sistema';
								$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] bloqueado.';
								insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
							}
							$sqlMaxIDUsrBloqTemp = "SELECT MAX(id) AS id FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d')";
							$queryMaxIDUsrBloqTemp = mysqli_query($conn,$sqlMaxIDUsrBloqTemp);
							$rowMaxIDUsrBloqTemp = mysqli_fetch_assoc($queryMaxIDUsrBloqTemp);
							$idTempBloqUser = $rowMaxIDUsrBloqTemp['id'];

							$sqlTbBloqTemp = "SELECT id, id_user, nome_user, login_user, count_tentativas, data_bloqueio, tempo_desbloqueio, timestampdiff(MINUTE, data_bloqueio, NOW()) AS tempo_decorrido FROM tb_bloqueio_temporario WHERE id = '$idTempBloqUser'";
							$queryTbBloqTemp = mysqli_query($conn,$sqlTbBloqTemp);
							$rowTbBloqTemp = mysqli_fetch_assoc($queryTbBloqTemp);

							$sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = '0' WHERE id = '$idTempBloqUser'";
							$queryUpdateBloqTemp = mysqli_query($conn,$sqlUpdateBloqTemp);
							if(mysqli_affected_rows($conn)){
								$dataLog = date('Y-m-d H:i:s');
								$appCallLog = 'Login Sistema';
								$msgLog = 'Registro usuário ['.$userLogin.'], update status bloqueio.';
								insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
							}else{
								$dataLog = date('Y-m-d H:i:s');
								$appCallLog = 'Login Sistema';
								$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] status bloqueio.';
								insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
							}
							$_SESSION['usuarioBloqDashsat'] = 0;
							$_SESSION['usuarioStatusDashSAT'] = 1;
							$_SESSION['statuslogon'] = 1;
						}
					}else if($_SESSION['usuarioStatusDashSAT'] == 3){
						$sqlExiteBloqueio = "SELECT COUNT(id) AS existe_registro FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1";
						$queryExisteBloqueio = mysqli_query($conn,$sqlExiteBloqueio);
						$rowExisteBloqueio = mysqli_fetch_assoc($queryExisteBloqueio);
						if($rowExisteBloqueio['existe_registro'] >= 1) {
							$sqlMaxIDUsrBloqTemp = "SELECT MAX(id) AS id FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d')";
							$queryMaxIDUsrBloqTemp = mysqli_query($conn, $sqlMaxIDUsrBloqTemp);
							$rowMaxIDUsrBloqTemp = mysqli_fetch_assoc($queryMaxIDUsrBloqTemp);
							$idTempBloqUser = $rowMaxIDUsrBloqTemp['id'];

							$sqlTbBloqTemp = "SELECT id, id_user, nome_user, login_user, count_tentativas, data_bloqueio, tempo_desbloqueio, timestampdiff(MINUTE, data_bloqueio, NOW()) AS tempo_decorrido FROM tb_bloqueio_temporario WHERE id = '$idTempBloqUser'";
							$queryTbBloqTemp = mysqli_query($conn, $sqlTbBloqTemp);
							$rowTbBloqTemp = mysqli_fetch_assoc($queryTbBloqTemp);
							$tentativa = $rowTbBloqTemp['count_tentativas'];
							if ($rowTbBloqTemp['tempo_decorrido'] <= $rowTbBloqTemp['tempo_desbloqueio'] && $rowTbBloqTemp['count_tentativas'] >= 5) {
								$sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 3 WHERE id = '$id'";
								$queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Usuário ['.$userLogin.'] bloqueado, número de tentativas ['.$tentativa.'].';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Erro registro usuário ['.$userLogin.'] bloqueado, número de tentativas ['.$tentativa.'].';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$_SESSION['usuarioBloqDashsat'] = 1;
								$_SESSION['usuarioStatusDashSAT'] = 3;
							}else if ($rowTbBloqTemp['tempo_decorrido'] > $rowTbBloqTemp['tempo_desbloqueio'] && $rowTbBloqTemp['count_tentativas'] > 5){								$sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
								$queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Usuário ['.$userLogin.'] bloqueado.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] bloqueado.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = '0' WHERE id = '$idTempBloqUser'";
								$queryUpdateBloqTemp = mysqli_query($conn,$sqlUpdateBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Registro usuário ['.$userLogin.'], update status bloqueio.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] status bloqueio.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$_SESSION['usuarioBloqDashsat'] = 0;
								$_SESSION['usuarioStatusDashSAT'] = 1;
								$_SESSION['statuslogon'] = 1;
							}else{
								$sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
								$queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Usuário ['.$userLogin.'] bloqueado.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] bloqueado.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = '0' WHERE id = '$idTempBloqUser'";
								$queryUpdateBloqTemp = mysqli_query($conn,$sqlUpdateBloqTemp);
								if(mysqli_affected_rows($conn)){
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Registro usuário ['.$userLogin.'], update status bloqueio.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}else{
									$dataLog = date('Y-m-d H:i:s');
									$appCallLog = 'Login Sistema';
									$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] status bloqueio.';
									insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
								}
								$_SESSION['usuarioBloqDashsat'] = 0;
								$_SESSION['usuarioStatusDashSAT'] = 1;
								$_SESSION['statuslogon'] = 1;
							}
						}else{
							$sqlMaxIDUsrBloqTemp = "SELECT MAX(id) AS id FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d')";
							$queryMaxIDUsrBloqTemp = mysqli_query($conn,$sqlMaxIDUsrBloqTemp);
							$rowMaxIDUsrBloqTemp = mysqli_fetch_assoc($queryMaxIDUsrBloqTemp);
							$idTempBloqUser = $rowMaxIDUsrBloqTemp['id'];

							$sqlTbBloqTemp = "SELECT id, id_user, nome_user, login_user, count_tentativas, data_bloqueio, tempo_desbloqueio, timestampdiff(MINUTE, data_bloqueio, NOW()) AS tempo_decorrido FROM tb_bloqueio_temporario WHERE id = '$idTempBloqUser'";
							$queryTbBloqTemp = mysqli_query($conn,$sqlTbBloqTemp);
							$rowTbBloqTemp = mysqli_fetch_assoc($queryTbBloqTemp);

							$sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = '0' WHERE id = '$idTempBloqUser'";
							$queryUpdateBloqTemp = mysqli_query($conn,$sqlUpdateBloqTemp);
							if(mysqli_affected_rows($conn)){
								$dataLog = date('Y-m-d H:i:s');
								$appCallLog = 'Login Sistema';
								$msgLog = 'Registro usuário ['.$userLogin.'], update status bloqueio.';
								insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
							}else{
								$dataLog = date('Y-m-d H:i:s');
								$appCallLog = 'Login Sistema';
								$msgLog = 'Nenhuma alteração no registro usuário ['.$userLogin.'] status bloqueio.';
								insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
							}
							$_SESSION['usuarioBloqDashsat'] = 0;
							$_SESSION['usuarioStatusDashSAT'] = 1;
							$_SESSION['statuslogon'] = 1;
						}
					}
					if($_SESSION['statuslogon'] == 1 ){

						$appCallLog = 'Login Sistema';
						$msgLog = '['.$st.']Usuário conectado com sucesso.';
						insert_log($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);

						$user = "root";
						$password = "diabrasil";

						// Monta uma consulta SQL (query) para procurar um usuário
						$dataIniSessao = date('Y-m-d H:i:s');
						$conn_ = mysqli_connect('localhost', $user, $password , 'srvremoto');
						$sqlInsertRegistraTempLogin = "INSERT INTO tb_sessoes_login_dashsat (data_inicio,token,id_user, nome_user, login_user) VALUES ('$dataIniSessao','$tokenLogon','$id', '$userName', '$userLogin')";
						$queryRegistraTempLogin = mysqli_query($conn_, $sqlInsertRegistraTempLogin);
						if(mysqli_insert_id($conn_)){
							//Grava LOG
							$_SESSION['idLoginTempDashSAT'] = mysqli_insert_id($conn_);
							$dataLog = date('Y-m-d H:i:s');
							$appCallLog = 'Registro de Sessão';
							$msgLog = 'Registro de sessão do login realizado com sucesso.';
							insert_log($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);

							//Login Temp
							insert_login_temp($id,$userLogin);
						}else {
							//Grava LOG
							$dataLog = date('Y-m-d H:i:s');
							$appCallLog = 'Registro de Sessão';
							$msgLog = 'Erro ao registrar sessão do login.';
							insert_log($id, $userName, $userLogin, $appCallLog, $dataLog, $msgLog);
						}
					}
					return true;
				}else{
					if($_SESSION['usuarioStatusDashSAT'] == 1 || $_SESSION['usuarioStatusDashSAT'] == 3){
						insert_bloqueio_temp($resulta['id'],$resulta['nome'],$_SESSION['usuarioLoginDashSAT']);
					}
					return false;
				}
			}
		}
	}
	/**
    * Função fpara gravar os Logs do usuário do Sistema
    *
    * @param int    $idUserLog - ID usuário registrado no Log
    * @param string $nomeUserLog - Usuário registrado no Log
    * @param string $loginUserLog - Login do usuário registrado no Log
    * @param string $appLog - Aplicação que executou o Log
    * @param string $dataLog - Data do Log
    * @param string $logDados - Dados do Log
    *
    * @return bool - Se o Log foi gravado (true/false)
    */
    function insert_log($idUserLog,$nomeUserLog,$loginUserLog,$appLog,$dataLog,$logDados){
        $user="root";
        $passwd="diabrasil";
        $host="localhost";
        $banco="srvremoto";
        $conn= mysqli_connect($host,$user,$passwd,$banco);

        $sqlInsertLog = "INSERT INTO tb_log_dashsat (id_user, nome_user, login_user, aplicacao, data_log, log_dados) VALUES ('$idUserLog','$nomeUserLog','$loginUserLog','$appLog','$dataLog','$logDados')";
        $queryInsertLog = mysqli_query($conn,$sqlInsertLog);
        
        if(mysqli_insert_id($conn)){
            return false;
        }else{
            return true;
        }
	}
	/**
    * Função fpara gravar os Logs do usuário do Sistema
    *
    * @param int    $idUserLog - ID usuário registrado no Log
    * @param string $loginUserLog - Login do usuário registrado no Log
    *
    * @return bool - Se o Log foi gravado (true/false)
    */
    function insert_login_temp($idUserLogin,$loginUserLogin){
        $user="root";
        $passwd="diabrasil";
        $host="localhost";
        $banco="srvremoto";
        $conn= mysqli_connect($host,$user,$passwd,$banco);

        $sqlInsertLoginTemp = "INSERT INTO tb_temp_login_dashsat (id_user, login_user) VALUES ('$idUserLogin','$loginUserLogin')";
        $queryInsertLog = mysqli_query($conn,$sqlInsertLoginTemp);
        
        if(mysqli_insert_id($conn)){
            return false;
        }else{
            return true;
        }
	}
	/**
    * Função fpara gravar os Logs do usuário do Sistema
    *
    * @param int    $idUserLog - ID usuário registrado no Log
    * @param string $loginUserLog - Login do usuário registrado no Log
    *
    * @return bool - Se o Log foi gravado (true/false)
    */
    function delete_login_temp($idUserLogin){
        $user="root";
        $passwd="diabrasil";
        $host="localhost";
        $banco="srvremoto";
        $conn= mysqli_connect($host,$user,$passwd,$banco);

        $sqlInsertLoginTemp = "DELETE FROM tb_temp_login_dashsat WHERE id_user = '$idUserLogin'";
        $queryInsertLog = mysqli_query($conn,$sqlInsertLoginTemp);
        
        if(mysqli_affected_rows($conn)){
            return false;
        }else{
            return true;
        }
    }
	/**
	 * Função para gravar o bloqueio temporário
	 *
	 * @param int    $id_user    - ID usuário registrado
	 * @param string $nome_user  - Usuário registrado
	 * @param string $login_user - Login do usuário registrado
	 *
	 * @return bool - Se o Log foi gravado (true/false)
	 */
    function insert_bloqueio_temp($id_user,$nome_user,$login_user){
		$user="root";
		$passwd="diabrasil";
		$host="localhost";
		$banco="srvremoto";
		$conn= mysqli_connect($host,$user,$passwd,$banco);

		$sqlExiteBloqueio = "SELECT COUNT(id) AS existe_registro FROM tb_bloqueio_temporario WHERE id_user = '$id_user' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
		$queryExisteBloqueio = mysqli_query($conn,$sqlExiteBloqueio);
		$rowExisteBloqueio = mysqli_fetch_assoc($queryExisteBloqueio);

		if($rowExisteBloqueio['existe_registro'] >= 1){
			$sqlMaxIDUsrBloqTemp = "SELECT MAX(id) AS id FROM tb_bloqueio_temporario WHERE id_user = '$id_user' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
			$queryMaxIDUsrBloqTemp = mysqli_query($conn,$sqlMaxIDUsrBloqTemp);
			$rowMaxIDUsrBloqTemp = mysqli_fetch_assoc($queryMaxIDUsrBloqTemp);
			$idTempBloqUser = $rowMaxIDUsrBloqTemp['id'];

			$sqlTbBloqTemp = "SELECT id, id_user, nome_user, login_user, count_tentativas, data_bloqueio, tempo_desbloqueio, timestampdiff(MINUTE, data_bloqueio, NOW()) AS tempo_decorrido FROM tb_bloqueio_temporario WHERE id = '$idTempBloqUser'";
			$queryTbBloqTemp = mysqli_query($conn,$sqlTbBloqTemp);
			$rowTbBloqTemp = mysqli_fetch_assoc($queryTbBloqTemp);

			if($rowTbBloqTemp['tempo_decorrido'] <= $rowTbBloqTemp['tempo_desbloqueio'] && $rowTbBloqTemp['count_tentativas'] <= 5){
				$tentativa = $rowTbBloqTemp['count_tentativas'] + 1;
				$sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET count_tentativas = '$tentativa' WHERE id = '$idTempBloqUser'";
				$queryUpdateBloqTemp = mysqli_query($conn,$sqlUpdateBloqTemp);
				if(mysqli_affected_rows($conn)){
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Login Sistema';
					$msgLog = 'Registro usuário ['.$login_user.'], update número de tentativas ['.$tentativa.'].';
					insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
					return false;
				}else{
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Login Sistema';
					$msgLog = 'Nenhuma alteração no registro usuário ['.$login_user.'], update número de tentativas ['.$tentativa.'].';
					insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
					return true;
				}
			}else if($rowTbBloqTemp['tempo_decorrido'] > $rowTbBloqTemp['tempo_desbloqueio'] && $rowTbBloqTemp['count_tentativas'] == 5){
				$tentativa = $rowTbBloqTemp['count_tentativas'] + 1;
				$sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id_user'";
				$queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
				if(mysqli_affected_rows($conn)){
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Login Sistema';
					$msgLog = 'Usuário ['.$login_user.'] bloqueado, número de tentativas ['.$tentativa.'].';
					insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
				}else{
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Login Sistema';
					$msgLog = 'Erro registro usuário ['.$login_user.'] bloqueado, número de tentativas ['.$tentativa.'].';
					insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
				}
				$sqlInsertBloqTemp = "INSERT INTO tb_bloqueio_temporario (id_user, nome_user, login_user, count_tentativas, data_bloqueio) VALUES ('$id_user', '$nome_user', '$login_user', 1,NOW())";
				$queryInsertBloqTemp = mysqli_query($conn,$sqlInsertBloqTemp);
				if(mysqli_insert_id($conn)){
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Login Sistema';
					$msgLog = 'Registro usuário ['.$login_user.'], inserir número de tentativas [0].';
					insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
					return false;
				}else{
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Login Sistema';
					$msgLog = 'Erro registro usuário ['.$login_user.'], inserir número de tentativas [0].';
					insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
					return true;
				}
			}else{
				$tentativa = $rowTbBloqTemp['count_tentativas'] + 1;
				$sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 3 WHERE id = '$id_user'";
				$queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
				if(mysqli_affected_rows($conn)){
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Login Sistema';
					$msgLog = 'Usuário ['.$login_user.'] bloqueado, número de tentativas ['.$tentativa.'].';
					insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
				}else{
					$dataLog = date('Y-m-d H:i:s');
					$appCallLog = 'Login Sistema';
					$msgLog = 'Erro registro usuário ['.$login_user.'] bloqueado, número de tentativas ['.$tentativa.'].';
					insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
				}
				$sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = 0 WHERE id = '$idTempBloqUser'";
				$queryUpdateBloqTemp = mysqli_query($conn,$sqlUpdateBloqTemp);
				if(mysqli_affected_rows($conn)) {
					$sqlInsertBloqTemp = "INSERT INTO tb_bloqueio_temporario (id_user, nome_user, login_user, count_tentativas, data_bloqueio) VALUES ('$id_user', '$nome_user', '$login_user', 5,NOW())";
					$queryInsertBloqTemp = mysqli_query($conn, $sqlInsertBloqTemp);
					if (mysqli_insert_id($conn)) {
						$dataLog = date('Y-m-d H:i:s');
						$appCallLog = 'Login Sistema';
						$msgLog = 'Registro usuário [' . $login_user . '], inserir número de tentativas [0].';
						insert_log('99', 'System', 'root', $appCallLog, $dataLog, $msgLog);
						return false;
					} else {
						$dataLog = date('Y-m-d H:i:s');
						$appCallLog = 'Login Sistema';
						$msgLog = 'Erro registro usuário [' . $login_user . '], inserir número de tentativas [0].';
						insert_log('99', 'System', 'root', $appCallLog, $dataLog, $msgLog);
						return true;
					}
				}
			}
		}else{
			$sqlInsertBloqTemp = "INSERT INTO tb_bloqueio_temporario (id_user, nome_user, login_user, count_tentativas, data_bloqueio) VALUES ('$id_user', '$nome_user', '$login_user', 1,NOW())";
			$queryInsertBloqTemp = mysqli_query($conn,$sqlInsertBloqTemp);
			if(mysqli_insert_id($conn)){
				$dataLog = date('Y-m-d H:i:s');
				$appCallLog = 'Login Sistema';
				$msgLog = 'Registro usuário ['.$login_user.'], inserir número de tentativas [0].';
				insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
				return false;
			}else{
				$dataLog = date('Y-m-d H:i:s');
				$appCallLog = 'Login Sistema';
				$msgLog = 'Erro registro usuário ['.$login_user.'], inserir número de tentativas [0].';
				insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);
				return true;
			}
		}
	}
    /**
    * Função que protege uma página
    */
    function protegePagina() {
      	global $_SG;
      	if (!isset($_SESSION['usuarioIDDashSAT']) OR !isset($_SESSION['usuarioNomeDashSAT'])) {
        // Não há usuário logado, manda pra página de login
        expulsaVisitante();
      	}else if(!isset($_SESSION['usuarioIDDashSAT']) OR !isset($_SESSION['usuarioNomeDashSAT'])) {
        // Há usuário logado, verifica se precisa validar o login novamente
			if ($_SG['validaSempreDashSAT'] == true) {
				// Verifica se os dados salvos na sessão batem com os dados do banco de dados
				if (!validaUsuario($_SESSION['usuarioLoginDashSAT'], $_SESSION['usuarioSenhaDashSAT'])) {
					// Os dados não batem, manda pra tela de login
					expulsaVisitante();
				}
        	}
      	}
    }
    /**
    * Função para expulsar um visitante
    */
    function expulsaVisitante() {
		$id = $_SESSION['usuarioIDDashSAT'];
		$userName = $_SESSION['usuarioNomeDashSAT'];
		$userLogin = $_SESSION['usuarioLoginDashSAT'];
		$dataLog = date('Y-m-d H:i:s');
		$appCallLog = 'Login Sistema'; 
		$msgLog = 'Usuário desconectado com sucesso.';
		if($id != 0){
			insert_log($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);
		}
		
		$user = "root";
		$password = "diabrasil";
		$idLoginTemp = $_SESSION['idLoginTempDashSAT'];
		// Monta uma consulta SQL (query) para procurar um usuário
		$dataFimSessao = date('Y-m-d H:i:s');
		$conn_ = mysqli_connect('localhost', $user, $password , 'srvremoto');
		$sqlDelRegistraTempLogin = "UPDATE tb_sessoes_login_dashsat SET data_fim = '$dataFimSessao' WHERE id = '$idLoginTemp'";
		$queryRegistraTempLogin = mysqli_query($conn_,$sqlDelRegistraTempLogin);
		if(mysqli_affected_rows($conn_)){
			//Grava LOG
			$dataLog = date('Y-m-d H:i:s');
			$appCallLog = 'Registro de Sessão'; 
			$msgLog = 'Sessão encerrada com sucesso.';
			if($id != 0){
				insert_log($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);
			}
		}else{
			//Grava LOG
			$dataLog = date('Y-m-d H:i:s');
			$appCallLog = 'Registro de Sessão'; 
			$msgLog = 'Erro ao registrar dados de Sessão.';
			if($id != 0){
				insert_log($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);
			}
		}
		global $_SG;
		
		delete_login_temp($_SESSION['usuarioIDDashSAT']);
      	// Remove as variáveis da sessão (caso elas existam)
		unset($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $_SESSION['usuarioSenhaDashSAT'], $_SESSION['usuarioAvatarDashSAT'], $_SESSION['usuarioIDAvatarDashSAT'],$_SESSION['idLoginTempDashSAT'],$_SESSION['tokenLogonDashSAT'],$_SESSION['usuarioStatusDashSAT'], $_SESSION['usuarioBloqDashsat'],$_SESSION['statuslogon'],$_SESSION['msg']);
		
		//Limpa cookie intativade
		setcookie('cookieInatividade', '', time()-3600);
      	// Manda pra tela de login
      	$token = 'logoff';
		$token = md5($token);
		$token = $token.md5($token);
		header("Location: ".$_SG['paginaLoginDashSAT']."?token=$token");
	}
	function encerraSessao() {
		$id = $_SESSION['usuarioIDDashSAT'];
		$userName = $_SESSION['usuarioNomeDashSAT'];
		$userLogin = $_SESSION['usuarioLoginDashSAT'];
		$dataLog = date('Y-m-d H:i:s');
		$appCallLog = 'Login Sistema'; 
		$msgLog = 'Usuário desconectado com sucesso.';
		if($id != 0){
			insert_log($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);
		}
		
		$user = "root";
		$password = "diabrasil";
		$idLoginTemp = $_SESSION['idLoginTempDashSAT'];
		// Monta uma consulta SQL (query) para procurar um usuário
		$dataFimSessao = date('Y-m-d H:i:s');
		$conn_ = mysqli_connect('localhost', $user, $password , 'srvremoto');
		$sqlDelRegistraTempLogin = "UPDATE tb_sessoes_login_dashsat SET data_fim = '$dataFimSessao' WHERE id = '$idLoginTemp'";
		$queryRegistraTempLogin = mysqli_query($conn_,$sqlDelRegistraTempLogin);
		if(mysqli_affected_rows($conn_)){
			//Grava LOG
			$dataLog = date('Y-m-d H:i:s');
			$appCallLog = 'Registro de Sessão'; 
			$msgLog = 'Sessão encerrada com sucesso.';
			if($id != 0){
				insert_log($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);
			}
		}else{
			//Grava LOG
			$dataLog = date('Y-m-d H:i:s');
			$appCallLog = 'Registro de Sessão'; 
			$msgLog = 'Erro ao registrar dados de Sessão.';
			if($id != 0){
				insert_log($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);
			}
		}
		global $_SG;
		delete_login_temp($_SESSION['usuarioIDDashSAT']);
      	// Remove as variáveis da sessão (caso elas existam)
      	unset($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $_SESSION['usuarioSenhaDashSAT'], $_SESSION['usuarioAvatarDashSAT'], $_SESSION['usuarioIDAvatarDashSAT'],$_SESSION['idLoginTempDashSAT'],$_SESSION['tokenLogonDashSAT'],$_SESSION['usuarioStatusDashSAT'], $_SESSION['usuarioBloqDashsat'],$_SESSION['statuslogon'],$_SESSION['msg']);
		// Manda pra tela de login
		$token = 'inativa';
		$token = md5($token);
		$token = $token.md5($token);
		header("Location: ".$_SG['paginaLoginDashSAT']."?token=$token");
	}
	
?>
