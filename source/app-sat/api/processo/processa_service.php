<?php 
    session_start();
    require_once("../../security/seguranca.php");
	protegePagina();
    require_once("../../security/connect.php");
    
    //unset($arrPdvs,$returnarr);

    $token = $_SESSION['tokenLogonDashSAT'];
    $page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING);
    $seletor =  filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $pdvs = filter_input(INPUT_GET, 'num-pdvs', FILTER_SANITIZE_STRING);
    $shop = filter_input(INPUT_POST,'num-shop',FILTER_SANITIZE_STRING);
    
    $returnarr=array();                
    $arrPdvs = array();

    if($seletor == 1){
        if(isset($_POST['status-pdv-1'])){
            array_push($arrPdvs,$_POST['status-pdv-1']);
        }
        if(isset($_POST['status-pdv-2'])){
            array_push($arrPdvs,$_POST['status-pdv-2']);
        }
    
        if(isset($_POST['status-pdv-3'])){
            array_push($arrPdvs,$_POST['status-pdv-3']);
        }
    
        if(isset($_POST['status-pdv-4'])){
            array_push($arrPdvs,$_POST['status-pdv-4']);
        }
    
        if(isset($_POST['status-pdv-5'])){
            array_push($arrPdvs,$_POST['status-pdv-5']);
        }
    
        if(isset($_POST['status-pdv-6'])){
            array_push($arrPdvs,$_POST['status-pdv-6']);
        }
    
        if(isset($_POST['status-pdv-7'])){
            array_push($arrPdvs,$_POST['status-pdv-7']);
        }
    
        if(isset($_POST['status-pdv-8'])){
            array_push($arrPdvs,$_POST['status-pdv-8']);
        }
    
        if(isset($_POST['status-pdv-9'])){
            array_push($arrPdvs,$_POST['status-pdv-9']);
        }
    }elseif($seletor == 2){
        if(isset($_POST['start-pdv-1'])){
            array_push($arrPdvs,$_POST['start-pdv-1']);
        }
        if(isset($_POST['start-pdv-2'])){
            array_push($arrPdvs,$_POST['start-pdv-2']);
        }
    
        if(isset($_POST['start-pdv-3'])){
            array_push($arrPdvs,$_POST['start-pdv-3']);
        }
    
        if(isset($_POST['start-pdv-4'])){
            array_push($arrPdvs,$_POST['start-pdv-4']);
        }
    
        if(isset($_POST['start-pdv-5'])){
            array_push($arrPdvs,$_POST['start-pdv-5']);
        }
    
        if(isset($_POST['start-pdv-6'])){
            array_push($arrPdvs,$_POST['start-pdv-6']);
        }
    
        if(isset($_POST['start-pdv-7'])){
            array_push($arrPdvs,$_POST['start-pdv-7']);
        }
    
        if(isset($_POST['start-pdv-8'])){
            array_push($arrPdvs,$_POST['start-pdv-8']);
        }
    
        if(isset($_POST['start-pdv-9'])){
            array_push($arrPdvs,$_POST['start-pdv-9']);
        }
    }elseif($seletor == 3){
        if(isset($_POST['restart-pdv-1'])){
            array_push($arrPdvs,$_POST['restart-pdv-1']);
        }
        if(isset($_POST['restart-pdv-2'])){
            array_push($arrPdvs,$_POST['restart-pdv-2']);
        }
    
        if(isset($_POST['restart-pdv-3'])){
            array_push($arrPdvs,$_POST['restart-pdv-3']);
        }
    
        if(isset($_POST['restart-pdv-4'])){
            array_push($arrPdvs,$_POST['restart-pdv-4']);
        }
    
        if(isset($_POST['restart-pdv-5'])){
            array_push($arrPdvs,$_POST['restart-pdv-5']);
        }
    
        if(isset($_POST['restart-pdv-6'])){
            array_push($arrPdvs,$_POST['restart-pdv-6']);
        }
    
        if(isset($_POST['restart-pdv-7'])){
            array_push($arrPdvs,$_POST['restart-pdv-7']);
        }
    
        if(isset($_POST['restart-pdv-8'])){
            array_push($arrPdvs,$_POST['restart-pdv-8']);
        }
    
        if(isset($_POST['restart-pdv-9'])){
            array_push($arrPdvs,$_POST['restart-pdv-9']);
        }
    }elseif($seletor == 4){
        if(isset($_POST['stop-pdv-1'])){
            array_push($arrPdvs,$_POST['stop-pdv-1']);
        }
        if(isset($_POST['stop-pdv-2'])){
            array_push($arrPdvs,$_POST['stop-pdv-2']);
        }
    
        if(isset($_POST['stop-pdv-3'])){
            array_push($arrPdvs,$_POST['stop-pdv-3']);
        }
    
        if(isset($_POST['stop-pdv-4'])){
            array_push($arrPdvs,$_POST['stop-pdv-4']);
        }
    
        if(isset($_POST['stop-pdv-5'])){
            array_push($arrPdvs,$_POST['stop-pdv-5']);
        }
    
        if(isset($_POST['stop-pdv-6'])){
            array_push($arrPdvs,$_POST['stop-pdv-6']);
        }
    
        if(isset($_POST['stop-pdv-7'])){
            array_push($arrPdvs,$_POST['stop-pdv-7']);
        }
    
        if(isset($_POST['stop-pdv-8'])){
            array_push($arrPdvs,$_POST['stop-pdv-8']);
        }
    
        if(isset($_POST['stop-pdv-9'])){
            array_push($arrPdvs,$_POST['stop-pdv-9']);
        }
    }elseif($seletor == 5){
        if(isset($_POST['remove-pdv-1'])){
            array_push($arrPdvs,$_POST['remove-pdv-1']);
        }
        if(isset($_POST['remove-pdv-2'])){
            array_push($arrPdvs,$_POST['remove-pdv-2']);
        }
    
        if(isset($_POST['remove-pdv-3'])){
            array_push($arrPdvs,$_POST['remove-pdv-3']);
        }
    
        if(isset($_POST['remove-pdv-4'])){
            array_push($arrPdvs,$_POST['remove-pdv-4']);
        }
    
        if(isset($_POST['remove-pdv-5'])){
            array_push($arrPdvs,$_POST['remove-pdv-5']);
        }
    
        if(isset($_POST['remove-pdv-6'])){
            array_push($arrPdvs,$_POST['remove-pdv-6']);
        }
    
        if(isset($_POST['remove-pdv-7'])){
            array_push($arrPdvs,$_POST['remove-pdv-7']);
        }
    
        if(isset($_POST['remove-pdv-8'])){
            array_push($arrPdvs,$_POST['remove-pdv-8']);
        }
    
        if(isset($_POST['remove-pdv-9'])){
            array_push($arrPdvs,$_POST['remove-pdv-9']);
        }
    }
    

    $sqlIPShop = "SELECT ip FROM tb_ip WHERE loja = '$shop'";
    $queryIPShop = mysqli_query($conn,$sqlIPShop);
    $rowIPShop = mysqli_fetch_assoc($queryIPShop);
    $ip = $rowIPShop['ip'];

    switch ($seletor) {
        case '1':
            foreach($arrPdvs as $key =>$value){
                $output=shell_exec("bash ../bash/service.sh status $shop $ip {$value}");
                echo $output;   
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/configuracao.php?token='.$token.'">';
            }
            break;
        case '2':
            foreach($arrPdvs as $key =>$value){
                $output=shell_exec("bash ../bash/service.sh start $shop $ip {$value}");
                echo $output;   
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/configuracao.php?token='.$token.'">';
            }
            break;
        case '3':
            foreach($arrPdvs as $key =>$value){
                $output=shell_exec("bash ../bash/service.sh restart $shop $ip {$value}");
                echo $output;   
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/configuracao.php?token='.$token.'">';
            }
            break;
        case '4':
            foreach($arrPdvs as $key =>$value){
                $output=shell_exec("bash ../bash/service.sh stop $shop $ip {$value}");
                echo $output;   
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/configuracao.php?token='.$token.'">';
            }
            break;
        case '5':
            foreach($arrPdvs as $key =>$value){
                $output=shell_exec("bash ../bash/service.sh remove $shop $ip {$value}");
                echo $output;   
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/configuracao.php?token='.$token.'">';
            }
            break;
        default:
            echo '<div class="form-group">';
            echo '<label for="recipient-name" class="control-label">Status: '.$output.'</label>';
            echo '</div>';
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../admin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 3){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../gestao/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 5){
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../../suporte/configuracao.php?token='.$token.'">';
            }
            break;
    }


?>