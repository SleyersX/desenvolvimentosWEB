<?php
    // Script para enviar e-mails automáticamente para os usuários selecionados diariamente
    // ou quando houver alguma mudança no estado operacional do SAT
    // Classes PHPMailer para envio dos e-mails
    include("/var/www/html/source/app-sat/phpmailer/class.phpmailer.php");
    include("/var/www/html/source/app-sat/phpmailer/class.smtp.php");

    /**
     * Dados usuário
     * 
     * @param int  $idUserVerificacao - ID do usuário novo usuário cadastrado no sistema
     * @param string $nomeVerificacao - Nome do novo usuário cadastrado no sistema
     * @param string $loginVerificacao - Login do novo usuário cadastrado no sistema
     * @param string $emailVerificacao - E-mail do novo usuário cadastrado no sistema
     * @param string $bodyEmail - Corpo do email a ser enviado
     * @param string $titleEmail - Título do Email a ser enviadp
     * 
     * @return bool
     * 
     */

    function send_email_alerta($idUserVerificacao,$emailVerificacao,$loginVerificacao,$nomeVerificacao,$bodyEmail, $titleEmail){
        
        // Conexão banco de dados
        $user="root";
        $passwd="diabrasil";
        $host="localhost";
        $banco="srvremoto";
        $conn = mysqli_connect($host,$user,$passwd,$banco);

        $dataVerificacao = date('Y-m-d H:i:s');
        
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
        $mail->addAddress($emailVerificacao,$emailVerificacao);
        $mail->addCC('monitoramento.sat.diagroup@gmail.com','Monitaramente SAT');
        $mail->WordWrap = 50;
        $mail->isHTML(true);
        $mail->Subject = "#[ALARME] Monitoramento SAT - $titleEmail";
        $mail->Body = " Olá $emailVerificacao,<br />" .
                    $bodyEmail . "
                    <br />
                    <br />
                    Obrigado,
                    <br />
                    Equipe Suporte IT PDVs - Monitoramento SAT
                    ";
        $mail->AltBody = "Monitoramento SAT - $titleEmail";

        if(!$mail->send()){
            require_once("/var/www/html/source/app-sat/cron/processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'E-mail Automatico';
            $msgLog = 'Erro ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.']:['.$bodyEmail.'], falha ao enviar e-mail.';
            insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
        }else{
            require_once("/var/www/html/source/app-sat/cron/processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'E-mail Automatico';
            $msgLog = 'Email ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.']:['.$bodyEmail.'], enviado com sucess.';
            insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
        }
    }    

?>