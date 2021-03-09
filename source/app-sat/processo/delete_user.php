<?php
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");

    $token = $_SESSION['tokenLogonDashSAT'];

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $login = filter_input(INPUT_GET, 'login-user', FILTER_SANITIZE_STRING);
    $nivel = filter_input(INPUT_GET, 'nivel-user', FILTER_SANITIZE_STRING);
    $resultado_nivel = "SELECT * FROM tb_usuarios_dashsat WHERE id = '".$id."'";
    $resulta_nivel = mysqli_query($conn, $resultado_nivel);
    $row_result_nivel = mysqli_fetch_assoc($resulta_nivel);
    
    $nivel = $row_result_nivel['nivel'];
    
    if(!empty($id)){
        $sqlUserLogado = "SELECT COUNT(id_user) AS user_logado FROM tb_temp_login_dashsat WHERE id_user = '$id'";
        $queryUserLogado = mysqli_query($conn,$sqlUserLogado);
        $rowUserLogado = mysqli_fetch_assoc($queryUserLogado);  
        $userLogado = $rowUserLogado['user_logado'];
        if($userLogado >= 1){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Delete User'; 
            $msgLog = 'Usuario ['.$id.']:['.$login.']:['.$nivel.'] está logado, não pode ser apagado.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Usuário logado, não pode ser apagado!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Usuário logado, não pode ser apagado!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }
        }else{
            if($nivel == 1 && $_SESSION['usuarioNivelDashSAT'] != 1){
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('Ops!Usuário não permitido!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('Ops!Usuário não permitido!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            }else{
                $sqlDelUser = "DELETE FROM tb_usuarios_dashsat WHERE id = '$id'";
                $queryDelUser = mysqli_query($conn, $sqlDelUser);
                if(mysqli_affected_rows($conn)){
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Delete User'; 
                    $msgLog = 'Usuario ['.$id.']:['.$login.']:['.$nivel.'], apagado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo "<script>alert('Usuário apagado com sucesso!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo "<script>alert('Usuário apagado com sucesso!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                }else{
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Delete User'; 
                    $msgLog = 'Usuario ['.$id.']:['.$login.']:['.$nivel.'], não foi apagado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                    if($_SESSION['usuarioNivelDashSAT'] == 1){
                        echo "<script>alert('Erro o usuário não foi apagado com sucesso!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                        echo "<script>alert('Erro o usuário não foi apagado com sucesso!');</script>";
                        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                    }
                }
            }
        }
    }else{
        //Grava LOG
        require_once("processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Delete User'; 
        $msgLog = 'Usuario ['.$id.']:['.$login.']:['.$nivel.'], necessário selecionar um usuário.';
        if($_SESSION['usuarioIDDashSAT'] != 0 ){
            insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
        }
        if($_SESSION['usuarioNivelDashSAT'] == 1){
            echo "<script>alert('Necessário selecionar um usuário!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
            echo "<script>alert('Necessário selecionar um usuário!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
        }
    }
    
?>