<?php
    session_start();
    require_once("../security/seguranca.php");
    protegePagina();
    require_once("../security/connect.php");

    if(!empty($_SESSION['usuarioIDDashSAT']) && $_SESSION['usuarioNivelDashSAT'] != 1 ){
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

	$sqlComboNivel = "SELECT nivel, descricao FROM tb_niveis_dashsat";
    $queryComboNivel = mysqli_query($conn,$sqlComboNivel);

    unset($_SESSION['tempNome']);
	unset($_SESSION['tempEmail']);
    unset($_SESSION['tempLogin']);
    
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
        <meta http-equiv="x-ua-compatible" content="ie=edge">
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
        <!--<link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.css">-->
        <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
                                        <a href="tables/tb-shops-closed.php?token=<?php echo $token;?>" class="nav-link">
                                            <i class="fas fa-store-alt-slash nav-icon"></i>
                                            <p>Lojas Fechadas</p>
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
                            <li class="nav-item has-treeview">
                                <a href="sistema.php?token=<?php echo $token;?>" class="nav-link active">
                                    <i class="fab fa-ubuntu nav-icon"></i>
                                    <p>Sistema</p>
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
                                <h1>Sistema</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="home.php?token=<?php echo $token;?>">Home</a></li>
                                    <li class="breadcrumb-item active">Sistema</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <section class="content">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#dadosLogsSistema" data-toggle="tab">
                                            Log
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#dadosLogsEmailAuto" data-toggle="tab">
                                            Log E-mail Auto
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#dadosLogImportCSV" data-toggle="tab">
                                            Log Import CSV
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#dadosUsuariosOnline" data-toggle="tab">
                                            Registro de Visitas
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#dadosSessoesUsuario" data-toggle="tab">
                                            Sessões de Usuário
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#dadosRegistroBloqTemporario" data-toggle="tab">
                                            Bloqueio de Usuários
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="dadosLogsSistema">
                                        <div class="card card-info card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">Logs do sistema</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="tbLogsSistema" class="table table-sm table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>ID User</th>
                                                                <th>Nome User</th>
                                                                <th>Login User</th>
                                                                <th>Aplicação</th>
                                                                <th>Data Registro</th>
                                                                <th>Registro</th>
                                                            </tr>
                                                        </thead>
                                                        <?php 
                                                            $sqlTbLogs = "SELECT id_user, nome_user, login_user, aplicacao, data_log, log_dados FROM tb_log_dashsat  WHERE DATE_FORMAT(`data_log`, '%Y-%m-%d') BETWEEN(NOW() + INTERVAL -(7) DAY) AND NOW() ORDER BY id DESC";
                                                            $queryTbLogs = mysqli_query($conn, $sqlTbLogs);
                                                            $i=1;
                                                            echo "<tbody>";
                                                            while($rowTbLogs = mysqli_fetch_array($queryTbLogs)){
                                                                $idUserLog    = $rowTbLogs['id_user'];
                                                                $nomeUserLog  = $rowTbLogs['nome_user'];
                                                                $loginUserLog = $rowTbLogs['login_user'];
                                                                $appUserLog   = $rowTbLogs['aplicacao'];
                                                                $dtUserLog    = $rowTbLogs['data_log'];
                                                                $logUser      = $rowTbLogs['log_dados'];

                                                                echo '<tr>';
                                                                    echo '<td>'.$i.'</td>';
                                                                    echo '<td>'.$idUserLog.'</td>';
                                                                    echo '<td>'.$nomeUserLog.'</td>';
                                                                    echo '<td>'.$loginUserLog.'</td>';
                                                                    echo '<td>'.$appUserLog.'</td>';
                                                                    echo '<td>'.$dtUserLog.'</td>';
                                                                    echo '<td>'.$logUser.'</td>';
                                                                echo '</tr>';

                                                                $i++;
                                                            }
                                                            echo '</tbody>';
                                                        ?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="dadosLogsEmailAuto">
                                        <div class="card card-info card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">Logs Send E-mail Auto</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="tbLogEmailAuto" class="table table-sm table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>ID User</th>
                                                                <th>Nome User</th>
                                                                <th>Login User</th>
                                                                <th>Aplicação</th>
                                                                <th>Data Registro</th>
                                                                <th>Registro</th>
                                                            </tr>
                                                        </thead>
                                                        <?php 
                                                            $sqlTbLogImportCSV = "SELECT id_user, nome_user, login_user, aplicacao, data_log, log_dados FROM tb_log_email_auto WHERE DATE_FORMAT(`data_log`, '%Y-%m-%d') BETWEEN(NOW() + INTERVAL -(7) DAY) AND NOW() ORDER BY id DESC";
                                                            $queryTbLogImportCSV = mysqli_query($conn, $sqlTbLogImportCSV);
                                                            $i=1;
                                                            echo "<tbody>";
                                                            while($rowTbLogImportCSV = mysqli_fetch_array($queryTbLogImportCSV)){
                                                                $idUserLogImportCSV    = $rowTbLogImportCSV['id_user'];
                                                                $nomeUserLogImportCSV  = $rowTbLogImportCSV['nome_user'];
                                                                $loginUserLogImportCSV = $rowTbLogImportCSV['login_user'];
                                                                $appUserLogImportCSV   = $rowTbLogImportCSV['aplicacao'];
                                                                $dtUserLogImportCSV    = $rowTbLogImportCSV['data_log'];
                                                                $logUserImportCSV      = $rowTbLogImportCSV['log_dados'];

                                                                echo '<tr>';
                                                                    echo '<td>'.$i.'</td>';
                                                                    echo '<td>'.$idUserLogImportCSV.'</td>';
                                                                    echo '<td>'.$nomeUserLogImportCSV.'</td>';
                                                                    echo '<td>'.$loginUserLogImportCSV.'</td>';
                                                                    echo '<td>'.$appUserLogImportCSV.'</td>';
                                                                    echo '<td>'.$dtUserLogImportCSV.'</td>';
                                                                    echo '<td>'.$logUserImportCSV.'</td>';
                                                                echo '</tr>';

                                                                $i++;
                                                            }
                                                            echo '</tbody>';
                                                        ?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="dadosLogImportCSV">
                                        <div class="card card-info card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">Logs Import CSV</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="tbLogImportCSV" class="table table-sm table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>ID User</th>
                                                                <th>Nome User</th>
                                                                <th>Login User</th>
                                                                <th>Aplicação</th>
                                                                <th>Data Registro</th>
                                                                <th>Registro</th>
                                                            </tr>
                                                        </thead>
                                                        <?php 
                                                            $sqlTbLogImportCSV = "SELECT id_user, nome_user, login_user, aplicacao, data_log, log_dados FROM tb_log_import_dashsat ORDER BY id DESC";
                                                            $queryTbLogImportCSV = mysqli_query($conn, $sqlTbLogImportCSV);
                                                            $i=1;
                                                            echo "<tbody>";
                                                            while($rowTbLogImportCSV = mysqli_fetch_array($queryTbLogImportCSV)){
                                                                $idUserLogImportCSV    = $rowTbLogImportCSV['id_user'];
                                                                $nomeUserLogImportCSV  = $rowTbLogImportCSV['nome_user'];
                                                                $loginUserLogImportCSV = $rowTbLogImportCSV['login_user'];
                                                                $appUserLogImportCSV   = $rowTbLogImportCSV['aplicacao'];
                                                                $dtUserLogImportCSV    = $rowTbLogImportCSV['data_log'];
                                                                $logUserImportCSV      = $rowTbLogImportCSV['log_dados'];

                                                                echo '<tr>';
                                                                    echo '<td>'.$i.'</td>';
                                                                    echo '<td>'.$idUserLogImportCSV.'</td>';
                                                                    echo '<td>'.$nomeUserLogImportCSV.'</td>';
                                                                    echo '<td>'.$loginUserLogImportCSV.'</td>';
                                                                    echo '<td>'.$appUserLogImportCSV.'</td>';
                                                                    echo '<td>'.$dtUserLogImportCSV.'</td>';
                                                                    echo '<td>'.$logUserImportCSV.'</td>';
                                                                echo '</tr>';

                                                                $i++;
                                                            }
                                                            echo '</tbody>';
                                                        ?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="dadosUsuariosOnline">
                                        <div class="card card-secondary card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">Registros de acessos</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="tbUsuariosOnline" class="table table-sm table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Data Inicio</th>
                                                                <th>Data Fim</th>
                                                                <th>ID User</th>
                                                                <th>Usuário</th>
                                                                <th>Login</th>
                                                            </tr>
                                                        </thead>
                                                        <?php 
                                                            $sqlTbLogs = "SELECT data_inicio, data_final, id_usuario, usuario, login FROM tb_visitas_dahshsat WHERE DATE_FORMAT(`data_inicio`, '%Y-%m-%d') BETWEEN(NOW() + INTERVAL -(7) DAY) AND NOW() ORDER BY id DESC";
                                                            $queryTbLogs = mysqli_query($conn, $sqlTbLogs);
                                                            $i=1;
                                                            echo "<tbody>";
                                                            while($rowTbLogs = mysqli_fetch_array($queryTbLogs)){
                                                                $dtIniUserOnline = $rowTbLogs['data_inicio'];
                                                                $dtFimUserOnline = $rowTbLogs['data_final'];
                                                                $idUserOnline    = $rowTbLogs['id_usuario'];
                                                                $nomeUserOnline  = $rowTbLogs['usuario'];
                                                                $loginUserOnline = $rowTbLogs['login'];

                                                                echo '<tr>';
                                                                    echo '<td>'.$i.'</td>';
                                                                    echo '<td>'.$dtIniUserOnline.'</td>';
                                                                    echo '<td>'.$dtFimUserOnline.'</td>';
                                                                    echo '<td>'.$idUserOnline.'</td>';
                                                                    echo '<td>'.$nomeUserOnline.'</td>';
                                                                    echo '<td>'.$loginUserOnline.'</td>';
                                                                echo '</tr>';

                                                                $i++;
                                                            }
                                                            echo '</tbody>';
                                                        ?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="dadosSessoesUsuario">
                                        <div class="card card-info card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">Sessões de Usuário</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="tbSessaoUsers" class="table table-sm table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Inicio Sessão</th>
                                                                <th>Token</th>
                                                                <th>ID User</th>
                                                                <th>Login User</th>
                                                                <th>Nome User</th>
                                                                <th>Tempo Inativo</th>
                                                                <th>Fim Sessão</th>
                                                                <th>Tempo Final</th>
                                                            </tr>
                                                        </thead>
                                                        <?php 
                                                            $sqlTbSessoes = "SELECT id, data_inicio, token, id_user, nome_user, login_user, tempo_inativo, data_fim, tempo_final FROM tb_sessoes_login_dashsat WHERE DATE_FORMAT(`data_inicio`, '%Y-%m-%d') BETWEEN(NOW() + INTERVAL -(7) DAY) AND NOW() ORDER BY id DESC";
                                                            $queryTbSessoes = mysqli_query($conn, $sqlTbSessoes);
                                                            $i=1;
                                                            echo "<tbody>";
                                                            while($rowTbSessoes = mysqli_fetch_array($queryTbSessoes)){
                                                                $dataIniSessao      = $rowTbSessoes['data_inicio'];
                                                                $tokenSessao        = $rowTbSessoes['token'];
                                                                $idUserSessao       = $rowTbSessoes['id_user'];
                                                                $loginUserSessao    = $rowTbSessoes['login_user'];
                                                                $nomeUserSessao     = $rowTbSessoes['nome_user'];
                                                                $tempoInativoSessao = $rowTbSessoes['tempo_inativo'];
                                                                $dataFimSessao      = $rowTbSessoes['data_fim'];
                                                                $tempoFinalSessao   = $rowTbSessoes['tempo_final'];

                                                                echo '<tr>';
                                                                    echo '<td>'.$i.'</td>';
                                                                    echo '<td>'.$dataIniSessao.'</td>';
                                                                    echo '<td>'.$tokenSessao.'</td>';
                                                                    echo '<td>'.$idUserSessao.'</td>';
                                                                    echo '<td>'.$loginUserSessao.'</td>';
                                                                    echo '<td>'.$nomeUserSessao.'</td>';
                                                                    echo '<td>'.$tempoInativoSessao.'</td>';
                                                                    echo '<td>'.$dataFimSessao.'</td>';
                                                                    echo '<td>'.$tempoFinalSessao.'</td>';
                                                                echo '</tr>';

                                                                $i++;
                                                            }
                                                            echo '</tbody>';
                                                        ?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="dadosRegistroBloqTemporario">
                                        <div class="card card-secondary card-outline">
                                            <div class="card-header">
                                                <h3 class="card-title">Registros de usuários com bloqueio temporário</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table id="tbUsuariosBloqueio" class="table table-sm table-bordered table-hover">
                                                        <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Data Bloqueio</th>
                                                            <th>ID User</th>
                                                            <th>Usuário</th>
                                                            <th>Login</th>
                                                            <th>Tentativas</th>
                                                            <th>Tempo</th>
                                                            <th>Status</th>
                                                        </tr>
                                                        </thead>
                                                        <?php
                                                        $sqlTbBloqueio = "SELECT id_user, nome_user, login_user, count_tentativas, data_bloqueio, tempo_desbloqueio, tempo_decorrido, status_bloqueio FROM cn_bloqueio_temp_usuarios_dashsat ORDER BY data_bloqueio DESC";
                                                        $queryTbBloqueio = mysqli_query($conn, $sqlTbBloqueio);
                                                        $i=1;
                                                        echo "<tbody>";
                                                        while($rowTbBloqueio = mysqli_fetch_array($queryTbBloqueio)){
                                                            $dtBloqueio     = $rowTbBloqueio['data_bloqueio'];
                                                            $idUserBloq     = $rowTbBloqueio['id_user'];
                                                            $nomeUserBloq   = $rowTbBloqueio['nome_user'];
                                                            $loginUserBloq  = $rowTbBloqueio['login_user'];
                                                            $tentativas     = $rowTbBloqueio['count_tentativas'];
                                                            $tempoDecorrido = $rowTbBloqueio['tempo_decorrido'];
                                                            $stBloqueio     = $rowTbBloqueio['status_bloqueio'];

                                                            echo '<tr>';
                                                                echo '<td>'.$i.'</td>';
                                                                echo '<td>'.$dtBloqueio.'</td>';
                                                                echo '<td>'.$idUserBloq.'</td>';
                                                                echo '<td>'.$nomeUserBloq.'</td>';
                                                                echo '<td>'.$loginUserBloq.'</td>';
                                                                echo '<td>'.$tentativas.'</td>';
                                                                echo '<td>'.$tempoDecorrido.'</td>';
                                                                echo '<td>'.$stBloqueio.'</td>';
                                                            echo '</tr>';

                                                            $i++;
                                                        }
                                                        echo '</tbody>';
                                                        ?>
                                                    </table>
                                                </div>
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
                </section>
            </div>
            
            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
            <footer class="main-footer">
                <strong>Copyright &copy; 2020-2020 <a href="#">Developed by TPVs</a>.</strong>
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.1
                </div>
            </footer>
        </div>
        <!-- ./wrapper -->

        <script src="../dist/js/pages/dashboard2.js"></script>
        <!-- jQuery -->
        <script src="../plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <!-- DataTables -->
        <script src="../plugins/datatables/jquery.dataTables.js"></script>
        <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
        <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
        <script src="../plugins/jszip/jszip.min.js"></script>
        <script src="../plugins/pdfmake/pdfmake.min.js"></script>
        <script src="../plugins/pdfmake/vfs_fonts.js"></script>
        <script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
        <script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
        <script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
        <!-- overlayScrollbars -->
        <script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- ChartJS -->
        <script src="../plugins/chart.js/Chart.min.js"></script>
        <!-- AdminLTE App -->
        <script src="../dist/js/adminlte.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="../dist/js/demo.js"></script>
        <!-- page script -->
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
        <script>
            $(function () {
                $('#tbLogsSistema').DataTable({
                    "reponsive": true,
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "buttons": ["excel", "pdf"]
                 }).buttons().container().appendTo('#tbLogsSistema_wrapper .col-md-6:eq(0)');
                $('#tbLogEmailAuto').DataTable({
                    "reponsive": true,
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "buttons": ["excel", "pdf"]
                 }).buttons().container().appendTo('#tbLogEmailAuto_wrapper .col-md-6:eq(0)');
                $('#tbLogImportCSV').DataTable({
                    "reponsive": true,
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "buttons": ["excel", "pdf"]
                 }).buttons().container().appendTo('#tbLogImportCSV_wrapper .col-md-6:eq(0)');
                $('#tbUsuariosOnline').DataTable({
                    "reponsive": true,
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "buttons": ["excel", "pdf"]
                 }).buttons().container().appendTo('#tbUsuariosOnline_wrapper .col-md-6:eq(0)');
                $('#tbSessaoUsers').DataTable({
                    "reponsive": true,
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "buttons": ["excel", "pdf"]
                 }).buttons().container().appendTo('#tbSessaoUsers_wrapper .col-md-6:eq(0)');
                $('#tbUsuariosBloqueio').DataTable({
                    "reponsive": true,
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 10,
                    "buttons": ["excel", "pdf"]
                 }).buttons().container().appendTo('#tbUsuariosBloqueio_wrapper .col-md-6:eq(0)');
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