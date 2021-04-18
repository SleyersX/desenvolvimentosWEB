<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");

    if(!empty($_SESSION['usuarioIDDashSAT']) && $_SESSION['usuarioNivelDashSAT'] != 5 ){
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
    
    $idsat = filter_input(INPUT_GET, 'idsat', FILTER_SANITIZE_NUMBER_INT);
    $sqlDadosSat = "SELECT retorno_status_operacional, msg_status_operacional, aviso_sefaz, msg_aviso_sefaz, sat, loja, caixa, tipo_lan, ip, mac, mask, gw, dns_1, dns_2, status_wan, nivel_bateria, disco, disco_usado, data_hora_atual, firmware, layout, ultimo_cfe, primeiro_cfe_memoria, ultimo_cfe_memoria, numero_cfes_emitidos, numeros_cfes_memoria, data_hora_transm_sefaz, data_hora_comun_sefaz, data_ativacao, data_fim_ativacao, estado_operacao, descricao_bloqueio, falha, status, modelo_sat, data_inclusao, data_atualizacao  FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE sat = '$idsat'";
    $queryDadosSat = mysqli_query($conn,$sqlDadosSat);
    $rowDadosSat = mysqli_fetch_assoc($queryDadosSat);
    $satNumero = $rowDadosSat['sat'];
    $shopNum = $rowDadosSat['loja'];
    $posNum = $rowDadosSat['caixa']; 

    $x=0;

    $sqlCountComunSatSefaz = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_comun_sefaz"] ." WHERE sat = '$idsat'";
    $queryCountComunSatSefaz = mysqli_query($conn,$sqlCountComunSatSefaz);
    $rowCountComunSatSefaz = mysqli_fetch_assoc($queryCountComunSatSefaz);
    $x = $x + $rowCountComunSatSefaz['total_registros'];

    $sqlCountComunSatDisco = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_disk_used"] ." WHERE sat = '$idsat'";
    $queryCountComunSatDisco = mysqli_query($conn,$sqlCountComunSatDisco);
    $rowCountComunSatDisco = mysqli_fetch_assoc($queryCountComunSatDisco);
    $x = $x + $rowCountComunSatDisco['total_registros'];

    $sqlDadosFiscais = "SELECT loja, persFisc, direFisc, codiPostFisc, locaFisc, provFisc, inscEstaClie, codiEstaClie, barrClie, numeDireClie, codiIden, codiMuniClie, email, regiao, divisao, centro, n_tpvs_setvari, iploja FROM ". DATA_CONFIG_BD["cn_tab_list_lojas_completo"] ." WHERE loja LIKE '".$rowDadosSat['loja']."'";
    $queryDadosFiscais = mysqli_query($conn,$sqlDadosFiscais);
    $rowDadosFiscais = mysqli_fetch_assoc($queryDadosFiscais);
    

    $SQLSatsLoja = "SELECT sat, caixa FROM ". DATA_CONFIG_BD["cn_tab_sat"]." WHERE loja LIKE '".$rowDadosSat['loja']."'";
    $querySatsLoja = mysqli_query($conn,$SQLSatsLoja);

    unset($_SESSION['tempNome']);
	unset($_SESSION['tempEmail']);
    unset($_SESSION['tempLogin']);
    
    //Botão voltar
    $idPage = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING);
    $page = "page=".$idPage;
    if(isset($_GET['str'],$_GET['id'])){
        $idGet = filter_input(INPUT_GET,'id',FILTER_SANITIZE_STRING);
        $str = filter_input(INPUT_GET,'str',FILTER_SANITIZE_STRING);
        $page = $page."&id=".$idGet."&str=".$str;
    }
    if(isset($_GET['search'])){
        $search = filter_input(INPUT_GET,'search',FILTER_SANITIZE_STRING);
        $page = $page."&search=".$search;
    }
    //Conexão ao banco de dados, exclusiva para monitorar inatividade do usuário
    $idLoginTemp = $_SESSION['idLoginTempDashSAT'];
    $conexao = new PDO('mysql:host=database;dbname=srvremoto',"root","8wFml6golmmbuKPv");
    $usuarioLogado = $conexao->prepare("SELECT * FROM tb_sessoes_login_dashsat WHERE id = '$idLoginTemp'");
    $usuarioLogado->execute();
    $fech = $usuarioLogado->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>App SAT | Tracking </title>
        <link rel="icon" href="../favicon.ico" />
        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="../dist/css/adminlte.min.css">
        <link rel="stylesheet" href="../dist/css/style.css">
        <!-- DataTables -->
        <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.css">
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
                            <a href="search.php?token=<?php echo $token;?>" class="nav-link">
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
                    </ul> 
                </nav>
            </aside>
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Dados S&#64;T</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="home.php?token=<?php echo $token;?>">Home</a></li>
                                    <li class="breadcrumb-item active">Tables</li>
                                    <li class="breadcrumb-item active">Status</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card card-primary card-outline">
                                    <div class="text-center">
                                        <?php 
                                            if($x>=1){
                                                echo '<div class="small-box bg-warning">';
                                                    echo '<div class="inner">';
                                                        echo '<h3>'.$rowDadosSat['sat'].'</h3>';
                                                        echo '<p>'.$rowDadosSat['modelo_sat'].'</p>';
                                                    echo '</div>';
                                                    echo '<div class="icon">';
                                                        echo '<i class="ion ion-stats-bars"></i>';
                                                    echo '</div>';
                                                    echo '<a href="#" class="small-box-footer"></a>';
                                                echo '</div>';
                                            }else{
                                                echo '<div class="small-box bg-success">';
                                                    echo '<div class="inner">';
                                                        echo '<h3>'.$rowDadosSat['sat'].'</h3>';
                                                        echo '<p>'.$rowDadosSat['modelo_sat'].'</p>';
                                                    echo '</div>';
                                                    echo '<div class="icon">';
                                                        echo '<i class="ion ion-stats-bars"></i>';
                                                    echo '</div>';
                                                    echo '<a href="#" class="small-box-footer"></a>';
                                                echo '</div>';
                                            }                                        
                                        ?>
                                    </div>
                                </div>
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <nav>
                                            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" aria-orientation="vertical">
                                                <h3><i class="far fa-function nav-icon"></i> Funções LibSAT</h3>
                                            </ul>
                                        </nav>
                                        <div class="card-tools">
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <nav class="mt-2">
                                            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" aria-orientation="vertical">
                                                <li class="nav-item">
                                                    <a href="../api/processo/processa_libsat.php?token=<?php echo $token;?>&<?php echo $page?>&id=3&sat=<?php echo $satNumero;?>&shop=<?php echo $shopNum;?>&pos=<?php echo $posNum;?>" class="nav-link">
                                                        <i class="fas fa-sync nav-icon"></i>
                                                        Sincronizar
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="../api/processo/processa_libsat.php?token=<?php echo $token;?>&<?php echo $page?>&id=4&sat=<?php echo $satNumero;?>&shop=<?php echo $shopNum;?>&pos=<?php echo $posNum;?>" class="nav-link">
                                                        <i class="fas fa-satellite-dish nav-icon"></i>
                                                        Testar Comunicação SEFAZ
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="../api/processo/processa_libsat.php?token=<?php echo $token;?>&<?php echo $page?>&id=1&sat=<?php echo $satNumero;?>&shop=<?php echo $shopNum;?>&pos=<?php echo $posNum;?>" class="nav-link">
                                                        <i class="far fa-file-alt nav-icon"></i>
                                                        Extrair Log SAT
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="../api/processo/processa_libsat.php?token=<?php echo $token;?>&<?php echo $page?>&id=2&sat=<?php echo $satNumero;?>&shop=<?php echo $shopNum;?>&pos=<?php echo $posNum;?>" class="nav-link">
                                                        <i class="fas fa-cloud-download-alt nav-icon"></i>
                                                        Atualizar Software Básico*
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="#" data-toggle="modal" data-target="#configRedeSAT" data-numsat="<?php echo $satNumero;?>" class="nav-link">
                                                        <i class="fas fa-ethernet nav-icon"></i>
                                                        Configurar Rede*
                                                    </a>
                                                </li>
                                            </ul>
                                        </nav>
                                        <div class="card card-warning">
                                            <h6 class="text-sm text-left text-red">*Para executar este processo, o caixa deve estar em Selecione Programa</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="card card-secondary card-outline collapsed-card">
                                    <div class="card-header">
                                        <h3 class="card-title"> SATs Filial L<?php echo $rowDadosSat['loja']?></h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <nav class="mt-2">
                                            <ul class="nav nav-pills nav-sidebar flex-column" role="menu" aria-orientation="vertical">
                                                <?php 
                                                    while($rowSatsLoja = mysqli_fetch_array($querySatsLoja)){
                                                        echo '<li class="nav-item">';
                                                            echo '<a href="getsat.php?token='.$token.'&idsat='.$rowSatsLoja['sat'].'&'.$page.'" class="nav-link">';
                                                                echo $rowSatsLoja['caixa'] . " | " . $rowSatsLoja['sat'];
                                                            echo '</a>';
                                                        echo '</li>';
                                                    }
                                                ?>
                                            </ul>
                                        </nav>
                                    </div>
                                    <div class="card-footer">
                                    </div>
                            </div>
                            </div>
                            <div class="col-md-9">
                                <div class="card">
                                    <div class="card-header p-2">
                                        <ol class="float-sm-right">
                                            <a href="../processo/back_page.php?<?php echo $page?>">
                                                <button type="button" class="btn btn-block btn-outline-info btn-sm">
                                                    Voltar
                                                </button>
                                            </a>
                                        </ol>
                                        <ul class="nav nav-pills">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#dadosGerais" data-toggle="tab">
                                                    Dados Gerais
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#dadosSefaz" data-toggle="tab">
                                                    SEFAZ
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#dadosRede" data-toggle="tab">
                                                    Rede
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#dadosLocalizacao" data-toggle="tab">
                                                    Localização
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <div class="active tab-pane" id="dadosGerais">
                                                <form class="form-horizontal">
                                                    <h5>Dados Gerais</h5>
                                                    <div class="form-group row">
                                                        <div class="col-sm-6">
                                                            <label for="dtAtualDados" class="col-sm-12 col-form-label">Data atualização dos dados</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="dtAtualDados" value="<?php print $rowDadosSat['data_atualizacao'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="dtHoraSAT" class="col-sm-12 col-form-label">Data/Hora SAT</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="dtHoraSAT" value="<?php print $rowDadosSat['data_hora_atual'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="layoutCFE" class="col-sm-12 col-form-label">Layout CFE</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="layoutCFE" value="<?php print $rowDadosSat['layout'];?>" readonly>
                                                            </div>
                                                            <label for="nameFabricante" class="col-sm-12 col-form-label">Fabricante</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="nameFabricante" value="DIMEP" readonly>
                                                            </div>
                                                            <label for="stsWan" class="col-sm-12 col-form-label">Status WAN</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="nameFabricante" value="<?php print $rowDadosSat['status_wan'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="versFirmware" class="col-sm-12 col-form-label">Versão da Firmware</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="versFirmware" value="<?php print $rowDadosSat['firmware'];?>" readonly>
                                                            </div>
                                                            <label for="numSerieSat" class="col-sm-12 col-form-label">Número de Série</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="numSerieSat" value="<?php print $rowDadosSat['sat'];?>" readonly>
                                                            </div>
                                                            <label for="disc" class="col-sm-12 col-form-label">Memória Total</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="disc" value="<?php print $rowDadosSat['disco'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="discUsado" class="col-sm-12 col-form-label">Memória Usada</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="discUsado" value="<?php print $rowDadosSat['disco_usado'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="nivelBateria" class="col-sm-12 col-form-label">Nível Bateria</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="nivelBateria" value="<?php print $rowDadosSat['nivel_bateria'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="estadoFalha" class="col-sm-12 col-form-label">Estado de Falha</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="estadoFalha" value="<?php if($rowDadosSat['falha']==1){print "Em Falha";}else{print "Normal";};?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="estadoOper" class="col-sm-12 col-form-label">Estado Operacional</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="estadoOper" value="<?php print $rowDadosSat['descricao_bloqueio'];?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane" id="dadosSefaz">
                                                <form class="form-horizontal">
                                                    <h5>SEFAZ</h5>
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <label for="utlCFeEmit" class="col-sm-8 col-form-label">Último CFe Emitido</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" id="utlCFeEmit" value="<?php print $rowDadosSat['ultimo_cfe'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <label for="primeiCFeMemoria" class="col-sm-8 col-form-label">Primeiro CFe em Memória</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" id="primeiCFeMemoria" value="<?php print $rowDadosSat['primeiro_cfe_memoria'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <label for="utltCFeMemoria" class="col-sm-8 col-form-label">Último CFe em Memória</label>
                                                            <div class="col-sm-8">
                                                                <input type="text" class="form-control" id="utltCFeMemoria" value="<?php print $rowDadosSat['ultimo_cfe_memoria'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="totalCFeEmitidos" class="col-sm-12 col-form-label">Total CFe Emitidos</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="totalCFeEmitidos" value="<?php print $rowDadosSat['numero_cfes_emitidos'];?>" readonly>
                                                            </div>
                                                            <label for="dtUltTransmSefaz" class="col-sm-12 col-form-label">Última Transmissão</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="dtUltTransmSefaz" value="<?php print $rowDadosSat['data_hora_transm_sefaz'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="totalCFeMemoria" class="col-sm-12 col-form-label">Total CFe Memória</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="totalCFeMemoria" value="<?php print $rowDadosSat['numeros_cfes_memoria'];?>" readonly>
                                                            </div>
                                                            <label for="dtUltComuniSefaz" class="col-sm-12 col-form-label">Última comunicação</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="dtUltComuniSefaz" value="<?php print $rowDadosSat['data_hora_comun_sefaz'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="dtEmisCertSefaz" class="col-sm-12 col-form-label">Emissão do Certificado Digital</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="dtEmisCertSefaz" value="<?php print $rowDadosSat['data_ativacao'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="dtExpirCertSefaz" class="col-sm-12 col-form-label">Expiração do Certificado Digital</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="dtExpirCertSefaz" value="<?php print $rowDadosSat['data_fim_ativacao'];?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane" id="dadosRede">
                                                <form class="form-horizontal">
                                                    <h5>Rede</h5>
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <label for="tipoLan" class="col-sm-6 col-form-label">Tipo LAN</label>
                                                            <div class="col-sm-6">
                                                                <input type="text" class="form-control" id="tipoLan" value="<?php print $rowDadosSat['tipo_lan'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="endIP" class="col-sm-12 col-form-label">Endereço de IP</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="endIP" value="<?php print $rowDadosSat['ip'];?>" readonly>
                                                            </div>
                                                            <label for="endMascara" class="col-sm-12 col-form-label">Mascara de IP</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="endMascara" value="<?php print $rowDadosSat['mask'];?>" readonly>
                                                            </div>
                                                            <label for="endGateway" class="col-sm-12 col-form-label">Gateway</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="endGateway" value="<?php print $rowDadosSat['gw'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="dnsPrimario" class="col-sm-12 col-form-label">DNS Primário</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="dnsPrimario" value="<?php print $rowDadosSat['dns_1'];?>" readonly>
                                                            </div>
                                                            <label for="dnsSecundario" class="col-sm-12 col-form-label">DNS Secundário</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="dnsSecundario" value="<?php print $rowDadosSat['dns_2'];?>" readonly>
                                                            </div>
                                                            <label for="mac" class="col-sm-12 col-form-label">Endereço MAC</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="mac" value="<?php print $rowDadosSat['mac'];?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="tab-pane" id="dadosLocalizacao">
                                                <form class="form-horizontal">
                                                    <h5>Localização</h5>
                                                    <div class="form-group row">
                                                        <div class="col-sm-6">
                                                            <label for="numLoja" class="col-sm-12 col-form-label">Loja</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="numLoja" value="<?php print $rowDadosSat['loja'];?>" readonly>
                                                            </div>
                                                            <label for="cnpj" class="col-sm-12 col-form-label">CNPJ</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="cnpj" value="<?php print $rowDadosFiscais['codiIden'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="numPDV" class="col-sm-12 col-form-label">Caixa</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="numPDV" value="<?php print $rowDadosSat['caixa'];?>" readonly>
                                                            </div>
                                                            <label for="ie" class="col-sm-12 col-form-label">Iscrição Estadual</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="ie" value="<?php print $rowDadosFiscais['inscEstaClie'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <label for="nomeSocial" class="col-sm-12 col-form-label">Nome social</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="nomeSocial" value="<?php print $rowDadosFiscais['persFisc'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="endLoja" class="col-sm-12 col-form-label">Endreço loja</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="endLoja" value="<?php print $rowDadosFiscais['direFisc'];?>" readonly>
                                                            </div>
                                                            <label for="bairro" class="col-sm-12 col-form-label">Bairro</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="bairro" value="<?php print $rowDadosFiscais['barrClie'];?>" readonly>
                                                            </div>
                                                            <label for="cidade" class="col-sm-12 col-form-label">Cidade</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="cidade" value="<?php print $rowDadosFiscais['locaFisc'];?>" readonly>
                                                            </div>
                                                            <label for="numCEP" class="col-sm-12 col-form-label">Centro</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="numCEP" value="<?php print $rowDadosFiscais['centro'];?>" readonly>
                                                            </div>
                                                            <label for="uf" class="col-sm-12 col-form-label">Região</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="uf" value="<?php print $rowDadosFiscais['regiao'];?>" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <label for="numEndLoja" class="col-sm-12 col-form-label">Número</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="numEndLoja" value="<?php print $rowDadosFiscais['numeDireClie'];?>" readonly>
                                                            </div>
                                                            <label for="numCEP" class="col-sm-12 col-form-label">CEP</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="numCEP" value="<?php print $rowDadosFiscais['codiPostFisc'];?>" readonly>
                                                            </div>
                                                            <label for="uf" class="col-sm-12 col-form-label">UF</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="uf" value="<?php print $rowDadosFiscais['codiEstaClie'];?>" readonly>
                                                            </div>
                                                            <label for="numEndLoja" class="col-sm-12 col-form-label">Divisão</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="numEndLoja" value="<?php print $rowDadosFiscais['divisao'];?>" readonly>
                                                            </div>
                                                            <label for="numEndLoja" class="col-sm-12 col-form-label">IP</label>
                                                            <div class="col-sm-12">
                                                                <input type="text" class="form-control" id="numEndLoja" value="<?php print $rowDadosFiscais['iploja'];?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
                                                <label for="text">ID </label>
                                                <input for="text" class="form-control" name="id-img" id="id-img" readonly style="display: none;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar" name="avatar" onclick="click_check_box_img(1);">
                                                    <label class="form-check-label"><img src="../dist/img/avatar.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar2" name="avatar" onclick="click_check_box_img(2);">
                                                    <label class="form-check-label"><img src="../dist/img/avatar2.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar3" name="avatar" onclick="click_check_box_img(3);">
                                                    <label class="form-check-label"><img src="../dist/img/avatar3.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar4" name="avatar" onclick="click_check_box_img(4);">
                                                    <label class="form-check-label"><img src="../dist/img/avatar4.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar5" name="avatar" onclick="click_check_box_img(5);">
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
                    <form name="frm_config_rede_sat" action="../api/processo/processa_config_rede.php?<?php echo $page?>&shop=<?php echo $shopNum;?>&pos=<?php echo $posNum;?>" method="POST">
                        <div class="modal fade" id="configRedeSAT" tabindex="-1" role="dialog" aria-labelledby="configRedeSAT" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center">Configuração Rede SAT</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <label for="text">SAT </label>
                                                        <input for="text" class="form-control" name="serieSAT" value="" id="serieSAT" readonly>
                                                        <label for="text">IP SAT(000.000.000.000)</label>
                                                        <input type="text" class="form-control" data-inputmask="'alias': 'ip'" data-mask name="ipSAT">
                                                        <label for="text">Gateway(000.000.000.000)</label>
                                                        <input type="text" class="form-control" data-inputmask="'alias': 'ip'" data-mask name="gateway" value="010.010.010.252">
                                                        <label for="text">Máscara(000.000.000.000)</label>
                                                        <input type="text" class="form-control" data-inputmask="'alias': 'ip'" data-mask name="mask" value="255.255.255.000">
                                                        <label for="text">DNS Primario(000.000.000.000)</label>
                                                        <input type="text" class="form-control" data-inputmask="'alias': 'ip'" data-mask name="dnsPrimario" value="010.105.186.005">
                                                        <label for="text">DNS Secundario(000.000.000.000)</label>
                                                        <input type="text" class="form-control" data-inputmask="'alias': 'ip'" data-mask name="dnsSecundario" value="010.106.068.066">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>        
                                    </div>        
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Fechar</button>
                                        <button type="submit" class="btn btn-outline-success">Enviar</button>        
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
        <script src="../dist/js/pages/dashboard2.js"></script>
        <!-- jQuery -->
        <script src="../plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- DataTables -->
        <script src="../plugins/datatables/jquery.dataTables.js"></script>
        <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
        <!-- InputMask -->
        <script src="../plugins/moment/moment.min.js"></script>
        <script src="../plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
        <!-- AdminLTE App -->
        <script src="../dist/js/adminlte.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="../dist/js/demo.js"></script>
        <script type="text/javascript" src="../plugins/pace-master/pace.min.js"></script>
        <script text="text/javascript">
            function click_check_box_img(id){
                document.getElementById('id-img').value = id;
                 
            }
        </script>
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
        <script type="text/javascript">
            $('#configRedeSAT').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var serieSat = button.data('numsat')

                var modal = $(this)
                modal.find('#serieSAT').val(serieSat)

            })
        </script>
        <script>
            $('[data-mask]').inputmask();
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