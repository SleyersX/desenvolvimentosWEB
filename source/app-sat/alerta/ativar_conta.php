<?php
    $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
    if(empty($token)){
        //Grava LOG
        require_once("processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Ativação Conta'; 
        $msgLog = 'Token ['.$token.'] não informado.';
        insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);

        echo "<script>alert('Ops!Token [$token] não informado.');</script>";
    }else{
        // Conexão banco de dados
        $user="root";
        $passwd="diabrasil";
        $host="database";
        $banco="srvremoto";
        $conn = mysqli_connect($host,$user,$passwd,$banco);

        $slqCheckToken = "SELECT COUNT(id) AS existe_token FROM tb_verifica_email_alerta WHERE token_verificacao LIKE '$token'";
        $queryCheckToken = mysqli_query($conn,$slqCheckToken);
        $rowCheckToken = mysqli_fetch_assoc($queryCheckToken);

        if($rowCheckToken['existe_token'] >= 1){
            $slqGetToken = "SELECT id_email,email_alarme, data_inclusao, token_verificacao, data_verificacao, status_token  FROM tb_verifica_email_alerta WHERE token_verificacao LIKE '$token'";
            $queryGetToken = mysqli_query($conn,$slqGetToken);
            $rowGetToken = mysqli_fetch_assoc($queryGetToken);
            $idEmail = $rowGetToken['id_email'];
            $emailAlerta = $rowGetToken['email_alarme'];
            $statustoken = $rowGetToken['status_token'];
            if($statustoken == 1){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Ativação Email Alarme'; 
                $msgLog = 'Token ['.$token.'] já utilizado.';
                insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
                echo "<script>alert('Token [$token] já utilizado.');</script>";
                echo "<script>window.close();</script>";

            }else{
                $tipoEmailAlerta = "SELECT COUNT(id) AS existe_email FROM tb_verifica_email_alerta WHERE id = '$idEmail' AND email LIKE '$emailAlerta'";
                $queryEmailAlerta = mysqli_query($conn,$tipoEmailAlerta);
                $rowEmailAlerta = mysqli_fetch_assoc($queryEmailAlerta);
                if($rowEmailAlerta['existe_email'] >= 1){
                    $sqlUpdateEmailAlarme = "UPDATE tb_email_alarmes SET status_email = 1, email_verificado = 1, data_verificacao_email = NOW() WHERE id = '$idEmail'";
                    $queryUpdateEmailAlarme = mysqli_query($conn,$sqlUpdateEmailAlarme);
                    if(mysqli_affected_rows($conn)){
                        $sqlUpdateVerEmail = "UPDATE tb_verifica_email_alerta SET status_token = 1, data_verificacao = NOW() WHERE  token_verificacao LIKE '$token'";
                        $queryUpdateVerEmail = mysqli_query($conn,$sqlUpdateVerEmail);
                        if(mysqli_affected_rows($conn)){
                            $sqlDadaToken = "SELECT id_email,email_alarme, data_inclusao, token_verificacao, data_verificacao, status_token  FROM tb_verifica_email_alerta WHERE token_verificacao LIKE '$token'";
                            $queryDataToken = mysqli_query($conn,$sqlDadaToken);
                            $rowDataToken = mysqli_fetch_assoc($queryDataToken);
                            
                            $idUserVerificacao = 99;
                            $emailVerificacao = $emailAlerta;
                            $loginVerificacao = 'root';
                            $nomeVerificacao = 'System';
                            
                            require_once("send_email.php");
                            send_email_verificacao_alarme_I($idUserVerificacao,$emailVerificacao,$loginVerificacao,$nomeVerificacao);

                            //Grava LOG
                            require_once("processa_log.php");
                            $dataLog = date('Y-m-d H:i:s');
                            $appCallLog = 'Ativação Email Alarme'; 
                            $msgLog = 'Token ['.$token.'] verificado com sucesso, conta ativada.';
                            insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
                            echo "<script>alert('Token verificado com sucesso, conta ativada, siga as intruções enviadas no seu e-mail.');</script>";
                            echo "<script>window.close();</script>";
                        }
                    }else{
                        //Grava LOG
                        require_once("processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'Ativação Email Alarme'; 
                        $msgLog = 'Erro ao verificar token ['.$token.'], contate o administrador do sistema.';
                        insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
                        echo "<script>alert('Erro ao verificar token [$token], contate o administrador do sistema.');</script>";
                        echo "<script>window.close();</script>";
                    }
                }else{
                    $sqlUpdateEmailAlarme = "UPDATE tb_email_alarmes_exception SET status_email = 1, email_verificado = 1, data_verificacao_email = NOW() WHERE id = '$idEmail'";
                    $queryUpdateEmailAlarme = mysqli_query($conn,$sqlUpdateEmailAlarme);
                    if(mysqli_affected_rows($conn)){
                        $sqlUpdateVerEmail = "UPDATE tb_verifica_email_alerta SET status_token = 1, data_verificacao = NOW() WHERE  token_verificacao LIKE '$token'";
                        $queryUpdateVerEmail = mysqli_query($conn,$sqlUpdateVerEmail);
                        if(mysqli_affected_rows($conn)){
                            $sqlDadaToken = "SELECT id_email,email_alarme, data_inclusao, token_verificacao, data_verificacao, status_token  FROM tb_verifica_email_alerta WHERE token_verificacao LIKE '$token'";
                            $queryDataToken = mysqli_query($conn,$sqlDadaToken);
                            $rowDataToken = mysqli_fetch_assoc($queryDataToken);
                            
                            $idUserVerificacao = 99;
                            $emailVerificacao = $emailAlerta;
                            $loginVerificacao = 'root';
                            $nomeVerificacao = 'System';
                            
                            require_once("send_email.php");
                            send_email_verificacao_alarme_I($idUserVerificacao,$emailVerificacao,$loginVerificacao,$nomeVerificacao);

                            //Grava LOG
                            require_once("processa_log.php");
                            $dataLog = date('Y-m-d H:i:s');
                            $appCallLog = 'Ativação Email Exception'; 
                            $msgLog = 'Token ['.$token.'] verificado com sucesso, conta ativada.';
                            insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
                            echo "<script>alert('Token verificado com sucesso, conta ativada, siga as intruções enviadas no seu e-mail.');</script>";
                            echo "<script>window.close();</script>";
                        }
                    }else{
                        //Grava LOG
                        require_once("processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'Ativação Email Exception'; 
                        $msgLog = 'Erro ao verificar token ['.$token.'], contate o administrador do sistema.';
                        insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
                        echo "<script>alert('Erro ao verificar token [$token], contate o administrador do sistema.');</script>";
                        echo "<script>window.close();</script>";
                    }
                }
            }
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Ativação Email Alarme'; 
            $msgLog = 'Token ['.$token.'] não encontrado.';
            insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
            echo "<script>alert('Token [$token] não encontrado.');</script>";
            echo "<script>window.close();</script>";
        }
            
    }
?>