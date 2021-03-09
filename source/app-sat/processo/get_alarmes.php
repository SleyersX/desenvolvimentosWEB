<?php
    include "../config/config.php";
    unset($existe);
    
    $cod = $_POST['cod'];

    if($cod == 1){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT dias_cupons_acumulados FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["dias_cupons_acumulados"];
        }else{
            echo 0;
        }
    }
    if($cod == 2){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT n_dias_cupons_acumulados FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["n_dias_cupons_acumulados"];
        }else{
            echo 0;
        } 
    }
    if($cod == 3){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT cupons_acumulados FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["cupons_acumulados"];
        }else{
            echo 0;
        }
    }
    if($cod == 4){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT numero_cupons_acumulados FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["numero_cupons_acumulados"];
        }else{
            echo 0;
        } 
    }
    if($cod == 5){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT nivel_bateria FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["nivel_bateria"];
        }else{
            echo 0;
        }
    }
    if($cod == 6){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT vencimento_certificado FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["vencimento_certificado"];
        }else{
            echo 0;
        }
    }
    if($cod == 7){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT dias_vencimento_certificado FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["dias_vencimento_certificado"];
        }else{
            echo 0;
        } 
    }
    if($cod == 8){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT comunicacao_sefaz FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["comunicacao_sefaz"];
        }else{
            echo 0;
        }
    }
    if($cod == 9){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT dias_sem_comunicarf_sefaz FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["dias_sem_comunicarf_sefaz"];
        }else{
            echo 0;
        } 
    }
    if($cod == 10){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT variacao_relogio FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["variacao_relogio"];
        }else{
            echo 0;
        }
    }
    if($cod == 11){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT variacao_ntp FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["variacao_ntp"];
        }else{
            echo 0;
        } 
    }
    if($cod == 12){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT estado_operacao_bloqueado FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["estado_operacao_bloqueado"];
        }else{
            echo 0;
        }
    }
    if($cod == 13){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT estado_wan_desligado FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["estado_wan_desligado"];
        }else{
            echo 0;
        }
    }
    if($cod == 14){
        $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
        $query = $conexao->prepare("SELECT loja_bloqueio FROM ". DATA_CONFIG_BD["tab_alarmes"] ." WHERE id = 1");
        $query->execute();
        $fech = $query->fetchAll();
        $existe = count($fech);
        if($existe >= 1){
            echo $fech[0]["loja_bloqueio"];
        }else{
            echo 0;
        }
    }
?>