<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");
    $token = $_SESSION['tokenLogonDashSAT'];
    $id = $_SESSION['usuarioIDDashSAT'];
    $nivel = $_SESSION['usuarioNivelDashSAT'];
    $nome = (isset($_POST['nome'])) ? $_POST['nome'] : '';
    $email = (isset($_POST['email'])) ? $_POST['email'] : '';
    $login = (isset($_POST['login'])) ? $_POST['login'] : ''; 
    $data = date('Y-m-d H:i:s');
    if(!empty($_POST['id-img'])){
        switch ($_POST['id-img']) {
            case '1':
                    $optAvatar = 'avatar';
                    $idAvatar = 1;
                break;
            case '2':
                    $optAvatar = 'avatar2';
                    $idAvatar = 2;
                break;
            case '3':
                    $optAvatar = 'avatar3';
                    $idAvatar = 3;
                break;
            case '4':
                    $optAvatar = 'avatar4';
                    $idAvatar = 4;
                break;
            case '5':
                    $optAvatar = 'avatar5';
                    $idAvatar = 5;
                break;
            default:
                    $optAvatar = 'nulo';
                    $idAvatar = $_SESSION['usuarioIDAvatar'];
                break;
        }
    }else{
        $optAvatar = 'nulo';
        $idAvatar = $_SESSION['usuarioIDAvatar']; 
    }

    if(!empty($id)){
        if($optAvatar == 'avatar'){
            $imgPath = '../dist/img/avatar.png';
        }elseif($optAvatar == 'avatar2'){
            $imgPath = '../dist/img/avatar2.png';
        }elseif($optAvatar == 'avatar3'){
            $imgPath = '../dist/img/avatar3.png';
        }elseif($optAvatar == 'avatar4'){
            $imgPath = '../dist/img/avatar4.png';
        }elseif($optAvatar == 'avatar5'){
            $imgPath = '../dist/img/avatar5.png';
        }elseif($optAvatar == 'nulo'){
            $imgPath = $_SESSION['usuarioAvatarDashSAT'];
        }else{
            $imgPath = $_SESSION['usuarioAvatarDashSAT'];
        }
        $result_usuario = "UPDATE tb_usuarios_dashsat SET nome='$nome', login='$login', email='$email', idavatar='$idAvatar' , avatar='$imgPath', data_modificacao='$data' WHERE id='$id'";
        $resultado_usuario = mysqli_query($conn, $result_usuario);
        if(mysqli_affected_rows($conn)){
            $_SESSION['usuarioNomeDashSAT'] = $nome; // Pega o valor da coluna 'nome' do registro encontrado no MySQL
            $_SESSION['usuarioAvatarDashSAT'] = $imgPath;
            $_SESSION['usuarioIDAvatar'] = $idAvatar;
            if($nivel == 1){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Perfil User'; 
                $msgLog = 'Update do usuario ['.$nome.']:['.$login.']:['.$email.']:['.$nivel.']:['.$imgPath.'], realizado com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Cadastro atualizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/home.php?token='.$token.'">';    
            }elseif($nivel == 2){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Perfil User'; 
                $msgLog = 'Update do usuario ['.$nome.']:['.$login.']:['.$email.']:['.$nivel.']:['.$imgPath.'], realizado com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Cadastro atualizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/home.php?token='.$token.'">';
            }elseif($nivel == 3){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Perfil User'; 
                $msgLog = 'Update do usuario ['.$nome.']:['.$login.']:['.$email.']:['.$nivel.']:['.$imgPath.'], realizado com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Cadastro atualizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
            }elseif($nivel == 4){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Perfil User'; 
                $msgLog = 'Update do usuario ['.$nome.']:['.$login.']:['.$email.']:['.$nivel.']:['.$imgPath.'], realizado com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Cadastro atualizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/home.php?token='.$token.'">';
            }elseif($nivel == 5){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Perfil User'; 
                $msgLog = 'Update do usuario ['.$nome.']:['.$login.']:['.$email.']:['.$nivel.']:['.$imgPath.'], realizado com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Cadastro atualizado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/home.php?token='.$token.'">';
            }
        }else{
            if($nivel == 1){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Perfil User'; 
                $msgLog = 'Update do usuario ['.$nome.']:['.$login.']:['.$email.']:['.$nivel.']:['.$imgPath.'], erro ao atualizar dados.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Erro ao atualizar dados!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/home.php?token='.$token.'">';    
            }elseif($nivel == 2){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Perfil User'; 
                $msgLog = 'Update do usuario ['.$nome.']:['.$login.']:['.$email.']:['.$nivel.']:['.$imgPath.'], erro ao atualizar dados.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Erro ao atualizar dados!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/home.php?token='.$token.'">';
            }elseif($nivel == 3){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Perfil User'; 
                $msgLog = 'Update do usuario ['.$nome.']:['.$login.']:['.$email.']:['.$nivel.']:['.$imgPath.'], erro ao atualizar dados.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Erro ao atualizar dados!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
            }elseif($nivel == 4){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Perfil User'; 
                $msgLog = 'Update do usuario ['.$nome.']:['.$login.']:['.$email.']:['.$nivel.']:['.$imgPath.'], erro ao atualizar dados.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Erro ao atualizar dados!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/home.php?token='.$token.'">';
            }elseif($nivel == 5){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Perfil User'; 
                $msgLog = 'Update do usuario ['.$nome.']:['.$login.']:['.$email.']:['.$nivel.']:['.$imgPath.'], erro ao atualizar dados.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Erro ao atualizar dados!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/home.php?token='.$token.'">';
            }
        }
    }else{
        if($nivel == 1){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Perfil User'; 
            $msgLog = 'Necessário selecionar um usuário.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Necessário selecionar um usuário!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/home.php?token='.$token.'">';    
        }elseif($nivel == 2){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Perfil User'; 
            $msgLog = 'Necessário selecionar um usuário.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Necessário selecionar um usuário!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/home.php?token='.$token.'">';
        }elseif($nivel == 3){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Perfil User'; 
            $msgLog = 'Necessário selecionar um usuário.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Necessário selecionar um usuário!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
        }elseif($nivel == 4){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Perfil User'; 
            $msgLog = 'Necessário selecionar um usuário.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Necessário selecionar um usuário!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../padrao/home.php?token='.$token.'">';
        }elseif($nivel == 5){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Perfil User'; 
            $msgLog = 'Necessário selecionar um usuário.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Necessário selecionar um usuário!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../suporte/home.php?token='.$token.'">';
        }
    }

?>