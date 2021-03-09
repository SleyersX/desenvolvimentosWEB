<?php
    require_once "/var/www/html/source/app-sat/config/config.php";

    // Conexão banco de dados
    $user="root";
    $passwd="diabrasil";
    $host="localhost";
    $banco="srvremoto";
    $conn = mysqli_connect($host,$user,$passwd,$banco);

    require_once "/var/www/html/source/app-sat/cron/config_email_auto.php";

    // 1º Verificar alarmes ativos tb_dashsat_alertas

    $sqlAlertas = "SELECT  dias_cupons_acumulados, n_dias_cupons_acumulados, cupons_acumulados, numero_cupons_acumulados, nivel_bateria, vencimento_certificado, dias_vencimento_certificado, comunicacao_sefaz, dias_sem_comunicarf_sefaz, variacao_relogio, variacao_ntp, estado_operacao_bloqueado, estado_wan_desligado FROM tb_dashsat_alertas";
    $queryAlertas = mysqli_query($conn,$sqlAlertas);
    $rowAlertas = mysqli_fetch_assoc($queryAlertas);

    $diasCuponsAcumulados      = $rowAlertas['dias_cupons_acumulados'];
    $nDiasCuponsAcumulados     = $rowAlertas['n_dias_cupons_acumulados'];
    $cuponsAcumulados          = $rowAlertas['cupons_acumulados'];
    $nCuponsAcumulados         = $rowAlertas['numero_cupons_acumulados'];
    $nivelBateria              = $rowAlertas['nivel_bateria'];
    $vencimentoCertificado     = $rowAlertas['vencimento_certificado'];
    $diasVencimentoCertificado = $rowAlertas['dias_vencimento_certificado'];
    $comunicacaoSefaz          = $rowAlertas['comunicacao_sefaz'];
    $diasSemComunicarfSefaz    = $rowAlertas['dias_sem_comunicarf_sefaz'];
    $variacaoRelogio           = $rowAlertas['variacao_relogio'];
    $variacaoNTP               = $rowAlertas['variacao_ntp'];
    $estadoOperacaoBloqueado   = $rowAlertas['estado_operacao_bloqueado'];
    $estadoWANDesligado        = $rowAlertas['estado_wan_desligado'];

    // 2º Verifica se têm email ativo
    $verificaEmailAtivo = "SELECT COUNT(id) AS total_registros FROM cn_email_alarmes WHERE status_email = 1 AND email_verif = 1 ";
    $queryVerEmailAtivo = mysqli_query($conn,$verificaEmailAtivo);
    $rowVerEmailAtivo = mysqli_fetch_assoc($queryVerEmailAtivo);

    if($rowVerEmailAtivo['total_registros'] >= 1){
        $sqlListEmailAlerta = "SELECT email FROM cn_email_alarmes WHERE status_email = 1 AND email_verif = 1";
        $queryListEmailAlerta = mysqli_query($conn,$sqlListEmailAlerta);
        while($rowListEmailAlerta = mysqli_fetch_array($queryListEmailAlerta)){
            $objListEmailAlerta[] = (object)$rowListEmailAlerta;
        }
        
        $countBDSAT = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ."";
        $queryCountBDSAT = mysqli_query($conn,$countBDSAT);
        $rowCountBDSAT = mysqli_fetch_assoc($queryCountBDSAT);

        if($rowCountBDSAT['total_registros'] >= 1){
            $readBDSAT = "SELECT sat, status_wan, nivel_bateria, descricao_bloqueio FROM ". DATA_CONFIG_BD["cn_tab_sat"] ."";
            $queryBDSAT = mysqli_query($conn,$readBDSAT);
            while($rowBDSAT = mysqli_fetch_array($queryBDSAT)){
                $sat = $rowBDSAT['sat'];
                if($vencimentoCertificado == 1){
                    $sqlVerificaSATVencimentoCertificado = "SELECT COUNT(sat) AS existe_sat, sat, loja, caixa, DATE_FORMAT(data_fim_ativacao,'%d/%m/%Y') AS data_fim_ativacao, n_dias FROM cn_hml_sat_vencimento_certificado WHERE sat = '$sat'";
                    $queryVerificaSATVencimentoCertificado = mysqli_query($conn,$sqlVerificaSATVencimentoCertificado);
                    $rowVerificaSATVencimentoCertificado = mysqli_fetch_assoc($queryVerificaSATVencimentoCertificado);
                    $shop = $rowVerificaSATVencimentoCertificado['loja'];
                    $pos = $rowVerificaSATVencimentoCertificado['caixa'];
                    if($rowVerificaSATVencimentoCertificado['existe_sat'] >= 1){
                        if($rowVerificaSATVencimentoCertificado['n_dias'] <= $diasVencimentoCertificado){
                            $objVencimentoCertificado[] = (object) $rowVerificaSATVencimentoCertificado;
                        }else{
                            $objVencimentoCertificadoI[] = (object) $rowVerificaSATVencimentoCertificado;
                        }
                    }
                }
                if($comunicacaoSefaz == 1){
                    $sqlVerificaSATComunicacaoSefaz = "SELECT COUNT(sat) AS existe_sat, sat, loja, caixa, DATE_FORMAT(data_hora_comun_sefaz,'%d/%m/%Y - %H:%i:%s') AS data_hora_comun_sefaz, n_dias, n_cfes_memoria FROM cn_hml_sat_data_comun_sefaz WHERE sat = '$sat'";
                    $queryVerificaSATComunicacaoSefaz = mysqli_query($conn,$sqlVerificaSATComunicacaoSefaz);
                    $rowVerificaSATComunicacaoSefaz = mysqli_fetch_assoc($queryVerificaSATComunicacaoSefaz);
                    $shop = $rowVerificaSATComunicacaoSefaz['loja'];
                    $pos = $rowVerificaSATComunicacaoSefaz['caixa'];
                    if($rowVerificaSATComunicacaoSefaz['existe_sat'] >= 1){
                        if($rowVerificaSATComunicacaoSefaz['n_dias'] >= $diasSemComunicarfSefaz && $rowVerificaSATComunicacaoSefaz['n_cfes_memoria'] >= 1){
                            $objComunicacaoSefaz[] = (object) $rowVerificaSATComunicacaoSefaz;
                        }else{
                            $objComunicacaoSefazI[] = (object) $rowVerificaSATComunicacaoSefaz;
                        }
                    }
                }
            }
            if(isset($objVencimentoCertificadoI)){
                $msgLog = "SATs ";
                foreach($objVencimentoCertificadoI as $key=>$val){
                    $msgLog = $msgLog . "[" . $objVencimentoCertificadoI[$key]->sat . "]";
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Vencimento Certificado'; 
                $msgLog = $msgLog . " sem necessidade de envio de e-mail.";
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
            }
            unset($msgLog);
            if(isset($objComunicacaoSefazI)){
                $msgLog = "SATs ";
                foreach($objComunicacaoSefazI as $key=>$val){
                    $msgLog = $msgLog . "[" . $objComunicacaoSefazI[$key]->sat . "]";
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Comunicação SEFAZ'; 
                $msgLog = $msgLog . " sem necessidade de envio de e-mail.";
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
            }
            $body = "Segue abaixo lista de SATs:<br /><br />";
            if(isset($objVencimentoCertificado)){
                foreach($objVencimentoCertificado as $key=>$val){
                    $body = $body . "# Número SAT: " . $objVencimentoCertificado[$key]->sat . " Loja: " . $objVencimentoCertificado[$key]->loja . " Caixa: " . $objVencimentoCertificado[$key]->caixa . " Data vencimento: " . $objVencimentoCertificado[$key]->data_fim_ativacao . " Dias restantes: " . $objVencimentoCertificado[$key]->n_dias . "<br />";
                }
                if(isset($objListEmailAlerta)){
                    foreach ($objListEmailAlerta as $key=>$val) {
                        send_email_alerta(99, $objListEmailAlerta[$key]->email, 'root', 'System', $body, 'Vencimento Certificado');
                    }
                }
                unset($msgLog);
                $msgLog = "SATs ";
                foreach($objVencimentoCertificado as $key=>$val){
                    $msgLog = $msgLog . ",[" . $objVencimentoCertificado[$key]->sat . "]";
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Vencimento Certificado';
                $msgLog = $msgLog . " adicionados a lista de envio de e-mail.";  
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                
            }
            unset($body);
            $body = "Segue abaixo lista de SATs:<br /><br />";
            if(isset($objComunicacaoSefaz)){
                foreach($objComunicacaoSefaz as $key=>$val){
                    $body = $body . "# Número SAT: " . $objComunicacaoSefaz[$key]->sat . " Loja: " . $objComunicacaoSefaz[$key]->loja . " Caixa: " . $objComunicacaoSefaz[$key]->caixa . " Data última comunicação: " . $objComunicacaoSefaz[$key]->data_hora_comun_sefaz . " Dias sem comunicar: " . $objComunicacaoSefaz[$key]->n_dias . "<br />";
                }
                if(isset($objListEmailAlerta)){
                    foreach ($objListEmailAlerta as $key=>$val) {
                        send_email_alerta(99, $objListEmailAlerta[$key]->email, 'root', 'System', $body, 'Comunicação SEFAZ');
                    }
                }
                unset($msgLog);
                $msgLog = "SATs ";
                foreach($objComunicacaoSefaz as $key=>$val){
                    $msgLog = $msgLog . ",[" . $objComunicacaoSefaz[$key]->sat . "]";
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Comunicação SEFAZ';
                $msgLog = $msgLog . " adicionados a lista de envio de e-mail.";  
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                
            }
        }else{
            require_once("/var/www/html/source/app-sat/cron/processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'E-mail automático'; 
            $msgLog = 'Nenhum SAT encontrado no banco de dados.';
            insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
        }
    }else{
        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'E-mail automático'; 
        $msgLog = 'Não há e-mails configurados para envio de alertas.';
        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
    }

?>