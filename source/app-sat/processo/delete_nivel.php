<?php
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");

    $token = $_SESSION['tokenLogonDashSAT'];

    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $descNivel = filter_input(INPUT_GET, 'desc-nivel', FILTER_SANITIZE_STRING);
    if(!empty($id)){
        
        $sqlExistNivel = "SELECT COUNT(nome) AS Ttotal FROM cn_dados_usuario_dashsat WHERE id = '$id'";
        $queryExistNivel = mysqli_query($conn,$sqlExistNivel);
        $rowExistNivel = mysqli_fetch_assoc($queryExistNivel);
        $countNivel = $rowExistNivel['Ttotal'];

        if($countNivel >= 1){
            $sqlDelNivel = "DELETE FROM tb_niveis_dashsat WHERE id = '$id'";
            $queryDelNivel = mysqli_query($conn, $sqlDelNivel);
            if(mysqli_affected_rows($conn)){
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Delete Nivel'; 
                $msgLog = 'Nível ['.$nivel.']:['.$descNivel.'], apagado com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('Nível apagado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('Nível apagado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            }else{
                //Grava LOG
                require_once("processa_log.php");
                $dataLog = date('Y-m-d H:i:s');
                $appCallLog = 'Delete Nivel'; 
                $msgLog = 'Nível ['.$nivel.']:['.$descNivel.'], não foi apagado com sucesso.';
                if($_SESSION['usuarioIDDashSAT'] != 0 ){
                    insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
                }
                if($_SESSION['usuarioNivelDashSAT'] == 1){
                    echo "<script>alert('Erro o nível não foi apagado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
                }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                    echo "<script>alert('Erro o nível não foi apagado com sucesso!');</script>";
                    echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
                }
            }
        }else{
            //Grava LOG
            require_once("processa_log.php");
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Delete Nivel'; 
            $msgLog = 'Nível ['.$nivel.']:['.$descNivel.'], não foi apagado com sucesso, pois existem usuários ['.$countNivel.'] associados a ele.';
            if($_SESSION['usuarioIDDashSAT'] != 0 ){
                insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
            }
            if($_SESSION['usuarioNivelDashSAT'] == 1){
                echo "<script>alert('O nível selecionado não pode ser deletado!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
            }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
                echo "<script>alert('O nível selecionado não pode ser deletado!');</script>";
                echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
            }
        }
    }else{
        //Grava LOG
        require_once("processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Delete Nivel'; 
        $msgLog = 'Nível ['.$nivel.']:['.$descNivel.'], necessário selecionar um nível.';
        if($_SESSION['usuarioIDDashSAT'] != 0 ){
            insert_log_I($_SESSION['usuarioIDDashSAT'],$_SESSION['usuarioNomeDashSAT'],$_SESSION['usuarioLoginDashSAT'],$appCallLog,$dataLog,$msgLog);
        }
        if($_SESSION['usuarioNivelDashSAT'] == 1){
            echo "<script>alert('Necessário selecionar um nível!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../superadmin/configuracao.php?token='.$token.'">';
        }elseif($_SESSION['usuarioNivelDashSAT'] == 2){
            echo "<script>alert('Necessário selecionar um nível!');</script>";
            echo '<meta HTTP-EQUIV="Refresh" CONTENT="0; URL=../admin/configuracao.php?token='.$token.'">';
        }
    }
?>