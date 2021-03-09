<?php
    session_start();
    require_once("../../security/seguranca.php");
	protegePagina();
    require_once("../../security/connect.php");
    
    //unset($arrPdvs,$returnarr);

    $token = $_SESSION['tokenLogonDashSAT'];
    $page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING);
    $seletor =  filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $pos = filter_input(INPUT_GET, 'pos', FILTER_SANITIZE_STRING);
    $shop = filter_input(INPUT_GET,'shop',FILTER_SANITIZE_STRING);
    $sat = filter_input(INPUT_GET,'sat',FILTER_SANITIZE_STRING);

    $sqlIPShop = "SELECT ip FROM tb_ip WHERE loja = '$shop'";
    $queryIPShop = mysqli_query($conn,$sqlIPShop);
    $rowIPShop = mysqli_fetch_assoc($queryIPShop);
    $ip = $rowIPShop['ip'];

    switch ($seletor) {
        case '1':
            $output=shell_exec("bash -x ../bash/libsat.sh log $shop $ip $pos $sat");
            echo $output;
            //$urlDownload=$output;
            //echo $urlDownload;
            //echo "<script>window.open('$urlDownload');</script>";

            //Grava LOG
            require_once("processa_log.php");
            $id = $_SESSION['usuarioIDDashSAT'];
            $userName = $_SESSION['usuarioNomeDashSAT'];
            $userLogin = $_SESSION['usuarioLoginDashSAT'];
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Pagina LibSAT - Extrair Log'; 
            $msgLog = "Extração Log [$shop]:[$pos]:[$sat].";
            insert_log_V($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);

            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }
            
            break;
        case '2':
            $output=shell_exec("bash -x ../bash/libsat.sh update $shop $ip $pos $sat");
            echo $output;
            
            //Grava LOG
            require_once("processa_log.php");
            $id = $_SESSION['usuarioIDDashSAT'];
            $userName = $_SESSION['usuarioNomeDashSAT'];
            $userLogin = $_SESSION['usuarioLoginDashSAT'];
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Pagina LibSAT - Update SAT'; 
            $msgLog = "Update firmware [$shop]:[$pos]:[$sat].";
            insert_log_V($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);

            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }
        break;
        case '3':
            $output=shell_exec("bash -x ../bash/libsat.sh resync $shop $ip $pos $sat");
            echo $output;

            //Grava LOG
            require_once("processa_log.php");
            $id = $_SESSION['usuarioIDDashSAT'];
            $userName = $_SESSION['usuarioNomeDashSAT'];
            $userLogin = $_SESSION['usuarioLoginDashSAT'];
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Pagina LibSAT - Sincronizar'; 
            $msgLog = "Sincronizar dados [$shop]:[$pos]:[$sat].";
            insert_log_V($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);

            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }
        break;
        case '4':
            $output=shell_exec("bash -x ../bash/libsat.sh teste_sefaz $shop $ip $pos $sat");
            echo $output;

            //Grava LOG
            require_once("processa_log.php");
            $id = $_SESSION['usuarioIDDashSAT'];
            $userName = $_SESSION['usuarioNomeDashSAT'];
            $userLogin = $_SESSION['usuarioLoginDashSAT'];
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Pagina LibSAT - Teste SEFAZ'; 
            $msgLog = "Teste comunicação SEFAZ [$shop]:[$pos]:[$sat].";
            insert_log_V($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);

            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }
        break;
        default:
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/getsat.php?token='.$token.'&idsat='.$sat.'&page='.$page.'">';
            }
        break;
    }
?>