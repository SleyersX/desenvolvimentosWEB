<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");
	
	$token = $_SESSION['tokenLogonDashSAT'];
	
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
	$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	$login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING); 
	$nivel = filter_input(INPUT_POST, 'cb_nivel', FILTER_SANITIZE_STRING);
	$ativo = filter_input(INPUT_POST, 'cb_status', FILTER_SANITIZE_STRING);
	$data = date('Y-m-d H:i:s'); 
	

	if(!empty($id)){
        if($id != $_SESSION['usuarioIDDashSAT']){
            if($nivel == 1 && $_SESSION['usuarioNivelDashSAT'] != 1){
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('Ops!Usuário não permitido!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('Ops!Usuário não permitido!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            }else{
                if(empty($nivel) || $nivel == 0){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Editar Usuário'; 
                    $msgLog = 'Dados ['.$nome.']:['.$email.']:['.$login.']:['.$nivel.']:['.$ativo.'], erro ao realizar atualização.Revise os dados.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo "<script>alert('Erro ao atualizar dados do usuário revise os dados!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo "<script>alert('Erro ao atualizar dados do usuário revise os dados!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                }
                if($ativo == 1){
                    $sqlExiteBloqueio = "SELECT COUNT(id) AS existe_registro FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
                    $queryExisteBloqueio = mysqli_query($conn,$sqlExiteBloqueio);
                    $rowExisteBloqueio = mysqli_fetch_assoc($queryExisteBloqueio);
        
                    if($rowExisteBloqueio['existe_registro'] >= 1) {
                        $sqlMaxIDUsrBloqTemp = "SELECT MAX(id) AS id FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
                        $queryMaxIDUsrBloqTemp = mysqli_query($conn, $sqlMaxIDUsrBloqTemp);
                        $rowMaxIDUsrBloqTemp = mysqli_fetch_assoc($queryMaxIDUsrBloqTemp);
                        $idTempBloqUser = $rowMaxIDUsrBloqTemp['id'];
                        $sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = 0 WHERE id = '$idTempBloqUser'";
                        $queryUpdateBloqTemp = mysqli_query($conn, $sqlUpdateBloqTemp);
                        if (mysqli_affected_rows($conn)) {
                            $dataLog = date('Y-m-d H:i:s');
                            $appCallLog = 'Editar Usuário';
                            $msgLog = 'Update usuário [' . $login . '], alteração status bloqueio.';
                            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                            }
                        } else {
                            $dataLog = date('Y-m-d H:i:s');
                            $appCallLog = 'Editar Usuário';
                            $msgLog = 'Usuário [' . $login . '], nenhum alteração no status de bloqueio.';
                            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                            }
                        }
                    }
                }elseif ($ativo == 2){
                    $sqlExiteBloqueio = "SELECT COUNT(id) AS existe_registro FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
                    $queryExisteBloqueio = mysqli_query($conn,$sqlExiteBloqueio);
                    $rowExisteBloqueio = mysqli_fetch_assoc($queryExisteBloqueio);
        
                    if($rowExisteBloqueio['existe_registro'] >= 1) {
                        $sqlMaxIDUsrBloqTemp = "SELECT MAX(id) AS id FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
                        $queryMaxIDUsrBloqTemp = mysqli_query($conn, $sqlMaxIDUsrBloqTemp);
                        $rowMaxIDUsrBloqTemp = mysqli_fetch_assoc($queryMaxIDUsrBloqTemp);
                        $idTempBloqUser = $rowMaxIDUsrBloqTemp['id'];
                        $sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = 0 WHERE id = '$idTempBloqUser'";
                        $queryUpdateBloqTemp = mysqli_query($conn, $sqlUpdateBloqTemp);
                        if (mysqli_affected_rows($conn)) {
                            $dataLog = date('Y-m-d H:i:s');
                            $appCallLog = 'Editar Usuário';
                            $msgLog = 'Update usuário [' . $login . '], alteração status bloqueio.';
                            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                            }
                        } else {
                            $dataLog = date('Y-m-d H:i:s');
                            $appCallLog = 'Editar Usuário';
                            $msgLog = 'Usuário [' . $login . '], nenhum alteração no status de bloqueio.';
                            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                            }
                        }
                    }
                }elseif ($ativo == 3){
                    $sqlExiteBloqueio = "SELECT COUNT(id) AS existe_registro FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
                    $queryExisteBloqueio = mysqli_query($conn,$sqlExiteBloqueio);
                    $rowExisteBloqueio = mysqli_fetch_assoc($queryExisteBloqueio);
        
                    if($rowExisteBloqueio['existe_registro'] >= 1){
                        $sqlMaxIDUsrBloqTemp = "SELECT MAX(id) AS id FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
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
                                $appCallLog = 'Editar Usuário';
                                $msgLog = 'Registro usuário ['.$login.'], update número de tentativas ['.$tentativa.'].';
                                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                    insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                                }
                            }else{
                                $dataLog = date('Y-m-d H:i:s');
                                $appCallLog = 'Editar Usuário';
                                $msgLog = 'Nenhuma alteração no registro usuário ['.$login.'], update número de tentativas ['.$tentativa.'].';
                                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                    insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                                }
                            }
                        }else if($rowTbBloqTemp['tempo_decorrido'] > $rowTbBloqTemp['tempo_desbloqueio'] && $rowTbBloqTemp['count_tentativas'] == 5){
                            $tentativa = $rowTbBloqTemp['count_tentativas'] + 1;
                            $sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
                            $queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
                            if(mysqli_affected_rows($conn)){
                                $dataLog = date('Y-m-d H:i:s');
                                $appCallLog = 'Editar Usuário';
                                $msgLog = 'Usuário ['.$login.'] bloqueado, número de tentativas ['.$tentativa.'].';
                                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                    insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                                }
                            }else{
                                $dataLog = date('Y-m-d H:i:s');
                                $appCallLog = 'Editar Usuário';
                                $msgLog = 'Erro registro usuário ['.$login.'] bloqueado, número de tentativas ['.$tentativa.'].';
                                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                    insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);  
                                }
                            }
                            $sqlInsertBloqTemp = "INSERT INTO tb_bloqueio_temporario (id_user, nome_user, login_user, count_tentativas, data_bloqueio) VALUES ('$id', '$nome', '$login', 5,NOW())";
                            $queryInsertBloqTemp = mysqli_query($conn,$sqlInsertBloqTemp);
                            if(mysqli_insert_id($conn)){
                                $dataLog = date('Y-m-d H:i:s');
                                $appCallLog = 'Editar Usuário';
                                $msgLog = 'Registro usuário ['.$login.'], inserir número de tentativas [0].';
                                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                    insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                                }
                            }else{
                                $dataLog = date('Y-m-d H:i:s');
                                $appCallLog = 'Editar Usuário';
                                $msgLog = 'Erro registro usuário ['.$login.'], inserir número de tentativas [0].';
                                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                    insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                                }
                            }
                        }else{
                            $tentativa = $rowTbBloqTemp['count_tentativas'] + 1;
                            $sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 3 WHERE id = '$id'";
                            $queryUpdateUsrBloqTemp = mysqli_query($conn,$sqlUpdateUsrBloqTemp);
                            if(mysqli_affected_rows($conn)){
                                $dataLog = date('Y-m-d H:i:s');
                                $appCallLog = 'Editar Usuário';
                                $msgLog = 'Usuário ['.$login.'] bloqueado, número de tentativas ['.$tentativa.'].';
                                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                    insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                                }
                            }else{
                                $dataLog = date('Y-m-d H:i:s');
                                $appCallLog = 'Editar Usuário';
                                $msgLog = 'Erro registro usuário ['.$login.'] bloqueado, número de tentativas ['.$tentativa.'].';
                                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                    insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                                }
                            }
                            $sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = 0 WHERE id = '$idTempBloqUser'";
                            $queryUpdateBloqTemp = mysqli_query($conn,$sqlUpdateBloqTemp);
                            if(mysqli_affected_rows($conn)) {
                                $sqlInsertBloqTemp = "INSERT INTO tb_bloqueio_temporario (id_user, nome_user, login_user, count_tentativas, data_bloqueio) VALUES ('$id', '$nome', '$login', 5,NOW())";
                                $queryInsertBloqTemp = mysqli_query($conn, $sqlInsertBloqTemp);
                                if (mysqli_insert_id($conn)) {
                                    $dataLog = date('Y-m-d H:i:s');
                                    $appCallLog = 'Editar Usuário';
                                    $msgLog = 'Registro usuário [' . $login . '], inserir número de tentativas [0].';
                                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                        insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                                    }
                                } else {
                                    $dataLog = date('Y-m-d H:i:s');
                                    $appCallLog = 'Editar Usuário';
                                    $msgLog = 'Erro registro usuário [' . $login . '], inserir número de tentativas [0].';
                                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                        insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                                    }
                                }
                            }
                        }
                    }else{
                        $sqlInsertBloqTemp = "INSERT INTO tb_bloqueio_temporario (id_user, nome_user, login_user, count_tentativas, data_bloqueio) VALUES ('$id', '$nome', '$login', 5,NOW())";
                        $queryInsertBloqTemp = mysqli_query($conn,$sqlInsertBloqTemp);
                        if(mysqli_insert_id($conn)){
                            $dataLog = date('Y-m-d H:i:s');
                            $appCallLog = 'Editar Usuário';
                            $msgLog = 'Registro usuário ['.$login.'], inserir número de tentativas [0].';
                            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                            }
                        }else{
                            $dataLog = date('Y-m-d H:i:s');
                            $appCallLog = 'Editar Usuário';
                            $msgLog = 'Erro registro usuário ['.$login.'], inserir número de tentativas [0].';
                            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                                insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                            }
                        }
                    }
                }
        
        
                $result_usuario = "UPDATE tb_usuarios_dashsat SET nome='$nome', login='$login',email='$email',nivel='$nivel',ativo='$ativo',data_modificacao='$data' WHERE id = '$id'";
                $resultado_usuario = mysqli_query($conn, $result_usuario);
                if(mysqli_affected_rows($conn)){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Editar Usuário'; 
                    $msgLog = 'Dados ['.$nome.']:['.$email.']:['.$login.']:['.$nivel.']:['.$ativo.'], atualizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo "<script>alert('Dados atualizado com sucesso!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo "<script>alert('Dados atualizado com sucesso!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                }else{
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Editar Usuário'; 
                    $msgLog = 'Dados ['.$nome.']:['.$email.']:['.$login.']:['.$nivel.']:['.$ativo.'], erro ao realizar atualização.Revise os dados.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo "<script>alert('Erro ao atualizar dados do usuário revise os dados!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo "<script>alert('Erro ao atualizar dados do usuário revise os dados!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                }
            }
        }else{
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Ops!Usuário não pode ser o mesmo!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Ops!Usuário não pode ser o mesmo!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }
        }
	}else{
		//Grava LOG
		require_once("processa_log.php");
		$dataLog = date('Y-m-d H:i:s');
		$appCallLog = 'Editar Usuário'; 
		$msgLog = 'Necessário selecionar um usuário!';
		if($_SESSION['usuarioIDDashSAT'] != 0 ){
			insert_log($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
		}	
        $_SESSION['msg'] = "<p style='color:red;'>Necessário selecionar um usuário</p>";
        if($_SESSION['usuarioNivelDashSAT'] == 1){
            echo "<script>alert('Necessário selecionar um usuário!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
            echo "<script>alert('Necessário selecionar um usuário!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
        }
	}

?>
