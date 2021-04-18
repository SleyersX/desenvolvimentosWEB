<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();

    // Classes PHPMailer para envio dos e-mails
    include("../phpmailer/class.phpmailer.php");
    include("../phpmailer/class.smtp.php");

    /**
     * Dados usuário
     * 
     * @param int  $idUserVerificacao - ID do usuário novo usuário cadastrado no sistema
     * @param string $nomeVerificacao - Nome do novo usuário cadastrado no sistema
     * @param string $loginVerificacao - Login do novo usuário cadastrado no sistema
     * @param string $emailVerificacao - E-mail do novo usuário cadastrado no sistema
     * 
     * @return bool
     * 
     */

    function send_email_verificacao($idUserVerificacao,$emailVerificacao,$loginVerificacao,$nomeVerificacao){
        
        // Conexão banco de dados
        $user="root";
        $passwd="diabrasil";
        $host="database";
        $banco="srvremoto";
        $conn = mysqli_connect($host,$user,$passwd,$banco);

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
            $appCallLog = 'E-mail de verificação'; 
            $msgLog = 'Erro ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], falha ao enviar e-mail.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
        }else{
            $sqlInsertEmailVerificacao = "INSERT INTO tb_email_conta (id_user, nome_user, login_user, email_user, token_verificacao, data_inclusao, status_token) VALUES ('$idUserVerificacao','$nomeVerificacao','$loginVerificacao','$emailVerificacao','$tokenVerificacao','$dataVerificacao',0)";
            $queryInsertEmailVerificacao = mysqli_query($conn,$sqlInsertEmailVerificacao);
            if(mysqli_insert_id($conn)){
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail de verificação'; 
                $msgLog = 'E-mail ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], enviado com sucesso com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
            }else{
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail de verificação'; 
                $msgLog = 'Erro ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], ao gravar dados no banco de dados.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
            }
        }
    }    

?>