<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");

    $token = $_SESSION['tokenLogonDashSAT'];
    
    $arquivo = $_FILES["file"]["tmp_name"];
    $nome = $_FILES["file"]["name"];

    $ext = explode(".", $nome);
    $extensao = end($ext);

    if($extensao != "csv" && $extensao != null){
        //Grava LOG
        require_once("processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Import CSV Dados Fiscais Lojas'; 
        $msgLog = 'Extensão inválida, arquivo deve ser CSV ['.$extensao.'].';
        if($_SESSION['usuarioIDDashSAT'] != 0 ){
            insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
        }
        $_SESSION['msg'] = "<div class='alert alert-warning'>Extensão inválida, arquivo deve ser CSV [".$extensao."]</div>";
    }elseif($extensao == 'csv'){
        $obejto = fopen($arquivo, 'r');

        while(($dados = fgetcsv($obejto, 5000, ";")) !== FALSE){
            //CodiFisc;PersFisc;DireFisc;CodiPostFisc;LocaFisc;ProvFisc;InscEstaClie;CodiEstaClie;BarrClie;NumeDireClie;CodiIden;CodiMuniClie;Email
            $CodiFisc = $dados[0];
            $PersFisc = $dados[1];
            $DireFisc = $dados[2];
            $CodiPostFisc = $dados[3];
            $LocaFisc = $dados[4];
            $ProvFisc = $dados[5];
            $InscEstaClie = $dados[6];
            $CodiEstaClie = $dados[7];
            $BarrClie = $dados[8];
            $NumeDireClie = $dados[9];
            $CodiIden = $dados[10];
            $CodiMuniClie = $dados[11];
            $Email = $dados[12];

            if(($dados[0] != '') && ($dados[0] != 'CodiFisc') && ($dados[1] != 'PersFisc') && ($dados[2] != 'DireFisc') && ($dados[3] != 'CodiPostFisc') && ($dados[4] != 'LocaFisc') && ($dados[5] != 'ProvFisc') && ($dados[6] != 'InscEstaClie') && ($dados[7] != 'CodiEstaClie') && ($dados[8] != 'BarrClie') && ($dados[9] != 'NumeDireClie') && ($dados[10] != 'CodiIden') && ($dados[11] != 'CodiMuniClie') && ($dados[12] != 'Email')){

                $sqlExistLojaCSV = "SELECT COUNT(id) AS total_registros FROM tb_dados_fiscais_lojas WHERE CodiFisc LIKE '$CodiFisc'";
                $queryExistLojaCSV = mysqli_query($conn,$sqlExistLojaCSV);
                $rowExistLojaCSV = mysqli_fetch_assoc($queryExistLojaCSV);
                
                if($rowExistLojaCSV["total_registros"] == 0){
                    $insertCSV = "INSERT INTO tb_dados_fiscais_lojas (CodiFisc, PersFisc, DireFisc, CodiPostFisc, LocaFisc, ProvFisc, InscEstaClie, CodiEstaClie, BarrClie, NumeDireClie, CodiIden, CodiMuniClie, Email) VALUES ('$CodiFisc', '$PersFisc', '$DireFisc', '$CodiPostFisc', '$LocaFisc', '$ProvFisc', '$InscEstaClie', '$CodiEstaClie', '$BarrClie', '$NumeDireClie', '$CodiIden', '$CodiMuniClie', '$Email')";
                    $queryCSV = mysqli_query($conn,$insertCSV);
                    //Grava LOG
                    require_once("processa_import_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Import CSV Dados Fiscais Lojas'; 
                    $msgLog = 'Insert dados ['.$CodiFisc.']:['.$PersFisc.']:['.$DireFisc.']:['.$CodiPostFisc.']:['.$LocaFisc.']:['.$ProvFisc.']:['.$InscEstaClie.']:['.$CodiEstaClie.']:['.$BarrClie.']:['.$NumeDireClie.']:['.$CodiIden.']:['.$CodiMuniClie.']:['.$Email.'], realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_IV($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                }else{
                    $updateCSV = "UPDATE tb_dados_fiscais_lojas SET PersFisc = '$PersFisc', DireFisc = '$DireFisc', CodiPostFisc = '$CodiPostFisc', LocaFisc = '$LocaFisc', ProvFisc = '$ProvFisc', InscEstaClie = '$InscEstaClie', BarrClie = '$BarrClie', NumeDireClie = '$NumeDireClie', CodiIden = '$CodiIden', CodiMuniClie = '$CodiMuniClie', Email = '$Email' WHERE CodiFisc LIKE '$CodiFisc'";
                    $queryUpdCSV = mysqli_query($conn,$updateCSV);
                    //Grava LOG
                    require_once("processa_import_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Import CSV Dados Fiscais Lojas'; 
                    $msgLog = 'Update dados ['.$CodiFisc.']:['.$PersFisc.']:['.$DireFisc.']:['.$CodiPostFisc.']:['.$LocaFisc.']:['.$ProvFisc.']:['.$InscEstaClie.']:['.$CodiEstaClie.']:['.$BarrClie.']:['.$NumeDireClie.']:['.$CodiIden.']:['.$CodiMuniClie.']:['.$Email.'], realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_IV($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                }
            }
        }
        if (mysqli_insert_id($conn)) {
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Import CSV Dados Fiscais Lojas'; 
            $msgLog = 'Import dados Fiscais por CSV, realizado com sucesso.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            $_SESSION['msg'] = "<div class='alert alert-success'>Dados inseridos com sucesso!</div>";
        }elseif(mysqli_affected_rows($conn)){
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Import CSV Dados Fiscais Lojas'; 
            $msgLog = 'Import dados Fiscais por CSV, realizado com sucesso.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            $_SESSION['msg'] = "<div class='alert alert-success'>Dados atualizados com sucesso!</div>";
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Import CSV Dados Fiscais Lojas'; 
            $msgLog = 'Erro import dados Fiscais por CSV.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            $_SESSION['msg'] = "<div class='alert alert-info'>Nenhuma alteração realizada!</div>";
        }
    }


?>