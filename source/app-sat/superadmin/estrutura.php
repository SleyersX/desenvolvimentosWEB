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
                                <a href="estrutura.php?token=<?php echo $token;?>" class="nav-link active">
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
                                <a href="sistema.php?token=<?php echo $token;?>" class="nav-link">
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
                                <h1>Estrutura</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="home.php?token=<?php echo $token;?>">Home</a></li>
                                    <li class="breadcrumb-item active">Estrutura</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="load" style="display: block;" id="div-preloader"> <p align="center"><img src="../dist/img/preloader.gif" width="48" height="48" ></p></div>
                                <div class="card" id="div-estrutura" style="display: none;">
                                    <div class="card-header">
                                        <h3 class="card-title">Lista de lojas</h3>
                                        <div class="card-tools">
                                            <a href="../export/export_estrutura.php" class="btn btn-tool btn-sm">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive-sm">
                                            <table id="tbDadosListaLojas" class="table table-sm table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Loja</th>
                                                        <th>Endereço</th>
                                                        <th>UF</th>
                                                        <th>Cidade</th>
                                                        <th>CNPJ</th>
                                                        <th>View</th> 
                                                    </tr>
                                                </thead>
                                                <tbody id="tbodyEstrutura">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form name="">
                            <div class="modal fade" id="viewDadosSat" tabindex="-1" role="dialog" aria-labelledby="viewDadosSat" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <a href="#" class="btn-sm btn-tools close" data-dismiss="modal" aria-label="Close">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                        <div class="modal-body">
                                            <form>
                                                <div class="card">
                                                    <div class="card-header">
                                                        <input for="text" class="form-control form-control-sm" id="numeroLoja" name="numeroLoja" readonly style="border-left: none;border-right: none;border-top: none; border-bottom: none;background-color: transparent;font-size:20px;font-weight:bold;">
                                                        <div class="card-tools">
                                                            <a href="#" class="btn btn-tool btn-sm">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_1">
                                                                <img src="" class="col-sm-4 img-fluid" id="img_pdv_1">
                                                                <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 1</b></div>
                                                                <div style="position: absolute;bottom: 35px;left:100px;font-size:13px">SAT</div>
                                                                <div style="position: absolute;bottom: 20px;left:100px;font-size:15px"><a href="#" id="link_pdv_1"><span id="span_pdv_1"></span></a></div>
                                                            </div>
                                                            <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_2">
                                                                <img src="" class="col-sm-4 img-fluid" id="img_pdv_2">
                                                                <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 2</b></div>
                                                                <div style="position: absolute;bottom: 35px;left:100px;font-size:13px">SAT</div>
                                                                <div style="position: absolute;bottom: 20px;left:100px;font-size:15px"><a href="#" id="link_pdv_2"><span id="span_pdv_2"></span></a></div>
                                                            </div>
                                                            <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_3">
                                                                <img src="" class="col-sm-4 img-fluid" id="img_pdv_3">
                                                                <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 3</b></div>
                                                                <div style="position: absolute;bottom: 35px;left:100px;font-size:13px">SAT</div>
                                                                <div style="position: absolute;bottom: 20px;left:100px;font-size:15px"><a href="#" id="link_pdv_3"><span id="span_pdv_3"></span></a></div>
                                                            </div>
                                                            <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_4">
                                                                <img src="" class="col-sm-4 img-fluid" id="img_pdv_4">
                                                                <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 4</b></div>
                                                                <div style="position: absolute;bottom: 35px;left:100px;font-size:13px">SAT</div>
                                                                <div style="position: absolute;bottom: 20px;left:100px;font-size:15px"><a href="#" id="link_pdv_4"><span id="span_pdv_4"></span></a></div>
                                                            </div>
                                                            <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_5">
                                                                <img src="" class="col-sm-4 img-fluid" id="img_pdv_5">
                                                                <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 5</b></div>
                                                                <div style="position: absolute;bottom: 35px;left:100px;font-size:13px">SAT</div>
                                                                <div style="position: absolute;bottom: 20px;left:100px;font-size:15px"><a href="#" id="link_pdv_5"><span id="span_pdv_5"></span></a></div>
                                                            </div>
                                                            <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_6">
                                                                <img src="" class="col-sm-4 img-fluid" id="img_pdv_6">
                                                                <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 6</b></div>
                                                                <div style="position: absolute;bottom: 35px;left:100px;font-size:13px">SAT</div>
                                                                <div style="position: absolute;bottom: 20px;left:100px;font-size:15px"><a href="#" id="link_pdv_6"><span id="span_pdv_6"></span></a></div>
                                                            </div>
                                                            <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_7">
                                                                <img src="" class="col-sm-4 img-fluid" id="img_pdv_7">
                                                                <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 7</b></div>
                                                                <div style="position: absolute;bottom: 35px;left:100px;font-size:13px">SAT</div>
                                                                <div style="position: absolute;bottom: 20px;left:100px;font-size:15px"><a href="#" id="link_pdv_7"><span id="span_pdv_7"></span></a></div>
                                                            </div>
                                                            <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_8">
                                                                <img src="" class="col-sm-4 img-fluid" id="img_pdv_8">
                                                                <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 8</b></div>
                                                                <div style="position: absolute;bottom: 35px;left:100px;font-size:13px">SAT</div>
                                                                <div style="position: absolute;bottom: 20px;left:100px;font-size:15px"><a href="#" id="link_pdv_8"><span id="span_pdv_8"></span></a></div>
                                                            </div>
                                                            <div class="col-sm-3 pb-4 mb-2" style="display: none;" id="pdv_9">
                                                                <img src="" class="col-sm-4 img-fluid" id="img_pdv_9">
                                                                <div style="position: absolute;bottom: 60px;left:70px;"><b>PDV 9</b></div>
                                                                <div style="position: absolute;bottom: 35px;left:100px;font-size:13px">SAT</div>
                                                                <div style="position: absolute;bottom: 20px;left:100px;font-size:15px"><a href="#" id="link_pdv_9"><span id="span_pdv_9"></span></a></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form> 
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

        <!-- OPTIONAL SCRIPTS -->
        <script src="../dist/js/demo.js"></script>
        <script type="text/javascript" src="../plugins/pace-master/pace.min.js"></script>
        <script text="text/javascript">
            function click_check_box_img(id){
                document.getElementById('id-img').value = id;
                 
            }
        </script>
        <script text="text/javascript">
            $(document).ready(function(){
                $.ajax({
                    url: "../processo/get_tables.php",
                    type: "POST",
                    dataType: "json",
                    data: {idTable:3},
                    success: function(data){
                        if(data){
                            $('#div-preloader').css("display","none")
                            $('#div-estrutura').css("display","block")
                            for(var i=0;data.length>i;i++){
                                $('#tbodyEstrutura').append('<tr><td>DIA-'+data[i].loja+'</td><td>'+data[i].DireFisc+'</td><td>'+data[i].CodiEstaClie+'</td><td>'+data[i].LocaFisc+'</td><td>'+data[i].CodiIden+'</td><td>&nbsp; &nbsp; &nbsp; &nbsp;<a href="#" data-toggle="modal" data-target="#viewDadosSat" data-loja="'+data[i].loja+'" data-tpvs="'+data[i].n_tpvs_setvari+'" data-token="<?php print $token;?>"><i class="fas fa-sitemap"></i></a></td></tr>')
                            }
                            $(function () {
                                $('#tbDadosListaLojas').DataTable({
                                "paging": true,
                                "lengthChange": false,
                                "searching": true,
                                "ordering": true,
                                "info": true,
                                "autoWidth": true,
                                "pageLength": 6,
                                });
                            });
                        }
                    }
                });
            });
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
            $('#viewDadosSat').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var numeroLoja = button.data('loja')
                var nTpvs = button.data('tpvs')
                var token = button.data('token')

                var modal = $(this)
                modal.find('#numeroLoja').val("Estrutura Loja - " + numeroLoja)
                for(var i = 1; i <= 9 ; i++){
                    if(i<=nTpvs){
                        getSAT(i);
                    }else{
                        modal.find('#pdv_'+i).css("display","none")
                    }
                }
                function getSAT(x){
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
                                modal.find('#pdv_'+x).css("display","block")
                                modal.find('#link_pdv_'+x).attr('href','#?')
                                modal.find('#img_pdv_'+x).attr('src','../dist/img/pdv_off.png')
                                modal.find('#span_pdv_'+x).html(data)
                            }else{
                                verificaBloq(data,x);
                            }
                        }
                    });
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
                                modal.find('#pdv_'+y).css("display","block")
                                modal.find('#link_pdv_'+y).attr('href','getsat.php?token='+token+'&idsat='+sat+'&page=f9dbbb417e1282002a8aecd54173b1a4')
                                modal.find('#img_pdv_'+y).attr('src','../dist/img/pdv_bloq.png')
                                modal.find('#span_pdv_'+y).html(sat)
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
                                modal.find('#pdv_'+y).css("display","block")
                                modal.find('#link_pdv_'+y).attr('href','getsat.php?token='+token+'&idsat='+sat+'&page=f9dbbb417e1282002a8aecd54173b1a4')
                                modal.find('#img_pdv_'+y).attr('src','../dist/img/pdv_desatualizado.png')
                                modal.find('#span_pdv_'+y).html(sat)
                            }else{    
                                modal.find('#pdv_'+y).css("display","block")
                                modal.find('#link_pdv_'+y).attr('href','getsat.php?token='+token+'&idsat='+sat+'&page=f9dbbb417e1282002a8aecd54173b1a4')
                                modal.find('#img_pdv_'+y).attr('src','../dist/img/pdv_on.png')
                                modal.find('#span_pdv_'+y).html(sat)
                            }
                        }  
                    });
                }
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