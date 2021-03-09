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
        $appCallLog = 'Import CSV CR'; 
        $msgLog = 'Extensão inválida, arquivo deve ser CSV ['.$extensao.'].';
        if($_SESSION['usuarioIDDashSAT'] != 0 ){
            insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
        }
        //echo "<script>alert('Extensão inválida, arquivo deve ser CSV [".$extensao."]');</script>";
        //echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
        $_SESSION['msg'] = "<div class='alert alert-warning'>Extensão inválida, arquivo deve ser CSV [".$extensao."]</div>";
    }elseif($extensao == 'csv'){
        $obejto = fopen($arquivo, 'r');

        while(($dados = fgetcsv($obejto, 1000, ";")) !== FALSE){
            $loja = $dados[0];
            $loja = str_pad($loja, 5, "0", STR_PAD_LEFT);
            $regiao = $dados[1];
            $divisao = $dados[2];
            $centro = $dados[3];
            $uf = $dados[4];

            if(($dados[0] != '') && ($dados[0] != 'loja') && ($dados[1] != 'regiao') && ($dados[2] != 'divisao') && ($dados[3] != 'centro') && ($dados[4] != 'uf')){
                $sqlExistLojaCSV = "SELECT COUNT(id) AS total_registros FROM tb_lojas_cr WHERE loja = '$loja'";
                $queryExistLojaCSV = mysqli_query($conn,$sqlExistLojaCSV);
                $rowExistLojaCSV = mysqli_fetch_assoc($queryExistLojaCSV);
                
                if($rowExistLojaCSV["total_registros"] == 0){
                    $insertCSV = "INSERT INTO tb_lojas_cr (loja, regiao, divisao,centro, uf) VALUES ('$loja','$regiao','$divisao','$centro', '$uf')";
                    $queryCSV = mysqli_query($conn,$insertCSV);
                    //Grava LOG
                    require_once("processa_import_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Import CSV CR'; 
                    $msgLog = 'Insert dados ['.$loja.']:['.$regiao.']:['.$divisao.']:['.$centro.']:['.$uf.'], realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_IV($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                }else{
                    $updateCSV = "UPDATE tb_lojas_cr SET regiao = '$regiao', divisao = '$divisao', centro = '$centro', uf = '$uf' WHERE loja = '$loja'";
                    $queryUpdCSV = mysqli_query($conn,$updateCSV);
                    //Grava LOG
                    require_once("processa_import_log.php");
                    $dataLog = date('Y-m-d H:i:s');
                    $appCallLog = 'Import CSV CR'; 
                    $msgLog = 'Update dados ['.$loja.']:['.$regiao.']:['.$divisao.']:['.$centro.']:['.$uf.'], realizado com sucesso.';
                    if($_SESSION['usuarioIDDashSAT'] != 0 ){
                        insert_log_IV($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                    }
                }
            }
        }
        if (mysqli_insert_id($conn)) {
            //echo "<script>alert('Dados inseridos com sucesso');</script>";
            //echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Import CSV CR'; 
            $msgLog = 'Import dados CR por CSV, realizado com sucesso.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            $_SESSION['msg'] = "<div class='alert alert-success'>Dados inseridos com sucesso!</div>";
        }elseif(mysqli_affected_rows($conn)){
            //echo "<script>alert('Dados inseridos com sucesso');</script>";
            //echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Import CSV CR'; 
            $msgLog = 'Import dados CR por CSV, realizado com sucesso.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            $_SESSION['msg'] = "<div class='alert alert-success'>Dados atualizados com sucesso!</div>";
        }else{
            //echo "<script>alert('Erro durante o processo de importação');</script>";
            //echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Import CSV CR'; 
            $msgLog = 'Erro import dados CR por CSV.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            $_SESSION['msg'] = "<div class='alert alert-info'>Nenhuma alteração realizada!</div>";
        }
    }


?>