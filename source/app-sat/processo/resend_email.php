<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();

    $token = $_SESSION['tokenLogonDashSAT'];

    // Conexão banco de dados
    $user="root";
    $passwd="diabrasil";
    $host="database";
    $banco="srvremoto";
    $conn = mysqli_connect($host,$user,$passwd,$banco);

    $idUserVerificacao = filter_input(INPUT_GET, 'id-user', FILTER_SANITIZE_NUMBER_INT);
    $emailVerificacao = filter_input(INPUT_GET, 'email-user', FILTER_SANITIZE_EMAIL);
    $loginVerificacao = filter_input(INPUT_GET, 'login-user', FILTER_SANITIZE_STRING);
    $nomeVerificacao = filter_input(INPUT_GET, 'nome-user', FILTER_SANITIZE_STRING);
    $nivel = filter_input(INPUT_GET,'nivel',FILTER_SANITIZE_NUMBER_INT);

    //echo $idUserVerificacao.$emailVerificacao.$loginVerificacao.$nomeVerificacao;
    // Classes PHPMailer para envio dos e-mails
    include("../phpmailer/class.phpmailer.php");
    include("../phpmailer/class.smtp.php");
    
    
    if(($nivel == 1 || $nivel == 2 ) && ($_SESSION['usuarioNivelDashSAT'] != 1 && $_SESSION['usuarioNivelDashSAT'] != 2)){
        //Grava LOG
        require_once("processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Reenivo e-mail de verificação';
        $msgLog = 'Erro ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], usuário não permitido.';
        if($_SESSION['usuarioIDDashSAT'] != 0 ){
            insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
        }
        if($_SESSION['usuarioNivelDashSAT'] == 1){
            echo "<script>alert('37:Ops!Usuário não permitido!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
            echo "<script>alert('40:Ops!Usuário não permitido!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
            echo "<script>alert('Ops!Usuário não permitido!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/configuracao.php?token='.$token.'">';
        }
    }elseif(($nivel == 1 || $nivel == 2) && ($_SESSION['usuarioNivelDashSAT'] == 1 || $_SESSION['usuarioNivelDashSAT'] == 2)){
        $dataVerificacao = date('Y-m-d H:i:s');

        //Token para verificação
        $randomVerificacao = mt_rand(0,999999);
        $tokenIDVerificacao = md5($idUserVerificacao);
        $tokenLoginVerificacao = md5($loginVerificacao);
        $tokenDataVerificacao = md5($dataVerificacao);
        $tokenEmailVerificacao = md5($emailVerificacao);
        $tokenNomeVerificaocao = md5($nomeVerificacao);
        $tokenNumVerificacao = md5($randomVerificacao);
        $tokenVerificacao = $tokenNumVerificacao.$tokenIDVerificacao.$tokenLoginVerificacao.$tokenDataVerificacao.$tokenEmailVerificacao.$tokenNomeVerificaocao;

        $urlToken = "https://$_SERVER[SERVER_ADDR]/source/app-sat/conta/ativar_conta.php?token=$tokenVerificacao";
        $mail = new PHPMailer();
        //$mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->Username = "monitoramento.sat.diagroup@gmail.com";
        $mail->Password = "di@br@silTpvs2020";
        $mail->From = "monitoramento.sat.diagroup@gmail.com";
        $mail->FromName = "Monitaramente SAT";
        $mail->addAddress($emailVerificacao,$nomeVerificacao);
        $mail->addCC('monitoramento.sat.diagroup@gmail.com','Monitaramente SAT');
        $mail->WordWrap = 50;
        $mail->isHTML(true);
        $mail->Subject = "Monitoramento SAT - Verificação de Conta";
        $mail->Body = "Olá $nomeVerificacao, bem vindo ao Monitoramento SAT.<br />
                    Clique no link abaixo para ativar sua conta.<br />
                    $urlToken<br />
                    <br />
                    Se você não solicitou a verificação deste endereço, ignore este e-mail.
                    <br />
                    Obrigado,
                    <br />
                    Equipe Suporte IT PDVs - Monitoramento SAT
                    ";
        $mail->AltBody = "Monitoramento SAT - Verificação de Conta";

        if(!$mail->send()){
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Reenivo e-mail de verificação';
            $msgLog = 'Erro ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], falha ao enviar e-mail.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('Erro ao reenviar e-mail!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('Erro ao reenviar e-mail!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }
        }else{
            $sqlInsertEmailVerificacao = "INSERT INTO tb_email_conta (id_user, nome_user, login_user, email_user, token_verificacao, data_inclusao, status_token) VALUES ('$idUserVerificacao','$nomeVerificacao','$loginVerificacao','$emailVerificacao','$tokenVerificacao','$dataVerificacao',0)";
            $queryInsertEmailVerificacao = mysqli_query($conn,$sqlInsertEmailVerificacao);
            if(mysqli_insert_id($conn)){
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Reenvio e-mail de verificação';
                $msgLog = 'E-mail ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], enviado com sucesso com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('E-mail enviado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('E-mail enviado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            }else{
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Reenvio e-mail de verificação';
                $msgLog = 'Erro ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], ao gravar dados no banco de dados.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('Erro ao gravar dados no banco de dados');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('Erro ao gravar dados no banco de dados');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            }
        }
    }elseif($nivel != 1 || $nivel != 2){
        $dataVerificacao = date('Y-m-d H:i:s');

        //Token para verificação
        $randomVerificacao = mt_rand(0,999999);
        $tokenIDVerificacao = md5($idUserVerificacao);
        $tokenLoginVerificacao = md5($loginVerificacao);
        $tokenDataVerificacao = md5($dataVerificacao);
        $tokenEmailVerificacao = md5($emailVerificacao);
        $tokenNomeVerificaocao = md5($nomeVerificacao);
        $tokenNumVerificacao = md5($randomVerificacao);
        $tokenVerificacao = $tokenNumVerificacao.$tokenIDVerificacao.$tokenLoginVerificacao.$tokenDataVerificacao.$tokenEmailVerificacao.$tokenNomeVerificaocao;

        $urlToken = "https://$_SERVER[SERVER_ADDR]/source/app-sat/conta/ativar_conta.php?token=$tokenVerificacao";
        $mail = new PHPMailer();
        //$mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->Username = "monitoramento.sat.diagroup@gmail.com";
        $mail->Password = "di@br@silTpvs2020";
        $mail->From = "monitoramento.sat.diagroup@gmail.com";
        $mail->FromName = "Monitaramente SAT";
        $mail->addAddress($emailVerificacao,$nomeVerificacao);
        $mail->addCC('monitoramento.sat.diagroup@gmail.com','Monitaramente SAT');
        $mail->WordWrap = 50;
        $mail->isHTML(true);
        $mail->Subject = "Monitoramento SAT - Verificação de Conta";
        $mail->Body = "Olá $nomeVerificacao, bem vindo ao Monitoramento SAT.<br />
                    Clique no link abaixo para ativar sua conta.<br />
                    $urlToken<br />
                    <br />
                    Se você não solicitou a verificação deste endereço, ignore este e-mail.
                    <br />
                    Obrigado,
                    <br />
                    Equipe Suporte IT PDVs - Monitoramento SAT
                    ";
        $mail->AltBody = "Monitoramento SAT - Verificação de Conta";

        if(!$mail->send()){
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Reenivo e-mail de verificação';
            $msgLog = 'Erro ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], falha ao enviar e-mail.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            echo "<script>alert('Erro ao reenviar e-mail!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
        }else{
            $sqlInsertEmailVerificacao = "INSERT INTO tb_email_conta (id_user, nome_user, login_user, email_user, token_verificacao, data_inclusao, status_token) VALUES ('$idUserVerificacao','$nomeVerificacao','$loginVerificacao','$emailVerificacao','$tokenVerificacao','$dataVerificacao',0)";
            $queryInsertEmailVerificacao = mysqli_query($conn,$sqlInsertEmailVerificacao);
            if(mysqli_insert_id($conn)){
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Reenvio e-mail de verificação';
                $msgLog = 'E-mail ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], enviado com sucesso com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('E-mail enviado com sucesso!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
            }else{
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Reenvio e-mail de verificação';
                $msgLog = 'Erro ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], ao gravar dados no banco de dados.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                echo "<script>alert('Erro ao gravar dados no banco de dados');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../gestao/home.php?token='.$token.'">';
            }
        }
    }
    
?>