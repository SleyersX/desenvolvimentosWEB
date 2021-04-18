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

        $slqCheckToken = "SELECT COUNT(id) AS existe_token FROM tb_email_conta WHERE token_verificacao LIKE '$token'";
        $queryCheckToken = mysqli_query($conn,$slqCheckToken);
        $rowCheckToken = mysqli_fetch_assoc($queryCheckToken);

        if($rowCheckToken['existe_token'] >= 1){
            $slqGetToken = "SELECT id_user, nome_user, login_user, email_user, token_verificacao, data_inclusao, data_expiracao, status_token FROM tb_email_conta WHERE token_verificacao LIKE '$token'";
            $queryGetToken = mysqli_query($conn,$slqGetToken);
            $rowGetToken = mysqli_fetch_assoc($queryGetToken);
            $iduser = $rowGetToken['id_user'];
            $statustoken = $rowGetToken['status_token'];
            if($statustoken == 1){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Ativação Conta'; 
                $msgLog = 'Token ['.$token.'] já utilizado.';
                insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
                echo "<script>alert('Token [$token] já utilizado.');</script>";
                echo "<script>window.close();</script>";

            }else{
                $sqlUpdateUser = "UPDATE tb_usuarios_dashsat SET ativo = 1, email_verificado = 1, data_verificacao_email = NOW() WHERE id = '$iduser'";
                $queryUpdateUser = mysqli_query($conn,$sqlUpdateUser);
                if(mysqli_affected_rows($conn)){
                    $sqlUpdateVerEmail = "UPDATE tb_email_conta SET status_token = 1 WHERE  token_verificacao LIKE '$token'";
                    $queryUpdateVerEmail = mysqli_query($conn,$sqlUpdateVerEmail);
                    if(mysqli_affected_rows($conn)){
                        $sqlDadaToken = "SELECT id_user, email_user, login_user, nome_user FROM tb_email_conta WHERE token_verificacao LIKE '$token'";
                        $queryDataToken = mysqli_query($conn,$sqlDadaToken);
                        $rowDataToken = mysqli_fetch_assoc($queryDataToken);
                        
                        $idUserVerificacao = $rowDataToken['id_user'];
                        $emailVerificacao = $rowDataToken['email_user'];
                        $loginVerificacao = $rowDataToken['login_user'];
                        $nomeVerificacao = $rowDataToken['nome_user'];
                        
                        require_once("send_email.php");
                        send_email_verificacao_I($idUserVerificacao,$emailVerificacao,$loginVerificacao,$nomeVerificacao);

                        //Grava LOG
                        require_once("processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'Ativação Conta'; 
                        $msgLog = 'Token ['.$token.'] verificado com sucesso, conta ativada.';
                        insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
                        echo "<script>alert('Token verificado com sucesso, conta ativada, siga as intruções enviadas no seu e-mail.');</script>";
                        echo "<script>window.close();</script>";
                    }
                }else{
                    //Grava LOG
                    require_once("processa_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Ativação Conta'; 
                    $msgLog = 'Erro ao verificar token ['.$token.'], contate o administrador do sistema.';
                    insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
                    echo "<script>alert('Erro ao verificar token [$token], contate o administrador do sistema.');</script>";
                    echo "<script>window.close();</script>";
                }
            }
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Ativação Conta'; 
            $msgLog = 'Token ['.$token.'] não encontrado.';
            insert_log_II('99','System','root',$appCallLog,$dataLog,$msgLog);
            echo "<script>alert('Token [$token] não encontrado.');</script>";
            echo "<script>window.close();</script>";
        }
            
    }
?>