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
    
    $sqlGroupModeloSat = "SELECT modelo_sat, count(id) AS qntd FROM ". DATA_CONFIG_BD["cn_tab_sat"] ." WHERE status = 'Ativo' GROUP BY modelo_sat";
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
    //SATs com falha
    $sqlCountSatFalha = "SELECT COUNT(loja) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_sats_falha"] ."";
    $queryCountSatFalha = mysqli_query($conn,$sqlCountSatFalha);
    $rowCountSatFalha = mysqli_fetch_assoc($queryCountSatFalha);
    $percCountSatFalha = round((($rowCountSatFalha['total_registros']/$totalGroupSat)*100),0);

    //Comunicação com a SEFAZ
    $sqlCountSatErrComunSefaz = "SELECT COUNT(loja) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_comun_sefaz"] ." WHERE n_cfes_memoria >= 1";
    $queryCountSatErrComunSefaz = mysqli_query($conn,$sqlCountSatErrComunSefaz);
    $rowCountSatErrComunSefaz = mysqli_fetch_assoc($queryCountSatErrComunSefaz);
    $percCountSatErrComunSefaz = round((($rowCountSatErrComunSefaz['total_registros']/$totalGroupSat)*100),0);

    //Transmissao com a SEFAZ
    $sqlCountSatErrTransmSefaz = "SELECT COUNT(loja) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_transm_sefaz"] ." WHERE numeros_cfes_memoria  >= 1";
    $queryCountSatErrTransmSefaz = mysqli_query($conn,$sqlCountSatErrTransmSefaz);
    $rowCountSatErrTransmSefaz = mysqli_fetch_assoc($queryCountSatErrTransmSefaz);
    $percCountSatErrTransmSefaz = round((($rowCountSatErrTransmSefaz['total_registros']/$totalGroupSat)*100),0);

    //XMLs Presos
    $sqlCountSatXmlPresos = "SELECT COUNT(loja) AS total_registros FROM ". DATA_CONFIG_BD["cn_tab_disk_used"] ."";
    $queryCountSatXmlPresos = mysqli_query($conn,$sqlCountSatXmlPresos);
    $rowCountSatXmlPresos = mysqli_fetch_assoc($queryCountSatXmlPresos);
    $percCountSatXmlPresos = round((($rowCountSatXmlPresos['total_registros']/$totalGroupSat)*100),0);

    //Charts
    $slqLojasMonitCentroCharts = "SELECT centro, total_lojas, total_monitoradas, percentual FROM ". DATA_CONFIG_BD["cn_tab_lojas_cr"] ."";
    $queryLojasMonitCentroCharts = mysqli_query($conn,$slqLojasMonitCentroCharts);
    while($resultLojasMonitCentroCharts = mysqli_fetch_assoc($queryLojasMonitCentroCharts)){
        $objLojasMonitCentroCharts[] = (object) $resultLojasMonitCentroCharts;
    }

    $sqlAlertas = "SELECT * FROM tb_dashsat_alertas";
    $queryAlertas = mysqli_query($conn,$sqlAlertas);
    $rowAlertas = mysqli_fetch_assoc($queryAlertas);

    $_SESSION['total_notificacoes'] = $rowCountSatErrComunSefaz['total_registros']+$rowCountSatXmlPresos['total_registros']+$rowCountSatFalha['total_registros']+$rowCountSatErrTransmSefaz['total_registros'];
    $_SESSION['total_reg_sat_s_comun_sefaz'] = $rowCountSatErrComunSefaz['total_registros'];
    $_SESSION['total_reg_sat_s_transm_sefaz'] = $rowCountSatErrTransmSefaz['total_registros'];
    $_SESSION['total_reg_sat_xml_presos'] = $rowCountSatXmlPresos['total_registros'];
    $_SESSION['total_reg_sat_falha'] = $rowCountSatFalha['total_registros'];

    unset($_SESSION['tempNome']);
	unset($_SESSION['tempEmail']);
    unset($_SESSION['tempLogin']);
    
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
                                <h1>Painel</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="home.php?token=<?php echo $token;?>">Home</a></li>
                                    <li class="breadcrumb-item active">Painel</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <section class="content">
                    <div class="container-fluid">
                        <h5 class="mt-4 mb-2">Informações S&#64;T</h5>
                        <div class="row">
                            <div class="col-md-3 col-sm-6 col-12">
                            <?php foreach($objGroupSat as $key=>$val){ ?>
                                <div class="info-box bg-success">
                                    <span class="info-box-icon">
                                        <i class="fas fa-ticket-alt"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?php print $objGroupSat[$key]->modelo_sat?></span>
                                            <span class="info-box-number"><?php print $objGroupSat[$key]->qntd?></span>
                                            <div class="progress">
                                            <div class="progress-bar" style="width:<?php print round((($objGroupSat[$key]->qntd/$totalGroupSat)*100),0)."%";?>">
                                            </div>
                                        </div>
                                        <span class="progress-description">
                                            <?php print round((($objGroupSat[$key]->qntd/$totalGroupSat)*100),0)."%";?> do total de <?php print $totalGroupSat ?> de S&#64;Ts.
                                        </span>
                                    </div>
                                </div>
                                <?php }?>
                            </div>
                            <div class="col-md-3 col-sm-6 col-12">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-satellite-dish"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Sem Comunicar</span>
                                        <span class="info-box-number"><?php print $rowCountSatErrComunSefaz['total_registros']?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width:<?php print $percCountSatErrComunSefaz."%";?>">
                                            </div>
                                        </div>
                                        <span class="progress-description">
                                            <?php print $percCountSatErrComunSefaz."%";?> do total de <?php print $totalGroupSat ?> de S&#64;Ts.
                                        </span>
                                    </div>
                                </div>
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-satellite-dish"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">Sem Transmitir</span>
                                        <span class="info-box-number"><?php print $rowCountSatErrTransmSefaz['total_registros']?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width:<?php print $percCountSatErrTransmSefaz."%";?>">
                                            </div>
                                        </div>
                                        <span class="progress-description">
                                            <?php print $percCountSatErrTransmSefaz."%";?> do total de <?php print $totalGroupSat ?> de S&#64;Ts.
                                        </span>
                                    </div>
                                </div>
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="far fa-hdd"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">XMLs em Memória</span>
                                        <span class="info-box-number"><?php print $rowCountSatXmlPresos['total_registros']?></span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width:<?php print $percCountSatXmlPresos."%";?>">
                                            </div>
                                        </div>
                                        <span class="progress-description">
                                            <?php print $percCountSatXmlPresos."%";?> do total de <?php print $totalGroupSat ?> de S&#64;Ts.
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-2 col-12"> 
                                <div class="info-box bg-secondary">
                                    <div class="table-responsive-sm">
                                        <h5 class="mr-1 ml-1 pr-1 pl-1">Top 10 Lojas s/ Comunicar</h5>
                                        <div class="d-flex">
                                            <p class="ml-auto d-flex flex-column text-right">
                                                <a href="../export/export_top_dez.php?id=1&str=top_10_s_comunicar&param=<?php echo $rowAlertas['n_dias_cupons_acumulados']; ?>" class="btn btn-tool btn-sm">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </p>
                                        </div>
                                        <table id="tbTopSComunSefaz" class="table table-sm table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <td>#</td>
                                                    <td>Loja</td>
                                                    <td>Caixa</td>
                                                    <td>Dias</td>
                                                    <td>View</td>
                                                </tr>
                                                <?php
                                                    $sqlSatComunSefaz = "SELECT sat, loja, caixa, n_dias, n_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_comun_sefaz"] ." WHERE caixa != '01' AND n_cfes_memoria > 0 AND n_dias > ". $rowAlertas['n_dias_cupons_acumulados'] ." ORDER BY n_dias  DESC LIMIT 10";
                                                    $querySatComunSefaz = mysqli_query($conn,$sqlSatComunSefaz);
                                                    echo "<tbody>";
                                                    $i=1;
                                                    while($resultSatComunSefaz = mysqli_fetch_array($querySatComunSefaz)){
                                                        $id             = $i;
                                                        $sat            = $resultSatComunSefaz['sat'];
                                                        $numLoja        = $resultSatComunSefaz['loja'];
                                                        $numCaixa       = $resultSatComunSefaz['caixa'];
                                                        $nCfesMemoria   = $resultSatComunSefaz['n_cfes_memoria'];
                                                        $nDiasSemComun  = $resultSatComunSefaz['n_dias'];
                                                        
                                                        echo "<tr>";
                                                            echo "<td>".$id."</td>";
                                                            echo "<td>".$numLoja."</td>";
                                                            echo "<td>".$numCaixa."</td>";
                                                            echo "<td>".$nDiasSemComun."</td>";
                                                            echo "<td><a href='getsat.php?token=".$token."&idsat=".$sat."&page=98b1ead80be34c0c6320b921adc83368'><i class='fas fa-binoculars'></i></td>";
                                                        echo "</tr>";
                                                        $i++;
                                                    }
                                                    echo "</tbody>";
                                                ?>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-2 col-12">
                                <div class="info-box bg-secondary">
                                    <div class="table-responsive-sm">
                                        <h5 class="mr-1 ml-1 pr-1 pl-1">Top 10 Lojas s/ Transmitir</h5>
                                        <div class="d-flex">
                                            <p class="ml-auto d-flex flex-column text-right">
                                                <a href="../export/export_top_dez.php?id=2&str=top_10_s_transmitir&param=<?php echo $rowAlertas['n_dias_cupons_acumulados']; ?>" class="btn btn-tool btn-sm">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </p>
                                        </div>
                                        <table id="tbTopSTransmSefaz" class="table table-sm table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <td>#</td>
                                                    <td>Loja</td>
                                                    <td>Caixa</td>
                                                    <td>Dias</td>
                                                    <td>Cupons</td>
                                                    <td>View</td>
                                                </tr>
                                            </thead>
                                            <?php
                                                $sqlSatTransmSefaz = "SELECT sat,loja, caixa, n_dias, numeros_cfes_memoria FROM ". DATA_CONFIG_BD["cn_tab_transm_sefaz"] ." WHERE caixa != '01' AND numeros_cfes_memoria > 0 AND n_dias > ". $rowAlertas['n_dias_cupons_acumulados'] ." ORDER BY numeros_cfes_memoria DESC LIMIT 10";
                                                $querySatTransmSefaz = mysqli_query($conn,$sqlSatTransmSefaz);
                                                echo "<tbody>";
                                                $i=1;
                                                while($resultSatTransmSefaz = mysqli_fetch_array($querySatTransmSefaz)){
                                                    $id             = $i;
                                                    $sat            = $resultSatTransmSefaz['sat'];
                                                    $numLoja        = $resultSatTransmSefaz['loja'];
                                                    $numCaixa       = $resultSatTransmSefaz['caixa'];
                                                    $numCfesMem     = $resultSatTransmSefaz['numeros_cfes_memoria'];
                                                    $nDiasSemTransm  = $resultSatTransmSefaz['n_dias'];
                                                    
                                                    echo "<tr>";
                                                        echo "<td>".$id."</td>";
                                                        echo "<td>".$numLoja."</td>";
                                                        echo "<td>".$numCaixa."</td>";
                                                        echo "<td>".$nDiasSemTransm."</td>";
                                                        echo "<td>".$numCfesMem."</td>";
                                                        echo "<td><a href='getsat.php?token=".$token."&idsat=".$sat."&page=98b1ead80be34c0c6320b921adc83368'><i class='fas fa-binoculars'></i></td>";
                                                    echo "</tr>";
                                                    $i++;
                                                }
                                                echo "</tbody>";
                                            ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="load col-sm-4" style="display: block;" id="div-preloaderx"><img src="../dist/img/preloader.gif" width="48" height="48"></div>
                                <div class="card" id="div-block-iminente" style="display: none;">
                                    <div class="card-header">
                                        <h3 class="card-title">Lista de loja com possível bloqueio Iminente</h3>
                                        <div class="card-tools">
                                            <a href="#" class="btn btn-tool btn-sm"></a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive-sm">
                                            <table id="tbBlockIminente" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <td>#</td>
                                                        <td>Loja</td>
                                                        <td>Caixas s/ Comunicar SEFAZ</td>
                                                        <td>Total caixas Loja</td>
                                                        <td>% s/ Comunicar</td>
                                                        <td>Status Loja</td>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyBlockIminente"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="load" style="display: block;" id="div-preloader"><img src="../dist/img/preloader.gif" width="48" height="48"></div>
                                <!-- data-card-widget="card-refresh" data-source="#?" data-source-selector="#card-refresh-content" -->
                                <div class="card text-sm" id="div-lojas-monitoradas" style="display: none;">
                                    <div class="card-header">
                                        <h3 class="card-title">Lojas Monitoradas por CR</h3>
                                        <div class="card-tools">
                                            <a href="#" class="btn btn-tool btn-sm">
                                            </a>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="d-flex table-responsive-sm">
                                            <table id="tbLojasMonitoradasCR" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Centro</th>
                                                        <th>Total Lojas</th>
                                                        <th>Total Lojas Monitoradas</th>
                                                        <th>%</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyLojasMonitoradas"></tbody>                                                
                                            </table>
                                        </div>
                                        <?php
                                            $total = 0;
                                            $monitoradas = 0;
                                            $percent = 0.0;
                                            $maior = 0;

                                            foreach ($objLojasMonitCentroCharts as $key=>$val) {
                                                $total = $total + $objLojasMonitCentroCharts[$key]->total_lojas;
                                                $monitoradas += $objLojasMonitCentroCharts[$key]->total_monitoradas;
                                                if($objLojasMonitCentroCharts[$key]->total_lojas>$maior){
                                                    $maior = $objLojasMonitCentroCharts[$key]->total_lojas;
                                                }
                                            }
                                            $percent = ($monitoradas/$total)*100;
                                    
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card text-sm">
                                    <div class="card-header">
                                        <h3 class="card-title"></h3>
                                        <div class="card-tools">
                                            <a href="#" class="btn btn-tool btn-sm">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <p class="d-flex flex-column">
                                                <span class="text-bold text-lg"><?php print $total;?></span>
                                                <span>Total de loja São Paulo</span>
                                            </p>
                                            <p class="ml-auto d-flex flex-column text-right">
                                                <span class="text-success">
                                                    <i class="fas fa-arrow-up"></i><?php print round($percent,2);?>%
                                                </span>
                                                <span>Percentual lojas</span>
                                            </p>
                                        </div>
                                        <div class="position-relative mb-4">
                                            <canvas id="lojas-cr-chart" height="200"></canvas>
                                        </div>
                                        <div class="d-flex flex-row justify-content-end">
                                            <span class="mr-2">
                                                <i class="fas fa-square text-primary"></i> Total de lojas
                                            </span>
                                            <span class="mr-2">
                                                <i class="fas fa-square text-warning"></i> Monitoradas
                                            </span>
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
        <!-- jQuery -->
        <script src="../plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- DataTables -->
        <script src="../plugins/datatables/jquery.dataTables.js"></script>
        <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
        <!-- overlayScrollbars -->
        <script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <!-- AdminLTE App -->
        <script src="../dist/js/adminlte.js"></script>
        <script src="../plugins/chart.js/Chart.min.js"></script>
        <!-- OPTIONAL SCRIPTS -->
        <script src="../dist/js/demo.js"></script>
        <script type="text/javascript" src="../plugins/pace-master/pace.min.js"></script>
        <script text="text/javascript">
            $(document).ready(function(){
                $.ajax({
                    url: "../processo/get_lojas_monitoradas.php",
                    type: "POST",
                    dataType: "json",
                    data: {get_lojas_monitoradas:1},
                    success: function(data){
                        if(data){
                            $('#div-preloader').css("display","none")
                            $('#div-lojas-monitoradas').css("display","block")
                            for(var i=0;data.length>i;i++){
                                $('#tbodyLojasMonitoradas').append('<tr><td>'+(i+1)+'</td><td><a href="getlojas.php?token=<?php echo $token;?>&id=1&str='+data[i].centro+'&page=790aee404dc63df1744bf9cc59c12535">'+data[i].centro+'</a></td><td>'+data[i].total_lojas+'</td><td>'+data[i].total_monitoradas+'</td><td>'+parseFloat(data[i].percentual).toFixed(2)+'</td></tr>')
                            }
                            //$('#div-lojas-monitoradas').css("display","block")
                        }
                    }
                });
                $.ajax({
                    url: "../processo/get_tables.php",
                    type: "POST",
                    dataType: "json",
                    data: {idTable:13},
                    success: function(data){
                        if(data){
                            $('#div-preloaderx').css("display","none")
                            $('#div-block-iminente').css("display","block")
                            for(var i=0;data.length>i;i++){
                                $('#tbodyBlockIminente').append('<tr class="table-warning"><td>'+(i+1)+'</td><td>'+data[i].shop+'</td><td>'+data[i].n_pos+'</td><td>'+data[i].tpvs+'</td><td>'+parseFloat(data[i].perc).toFixed(2)+'</td><td>Atenção</td></tr>')
                            }
                            $('#tbBlockIminente').DataTable({
                                "paging": true,
                                "lengthChange": false,
                                "searching": true,
                                "ordering": true,
                                "info": false,
                                "autoWidth": true,
                                "pageLength": 5
                            });
                        }else{
                            $('#div-preloaderx').css("display","none")
                            $('#div-block-iminente').css("display","block")
                            $('#tbBlockIminente').DataTable({
                                "paging": true,
                                "lengthChange": false,
                                "searching": true,
                                "ordering": true,
                                "info": false,
                                "autoWidth": true,
                                "pageLength": 5
                            });
                        }
                    }
                });
            });
        </script>
        <script text="text/javascript">
            $(function () {
                'use strict'

                var ticksStyle = {
                    fontColor: '#495057',
                    fontStyle: 'bold'
                }

                var mode      = 'index'
                var intersect = true

                var $lojascrChart = $('#lojas-cr-chart')
                var lojascrChart  = new Chart($lojascrChart, {
                    data   : {
                    labels  : [<?php 
                                foreach ($objLojasMonitCentroCharts as $key=>$val) {
                                    echo '"'. $objLojasMonitCentroCharts[$key]->centro . '",';
                                }                            
                            ?>],
                    datasets: [{
                        type                : 'line',
                        data                :[<?php 
                                                foreach ($objLojasMonitCentroCharts as $key=>$val) {
                                                    echo '"'. $objLojasMonitCentroCharts[$key]->total_lojas . '",';
                                                }
                                            ?>],
                        backgroundColor     : 'transparent',
                        borderColor         : '#007bff',
                        pointBorderColor    : '#007bff',
                        pointBackgroundColor: '#007bff',
                        fill                : false
                        // pointHoverBackgroundColor: '#007bff',
                        // pointHoverBorderColor    : '#007bff'
                    },
                        {
                        type                : 'line',
                        data                : [<?php 
                                                foreach ($objLojasMonitCentroCharts as $key=>$val) {
                                                    echo '"' . $objLojasMonitCentroCharts[$key]->total_monitoradas . '",';
                                                }
                                            ?>],
                        backgroundColor     : 'tansparent',
                        borderColor         : '#ffc107',
                        pointBorderColor    : '#ffc107',
                        pointBackgroundColor: '#ffc107',
                        fill                : false
                        // pointHoverBackgroundColor: '#ced4da',
                        // pointHoverBorderColor    : '#ced4da'
                        }]
                    },
                    options: {
                    maintainAspectRatio: false,
                    tooltips           : {
                        mode     : mode,
                        intersect: intersect
                    },
                    hover              : {
                        mode     : mode,
                        intersect: intersect
                    },
                    legend             : {
                        display: false
                    },
                    scales             : {
                        yAxes: [{
                        // display: false,
                        gridLines: {
                            display      : true,
                            lineWidth    : '4px',
                            color        : 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks    : $.extend({
                            beginAtZero : true,
                            suggestedMax: <?php print $maior;?>,
                            stepSize: 25
                        }, ticksStyle)
                        }],
                        xAxes: [{
                        display  : true,
                        gridLines: {
                            display: false
                        },
                        ticks    : ticksStyle
                        }]
                    }
                    }
                })
            })
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