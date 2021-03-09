<?php
//Incluimos o codigo de sistema de sedgurança
require_once("seguranca.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    //Salva duas variaveis com o que foi digitado no formulario do login
    //Detalhe: faz uma verificação utilizando a função isset() para saber se o campo foi preenchido
    $usuariot = (isset($_POST['txt_usuario'])) ? $_POST['txt_usuario'] : '';
    $senhat = (isset($_POST['txt_senha'])) ? $_POST['txt_senha'] : '';
    $remembert = (isset($_POST['lembrete'])) ? $_POST['lembrete'] : '';
	
    //Utilizamos uma função criada no seguranca.php para valida os dados digitados
    if (validaUsuario($usuariot,$senhat) == true){
        //Usuarios e a senha foram validados, manda para pagina respectiva ao nivel
        //de acesso do usuário, também gavamos um varável global com o resultado.
        if($_SESSION['usuarioStatusDashSAT'] == 1 && $_SESSION['usuarioBloqDashsat'] == 0){
            if($remembert == 'rememberme')
            {
                $expira = time() + 60*60*24*30;
                setcookie('cookieLembreme', base64_encode('rememberme'), $expira);
                setcookie('cookieUsuario', base64_encode($usuariot), $expira);
                setcookie('cookieSenha', base64_encode($senhat), $expira);
                
            }else{
                setcookie('cookieLembreme');
                setcookie('cookieUsuario');
                setcookie('cookieSenha');
            }
            if(isset($_SESSION['msg'])){
                unset($_SESSION['msg']);
            }            
            $_SESSION['resultadoDashSAT'] = "Usuario conectado com sucesso!";
            if ($_SESSION['usuarioNivelDashSAT'] == 1){
                $token = $_SESSION['tokenLogonDashSAT'];
                header("Location: superadmin/home.php?token=$token");
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                $token = $_SESSION['tokenLogonDashSAT'];
                header("Location: admin/home.php?token=$token");
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                $token = $_SESSION['tokenLogonDashSAT'];
                header("Location: gestao/home.php?token=$token");
            }elseif($_SESSION['usuarioNivelDashSAT'] == 4){
                $token = $_SESSION['tokenLogonDashSAT'];
                header("Location: padrao/home.php?token=$token");
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                $token = $_SESSION['tokenLogonDashSAT'];
                header("Location: suporte/home.php?token=$token");
            }
        }else if($_SESSION['usuarioStatusDashSAT'] == 2 && $_SESSION['usuarioBloqDashsat'] == 1){
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Login Sistema';
            $msgLog = 'Usuário [' . $usuariot . '] inativo, verifique com o administrador do sistema.';
            insert_log('99', 'System', 'root', $appCallLog, $dataLog, $msgLog);

            $_SESSION['resultadoDashSAT'] = "Usuário inativo, verifique com o administrador do sistema!";
            echo '<div class="alert alert-warning" role="alert">';
            echo " <strong>Ops! Usuário inativo, verifique com o administrador do sistema.</strong>";
            echo "</div>";
        }else if($_SESSION['usuarioStatusDashSAT'] == 2 && $_SESSION['usuarioBloqDashsat'] == 0){
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Login Sistema';
            $msgLog = 'Usuário [' . $usuariot . '] inativo, verifique com o administrador do sistema.';
            insert_log('99', 'System', 'root', $appCallLog, $dataLog, $msgLog);

            $_SESSION['resultadoDashSAT'] = "Usuário inativo, verifique com o administrador do sistema!";
            echo '<div class="alert alert-warning" role="alert">';
            echo " <strong>Ops! Usuário inativo, verifique com o administrador do sistema.</strong>";
            echo "</div>";
        }else if( $_SESSION['usuarioStatusDashSAT'] == 1 && $_SESSION['usuarioBloqDashsat'] == 1){
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Login Sistema';
            $msgLog = 'Usuário ['.$usuariot.'] com bloqueio temporário, verifique com o administrador do sistema.';
            insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);

            $_SESSION['resultadoDashSAT'] = "Usuário com bloqueio temporario, verifique com o administrador do sistema!";
            echo '<div class="alert alert-warning" role="alert">';
            echo " <strong>Ops! Usuário com bloqueio temporário, espere 5 min e tente fazer o login novamente.</strong>";
            echo "</div>";
        }else if( $_SESSION['usuarioStatusDashSAT'] == 3 && $_SESSION['usuarioBloqDashsat'] == 1){
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Login Sistema';
            $msgLog = 'Usuário ['.$usuariot.'] com bloqueio temporário, verifique com o administrador do sistema.';
            insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);

            $_SESSION['resultadoDashSAT'] = "Usuário com bloqueio temporario, verifique com o administrador do sistema!";
            echo '<div class="alert alert-warning" role="alert">';
            echo " <strong>Ops! Usuário com bloqueio temporário, espere 5 min e tente fazer o login novamente.</strong>";
            echo "</div>";
        }
    }else{
        //Grava LOG
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Login Sistema'; 
        $msgLog = 'Usuário ou Senha inválidos ['.$usuariot.'].';
        insert_log('99','System','root',$appCallLog,$dataLog,$msgLog);

        $_SESSION['resultadoDashSAT'] = "Erro ao executar comando, usuario ou senha invalidos!";
		echo '<div class="alert alert-danger" role="alert">';
        echo " <strong>Ops! Usuário ou Senha inválidos.</strong>";
        echo "</div>";				
    }
}
?>
