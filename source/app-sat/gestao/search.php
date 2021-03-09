<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");
    
    if(!empty($_SESSION['usuarioIDDashSAT']) && $_SESSION['usuarioNivelDashSAT'] != 3 ){
        //Grava LOG
        require_once("../processo/processa_log.php");
        $id = $_SESSION['usuarioIDDashSAT'];
        $userName = $_SESSION['usuarioNomeDashSAT'];
        $userLogin = $_SESSION['usuarioLoginDashSAT'];
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Pagina ' . $_SERVER["SCRIPT_NAME"]; 
        $msgLog = 'Usuário logado não tem permissão para acesso a esta pagina, será direcionado a pagina de login.';
        insert_log_I($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);
        expulsaVisitante();
    }elseif(empty($_SESSION['usuarioIDDashSAT'])){
        //Grava LOG
        require_once("../processo/processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Pagina ' . $_SERVER["SCRIPT_NAME"]; 
        $msgLog = 'Usuário logado não tem permissão para acesso a esta pagina, será direcionado a pagina de login.';
        insert_log_I('99','System','root',$appCallLog,$dataLog,$msgLog);
        expulsaVisitante();
    }

    $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
    if(!empty($token) && !empty($_SESSION['usuarioIDDashSAT'])){
        if($_SESSION['tokenLogonDashSAT'] != $token){
            //Grava LOG
            require_once("../processo/processa_log.php");
            $id = $_SESSION['usuarioIDDashSAT'];
            $userName = $_SESSION['usuarioNomeDashSAT'];
            $userLogin = $_SESSION['usuarioLoginDashSAT'];
            $dataLog = date('Y-m-d H:i:s');
            $appCallLog = 'Pagina ' . $_SERVER["SCRIPT_NAME"]; 
            $msgLog = 'Token inválido ou expirado ['.$token.'].';
            insert_log_I($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);
            expulsaVisitante();
        }
    }elseif(empty($token) && !empty($_SESSION['usuarioIDDashSAT'])){
        //Grava LOG
        require_once("../processo/processa_log.php");
        $id = $_SESSION['usuarioIDDashSAT'];
        $userName = $_SESSION['usuarioNomeDashSAT'];
        $userLogin = $_SESSION['usuarioLoginDashSAT'];
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Pagina ' . $_SERVER["SCRIPT_NAME"]; 
        $msgLog = 'Token não informado ['.$token.'].';
        insert_log_I($id,$userName,$userLogin,$appCallLog,$dataLog,$msgLog);
        expulsaVisitante();
    }elseif(empty($token) && empty($_SESSION['usuarioIDDashSAT'])){
        //Grava LOG
        require_once("../processo/processa_log.php");
        $dataLog = date('Y-m-d H:i:s');
        $appCallLog = 'Pagina ' . $_SERVER["SCRIPT_NAME"]; 
        $msgLog = 'Token inválido ou expirado ['.$token.'].';
        insert_log_I('99','System','root',$appCallLog,$dataLog,$msgLog);
        expulsaVisitante();
    }
    
    $id = $_SESSION['usuarioIDDashSAT'];
    $userName = $_SESSION['usuarioNomeDashSAT'];
    $userLogin = $_SESSION['usuarioLoginDashSAT'];
	$result_usuario = "SELECT * FROM tb_usuarios_dashsat WHERE id = '$id'";
	$resultado_usuario = mysqli_query($conn, $result_usuario);
    $row_usuario = mysqli_fetch_assoc($resultado_usuario);
    $avatar = $_SESSION['usuarioAvatarDashSAT'];

    include "../config/config.php";
    
    $sqlGroupModeloSat = "SELECT modelo_sat, count(id) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE status = 'Ativo' AND data_atualizacao BETWEEN DATE_ADD(NOW(), INTERVAL -7 DAY) AND NOW() GROUP BY modelo_sat";
    $queryGroupModeloSat = mysqli_query($conn,$sqlGroupModeloSat);
    while($resultGroupModeloSat = mysqli_fetch_assoc($queryGroupModeloSat)){
        //echo "Modelo:" . $resultGroupModeloSat['modelo_sat'] . "Qntd:" . $resultGroupModeloSat['qntd'] ."";
        $objGroupSat[] = (object) $resultGroupModeloSat;
    }
    //print_r($obj);
    $totalGroupSat=0;
    foreach($objGroupSat as $key=>$val){
        //print "#{$key} " . $obj[$key]->qntd . "<br/>";
        $totalGroupSat = $objGroupSat[$key]->qntd + $totalGroupSat;
    }
    /*print "Total" . $totalGroupSat;
    print "<br/>";
    foreach($objGroupSat as $key=>$val){
        print round((($objGroupSat[$key]->qntd/$totalGroupSat)*100),3) . "<br/>";
    }
    */
    
    unset($_SESSION['tempNome']);
	unset($_SESSION['tempEmail']);
    unset($_SESSION['tempLogin']);
    
    if(empty($_POST['consulta'])){
        if(isset($_GET['search'])){
            if ((strlen($_GET['search']) <= 5) && ($_GET['search'] <= 9999)) {
                $sqlSerch = "SELECT * FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = ". str_pad($_GET['search'], 5, "0", STR_PAD_LEFT) ."";
                $consulta = str_pad($_GET['search'], 5, "0", STR_PAD_LEFT);
                
                $shop = str_pad($_GET['search'], 5, "0", STR_PAD_LEFT);
    
                $sqlCountSAT1 = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE 'SAT-1.0' AND loja = '$shop'";
                $queryCountSAT1 = mysqli_query($conn,$sqlCountSAT1);
                $rowCountSAT1 = mysqli_fetch_assoc($queryCountSAT1);
                
                $sqlCountSAT2 = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE 'SAT-2.0' AND loja = '$shop'";
                $queryCountSAT2 = mysqli_query($conn,$sqlCountSAT2);
                $rowCountSAT2 = mysqli_fetch_assoc($queryCountSAT2);
    
                $sqlGroupFirmware = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' GROUP BY firmware";
                $queryGroupFirmware = mysqli_query($conn,$sqlGroupFirmware);
    
                $sqlGroupFirmware1 = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' GROUP BY firmware";
                $queryGroupFirmware1 = mysqli_query($conn,$sqlGroupFirmware1);
    
                $sqlGroupLayout1 = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' GROUP BY layout";
                $queryGroupLayout1 = mysqli_query($conn,$sqlGroupLayout1);
    
                $sqlGroupLayout = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' GROUP BY layout";
                $queryGroupLayout = mysqli_query($conn,$sqlGroupLayout);
                
                $sqlGroupCFes = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop'" ;
                $queryGroupCFes = mysqli_query($conn,$sqlGroupCFes);
    
                $sqlGroupCFesI = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop'" ;
                $queryGroupCFesI = mysqli_query($conn,$sqlGroupCFesI);
    
                $sqlGroupCFesII = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop'" ;
                $queryGroupCFesII = mysqli_query($conn,$sqlGroupCFesII);
    
                $sqlGroupCFesIII = "SELECT MAX(numero_cfes_emitidos) AS maior FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop'";
                $queryGroupCFesIII = mysqli_query($conn,$sqlGroupCFesIII);
                $rowGroupCFesIII = mysqli_fetch_assoc($queryGroupCFesIII);
    
                $sqlGroupYears = "SELECT sat, data_ativacao, data_fim_ativacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' ORDER BY data_ativacao ASC" ;
                $queryGroupYears = mysqli_query($conn,$sqlGroupYears);
    
                $sqlGroupDadosGerais = "SELECT sat, nivel_bateria, disco_usado, data_hora_atual, data_hora_transm_sefaz, data_hora_comun_sefaz, descricao_bloqueio, data_atualizacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' " ;
                $queryGroupDadosGerais = mysqli_query($conn,$sqlGroupDadosGerais);
    
            } else {
                $sqlSerch = "SELECT * FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = ". str_pad($_GET['search'], 9, "0", STR_PAD_LEFT) ."";
                $consulta = str_pad($_GET['search'], 9, "0", STR_PAD_LEFT);
    
                //$sqlShop = "SELECT loja * FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = ". str_pad($_POST['consulta'], 9, "0", STR_PAD_LEFT) ."";
               //$queryShop = mysqli_query($conn,$sqlShop);
                //$rowShop = mysqli_fetch_assoc($queryShop);
    
                $sat = str_pad($_GET['search'], 9, "0", STR_PAD_LEFT);
    
                $sqlCountSAT1 = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE 'SAT-1.0' AND sat = '$sat'";
                $queryCountSAT1 = mysqli_query($conn,$sqlCountSAT1);
                $rowCountSAT1 = mysqli_fetch_assoc($queryCountSAT1);
                
                $sqlCountSAT2 = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE 'SAT-2.0' AND sat = '$sat'";
                $queryCountSAT2 = mysqli_query($conn,$sqlCountSAT2);
                $rowCountSAT2 = mysqli_fetch_assoc($queryCountSAT2);
    
                $sqlGroupFirmware = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' GROUP BY firmware";
                $queryGroupFirmware = mysqli_query($conn,$sqlGroupFirmware);
    
                $sqlGroupFirmware1 = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' GROUP BY firmware";
                $queryGroupFirmware1 = mysqli_query($conn,$sqlGroupFirmware1);
    
                $sqlGroupLayout1 = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' GROUP BY layout";
                $queryGroupLayout1 = mysqli_query($conn,$sqlGroupLayout1);
    
                $sqlGroupLayout = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' GROUP BY layout";
                $queryGroupLayout = mysqli_query($conn,$sqlGroupLayout);
                
                $sqlGroupCFes = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat'" ;
                $queryGroupCFes = mysqli_query($conn,$sqlGroupCFes);
    
                $sqlGroupCFesI = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat'" ;
                $queryGroupCFesI = mysqli_query($conn,$sqlGroupCFesI);
    
                $sqlGroupCFesII = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat'" ;
                $queryGroupCFesII = mysqli_query($conn,$sqlGroupCFesII);
    
                $sqlGroupCFesIII = "SELECT MAX(numero_cfes_emitidos) AS maior FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat'";
                $queryGroupCFesIII = mysqli_query($conn,$sqlGroupCFesIII);
                $rowGroupCFesIII = mysqli_fetch_assoc($queryGroupCFesIII);
    
                $sqlGroupYears = "SELECT sat, data_ativacao, data_fim_ativacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' ORDER BY data_ativacao ASC" ;
                $queryGroupYears = mysqli_query($conn,$sqlGroupYears);
    
                $sqlGroupDadosGerais = "SELECT sat, nivel_bateria, disco_usado, data_hora_atual, data_hora_transm_sefaz, data_hora_comun_sefaz, descricao_bloqueio, data_atualizacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' " ;
                $queryGroupDadosGerais = mysqli_query($conn,$sqlGroupDadosGerais);
            }
            $querySearch = mysqli_query($conn, $sqlSerch);
        }
    }else{
        if ((strlen($_POST['consulta']) <= 5) && ($_POST['consulta'] <= 9999)) {
            $sqlSerch = "SELECT * FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = ". str_pad($_POST['consulta'], 5, "0", STR_PAD_LEFT) ."";
            $consulta = str_pad($_POST['consulta'], 5, "0", STR_PAD_LEFT);
            
            $shop = str_pad($_POST['consulta'], 5, "0", STR_PAD_LEFT);

            $sqlCountSAT1 = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE 'SAT-1.0' AND loja = '$shop'";
            $queryCountSAT1 = mysqli_query($conn,$sqlCountSAT1);
            $rowCountSAT1 = mysqli_fetch_assoc($queryCountSAT1);
            
            $sqlCountSAT2 = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE 'SAT-2.0' AND loja = '$shop'";
            $queryCountSAT2 = mysqli_query($conn,$sqlCountSAT2);
            $rowCountSAT2 = mysqli_fetch_assoc($queryCountSAT2);

            $sqlGroupFirmware = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' GROUP BY firmware";
            $queryGroupFirmware = mysqli_query($conn,$sqlGroupFirmware);

            $sqlGroupFirmware1 = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' GROUP BY firmware";
            $queryGroupFirmware1 = mysqli_query($conn,$sqlGroupFirmware1);

            $sqlGroupLayout1 = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' GROUP BY layout";
            $queryGroupLayout1 = mysqli_query($conn,$sqlGroupLayout1);

            $sqlGroupLayout = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' GROUP BY layout";
            $queryGroupLayout = mysqli_query($conn,$sqlGroupLayout);
            
            $sqlGroupCFes = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop'" ;
            $queryGroupCFes = mysqli_query($conn,$sqlGroupCFes);

            $sqlGroupCFesI = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop'" ;
            $queryGroupCFesI = mysqli_query($conn,$sqlGroupCFesI);

            $sqlGroupCFesII = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop'" ;
            $queryGroupCFesII = mysqli_query($conn,$sqlGroupCFesII);

            $sqlGroupCFesIII = "SELECT MAX(numero_cfes_emitidos) AS maior FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop'";
            $queryGroupCFesIII = mysqli_query($conn,$sqlGroupCFesIII);
            $rowGroupCFesIII = mysqli_fetch_assoc($queryGroupCFesIII);

            $sqlGroupYears = "SELECT sat, data_ativacao, data_fim_ativacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' ORDER BY data_ativacao ASC" ;
            $queryGroupYears = mysqli_query($conn,$sqlGroupYears);

            $sqlGroupDadosGerais = "SELECT sat, nivel_bateria, disco_usado, data_hora_atual, data_hora_transm_sefaz, data_hora_comun_sefaz, descricao_bloqueio, data_atualizacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE loja = '$shop' " ;
            $queryGroupDadosGerais = mysqli_query($conn,$sqlGroupDadosGerais);

        } else {
            $sqlSerch = "SELECT * FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = ". str_pad($_POST['consulta'], 9, "0", STR_PAD_LEFT) ."";
            $consulta = str_pad($_POST['consulta'], 9, "0", STR_PAD_LEFT);

            //$sqlShop = "SELECT loja * FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = ". str_pad($_POST['consulta'], 9, "0", STR_PAD_LEFT) ."";
           //$queryShop = mysqli_query($conn,$sqlShop);
            //$rowShop = mysqli_fetch_assoc($queryShop);

            $sat = str_pad($_POST['consulta'], 9, "0", STR_PAD_LEFT);

            $sqlCountSAT1 = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE 'SAT-1.0' AND sat = '$sat'";
            $queryCountSAT1 = mysqli_query($conn,$sqlCountSAT1);
            $rowCountSAT1 = mysqli_fetch_assoc($queryCountSAT1);
            
            $sqlCountSAT2 = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE 'SAT-2.0' AND sat = '$sat'";
            $queryCountSAT2 = mysqli_query($conn,$sqlCountSAT2);
            $rowCountSAT2 = mysqli_fetch_assoc($queryCountSAT2);

            $sqlGroupFirmware = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' GROUP BY firmware";
            $queryGroupFirmware = mysqli_query($conn,$sqlGroupFirmware);

            $sqlGroupFirmware1 = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' GROUP BY firmware";
            $queryGroupFirmware1 = mysqli_query($conn,$sqlGroupFirmware1);

            $sqlGroupLayout1 = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' GROUP BY layout";
            $queryGroupLayout1 = mysqli_query($conn,$sqlGroupLayout1);

            $sqlGroupLayout = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' GROUP BY layout";
            $queryGroupLayout = mysqli_query($conn,$sqlGroupLayout);
            
            $sqlGroupCFes = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat'" ;
            $queryGroupCFes = mysqli_query($conn,$sqlGroupCFes);

            $sqlGroupCFesI = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat'" ;
            $queryGroupCFesI = mysqli_query($conn,$sqlGroupCFesI);

            $sqlGroupCFesII = "SELECT sat, numero_cfes_emitidos, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat'" ;
            $queryGroupCFesII = mysqli_query($conn,$sqlGroupCFesII);

            $sqlGroupCFesIII = "SELECT MAX(numero_cfes_emitidos) AS maior FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat'";
            $queryGroupCFesIII = mysqli_query($conn,$sqlGroupCFesIII);
            $rowGroupCFesIII = mysqli_fetch_assoc($queryGroupCFesIII);

            $sqlGroupYears = "SELECT sat, data_ativacao, data_fim_ativacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' ORDER BY data_ativacao ASC" ;
            $queryGroupYears = mysqli_query($conn,$sqlGroupYears);

            $sqlGroupDadosGerais = "SELECT sat, nivel_bateria, disco_usado, data_hora_atual, data_hora_transm_sefaz, data_hora_comun_sefaz, descricao_bloqueio, data_atualizacao FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$sat' " ;
            $queryGroupDadosGerais = mysqli_query($conn,$sqlGroupDadosGerais);
        }
        $querySearch = mysqli_query($conn, $sqlSerch);
    }

    //Conexão ao banco de dados, exclusiva para monitorar inatividade do usuário
    $idLoginTemp = $_SESSION['idLoginTempDashSAT'];
    $conexao = new PDO('mysql:host=localhost;dbname=srvremoto',"root","diabrasil");
    $usuarioLogado = $conexao->prepare("SELECT * FROM tb_sessoes_login_dashsat WHERE id = '$idLoginTemp'");
    $usuarioLogado->execute();
    $fech = $usuarioLogado->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>App SAT | Tracking </title>
        <link rel="icon" href="../favicon.ico" />
        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
        <!-- DataTables -->
        <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="../dist/css/adminlte.min.css">
        <link rel="stylesheet" href="../dist/css/style.css">
        <!-- Google Font: Source Sans Pro -->
        <link href="../plugins/fonts-google/fontgoogle.css" rel="stylesheet">
    </head>
    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed" id="limpaTempo">
    <?php
            date_default_timezone_set("America/Sao_Paulo");
            setlocale(LC_ALL, 'pt_BR');
            $num_users = "SELECT count(id) AS cadastrado FROM tb_usuarios_dashsat";
            //Obter a data atual
            $resultado_qnt_cadastros = mysqli_query($conn, $num_users);
            $row_qnt_cadastros = mysqli_fetch_assoc($resultado_qnt_cadastros);
            
            $data['atual'] = date('Y-m-d H:i:s'); 

            //Diminuir 20 segundos 
            $data['online'] = strtotime($data['atual'] . " - 20 seconds");
            $data['online'] = date("Y-m-d H:i:s",$data['online']);
            
            //Pesquisar os ultimos usuarios online nos 20 segundo
            $result_qnt_visitas = "SELECT count(id) AS online FROM tb_visitas_dahshsat WHERE data_final >= '" . $data['online'] . "'";
            
            $resultado_qnt_visitas = mysqli_query($conn, $result_qnt_visitas);
            $row_qnt_visitas = mysqli_fetch_assoc($resultado_qnt_visitas);
            
            $qnt_offline = ($row_qnt_cadastros['cadastrado'] - $row_qnt_visitas['online']);
            $qnt_perc = round((($row_qnt_visitas['online'] / $row_qnt_cadastros['cadastrado'])*100),2);

        ?>   
        <script type="text/javascript">
            //Executar a cada 10 segundos, para atualizar a qunatidade de usuários online
            setInterval(function(){
            //Incluir e enviar o POST para o arquivo responsável em fazer contagem
                $.post("../processo/processa_vis.php", {contar: '',}, function(data){
                    $('#online').text(data);
                });
            }, 10000);
        </script>
        <div class="wrapper">
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="home.php?token=<?php echo $token;?>" class="nav-link">
                            Home
                        </a>
                    </li>
                </ul>
                <!-- Right navbar links -->
                <?php
                    require_once("../notificacao/alertas.php");
                ?>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle dropdown-hover" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-alt"></i>
                            <span class="text">
                                Account
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editaDadosPessoais">
                                <i class="far fa-id-card"></i> Pefil</a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editaSenha">
                            <i class="fas fa-lock"></i> Alterar senha</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="../security/sair.php">
                                <i class="fas fa-sign-out-alt"></i> Log Out</a>
                        </div>
                    </li>
                </ul>   
            </nav>
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <a href="home.php?token=<?php echo $token;?>" class="brand-link">
                    <img src="../dist/img/logo-dia.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                    <span class="brand-text font-weight-light">Admin SAT</span>
                </a>
                <div class="sidebar">
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                        <?php
                            echo  "<img src='$avatar' class='img-circle elevation-2' alt='User Image'>";
                        ?>
                        </div>
                        <div class="info">
                            <a href="#" class="d-block"><?php print $_SESSION['usuarioNomeDashSAT']?></a>
                        </div>
                    </div>
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <li class="nav-item has-treeview">
                                <a href="search.php?token=<?php echo $token;?>" class="nav-link active">
                                    <i class="fas fa-search nav-icon"></i>
                                    <p>Search</p>
                                </a>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="estrutura.php?token=<?php echo $token;?>" class="nav-link">
                                    <i class="fas fa-code-branch nav-icon"></i>
                                    <p>Estrutura</p>
                                </a>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>
                                        Dashboard
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="dashboard.php?token=<?php echo $token;?>" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Dashboard S&#64;T</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="tables/table.php?token=<?php echo $token;?>" class="nav-link">
                                    <i class="nav-icon fas fa-table"></i>
                                    <p>
                                        Tables
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="tables/table.php?token=<?php echo $token;?>" class="nav-link">
                                            <i class="fas fa-server nav-icon"></i>
                                            <p>Todos os S&#64;Ts</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tables/tb-sats-inativos.php?token=<?php echo $token;?>" class="nav-link">
                                            <i class="fas fa-wifi-slash nav-icon"></i>
                                            <p>S&#64;Ts Inativos</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tables/tb-comun-sefaz.php?token=<?php echo $token;?>" class="nav-link">
                                            <i class="fas fa-satellite-dish nav-icon"></i>
                                            <p>Comunicação SEFAZ</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tables/tb-xml-presos.php?token=<?php echo $token;?>" class="nav-link">
                                            <i class="fas fa-hdd nav-icon"></i>
                                            <p>XMLs Pendentes</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tables/tb-erro-config.php?token=<?php echo $token;?>" class="nav-link">
                                            <i class="fas fa-cogs nav-icon"></i>
                                            <p>Erros de Configurações</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="tables/tb-sats-falha.php?token=<?php echo $token;?>" class="nav-link">
                                            <i class="fas fa-exclamation-circle nav-icon"></i>
                                            <p>Incidencias</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="configuracao.php?token=<?php echo $token;?>" class="nav-link">
                                    <i class="fas fa-cogs nav-icon"></i>
                                    <p>Configurações</p>
                                </a>
                            </li>
                        </ul> 
                    </nav>
                </div>    
            </aside>
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Search</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="home.php?token=<?php echo $token;?>">Home</a></li>
                                    <li class="breadcrumb-item active">Search</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 d-flex d-flex-row justify-content-end">
                                <form class="form-inline" action="" method="POST">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search" name="consulta" id="consulta" value="<?php if(!empty($_POST['consulta'])){print $consulta;}elseif(empty($_POST['consulta']) && !empty($_GET['search'])){print $consulta;} ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-navbar" type="submit">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-sm-4">
                            </div>
                            <div class="col-sm-4">
                                <div class="load" style="display: none;" id="div-loading"><img src="../dist/img/loading.gif" width="48" height="48"></div>
                            </div>
                            <div class="col-sm-4">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="row">
                                        <div style="display: none;" id="div-num-loja">
                                        <b style="font-size:20px">Dados consulta: <span id="span-num-loja" style="font-size:20px"></span></b>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="row">
                                        <div class="col-sm-6" style="display: none;" id="div-n-loja">
                                            <b style="font-size:20px">Loja <span id="span-n-loja" style="font-size:20px"></span></b>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="row">
                                        <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_1">
                                            <img src="" class="col-sm-4 img-fluid" id="img_pdv_1">
                                            <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 1</b></div>
                                            <div style="position: absolute;bottom: 35px;left:115px;font-size:13px">SAT</div>
                                            <div class="text-info" style="position: absolute;bottom: 20px;left:115px;font-size:15px"><a id="link_pdv_1"><span id="span_pdv_1"></span></a></div>
                                        </div>
                                        <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_2">
                                            <img src="" class="col-sm-4 img-fluid" id="img_pdv_2">
                                            <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 2</b></div>
                                            <div style="position: absolute;bottom: 35px;left:115px;font-size:13px">SAT</div>
                                            <div class="text-info" style="position: absolute;bottom: 20px;left:115px;font-size:15px"><a id="link_pdv_2"><span id="span_pdv_2"></span></a></div>
                                        </div>
                                        <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_3">
                                            <img src="" class="col-sm-4 img-fluid" id="img_pdv_3">
                                            <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 3</b></div>
                                            <div style="position: absolute;bottom: 35px;left:115px;font-size:13px">SAT</div>
                                            <div class="text-info" style="position: absolute;bottom: 20px;left:115px;font-size:15px"><a id="link_pdv_3"><span id="span_pdv_3"></span></a></div>
                                        </div>
                                        <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_4">
                                            <img src="" class="col-sm-4 img-fluid" id="img_pdv_4">
                                            <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 4</b></div>
                                            <div style="position: absolute;bottom: 35px;left:115px;font-size:13px">SAT</div>
                                            <div class="text-info" style="position: absolute;bottom: 20px;left:115px;font-size:15px"><a id="link_pdv_4"><span id="span_pdv_4"></span></a></div>
                                        </div>
                                        <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_5">
                                            <img src="" class="col-sm-4 img-fluid" id="img_pdv_5">
                                            <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 5</b></div>
                                            <div style="position: absolute;bottom: 35px;left:115px;font-size:13px">SAT</div>
                                            <div class="text-info" style="position: absolute;bottom: 20px;left:115px;font-size:15px"><a id="link_pdv_5"><span id="span_pdv_5"></span></a></div>
                                        </div>
                                        <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_6">
                                            <img src="" class="col-sm-4 img-fluid" id="img_pdv_6">
                                            <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 6</b></div>
                                            <div style="position: absolute;bottom: 35px;left:115px;font-size:13px">SAT</div>
                                            <div class="text-info" style="position: absolute;bottom: 20px;left:115px;font-size:15px"><a id="link_pdv_6"><span id="span_pdv_6"></span></a></div>
                                        </div>
                                        <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_7">
                                            <img src="" class="col-sm-4 img-fluid" id="img_pdv_7">
                                            <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 7</b></div>
                                            <div style="position: absolute;bottom: 35px;left:115px;font-size:13px">SAT</div>
                                            <div class="text-info" style="position: absolute;bottom: 20px;left:115px;font-size:15px"><a id="link_pdv_7"><span id="span_pdv_7"></span></a></div>
                                        </div>
                                        <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_8">
                                            <img src="" class="col-sm-4 img-fluid" id="img_pdv_8">
                                            <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 8</b></div>
                                            <div style="position: absolute;bottom: 35px;left:115px;font-size:13px">SAT</div>
                                            <div class="text-info" style="position: absolute;bottom: 20px;left:115px;font-size:15px"><a id="link_pdv_8"><span id="span_pdv_8"></span></a></div>
                                        </div>
                                        <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_9">
                                            <img src="" class="col-sm-4 img-fluid" id="img_pdv_9">
                                            <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 9</b></div>
                                            <div style="position: absolute;bottom: 35px;left:115px;font-size:13px">SAT</div>
                                            <div class="text-info" style="position: absolute;bottom: 20px;left:115px;font-size:15px"><a id="link_pdv_9"><span id="span_pdv_9"></span></a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content">
                        <div class="container-fluid">
                            <div class="row" style="display: none;" id="div-charts-0">
                                <div class="col-sm-6">
                                    <div class="table-responsive-sm">
                                        <table id="tbDataCertificadoSAT" class="table table-sm table-hover table-bordered text-sm">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>S&#64;T</th>
                                                    <th>Data Ativação</th>
                                                    <th>Data Fim Ativação</th>
                                                </tr>
                                            </thead>
                                            <?php 
                                                echo "<tbody>";
                                                $i=1;
                                                while($rowGroupYears = mysqli_fetch_array($queryGroupYears)){
                                                    $id=$i;
                                                    $numSat = $rowGroupYears['sat'];
                                                    $dtAtivacao = $rowGroupYears['data_ativacao'];
                                                    $dtFimAtivacao = $rowGroupYears['data_fim_ativacao'];

                                                    echo "<tr>";
                                                        echo "<td>".$id."</td>";
                                                        echo "<td>".$numSat."</td>";
                                                        echo "<td>".$dtAtivacao."</td>";
                                                        echo "<td>".$dtFimAtivacao."</td>";
                                                    echo "</tr>";

                                                    $i++;
                                                }
                                                echo "</tbody>";
                                            
                                            ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <br />
                            <br />
                            <br />
                            <div class="row" style="display: none;" id="div-charts-1">
                                <div class="d-flex">
                                    <div class="col-sm-4 d-flex flex-column">
                                        <span class="text-center"><i class="fas fa-digital-tachograph"></i> Modelos</span>   
                                        <canvas id="donutChartModelosSat" style="min-height: 200px; height: 200px; max-height: 200px; max-width: 100%;"></canvas>
                                    </div>
                                    <div class="col-sm-4 d-flex flex-column">
                                        <span class="text-center"><i class="fas fa-microchip"></i> Firmware</span>
                                        <canvas id="pieChartFirmwareSAT" style="min-height: 200px; height: 200px; max-height: 200px; max-width: 100%;"></canvas>
                                    </div>
                                    <div class="col-sm-4 d-flex flex-column">
                                        <span class="text-center"><i class="fas fa-file-code"></i> Layout</span>
                                        <canvas id="pieChartLayoutSAT" style="min-height: 200px; height: 200px; max-height: 200px; max-width: 100%;"></canvas>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <br />
                            <br />
                            <br />
                            <div class="row" style="display: none;" id="div-charts-2">
                                <div class="col-sm-12">
                                    <div class="table-responsive-sm">
                                        <table id="tbDadosGerais" class="table table-sm table-hover table-bordered text-sm">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>S&#64;T</th>
                                                    <th>Bateria</th>
                                                    <th>Memória Usada</th>
                                                    <th>Data/Hora Atual</th>
                                                    <th>Data/Hora Comunicação</th>
                                                    <th>Data/Hora Transmissão</th>
                                                    <th>Estado Operacional</th>
                                                    <th>Data Atualização</th>
                                                </tr>
                                            </thead>
                                            <?php
                                                //sat, nivel_bateria, disco_usado, data_hora_atual, data_hora_transm_sefaz, data_hora_comun_sefaz,descricao_bloqueio, data_atualizacao 
                                                echo "<tbody>";
                                                $i=1;
                                                while($rowGroupDadosGerais = mysqli_fetch_array($queryGroupDadosGerais)){
                                                    $id=$i;
                                                    $numSat = $rowGroupDadosGerais['sat'];
                                                    $bateria = $rowGroupDadosGerais['nivel_bateria'];
                                                    $discoUsado = $rowGroupDadosGerais['disco_usado'];
                                                    $dtHoraAtual = $rowGroupDadosGerais['data_hora_atual'];
                                                    $descBloqueio = $rowGroupDadosGerais['descricao_bloqueio'];
                                                    $dtHoraComun = $rowGroupDadosGerais['data_hora_comun_sefaz'];
                                                    $dtHoraTransm = $rowGroupDadosGerais['data_hora_transm_sefaz'];
                                                    $dtAtualizacao = $rowGroupDadosGerais['data_atualizacao'];

                                                    echo "<tr>";
                                                        echo "<td>".$id."</td>";
                                                        echo "<td>".$numSat."</td>";
                                                        echo "<td>".$bateria."</td>";
                                                        echo "<td>".$discoUsado."</td>";
                                                        echo "<td>".$dtHoraAtual."</td>";
                                                        echo "<td>".$dtHoraComun."</td>";
                                                        echo "<td>".$dtHoraTransm."</td>";
                                                        echo "<td>".$descBloqueio."</td>";
                                                        echo "<td>".$dtAtualizacao."</td>";
                                                    echo "</tr>";

                                                    $i++;
                                                }
                                                echo "</tbody>";
                                            
                                            ?>
                                        </table>
                                    </div>
                                </div>
                                <br />
                                <br />
                                <br />
                                <br />
                                <div class="col-sm-12">
                                    <span class="text-center"><i class="fas fa-file-invoice"></i> CFEs Emitidos</span>
                                    <canvas id="stackedBarChart" style="min-height: 200px; height: 200px; max-height: 200px; max-width: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        require_once("../share/update_passwd.php");
                    ?>
                    <form name="frm_dados_pessoais" action="../processo/atualizar_usuario.php" method="POST">
                        <div class="modal fade" id="editaDadosPessoais" tabindex="-1" role="dialog" aria-labelledby="editaDadosPessoaisLabel" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center">Dados Pessoais</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form>
                                            <div class="form-group">
                                                <label for="text">Avatar </label>    
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar" name="avatar">
                                                    <label class="form-check-label"><img src="../dist/img/avatar.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar2" name="avatar2">
                                                    <label class="form-check-label"><img src="../dist/img/avatar2.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar3" name="avatar3">
                                                    <label class="form-check-label"><img src="../dist/img/avatar3.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar4" name="avatar4">
                                                    <label class="form-check-label"><img src="../dist/img/avatar4.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar5" name="avatar5">
                                                    <label class="form-check-label"><img src="../dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <label for="text">ID </label>
                                                <input for="text" class="form-control" name="id" value="<?php echo $row_usuario['id'] ?>" readonly>
                                                <label for="text">Nome </label>
                                                <input type="text" class="form-control" name="nome" value="<?php echo $row_usuario['nome']; ?>" >
                                                <label for="email">E-mail </label>
                                                <input type="email" class="form-control" name="email" value="<?php echo $row_usuario['email']; ?>">
                                                <label for="text">Login </label>
                                                <input type="text" class="form-control" name="login" value="<?php echo $row_usuario['login']; ?>" readonly>
                                            </div>
                                        </form>        
                                    </div>        
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Fechar</button>
                                        <button type="submit" class="btn btn-outline-success">Atualizar</button>        
                                    </div>      
                                </div> 
                            </div>
                        </div>
                    </form>
                </section>
            </div>
            <!-- /.control-sidebar -->
            <footer class="main-footer">
                <strong>Copyright &copy; 2020-2020 <a href="#">Developed by TPVs</a>.</strong>
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.1
                </div>
            </footer>
            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
        </div>
        <!-- ./wrapper -->

        <!-- REQUIRED SCRIPTS -->
        <script src="../dist/js/pages/dashboard2.js"></script>
        <!-- jQuery -->
        <script src="../plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- DataTables -->
        <script src="../plugins/datatables/jquery.dataTables.js"></script>
        <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
        <!-- ChartJS -->
        <script src="../plugins/chart.js/Chart.min.js"></script>
        <!-- overlayScrollbars -->
        <script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <!-- AdminLTE App -->
        <script src="../dist/js/adminlte.js"></script>
        <script src="../plugins/chart.js/Chart.min.js"></script>
        <!-- OPTIONAL SCRIPTS -->
        <script src="../dist/js/demo.js"></script>
        <script type="text/javascript" src="../plugins/pace-master/pace.min.js"></script>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#show_password').click(function (e) {
                    if($('#nova-senha').attr('type') == 'password'){
                        $('#nova-senha').attr('type', 'text');
                        $('#conf-senha').attr('type', 'text');
                        $('#show_password').attr('class', 'fas fa-eye-slash');
                    }else{
                        $('#nova-senha').attr('type', 'password');
                        $('#conf-senha').attr('type', 'password');
                        $('#show_password').attr('class', 'fas fa-eye');
                    }
                })
            })
        </script>
        <script>
            $(function () {
                $('#tbDataCertificadoSAT').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": true,
                "pageLength": 9
                });
                $('#tbDadosGerais').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": true,
                "pageLength": 9
                });
            })
            $(document).ready(function () {

                //-------------
                //- DONUT CHART - Modelos
                //-------------
                // Get context with jQuery - using jQuery's .get() method.
                var donutChartCanvas = $('#donutChartModelosSat').get(0).getContext('2d')
                var donutData        = {
                                            labels: [
                                                'SAT 1.0', 
                                                'SAT 2.0',
                                            ],
                                            datasets: [
                                                {
                                                data: [<?php print $rowCountSAT1['total_registros'];?>,<?php print $rowCountSAT2['total_registros'];?>],
                                                backgroundColor : ['#f56954', '#00a65a'],
                                                }
                                            ]
                }
                var donutOptions     = {
                                            maintainAspectRatio : false,
                                            responsive : true,
                }
                    //Create pie or douhnut chart
                    // You can switch between pie and douhnut using the method below.
                var donutChart = new Chart(donutChartCanvas, {
                                                                type: 'doughnut',
                                                                data: donutData,
                                                                options: donutOptions      
                })
                
                //-------------
                //- DONUT CHART - Firmware
                //-------------
                // Get context with jQuery - using jQuery's .get() method.
                var donutChartCanvas = $('#pieChartFirmwareSAT').get(0).getContext('2d')
                var donutData        = {
                                            labels: [<?php 
                                                            while($rowGroupFirmware1 = mysqli_fetch_array($queryGroupFirmware1)){
                                                                echo '"'. $rowGroupFirmware1['firmware'].'",';
                                                            }
                                                    ?>],
                                            datasets: [
                                                {
                                                data: [<?php 
                                                            while($rowGroupFirmware = mysqli_fetch_array($queryGroupFirmware)){
                                                                echo '"'. $rowGroupFirmware['qntd'].'",';
                                                            }
                                                    ?>],
                                                backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de','#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
                                                }
                                            ]
                }
                var donutOptions     = {
                                            maintainAspectRatio : false,
                                            responsive : true,
                }
                    //Create pie or douhnut chart
                    // You can switch between pie and douhnut using the method below.
                var donutChart = new Chart(donutChartCanvas, {
                                                                type: 'doughnut',
                                                                data: donutData,
                                                                options: donutOptions      
                })

                //-------------
                //- DONUT CHART - Layout
                //-------------
                // Get context with jQuery - using jQuery's .get() method.
                var donutChartCanvas = $('#pieChartLayoutSAT').get(0).getContext('2d')
                var donutData        = {
                                            labels: [<?php 
                                                            while($rowGroupLayout1 = mysqli_fetch_array($queryGroupLayout1)){
                                                                echo '"'. $rowGroupLayout1['layout'].'",';
                                                            }
                                                    ?>],
                                            datasets: [
                                                {
                                                data: [<?php 
                                                            while($rowGroupLayout = mysqli_fetch_array($queryGroupLayout)){
                                                                echo '"'. $rowGroupLayout['qntd'].'",';
                                                            }
                                                    ?>],
                                                backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de','#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
                                                }
                                            ]
                }
                var donutOptions     = {
                                            maintainAspectRatio : false,
                                            responsive : true,
                }
                    //Create pie or douhnut chart
                    // You can switch between pie and douhnut using the method below.
                var donutChart = new Chart(donutChartCanvas, {
                                                                type: 'doughnut',
                                                                data: donutData,
                                                                options: donutOptions      
                })
                
                //---------------------
                //- STACKED BAR CHART -
                //---------------------
                var stackedBarChartCanvas = $('#stackedBarChart').get(0).getContext('2d')
                var stackedBarChartData = {
                                        labels  : [
                                                    <?php 
                                                        while($rowGroupCFes = mysqli_fetch_array($queryGroupCFes)){
                                                            echo '"'. $rowGroupCFes['sat'].'",';
                                                        }
                                                    ?>
                                                ],
                                        datasets: [
                                                    /*{
                                                        label               : 'CFEs em Memoria',
                                                        backgroundColor     : 'rgba(60,141,188,0.9)',
                                                        borderColor         : 'rgba(60,141,188,0.8)',
                                                        pointRadius         : false,
                                                        pointColor          : '#3b8bba',
                                                        pointStrokeColor    : 'rgba(60,141,188,1)',
                                                        pointHighlightFill  : '#fff',
                                                        pointHighlightStroke: 'rgba(60,141,188,1)',
                                                        data:
                                                                [
                                                                    <?php /* 
                                                                        while($rowGroupCFesI = mysqli_fetch_array($queryGroupCFesI)){
                                                                            echo '"'. $rowGroupCFesI['numeros_cfes_memoria'].'",';
                                                                        }
                                                                        */
                                                                    ?>
                                                                ]
                                            
                                                    },*/
                                                    {
                                                        label               : 'CFEs Emitidos',
                                                        backgroundColor     : 'rgba(60,141,188,0.9)',
                                                        borderColor         : 'rgba(60,141,188,0.8)',
                                                        pointRadius         : false,
                                                        pointColor          : '#3b8bba',
                                                        pointStrokeColor    : 'rgba(60,141,188,1)',
                                                        pointHighlightFill  : '#fff',
                                                        pointHighlightStroke: 'rgba(60,141,188,1)',
                                                        data: 
                                                                [
                                                                    <?php 
                                                                        while($rowGroupCFesII = mysqli_fetch_array($queryGroupCFesII)){
                                                                            echo '"'. $rowGroupCFesII['numero_cfes_emitidos'].'",';
                                                                        }
                                                                    ?>
                                                                ]
                                                    }
                                                ]
                    }

                var stackedBarChartOptions = {
                responsive              : true,
                legend: {
                    display: false
                },
                maintainAspectRatio     : false,
                scales: {
                    xAxes: [{
                        stacked: true,
                        }],
                    yAxes: [{
                        stacked: true,
                        }]
                    }
                }

                var stackedBarChart = new Chart(stackedBarChartCanvas, {
                    type: 'bar', 
                    data: stackedBarChartData,
                    options: stackedBarChartOptions
                })
            })
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
                var numeroLoja = document.getElementById('consulta').value
                
                if(numeroLoja != ''){
                    $('#div-loading').css("display","block")
                    getNTpvs(numeroLoja);
                    
                    function getNTpvs(loja){
                        if(numeroLoja.length <= 5 && numeroLoja <= 9999){
                            $.ajax({
                                url: "../processo/get_num_tpvs.php",
                                type: "POST",
                                data:
                                    {
                                        consulta: loja,
                                    },
                                success: function(data){
                                    if(data>=1){
                                        var nTpvs = data
                                        for(var i = 1; i <= 9 ; i++){
                                            if(i<=nTpvs){
                                                getSAT(i);
                                            }else{
                                                $('#pdv_'+i).css("display","none")
                                            }
                                        }
                                    }else{
                                        $('#div-loading').css("display","none")
                                        alert('Nenhum dado econtrado no banco de dados!')
                                    }
                                }
                            })
                        }else{
                            $('#div-loading').css("display","block")
                            $.ajax({
                                url: "../processo/get_num_tpvs.php",
                                type: "POST",
                                data:
                                    {
                                        consulta: loja,
                                    },
                                success: function(data){
                                    if(data>=1){
                                        var nTpvs = data
                                        for(var i = 1; i <= 9 ; i++){
                                            if(i==nTpvs){
                                                getSAT(i);
                                            }else{
                                                $('#pdv_'+i).css("display","none")
                                            }
                                        }
                                    }else{
                                        $('#div-loading').css("display","none")
                                        alert('Nenhum dado econtrado no banco de dados!')
                                    }
                                }
                            })
                        }
                    }

                    function getSAT(x){
                        if(numeroLoja.length <= 5 && numeroLoja <= 9999){
                            $.ajax({
                            url: "../processo/get_sat_pdvs.php",
                            type: "POST",
                            data: 
                                {
                                    nshop: numeroLoja,
                                    ncaixa: x
                                },
                            success: function(data){
                                    if(data==0){
                                        $('#div-loading').css("display","none")
                                        $('#div-num-loja').css("display","block")
                                        document.getElementById('span-num-loja').innerHTML = numeroLoja
                                        $('#pdv_'+x).css("display","block")
                                        $('#link_pdv_'+x).attr('href','getsat.php?token=<?php echo $token?>&idsat='+sat+'&page=4c11ce1f483bac29acd478620c8185ba&search='+numeroLoja)
                                        $('#img_pdv_'+x).attr('src','../dist/img/pdv_off.png')
                                        document.getElementById('span_pdv_'+x).innerHTML = sat
                                        $('#div-charts-0').css("display","block")
                                        $('#div-charts-1').css("display","block")
                                        $('#div-charts-2').css("display","block")
                                    }else{
                                        verificaBloq(data,x);
                                    }
                                }
                            });
                        }else{
                            $.ajax({
                                url: "../processo/get_n_loja.php",
                                type: "POST",
                                data:
                                    {
                                        nsat: numeroLoja,
                                    },
                                success: function(data){
                                    if(data>=1){
                                        $('#div-loading').css("display","none")
                                        $('#div-n-loja').css("display","block")
                                        document.getElementById('span-n-loja').innerHTML = data
                                    }
                                }
                            })
                            $.ajax({
                            url: "../processo/get_num_loja.php",
                            type: "POST",
                            data: 
                                {
                                    nsat: numeroLoja,
                                },
                            success: function(data){
                                    if(data==0){
                                        $('#div-loading').css("display","none")
                                        $('#div-num-loja').css("display","block")
                                        document.getElementById('span-num-loja').innerHTML = numeroLoja
                                        $('#pdv_'+x).css("display","block")
                                        $('#link_pdv_'+x).attr('href','getsat.php?token=<?php echo $token?>&idsat='+sat+'&page=4c11ce1f483bac29acd478620c8185ba&search='+numeroLoja)
                                        $('#img_pdv_'+x).attr('src','../dist/img/pdv_off.png')
                                        document.getElementById('span_pdv_'+x).innerHTML = numeroLoja
                                    }else{
                                        verificaBloq(data,x);
                                    }
                                }
                            });
                        }
                    }
                    
                    function verificaBloq(sat,y){
                        $.ajax({
                            url: "../processo/get_estado_oper_sat.php",
                            type: "POST",
                            data:
                                {
                                    nsat: sat,
                                },
                            success: function(data){
                                if(data==0){
                                    $('#div-loading').css("display","none")
                                    $('#div-num-loja').css("display","block")
                                    document.getElementById('span-num-loja').innerHTML = numeroLoja
                                    $('#pdv_'+y).css("display","block")
                                    $('#link_pdv_'+y).attr('href','getsat.php?token=<?php echo $token?>&idsat='+sat+'&page=4c11ce1f483bac29acd478620c8185ba&search='+numeroLoja)
                                    $('#img_pdv_'+y).attr('src','../dist/img/pdv_bloq.png')
                                    document.getElementById('span_pdv_'+y).innerHTML = sat
                                    $('#div-charts-0').css("display","block")
                                    $('#div-charts-1').css("display","block")
                                    $('#div-charts-2').css("display","block")
                                }else{
                                    atualizaSAT(sat,y);
                                }
                            }
                        });
                    }

                    function atualizaSAT(sat,y){
                        $.ajax({
                        url: "../processo/get_data_atualizacao_sat.php",
                        type: "POST",
                        data: 
                                {
                                    nsat: sat
                                },
                            success: function(data){
                                if(data==0){
                                    $('#div-loading').css("display","none")
                                    $('#div-num-loja').css("display","block")
                                    document.getElementById('span-num-loja').innerHTML = numeroLoja
                                    $('#pdv_'+y).css("display","block")
                                    $('#link_pdv_'+y).attr('href','getsat.php?token=<?php echo $token?>&idsat='+sat+'&page=4c11ce1f483bac29acd478620c8185ba&search='+numeroLoja)
                                    $('#img_pdv_'+y).attr('src','../dist/img/pdv_desatualizado.png')
                                    document.getElementById('span_pdv_'+y).innerHTML = sat
                                    $('#div-charts-0').css("display","block")
                                    $('#div-charts-1').css("display","block")
                                    $('#div-charts-2').css("display","block")
                                }else{
                                    $('#div-loading').css("display","none")
                                    $('#div-num-loja').css("display","block")
                                    document.getElementById('span-num-loja').innerHTML = numeroLoja    
                                    $('#pdv_'+y).css("display","block")
                                    $('#link_pdv_'+y).attr('href','getsat.php?token=<?php echo $token?>&idsat='+sat+'&page=4c11ce1f483bac29acd478620c8185ba&search='+numeroLoja)
                                    $('#img_pdv_'+y).attr('src','../dist/img/pdv_on.png')
                                    document.getElementById('span_pdv_'+y).innerHTML = sat
                                    $('#div-charts-0').css("display","block")
                                    $('#div-charts-1').css("display","block")
                                    $('#div-charts-2').css("display","block")
                                }
                            }  
                        });
                    }
                }                
            });
        </script>
    </body>
    <?php
        echo '
        <script type="text/javascript">
            $(document).ready(function(){
                function atualizaTempo(){
                    $.ajax({
                        url: "../processo/atualiza_tempo.php",
                        type: "POST",
                        data: {id:'.$fech[0]["id"].'},
                        success: function(data){
                            if(data == 1){
                                location.href="../processo/encerra_sessao.php";
                            }
                        }
                    }); 
                }setInterval(atualizaTempo,10000);
            });
            $("#limpaTempo").on("click", function(){
                $.ajax({
                    url: "../processo/limpa_tempo.php",
                    type: "POST",
                    data: {id:'.$fech[0]["id"].'}
                });
            });
        </script>';
    ?>
</html>