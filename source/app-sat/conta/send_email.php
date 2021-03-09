<?php
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

    function send_email_verificacao_I($idUserVerificacao,$emailVerificacao,$loginVerificacao,$nomeVerificacao){
        
        // Conexão banco de dados
        $user="root";
        $passwd="diabrasil";
        $host="localhost";
        $banco="srvremoto";
        $conn = mysqli_connect($host,$user,$passwd,$banco);

        $dataVerificacao = date('Y-m-d H:i:s');

        //Token para verificação
        
        $urlToken = "https://$_SERVER[SERVER_ADDR]/source/app-sat/";
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
        $mail->Body = "Bem vindo, $nomeVerificacao ao Monitoramento SAT.<br />
                    Conta verificada com sucesso, clique no link abaixo para acessar a ferramenta, você está recebendo uma senha padrão, é aconselhavél atualizar sua senha.<br />
                    Usuário: $loginVerificacao <br />
                    Senha: 123456 <br />
                    $urlToken<br />
                    ";
        $mail->AltBody = "Monitoramento SAT - Verificação de Conta";

        if(!$mail->send()){
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'E-mail de login'; 
            $msgLog = 'Erro ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], falha ao enviar e-mail.';
            insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
        }else{
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'E-mail de login'; 
            $msgLog = 'E-mail ['.$idUserVerificacao.']:['.$nomeVerificacao.']:['.$emailVerificacao.']:['.$loginVerificacao.']:['.$dataVerificacao.'], enviado com sucesso com sucesso.';
            insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
            
        }
    }    

?>