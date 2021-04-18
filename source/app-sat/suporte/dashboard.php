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

    $sqlCountSAT1 = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE 'SAT-1.0'";
    $queryCountSAT1 = mysqli_query($conn,$sqlCountSAT1);
    $rowCountSAT1 = mysqli_fetch_assoc($queryCountSAT1);

    $sqlCountSAT2 = "SELECT COUNT(sat) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE modelo_sat LIKE 'SAT-2.0'";
    $queryCountSAT2 = mysqli_query($conn,$sqlCountSAT2);
    $rowCountSAT2 = mysqli_fetch_assoc($queryCountSAT2);

    $sqlGroupFirmware = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." GROUP BY firmware";
    $queryGroupFirmware = mysqli_query($conn,$sqlGroupFirmware);
    
    $sqlGroupFirmware1 = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." GROUP BY firmware";
    $queryGroupFirmware1 = mysqli_query($conn,$sqlGroupFirmware1);

    $sqlGroupLayout1 = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." GROUP BY layout";
    $queryGroupLayout1 = mysqli_query($conn,$sqlGroupLayout1);

    $sqlGroupLayout = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." GROUP BY layout";
    $queryGroupLayout = mysqli_query($conn,$sqlGroupLayout);

    $sqlGroupDtExpirCert = "SELECT YEAR(data_fim_ativacao) AS ano,modelo_sat, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE YEAR(data_fim_ativacao) != 0 GROUP BY YEAR(data_fim_ativacao) ORDER BY YEAR(data_fim_ativacao) ASC";
    $queryGroupDtExpirCert = mysqli_query($conn,$sqlGroupDtExpirCert);

    $sqlGroupDtExpirCert1 = "SELECT YEAR(data_fim_ativacao) AS ano,modelo_sat, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE YEAR(data_fim_ativacao) != 0 GROUP BY YEAR(data_fim_ativacao) ORDER BY YEAR(data_fim_ativacao) ASC";
    $queryGroupDtExpirCert1 = mysqli_query($conn,$sqlGroupDtExpirCert1);

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
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="dashboard.php?token=<?php echo $token;?>" class="nav-link active">
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
                                <h1>Dashboard</h1>
                            </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="home.php?token=<?php echo $token;?>">Home</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- DONUT CHART -->
                                <div class="card card-info card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Modelos S&#64;T</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="donutChartModelosSat" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                <!-- /.card-body -->
                                </div>
                            </div>                            
                            <div class="col-sm-6">
                                <div class="card card-info card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Modelos S&#64;T</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive-sm">
                                            <table id="tbModelosSAT" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Modelo S&#64;T</th>
                                                        <th>Quantidade</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                    $sqlGroupModelo = "SELECT modelo_sat, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." GROUP BY modelo_sat";
                                                    $queryGroupModelo = mysqli_query($conn,$sqlGroupModelo);
                                                    echo '<tbody>';
                                                    $i=1;
                                                    while($rowGroupModelo = mysqli_fetch_array($queryGroupModelo)){
                                                        $id         = $i;
                                                        $modeloSAT  = $rowGroupModelo['modelo_sat'];
                                                        $qntdModelo = $rowGroupModelo['qntd'];

                                                        echo '<tr>';
                                                            echo '<td>'.$id.'</td>';
                                                            echo "<td><a href='getdashsat.php?token=".$token."&str=".$modeloSAT."&id=1'>".$modeloSAT."</a></td>";
                                                            echo '<td>'.$qntdModelo.'</td>';
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
                            <!-- /.card -->
                        </div>
                        <!-- /.row -->
                        <div class="row">
                            <!-- PIE CHART -->
                            <div class="col-sm-6">
                                <div class="card card-warning card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Firmware S&#64;T</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="pieChartFirmwareSAT" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                <!-- /.card-body -->
                                </div>
                            <!-- /.card -->
                            </div>
                            <div class="col-sm-6">
                                <div class="card card-warning card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Firmware S&#64;T</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive-sm">
                                            <table id="tbFirmwareSAT" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Firmware</th>
                                                        <th>Quantidade</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                    $sqlGroupFW = "SELECT firmware, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." GROUP BY firmware ORDER BY firmware ASC";
                                                    $queryGroupFW = mysqli_query($conn,$sqlGroupFW);
                                                    echo '<tbody>';
                                                    $i=1;
                                                    while($rowGroupFW = mysqli_fetch_array($queryGroupFW)){
                                                        $id     = $i;
                                                        $fw     = $rowGroupFW['firmware'];
                                                        $qntdFW = $rowGroupFW['qntd'];

                                                        echo '<tr>';
                                                            echo '<td>'.$id.'</td>';
                                                            echo "<td><a href='getdashsat.php?token=".$token."&str=".$fw."&id=2'>".$fw."</a></td>";
                                                            echo '<td>'.$qntdFW.'</td>';
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
                        <div class="row">
                            <div class="col-sm-6">
                                <!-- DONUT CHART -->
                                <div class="card card-success card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Layout SAT</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="donutChartLayoutSat" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                    </div>
                                <!-- /.card-body -->
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card card-success card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Layout S&#64;T</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive-sm">
                                            <table id="tbLayoutSAT" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Layout</th>
                                                        <th>Quantidade</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                    $sqlGroupLayout2 = "SELECT layout, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." GROUP BY layout";
                                                    $queryGroupLayout2 = mysqli_query($conn,$sqlGroupLayout2);
                                                    echo '<tbody>';
                                                    $i=1;
                                                    while($rowGroupLayout2 = mysqli_fetch_array($queryGroupLayout2)){
                                                        $id         = $i;
                                                        $layout     = $rowGroupLayout2['layout'];
                                                        $qntdLayout = $rowGroupLayout2['qntd'];

                                                        echo '<tr>';
                                                            echo '<td>'.$id.'</td>';
                                                            echo "<td><a href='getdashsat.php?token=".$token."&str=".$layout."&id=3'>".$layout."</a></td>";
                                                            echo '<td>'.$qntdLayout.'</td>';
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
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card card-danger card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Expiração do Certificado Digital</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart">
                                            <canvas id="barChartAnoVencimento" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                    </div>
                                <!-- /.card-body -->
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card card-danger card-outline">
                                    <div class="card-header">
                                        <h3 class="card-title">Expiração do Certificado Digital</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive-sm">
                                            <table id="tbExpCertDigital" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Ano</th>
                                                        <th>Modelo S&#64;T</th>
                                                        <th>Quantidade</th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                    $sqlGroupDtExpCert = "SELECT YEAR(data_fim_ativacao) AS ano,modelo_sat, COUNT(sat) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE YEAR(data_fim_ativacao) != 0 GROUP BY YEAR(data_fim_ativacao) ORDER BY YEAR(data_fim_ativacao) ASC";
                                                    $queryGroupDtExpCert = mysqli_query($conn,$sqlGroupDtExpCert);
                                                    echo '<tbody>';
                                                    $i=1;
                                                    while($rowGroupDtExpCert = mysqli_fetch_array($queryGroupDtExpCert)){
                                                        $id        = $i;
                                                        $ano       = $rowGroupDtExpCert['ano'];
                                                        $modeloSAT = $rowGroupDtExpCert['modelo_sat'];
                                                        $qntdAno   = $rowGroupDtExpCert['qntd'];

                                                        echo '<tr>';
                                                            echo '<td>'.$id.'</td>';
                                                            echo "<td><a href='getdashsat.php?token=".$token."&str=".$ano."&id=4'>".$ano."</a></td>";
                                                            echo '<td>'.$modeloSAT.'</td>';
                                                            echo '<td>'.$qntdAno.'</td>';
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
                    </div><!-- /.container-fluid -->
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
                <!-- /.content -->
            </div>
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
        <script src="../dist/js/pages/dashboard2.js"></script>
        <!-- jQuery -->
        <script src="../plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <!-- DataTables -->
        <script src="../plugins/datatables/jquery.dataTables.js"></script>
        <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
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
            $('#tbModelosSAT').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 3
            });
            $('#tbFirmwareSAT').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 3
            });
            $('#tbLayoutSAT').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 3
            });
            $('#tbExpCertDigital').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "pageLength": 3
            });
        });
        $(function () {
            
            //-------------
            //- DONUT CHART -
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
            //- PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var dataChars = {
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
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de','#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
                    }
                ]
            }
            var pieChartCanvas = $('#pieChartFirmwareSAT').get(0).getContext('2d')
            var pieData        = dataChars;
            var pieOptions     = {
            maintainAspectRatio : false,
            responsive : true,
            }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            var pieChart = new Chart(pieChartCanvas, {
            type: 'doughnut',
            data: pieData,
            options: pieOptions      
            })

            //-------------
            //- PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var dataChars = {
                labels: [<?php 
                        while($rowGroupLayout = mysqli_fetch_array($queryGroupLayout)){
                            echo '"'. $rowGroupLayout['layout'].'",';
                        }
                ?>],
                datasets: [
                    {
                        data: [<?php 
                        while($rowGroupLayout = mysqli_fetch_array($queryGroupLayout1)){
                            echo '"'. $rowGroupLayout['qntd'].'",';
                        }
                ?>],
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de','#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
                    }
                ]
            }
            var pieChartCanvas = $('#donutChartLayoutSat').get(0).getContext('2d')
            var pieData        = dataChars;
            var pieOptions     = {
            maintainAspectRatio : false,
            responsive : true,
            }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            var pieChart = new Chart(pieChartCanvas, {
            type: 'doughnut',
            data: pieData,
            options: pieOptions      
            })

            //-------------
            //- BAR CHART -
            //-------------
            var barChartCanvas = $('#barChartAnoVencimento').get(0).getContext('2d')
            var barChartData =  {
                labels: [<?php 
                        while($rowGroupDtExpirCert = mysqli_fetch_array($queryGroupDtExpirCert)){
                            echo '"'. $rowGroupDtExpirCert['ano'].'",';
                        }
                ?>],
                datasets: [
                    {
                        label:'Expiração do Certificado Digital',   
                        data: [<?php 
                            while($rowGroupDtExpirCert = mysqli_fetch_array($queryGroupDtExpirCert1)){
                                echo '"'. $rowGroupDtExpirCert['qntd'].'",';
                            }
                        ?>],
                backgroundColor     : '#17a2b8',
                pointRadius         : false,
                pointColor          : '#3b8bba',
                pointStrokeColor    : 'rgba(60,141,188,1)',
                pointHighlightFill  : '#fff',
                pointHighlightStroke: 'rgba(60,141,188,1)',
                borderColor         : 'rgba(60,141,188,0.8)',
                    }
                ]
            }


            var barChartOptions = {
                responsive              : true,
                maintainAspectRatio     : false,
                datasetFill             : false
            }
            var barChart = new Chart(barChartCanvas, {
            type: 'bar', 
            data: barChartData,
            options: barChartOptions
            })
        })
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