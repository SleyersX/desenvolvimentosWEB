<?php
    session_start();
    require_once("/var/www/html/source/app-sat/security/seguranca.php");
    protegePagina();
    require_once("/var/www/html/source/app-sat/security/connect.php");

    $token = $_SESSION['tokenLogonDashSAT'];
    $page= filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING);
    $sat = filter_input(INPUT_POST,'serieSAT',FILTER_SANITIZE_STRING);
    $ipSat = filter_input(INPUT_POST,'ipSAT', FILTER_SANITIZE_STRING);
    $mask = filter_input(INPUT_POST,'mask',FILTER_SANITIZE_STRING);
    $gateway = filter_input(INPUT_POST,'gateway',FILTER_SANITIZE_STRING);
    $dnsPrimario = filter_input(INPUT_POST,'dnsPrimario', FILTER_SANITIZE_STRING);
    $dnsSecundario = filter_input(INPUT_POST,'dnsSecundario', FILTER_SANITIZE_STRING);

    $pos = filter_input(INPUT_GET, 'pos', FILTER_SANITIZE_STRING);
    $shop = filter_input(INPUT_GET,'shop',FILTER_SANITIZE_STRING);

    $sqlIPShop = "SELECT ip FROM tb_ip WHERE loja = '$shop'";
    $queryIPShop = mysqli_query($conn,$sqlIPShop);
    $rowIPShop = mysqli_fetch_assoc($queryIPShop);
    $ip = $rowIPShop['ip'];

    $openXML = fopen("xml/config_rede.xml","w+");

    @fwrite($openXML,"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    @fwrite($openXML,"<config>\n");
    @fwrite($openXML,"\t<tipoInter>ETHE</tipoInter>\n");
    @fwrite($openXML,"\t<tipoLan>IPFIX</tipoLan>\n");
    @fwrite($openXML,"\t<lanIP>$ipSat</lanIP>\n");
    @fwrite($openXML,"\t<lanMask>$mask</lanMask>\n");
    @fwrite($openXML,"\t<lanGW>$gateway</lanGW>\n");
    @fwrite($openXML,"\t<lanDNS1>$dnsPrimario</lanDNS1>\n");
    @fwrite($openXML,"\t<lanDNS2>$dnsSecundario</lanDNS2>\n");
    @fwrite($openXML,"\t<proxy>0</proxy>\n");
    @fwrite($openXML,"\t<proxy_ip/>\n");
    @fwrite($openXML,"\t<proxy_user/>\n");
    @fwrite($openXML,"\t<proxy_senha/>\n");
    @fwrite($openXML,"</config>");


    $output=shell_exec("bash -x ../bash/libsat.sh config_rede $shop $ip $pos $sat");
    echo $output;
    
    //Grava LOG
    require_once("processa_log.php");
    $id = $_SESSION['usuarioIDDashSAT'];
    $userName = $_SESSION['usuarioNomeDashSAT'];
    $userLogin = $_SESSION['usuarioLoginDashSAT'];
    $dataLog = date('Y-m-d H:i:s');
    $appCallLog = 'Pagina Config Rede SAT'; 
    $msgLog = "Rede SAT [$shop]:[$pos]:[$sat]:[ETHE]:[IPFIX]:[$ipSat]:[$mask]:[$gateway]:[$dnsPrimario]:[$dnsSecundario]:[0].";
    insert_log_V($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);
    
    if($_SESSION['usuarioNivelDashSAT'] == 1){
        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
    }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/configuracao.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
    }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/configuracao.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
    }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
        echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/configuracao.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
    }

?>