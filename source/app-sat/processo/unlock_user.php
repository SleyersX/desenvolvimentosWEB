<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");

    $token = $_SESSION['tokenLogonDashSAT'];
    $idUserActual = $_SESSION['usuarioIDDashSAT'];

    $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
    $login = filter_input(INPUT_GET, 'login-user', FILTER_SANITIZE_STRING);
    $nome = filter_input(INPUT_GET,'nome-user',FILTER_SANITIZE_STRING);
    $nivel = filter_input(INPUT_GET,'nivel',FILTER_SANITIZE_NUMBER_INT);

    if ($id != $idUserActual && $_SESSION['usuarioNivelDashSAT'] == 1 || $_SESSION['usuarioNivelDashSAT'] == 2) {
        $sqlExiteBloqueio = "SELECT COUNT(id) AS existe_registro FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
        $queryExisteBloqueio = mysqli_query($conn, $sqlExiteBloqueio);
        $rowExisteBloqueio = mysqli_fetch_assoc($queryExisteBloqueio);

        if ($rowExisteBloqueio['existe_registro'] >= 1) {
            $sqlMaxIDUsrBloqTemp = "SELECT MAX(id) AS id FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
            $queryMaxIDUsrBloqTemp = mysqli_query($conn, $sqlMaxIDUsrBloqTemp);
            $rowMaxIDUsrBloqTemp = mysqli_fetch_assoc($queryMaxIDUsrBloqTemp);
            $idTempBloqUser = $rowMaxIDUsrBloqTemp['id'];
            $sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = 0 WHERE id = '$idTempBloqUser'";
            $queryUpdateBloqTemp = mysqli_query($conn, $sqlUpdateBloqTemp);
            if (mysqli_affected_rows($conn)) {
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Unlock Usuário';
                $msgLog = 'Bloqueio temporário usuário ['.$login .'] removido com sucesso.';
                if ($_SESSION['usuarioIDDashSAT'] != 0) {
                    insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                }
                $sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
                $queryUpdateUsrBloqTemp = mysqli_query($conn, $sqlUpdateUsrBloqTemp);
                if (mysqli_affected_rows($conn)) {
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Unlock Usuário';
                    $msgLog = 'Usuário ['.$login.'] desbloqueado com sucesso.';
                    if ($_SESSION['usuarioIDDashSAT'] != 0) {
                        insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo '<script>alert("Usuário ['.$login.'] desbloqueado com sucesso!");</script>';
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo '<script>alert("Usuário ['.$login.'] desbloqueado com sucesso!");</script>';
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                } else {
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Unlock Usuário';
                    $msgLog = 'Nenhuma alteração de bloqueio para o usuário ['.$login.'].';
                    if ($_SESSION['usuarioIDDashSAT'] != 0) {
                        insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo '<script>alert("Nenhuma alteração de bloqueio para o usuário ['.$login.']!");</script>';
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo '<script>alert("Nenhuma alteração de bloqueio para o usuário ['.$login.']!");</script>';
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                }
            } else {
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Unlock Usuário';
                $msgLog = 'Usuário ['.$login.'], nenhum alteração no status de bloqueio.';
                if ($_SESSION['usuarioIDDashSAT'] != 0) {
                    insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                }
                $sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
                $queryUpdateUsrBloqTemp = mysqli_query($conn, $sqlUpdateUsrBloqTemp);
                if (mysqli_affected_rows($conn)) {
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Unlock Usuário';
                    $msgLog = 'Usuário ['.$login.'] desbloqueado com sucesso.';
                    if ($_SESSION['usuarioIDDashSAT'] != 0) {
                        insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo '<script>alert("Usuário ['.$login.'] desbloqueado com sucesso!");</script>';
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo '<script>alert("Usuário ['.$login.'] desbloqueado com sucesso!");</script>';
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                } else {
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Unlock Usuário';
                    $msgLog = 'Nenhuma alteração de bloqueio para o usuário ['.$login.'].';
                    if ($_SESSION['usuarioIDDashSAT'] != 0) {
                        insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo '<script>alert("Nenhuma alteração de bloqueio para o usuário ['.$login.']!");</script>';
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo '<script>alert("Nenhuma alteração de bloqueio para o usuário ['.$login.']!");</script>';
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                }
            }
        } else {
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Unlock Usuário';
            $msgLog = 'Usuário ['.$login.'], sem registro de bloqueio temporário.';
            if ($_SESSION['usuarioIDDashSAT'] != 0) {
                insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
            }
            $sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
            $queryUpdateUsrBloqTemp = mysqli_query($conn, $sqlUpdateUsrBloqTemp);
            if (mysqli_affected_rows($conn)) {
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Unlock Usuário';
                $msgLog = 'Usuário ['.$login.'] desbloqueado com sucesso.';
                if ($_SESSION['usuarioIDDashSAT'] != 0) {
                    insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo '<script>alert("Usuário ['.$login.'] desbloqueado com sucesso!");</script>';
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo '<script>alert("Usuário ['.$login.'] desbloqueado com sucesso!");</script>';
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            } else {
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Unlock Usuário';
                $msgLog = 'Nenhuma alteração de bloqueio para o usuário ['.$login.'].';
                if ($_SESSION['usuarioIDDashSAT'] != 0) {
                    insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo '<script>alert("Nenhuma alteração de bloqueio para o usuário ['.$login.']!");</script>';
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo '<script>alert("Nenhuma alteração de bloqueio para o usuário ['.$login.']!");</script>';
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            }
        }
    }elseif($id != $idUserActual  && $nivel == 1 || $nivel == 2){
        //Grava LOG
        require_once("processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Unlock Usuário'; 
        $msgLog = 'Unlock ['.$id.']:['.$login.']:['.$nivel.'] senha.Ops!Usuário não permitido.';
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
    }elseif($id != $idUserActual  && $nivel != 1 || $nivel != 2){
        $sqlExiteBloqueio = "SELECT COUNT(id) AS existe_registro FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
        $queryExisteBloqueio = mysqli_query($conn, $sqlExiteBloqueio);
        $rowExisteBloqueio = mysqli_fetch_assoc($queryExisteBloqueio);

        if ($rowExisteBloqueio['existe_registro'] >= 1) {
            $sqlMaxIDUsrBloqTemp = "SELECT MAX(id) AS id FROM tb_bloqueio_temporario WHERE id_user = '$id' AND STR_TO_DATE(data_bloqueio, '%Y-%m-%d') = STR_TO_DATE(NOW(), '%Y-%m-%d') AND status_bloqueio = 1 ";
            $queryMaxIDUsrBloqTemp = mysqli_query($conn, $sqlMaxIDUsrBloqTemp);
            $rowMaxIDUsrBloqTemp = mysqli_fetch_assoc($queryMaxIDUsrBloqTemp);
            $idTempBloqUser = $rowMaxIDUsrBloqTemp['id'];
            $sqlUpdateBloqTemp = "UPDATE tb_bloqueio_temporario SET status_bloqueio = 0 WHERE id = '$idTempBloqUser'";
            $queryUpdateBloqTemp = mysqli_query($conn, $sqlUpdateBloqTemp);
            if (mysqli_affected_rows($conn)) {
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Unlock Usuário';
                $msgLog = 'Bloqueio temporário usuário ['.$login .'] removido com sucesso.';
                if ($_SESSION['usuarioIDDashSAT'] != 0) {
                    insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                }
                $sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
                $queryUpdateUsrBloqTemp = mysqli_query($conn, $sqlUpdateUsrBloqTemp);
                if (mysqli_affected_rows($conn)) {
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Unlock Usuário';
                    $msgLog = 'Usuário ['.$login.'] desbloqueado com sucesso.';
                    if ($_SESSION['usuarioIDDashSAT'] != 0) {
                        insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                    }
                    echo '<script>alert("Usuário ['.$login.'] desbloqueado com sucesso!");</script>';
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
                } else {
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Unlock Usuário';
                    $msgLog = 'Nenhuma alteração de bloqueio para o usuário ['.$login.'].';
                    if ($_SESSION['usuarioIDDashSAT'] != 0) {
                        insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                    }
                    echo '<script>alert("Nenhuma alteração de bloqueio para o usuário ['.$login.']!");</script>';
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
                }
            } else {
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Unlock Usuário';
                $msgLog = 'Usuário ['.$login.'], nenhum alteração no status de bloqueio.';
                if ($_SESSION['usuarioIDDashSAT'] != 0) {
                    insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                }
                $sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
                $queryUpdateUsrBloqTemp = mysqli_query($conn, $sqlUpdateUsrBloqTemp);
                if (mysqli_affected_rows($conn)) {
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Unlock Usuário';
                    $msgLog = 'Usuário ['.$login.'] desbloqueado com sucesso.';
                    if ($_SESSION['usuarioIDDashSAT'] != 0) {
                        insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                    }
                    echo '<script>alert("Usuário ['.$login.'] desbloqueado com sucesso!");</script>';
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
                } else {
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Unlock Usuário';
                    $msgLog = 'Nenhuma alteração de bloqueio para o usuário ['.$login.'].';
                    if ($_SESSION['usuarioIDDashSAT'] != 0) {
                        insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                    }
                    echo '<script>alert("Nenhuma alteração de bloqueio para o usuário ['.$login.']!");</script>';
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
                }
            }
        } else {
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Unlock Usuário';
            $msgLog = 'Usuário ['.$login.'], sem registro de bloqueio temporário.';
            if ($_SESSION['usuarioIDDashSAT'] != 0) {
                insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
            }
            $sqlUpdateUsrBloqTemp = "UPDATE tb_usuarios_dashsat SET ativo = 1 WHERE id = '$id'";
            $queryUpdateUsrBloqTemp = mysqli_query($conn, $sqlUpdateUsrBloqTemp);
            if (mysqli_affected_rows($conn)) {
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Unlock Usuário';
                $msgLog = 'Usuário ['.$login.'] desbloqueado com sucesso.';
                if ($_SESSION['usuarioIDDashSAT'] != 0) {
                    insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                }
                echo '<script>alert("Usuário ['.$login.'] desbloqueado com sucesso!");</script>';
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
            } else {
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Unlock Usuário';
                $msgLog = 'Nenhuma alteração de bloqueio para o usuário ['.$login.'].';
                if ($_SESSION['usuarioIDDashSAT'] != 0) {
                    insert_log($_SESSION['usuarioIDDashSAT'], $_SESSION['usuarioNomeDashSAT'], $_SESSION['usuarioLoginDashSAT'], $appCallLog, $dataLog, $msgLog);
                }
                echo '<script>alert("Nenhuma alteração de bloqueio para o usuário ['.$login.']!");</script>';
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
            }
        }
    }else{
        if($_SESSION['usuarioNivelDashSAT'] == 1){
            echo "<script>alert('Erro, usuário atual!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
            echo "<script>alert('Erro, usuário atual!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
            echo "<script>alert('Erro, usuário atual!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
        }
    }
?>
