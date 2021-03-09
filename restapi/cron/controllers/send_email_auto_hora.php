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
    $verificaEmailAtivo = "SELECT COUNT(id) AS total_registros FROM cn_email_alarmes WHERE status_email = 1 AND  email_verif = 1";
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
                if($diasCuponsAcumulados == 1){
                    $sendEmail = "SELECT COUNT(sat) AS send_email FROM tb_send_email_auto WHERE motivo_email LIKE 'DIAS_CFES_MEMORIA' AND sat = '$sat' AND DATE_FORMAT( data_hora_envio, '%Y-%m-%d' ) =  DATE_FORMAT(NOW(), '%Y-%m-%d')";
                    $querySendEmail = mysqli_query($conn,$sendEmail);
                    $rowSendEmail = mysqli_fetch_assoc($querySendEmail);
                    if($rowSendEmail['send_email'] == 0){
                        $sqlVerificaSATDiasCuponsAcumulados = "SELECT COUNT(sat) AS existe_sat, sat, loja, caixa, numeros_cfes_memoria, n_dias FROM cn_hml_sat_data_transm_sefaz WHERE sat = '$sat'";
                        $queryVerificaSATDiasCuponsAcumulados = mysqli_query($conn,$sqlVerificaSATDiasCuponsAcumulados);
                        $rowVerificaSATDiasCuponsAcumulados = mysqli_fetch_assoc($queryVerificaSATDiasCuponsAcumulados);
                        $shop = $rowVerificaSATDiasCuponsAcumulados['loja'];
                        $pos = $rowVerificaSATDiasCuponsAcumulados['caixa'];
                        if($rowVerificaSATDiasCuponsAcumulados['existe_sat'] >= 1){
                            if($rowVerificaSATDiasCuponsAcumulados['numeros_cfes_memoria'] >= 1 && $rowVerificaSATDiasCuponsAcumulados['n_dias'] >= $nDiasCuponsAcumulados ){
                                $objDiasCuponsAculados[] = (object) $rowVerificaSATDiasCuponsAcumulados;
                            }else{
                                $objDiasCuponsAculadosI[] = (object) $rowVerificaSATDiasCuponsAcumulados;
                            }
                        }
                    }
                }
                if($cuponsAcumulados == 1){
                    $sendEmail = "SELECT COUNT(sat) AS send_email FROM tb_send_email_auto WHERE motivo_email LIKE 'NUMERO_CFES_MEMORIA' AND sat = '$sat' AND DATE_FORMAT( data_hora_envio, '%Y-%m-%d' ) =  DATE_FORMAT(NOW(), '%Y-%m-%d')";
                    $querySendEmail = mysqli_query($conn,$sendEmail);
                    $rowSendEmail = mysqli_fetch_assoc($querySendEmail);
                    if ($rowSendEmail['send_email'] == 0) {
                        $sqlVerificaSATCuponsAcumulados = "SELECT COUNT(sat) AS existe_sat, sat, loja, caixa, numeros_cfes_memoria, n_dias FROM cn_hml_sat_cupons_memeoria WHERE sat = '$sat'";
                        $queryVerificaSATCuponsAcumulados = mysqli_query($conn, $sqlVerificaSATCuponsAcumulados);
                        $rowVerificaSATCuponsAcumulados = mysqli_fetch_assoc($queryVerificaSATCuponsAcumulados);
                        $shop = $rowVerificaSATCuponsAcumulados['loja'];
                        $pos = $rowVerificaSATCuponsAcumulados['caixa'];
                        if ($rowVerificaSATCuponsAcumulados['existe_sat'] >= 1) {
                            if ($rowVerificaSATCuponsAcumulados['numeros_cfes_memoria'] >= $nCuponsAcumulados) {
                                $objCuponsAculados[] = (object) $rowVerificaSATCuponsAcumulados;
                            } else {
                                $objCuponsAculadosI[] = (object) $rowVerificaSATCuponsAcumulados;
                            }
                        }
                    }
                }
                if($nivelBateria == 1){
                    $sendEmail = "SELECT COUNT(sat) AS send_email FROM tb_send_email_auto WHERE motivo_email LIKE 'NIVEL_BATERIA' AND sat = '$sat'  AND DATE_FORMAT( data_hora_envio, '%Y-%m-%d' ) =  DATE_FORMAT(NOW(), '%Y-%m-%d')";
                    $querySendEmail = mysqli_query($conn,$sendEmail);
                    $rowSendEmail = mysqli_fetch_assoc($querySendEmail);
                    if ($rowSendEmail['send_email'] == 0) {
                        $sqlVerificaSATNivelBateria = "SELECT COUNT(sat) AS existe_sat, sat, loja, caixa, nivel_bateria FROM cn_hml_sat_nivel_bateria WHERE sat = '$sat'";
                        $queryVerificaSATNivelBateria = mysqli_query($conn, $sqlVerificaSATNivelBateria);
                        $rowVerificaSATNivelBateria = mysqli_fetch_assoc($queryVerificaSATNivelBateria);
                        $shop = $rowVerificaSATNivelBateria['loja'];
                        $pos = $rowVerificaSATNivelBateria['caixa'];
                        $batery = $rowVerificaSATNivelBateria['nivel_bateria'];
                        if ($rowVerificaSATNivelBateria['existe_sat'] >= 1) {
                            if ($rowVerificaSATNivelBateria['nivel_bateria'] == 'BAIXO') {
                                $objNivelBateria[] = (object) $rowVerificaSATNivelBateria;
                            } else {
                                $objNivelBateriaI[] = (object) $rowVerificaSATNivelBateria;
                            }
                        }
                    }
                }
                if($variacaoRelogio == 1){
                    $sendEmail = "SELECT COUNT(sat) AS send_email FROM tb_send_email_auto WHERE motivo_email LIKE 'VARIACAO_RELOGIO' AND sat = '$sat' AND DATE_FORMAT( data_hora_envio, '%Y-%m-%d' ) =  DATE_FORMAT(NOW(), '%Y-%m-%d')";
                    $querySendEmail = mysqli_query($conn,$sendEmail);
                    $rowSendEmail = mysqli_fetch_assoc($querySendEmail);
                    if ($rowSendEmail['send_email'] == 0) {
                        $sqlVerificaSATVariacaoRelogio = "SELECT COUNT(sat) AS existe_sat, sat, loja, caixa, data_atualizacao, data_hora_atual, diff_minutos FROM cn_hml_diff_hora_sat WHERE sat = '$sat' AND diff_minutos != 0";
                        $queryVerificaSATVariacaoRelogio = mysqli_query($conn, $sqlVerificaSATVariacaoRelogio);
                        $rowVerificaSATVariacaoRelogio = mysqli_fetch_assoc($queryVerificaSATVariacaoRelogio);
                        $shop = $rowVerificaSATVariacaoRelogio['loja'];
                        $pos = $rowVerificaSATVariacaoRelogio['caixa'];
                        if ($rowVerificaSATVariacaoRelogio['existe_sat'] >= 1) {
                            if ($rowVerificaSATVariacaoRelogio['diff_minutos'] >= $variacaoNTP) {
                                $objVariacaoRelogio[] = (object) $rowVerificaSATVariacaoRelogio;
                            } else {
                                $objVariacaoRelogioI[] = (object) $rowVerificaSATVariacaoRelogio;
                            }
                        }
                    }
                }
                if($estadoOperacaoBloqueado == 1){
                    $sendEmail = "SELECT COUNT(sat) AS send_email FROM tb_send_email_auto WHERE motivo_email LIKE 'ESTADO_BLOQUEIO' AND sat = '$sat' AND DATE_FORMAT( data_hora_envio, '%Y-%m-%d' ) =  DATE_FORMAT(NOW(), '%Y-%m-%d')";
                    $querySendEmail = mysqli_query($conn,$sendEmail);
                    $rowSendEmail = mysqli_fetch_assoc($querySendEmail);
                    if ($rowSendEmail['send_email'] == 0) {
                        $sqlVerificaSATEstadoOperacaoBloqueado = "SELECT COUNT(sat) AS existe_sat, sat, loja, caixa, descricao_bloqueio FROM cn_hml_sats_bloqueio WHERE sat = '$sat'";
                        $queryVerificaSATEstadoOperacaoBloqueado = mysqli_query($conn, $sqlVerificaSATEstadoOperacaoBloqueado);
                        $rowVerificaSATEstadoOperacaoBloqueado = mysqli_fetch_assoc($queryVerificaSATEstadoOperacaoBloqueado);
                        $shop = $rowVerificaSATEstadoOperacaoBloqueado['loja'];
                        $pos = $rowVerificaSATEstadoOperacaoBloqueado['caixa'];
                        if ($rowVerificaSATEstadoOperacaoBloqueado['existe_sat'] >= 1) {
                            $objEstadoOperacaoBloqueado[] = (object) $rowVerificaSATEstadoOperacaoBloqueado;
                        }
                    }
                }
                if($estadoWANDesligado == 1){
                    $sendEmail = "SELECT COUNT(sat) AS send_email FROM tb_send_email_auto WHERE motivo_email LIKE 'ESTADO_WAN' AND sat = '$sat' AND DATE_FORMAT( data_hora_envio, '%Y-%m-%d' ) =  DATE_FORMAT(NOW(), '%Y-%m-%d')";
                    $querySendEmail = mysqli_query($conn,$sendEmail);
                    $rowSendEmail = mysqli_fetch_assoc($querySendEmail);
                    if ($rowSendEmail['send_email'] == 0) {
                        $sqlVerificaSATEstadoWANDesligado = "SELECT COUNT(sat) AS existe_sat, sat, loja, caixa, status_wan FROM cn_hml_status_wan WHERE sat = '$sat'";
                        $queryVerificaSATEstadoWANDesligado = mysqli_query($conn, $sqlVerificaSATEstadoWANDesligado);
                        $rowVerificaSATEstadoWANDesligado = mysqli_fetch_assoc($queryVerificaSATEstadoWANDesligado);
                        $shop = $rowVerificaSATEstadoWANDesligado['loja'];
                        $pos = $rowVerificaSATEstadoWANDesligado['caixa'];
                        if ($rowVerificaSATEstadoWANDesligado['existe_sat'] >= 1) {
                            $objEstadoWANDesligado[] = (object) $rowVerificaSATEstadoWANDesligado;
                        }
                    }
                }
            }
            if(isset($objDiasCuponsAculadosI)){
                $msgLog = "SATs ";
                foreach($objDiasCuponsAculadosI as $key=>$val){
                    $msgLog = $msgLog . "[" . $objDiasCuponsAculadosI[$key]->sat . "]";
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Dias Cupons Memoria'; 
                $msgLog = $msgLog . " sem necessidade de envio de e-mail.";
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
            }
            unset($msgLog);
            if(isset($objCuponsAculadosI)){
                $msgLog = "SATs ";
                foreach($objCuponsAculadosI as $key=>$val){
                    $msgLog = $msgLog . "[" . $objCuponsAculadosI[$key]->sat . "]";
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Cupons Acumulados Memória';
                $msgLog = $msgLog . " sem necessidade de envio de e-mail."; 
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
            }
            unset($msgLog);
            if(isset($objNivelBateriaI)){
                $msgLog = "SATs ";
                foreach($objNivelBateriaI as $key=>$val){
                    $msgLog = $msgLog . "[" . $objNivelBateriaI[$key]->sat . "]";
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Nivel Bateria';
                $msgLog = $msgLog . " sem necessidade de envio de e-mail."; 
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
            }
            unset($msgLog);
            if(isset($objVariacaoRelogioI)){
                $msgLog = "SATs ";
                foreach($objVariacaoRelogioI as $key=>$val){
                    $msgLog = $msgLog . "[" . $objVariacaoRelogioI[$key]->sat . "]";
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Variação do Relógio';
                $msgLog = $msgLog . " sem necessidade de envio de e-mail."; 
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
            }
            $body = "Segue abaixo lista de SATs:<br /><br />";
            if(isset($objDiasCuponsAculados)){
                foreach($objDiasCuponsAculados as $key=>$val){
                    $body = $body . "# Número SAT: " . $objDiasCuponsAculados[$key]->sat . " Loja: " . $objDiasCuponsAculados[$key]->loja . " Caixa: " . $objDiasCuponsAculados[$key]->caixa . " Dias cupons em memória: " . $objDiasCuponsAculados[$key]->n_dias . "<br />";
                }
                if(isset($objListEmailAlerta)){
                    foreach ($objListEmailAlerta as $key=>$val) {
                        send_email_alerta(99, $objListEmailAlerta[$key]->email, 'root', 'System', $body, 'Dias Cupons Acumulados em Memória');
                    }
                }
                unset($msgLog);
                $msgLog = "SATs ";
                foreach($objDiasCuponsAculados as $key=>$val){
                    $msgLog = $msgLog . ",[" . $objDiasCuponsAculados[$key]->sat . "]";
                    $nsat = $objVariacaoRelogio[$key]->sat;
                    $insertSendEmail = "INSERT INTO tb_send_email_auto (sat,motivo_email,data_hora_envio) VALUES ('$nsat','DIAS_CFES_MEMORIA',NOW())";
                    $queryInsertSendEmail = mysqli_query($conn,$insertSendEmail);
                    if(mysqli_insert_id($conn)){
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Dias Cupons Memoria';
                        $msgLog = "SAT ['".$nsat."'] adicionados com sucesso no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                    }else{
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Dias Cupons Memoria';
                        $msgLog = "Erro ao adicionar SAT ['".$nsat."'] no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                    }
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Dias Cupons Memoria';
                $msgLog = $msgLog . " adicionados a lista de envio de e-mail.";  
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                
            }
            unset($body);
            $body = "Segue abaixo lista de SATs:<br /><br />";
            if(isset($objCuponsAculados)){
                foreach($objCuponsAculados as $key=>$val){
                    $body = $body . "# Número SAT: " . $objCuponsAculados[$key]->sat . " Loja: " . $objCuponsAculados[$key]->loja . " Caixa: " . $objCuponsAculados[$key]->caixa . " Total cupons em memória: " . $objCuponsAculados[$key]->numeros_cfes_memoria . "<br />";
                }
                if (isset($objListEmailAlerta)) {
                    foreach ($objListEmailAlerta as $key=>$val) {
                        send_email_alerta(99, $objListEmailAlerta[$key]->email, 'root', 'System', $body, 'Cupons Acumulados em Memória');
                    }
                }
                unset($msgLog);
                $msgLog = "SATs ";
                foreach($objCuponsAculados as $key=>$val){
                    $msgLog = $msgLog . ",[" . $objCuponsAculados[$key]->sat . "]";
                    $nsat = $objVariacaoRelogio[$key]->sat;
                    $insertSendEmail = "INSERT INTO tb_send_email_auto (sat,motivo_email,data_hora_envio) VALUES ('$nsat','NUMERO_CFES_MEMORIA',NOW())";
                    $queryInsertSendEmail = mysqli_query($conn,$insertSendEmail);
                    if(mysqli_insert_id($conn)){
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Cupons Acumulados Memória';
                        $msgLog = "SAT ['".$nsat."'] adicionados com sucesso no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                    }else{
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Cupons Acumulados Memória';
                        $msgLog = "Erro ao adicionar SAT ['".$nsat."'] no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                    }
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Cupons Acumulados Memória';
                $msgLog = $msgLog . " adicionados a lista de envio de e-mail."; 
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                
            }
            unset($body);
            $body = "Segue abaixo lista de SATs:<br /><br />";
            if(isset($objNivelBateria)){
                foreach($objNivelBateria as $key=>$val){
                    $body = $body . "# Número SAT: " . $objNivelBateria[$key]->sat . " Loja: " . $objNivelBateria[$key]->loja . " Caixa: " . $objNivelBateria[$key]->caixa . " Bateria: " . $objNivelBateria[$key]->nivel_bateria . "<br />";
                }
                if (isset($objListEmailAlerta)) {
                    foreach ($objListEmailAlerta as $key=>$val) {
                        send_email_alerta(99, $objListEmailAlerta[$key]->email, 'root', 'System', $body, 'Nivel Bateria');
                    }
                }
                unset($msgLog);
                $msgLog = "SATs ";
                foreach($objNivelBateria as $key=>$val){
                    $msgLog = $msgLog . ",[" . $objNivelBateria[$key]->sat . "]";
                    $nsat = $objNivelBateria[$key]->sat;
                    $insertSendEmail = "INSERT INTO tb_send_email_auto (sat,motivo_email,data_hora_envio) VALUES ('$nsat','NIVEL_BATERIA',NOW())";
                    $queryInsertSendEmail = mysqli_query($conn,$insertSendEmail);
                    if(mysqli_insert_id($conn)){
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Nivel Bateria';
                        $msgLog = "SAT ['".$nsat."'] adicionados com sucesso no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                    }else{
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Nivel Bateria';
                        $msgLog = "Erro ao adicionar SAT ['".$nsat."'] no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                    }
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Nivel Bateria';
                $msgLog = $msgLog . " adicionados a lista de envio de e-mail."; 
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                
            }
            unset($body);
            $body = "Segue abaixo lista de SATs:<br /><br />";
            if(isset($objVariacaoRelogio)){
                foreach($objVariacaoRelogio as $key=>$val){
                    $body = $body . "# Número SAT: " . $objVariacaoRelogio[$key]->sat . " Loja: " . $objVariacaoRelogio[$key]->loja . " Caixa: " . $objVariacaoRelogio[$key]->caixa . " Data/Hora SAT: " . $objVariacaoRelogio[$key]->data_hora_atual . " Data/Hora atualização: " . $objVariacaoRelogio[$key]->data_atualizacao . " Diff Minutos: " . $objVariacaoRelogio[$key]->diff_minutos . "<br />";
                }
                if (isset($objListEmailAlerta)) {
                    foreach ($objListEmailAlerta as $key=>$val) {
                        send_email_alerta(99, $objListEmailAlerta[$key]->email, 'root', 'System', $body, 'Variação do Relógio');
                    }
                }
                unset($msgLog);
                $msgLog = "SATs ";
                foreach($objVariacaoRelogio as $key=>$val){
                    $msgLog = $msgLog . ",[" . $objVariacaoRelogio[$key]->sat . "]";
                    $nsat = $objVariacaoRelogio[$key]->sat;
                    $insertSendEmail = "INSERT INTO tb_send_email_auto (sat,motivo_email,data_hora_envio) VALUES ('$nsat','VARIACAO_RELOGIO',NOW())";
                    $queryInsertSendEmail = mysqli_query($conn,$insertSendEmail);
                    if(mysqli_insert_id($conn)){
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Variação do Relógio';
                        $msg = "SAT ['".$nsat."'] adicionados com sucesso no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msg);
                    }else{
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Variação do Relógio';
                        $msg = "Erro ao adicionar SAT ['".$nsat."'] no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msg);
                    }
                    unset($nsat);
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Variação do Relógio';
                $msgLog = $msgLog . " adicionados a lista de envio de e-mail."; 
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                
            }
            unset($body);
            $body = "Segue abaixo lista de SATs:<br /><br />";
            if(isset($objEstadoOperacaoBloqueado)){
                foreach($objEstadoOperacaoBloqueado as $key=>$val){
                    $body = $body . "# Número SAT: " . $objEstadoOperacaoBloqueado[$key]->sat . " Loja: " . $objEstadoOperacaoBloqueado[$key]->loja . " Caixa: " . $objEstadoOperacaoBloqueado[$key]->caixa . " Estado bloqueio: " . $objEstadoOperacaoBloqueado[$key]->descricao_bloqueio . "<br />";
                }
                if (isset($objListEmailAlerta)) {
                    foreach ($objListEmailAlerta as $key=>$val) {
                        send_email_alerta(99, $objListEmailAlerta[$key]->email, 'root', 'System', $body, 'Estado Bloqueio');
                    }
                }
                unset($msgLog);
                $msgLog = "SATs ";
                foreach($objEstadoOperacaoBloqueado as $key=>$val){
                    $msgLog = $msgLog . ",[" . $objEstadoOperacaoBloqueado[$key]->sat . "]";
                    $nsat = $objEstadoOperacaoBloqueado[$key]->sat;
                    $insertSendEmail = "INSERT INTO tb_send_email_auto (sat,motivo_email,data_hora_envio) VALUES ('$nsat','ESTADO_BLOQUEIO',NOW())";
                    $queryInsertSendEmail = mysqli_query($conn,$insertSendEmail);
                    if(mysqli_insert_id($conn)){
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Estado Bloqueio';
                        $msgLog = "SAT ['".$nsat."'] adicionados com sucesso no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                    }else{
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Estado Bloqueio';
                        $msgLog = "Erro ao adicionar SAT ['".$nsat."'] no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                    }
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Estado Bloqueio';
                $msgLog = $msgLog . " adicionados a lista de envio de e-mail."; 
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
            }else{
                unset($msgLog);
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Estado Bloqueio';
                $msgLog = "Nenhum SAT bloqueado."; 
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog); 
            }
            unset($body);
            $body = "Segue abaixo lista de SATs:<br /><br />";
            if(isset($objEstadoWANDesligado)){
                foreach($objEstadoWANDesligado as $key=>$val){
                    $body = $body . "# Número SAT: " . $objEstadoWANDesligado[$key]->sat . " Loja: " . $objEstadoWANDesligado[$key]->loja . " Caixa: " . $objEstadoWANDesligado[$key]->caixa . " Estado WAN: " . $objEstadoWANDesligado[$key]->status_wan . "<br />";
                }
                if (isset($objListEmailAlerta)) {
                    foreach ($objListEmailAlerta as $key=>$val) {
                        send_email_alerta(99, $objListEmailAlerta[$key]->email, 'root', 'System', $body, 'Estado WAN');
                    }
                }
                unset($msgLog);
                $msgLog = "SATs ";
                foreach($objEstadoWANDesligado as $key=>$val){
                    $msgLog = $msgLog . ",[" . $objEstadoWANDesligado[$key]->sat . "]";
                    $nsat = $objEstadoWANDesligado[$key]->sat;
                    $insertSendEmail = "INSERT INTO tb_send_email_auto (sat,motivo_email,data_hora_envio) VALUES ('$nsat','ESTADO_WAN',NOW())";
                    $queryInsertSendEmail = mysqli_query($conn,$insertSendEmail);
                    if(mysqli_insert_id($conn)){
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Estado WAN';
                        $msgLog = "SAT ['".$nsat."'] adicionados com sucesso no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                    }else{
                        require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                        $dataLog = date('Y-m-d H:i:s');
                        $appCallLog = 'E-mail automático - Estado WAN';
                        $msgLog = "Erro ao adicionar SAT ['".$nsat."'] no banco de dados.";  
                        insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
                    }
                }
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Estado WAN';
                $msgLog = $msgLog . " adicionados a lista de envio de e-mail."; 
                insert_log_III('99','System','root',$appCallLog,$dataLog,$msgLog);
            }else{
                unset($msgLog);
                require_once("/var/www/html/source/app-sat/cron/processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'E-mail automático - Estado WAN';
                $msgLog = "Nenhum SAT com a porta WAN desconectada."; 
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