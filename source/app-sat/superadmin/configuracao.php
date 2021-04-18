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
    $sqlComboStatus = "SELECT status, descricao FROM tb_status_user";
    $queryComboStatus = mysqli_query($conn,$sqlComboStatus);

    $sqlComboStatusEdit = "SELECT status, descricao FROM tb_status_user";
    $queryComboStatusEdit = mysqli_query($conn,$sqlComboStatusEdit);
    $sqlComboNivelEdit = "SELECT nivel, descricao FROM tb_niveis_dashsat";
    $queryComboNivelEdit = mysqli_query($conn,$sqlComboNivelEdit);

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
        <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.css">
        <!-- Datepicker -->
        <link href="../plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet">
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
                                <a href="configuracao.php?token=<?php echo $token;?>" class="nav-link active">
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
                                <h1>Configurações</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="home.php?token=<?php echo $token;?>">Home</a></li>
                                    <li class="breadcrumb-item active">Configurações</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <section class="content">
                    <div class="container-fluid">
                    <?php
                        if(isset($_SESSION['msg'])){
                            echo $_SESSION['msg'];
                            unset($_SESSION['msg']);
                        }
                    ?>
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#dadosNivel" data-toggle="tab">
                                            Nível
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#dadosUsuarios" data-toggle="tab">
                                            Usuários
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#dadosAlertas" data-toggle="tab">
                                            Alertas
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#dadosSistema" data-toggle="tab">
                                            Sistema
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#importCSV" data-toggle="tab">
                                            Import CSV
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" href="#dadosParam" data-toggle="tab">
                                            Parametros
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="dadosNivel">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="card card-info card-outline">
                                                    <div class="card-header">
                                                        <h3 class="card-title">Niveis de acesso</h3>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive-sm">
                                                            <table id="tbNiveis" class="table table-sm table-bordered table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>Nível</th>
                                                                        <th>Ações</th>
                                                                    </tr>
                                                                </thead>
                                                                <?php
                                                                    $sqlNiveis = "SELECT id, nivel, descricao, data_criacao, data_modificacao FROM tb_niveis_dashsat";
                                                                    $queryNiveis = mysqli_query($conn,$sqlNiveis);
                                                                    echo '<tbody>';
                                                                    while($rowNiveis = mysqli_fetch_array($queryNiveis)){
                                                                        $idNivel = $rowNiveis['id'];
                                                                        $descNivel = $rowNiveis['descricao'];

                                                                        echo '<tr>';
                                                                            echo '<td>'.$idNivel.'</td>';
                                                                            echo '<td>'.$descNivel.'</td>';
                                                                            echo '<td><a href="#" data-toggle="modal" data-target="#editaDadosNivel" data-id="'.$idNivel.'" data-nome="'.$descNivel.'"><i class="fas fa-edit"></i></a>&nbsp; &nbsp;&nbsp;<a href="../processo/delete_nivel.php?id='.$idNivel.'&desc-nivel='.$descNivel.'"><i class="fas fa-trash-alt"></i></a></td>';
                                                                        echo '</tr>';

                                                                    }
                                                                    echo '</tbody>';
                                                                ?>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="card card-secondary card-outline">
                                                    <div class="card-header">
                                                    <h3 class="card-title">Novo nível</h3>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
													<form action="../processo/cadastrar_nivel.php" name="frm_cad_nivel"  method="POST">
                                                            <div class="row">
                                                                <div class="col-sm-12">
                                                                    <label for="nivel" class="col-sm-4 col-form-label">Nível</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="number" maxlength="5" class="form-control form-control-sm col-sm-4" id="nivel" name="nivel">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <label for="desc-nivel" class="col-sm-6 col-form-label">Descrição nível</label>
                                                                    <div class="col-sm-6">
                                                                        <input type="text" class="form-control form-control-sm" id="desc-nivel" name="desc-nivel">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <div class="col-sm-6">
                                                                        <label for="cb_nivel" class="col-sm-6 col-form-label"></label>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <a href="">
                                                                            <button type="submit" class="btn btn-outline-primary" >Salvar</button>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>				
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="dadosUsuarios">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="card card-info card-outline">
                                                    <div class="card-header">
                                                    <h3 class="card-title">Usuários</h3>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive-sm">
                                                            <table id="tbUsuarios" class="table table-sm table-striped table-bordered table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>Login</th>
                                                                        <th>Nível</th>
                                                                        <th>Ações</th>
                                                                    </tr>
                                                                </thead>
                                                                <?php
                                                                    $sqlTbUsuarios = "SELECT id, nome, login, email, nivel, descricao, ativo,desc_status, avatar, data_criacao, data_modificacao, status_email, data_verificacao_email FROM cn_dados_usuario_dashsat";
                                                                    $queryTbUsuarios = mysqli_query($conn,$sqlTbUsuarios);
                                                                    echo '<tbody>';
                                                                        while($rowTbUsuarios = mysqli_fetch_array($queryTbUsuarios)){
                                                                            $idTbUser     = $rowTbUsuarios['id'];
                                                                            $loginTbUser  = $rowTbUsuarios['login'];
                                                                            $nivelTbUser  = $rowTbUsuarios['descricao'];
                                                                            $nomeTbUser   = $rowTbUsuarios['nome'];
                                                                            $emailTbUser  = $rowTbUsuarios['email'];
                                                                            $nvTbUser     = $rowTbUsuarios['nivel'];
                                                                            $statusTbuser = $rowTbUsuarios['ativo'];
                                                                            $dtCriacaoTbUser = $rowTbUsuarios['data_criacao'];
                                                                            $dtModifTbUser = $rowTbUsuarios['data_modificacao'];
																			$descStatusTbuser = $rowTbUsuarios['desc_status'];
																			$dtVerificacaoEmail = $rowTbUsuarios['data_verificacao_email'];
																			$stVerificacaoEmail = $rowTbUsuarios['status_email'];

                                                                            $data_user['atual'] = date('Y-m-d H:i:s');  
                                                                            //Diminuir 20 segundos 
                                                                            $data_user['online'] = strtotime($data_user['atual'] . " - 20 seconds");
                                                                            $data_user['online'] = date("Y-m-d H:i:s",$data_user['online']);
                                                                            
                                                                            //Pesquisar os ultimos usuarios online nos 20 segundo
                                                                            $result_user_online = "SELECT * FROM tb_visitas_dahshsat WHERE id_usuario = '" . $rowTbUsuarios['id'] . "' AND data_final >= '" . $data_user['online'] . "'";
                                                                            
                                                                            $resultado_user_online = mysqli_query($conn, $result_user_online);
                                                                            $row_user_online = mysqli_fetch_assoc($resultado_user_online);

                                                                            echo '<tr>';
                                                                                echo '<td>'.$idTbUser.'</td>';
                                                                                if (!empty($row_user_online))
                                                                                {
                                                                                echo "<td><img width='10px' height='10px' src='../dist/img/online.png' mr-2/>".$loginTbUser."</td>";
                                                                                }else{
                                                                                echo "<td><img width='10px' height='10px' src='../dist/img/offline.png' mr-2/>".$loginTbUser."</td>";
                                                                                }
                                                                                echo '<td>'.$nivelTbUser.'</td>';
                                                                                if($statusTbuser == 3){
                                                                                    echo '<td>
                                                                                        <a href="#" data-toggle="modal" data-target="#viewDadosUsuario" data-id="'.$idTbUser.'" data-nome="'.$nomeTbUser.'" data-email="'.$emailTbUser.'" data-login="'.$loginTbUser.'" data-nivel="'.$nivelTbUser.'" data-ativo="'.$descStatusTbuser.'" data-dtcriacao="'.$dtCriacaoTbUser.'" data-dtmodif="'.$dtModifTbUser.'" data-stemail="'.$stVerificacaoEmail.'" data-dtemail="'.$dtVerificacaoEmail.'"><i class="fas fa-info"></i></a>&nbsp; &nbsp;&nbsp;
                                                                                        <a href="#" data-toggle="modal" data-target="#editaDadosUsuario" data-id="'.$idTbUser.'" data-nome="'.$nomeTbUser.'" data-email="'.$emailTbUser.'" data-login="'.$loginTbUser.'" data-nivel="'.$nvTbUser.'" data-ativo="'.$statusTbuser.'"><i class="fas fa-edit"></i></a>&nbsp; &nbsp;&nbsp;
                                                                                        <a href="../processo/delete_user.php?id='.$idTbUser.'&login-user='.$loginTbUser.'&nivel-user='.$nivelTbUser.'"><i class="fas fa-trash-alt"></i></a>&nbsp; &nbsp;&nbsp;
                                                                                        <a href="../processo/reset_passwd.php?id='.$idTbUser.'&login-user='.$loginTbUser.'&nivel-user='.$nivelTbUser.'"><i class="fas fa-key"></i></a>&nbsp; &nbsp;&nbsp;
                                                                                        <a href="../processo/resend_email.php?id-user='.$idTbUser.'&login-user='.$loginTbUser.'&email-user='.$emailTbUser.'&nome-user='.$nomeTbUser.'&nivel='.$nvTbUser.'"><i class="fas fa-reply"></i></a>&nbsp;&nbsp;<a href="../processo/unlock_user.php?id='.$idTbUser.'&login-user='.$loginTbUser.'&nome-user='.$nomeTbUser.'&nivel='.$nvTbUser.'"><i class="fas fa-user-lock"></i></a>
                                                                                    </td>';
                                                                                }else{
                                                                                    echo '<td>
                                                                                        <a href="#" data-toggle="modal" data-target="#viewDadosUsuario" data-id="'.$idTbUser.'" data-nome="'.$nomeTbUser.'" data-email="'.$emailTbUser.'" data-login="'.$loginTbUser.'" data-nivel="'.$nivelTbUser.'" data-ativo="'.$descStatusTbuser.'" data-dtcriacao="'.$dtCriacaoTbUser.'" data-dtmodif="'.$dtModifTbUser.'" data-stemail="'.$stVerificacaoEmail.'" data-dtemail="'.$dtVerificacaoEmail.'"><i class="fas fa-info"></i></a>&nbsp; &nbsp;&nbsp;
                                                                                        <a href="#" data-toggle="modal" data-target="#editaDadosUsuario" data-id="'.$idTbUser.'" data-nome="'.$nomeTbUser.'" data-email="'.$emailTbUser.'" data-login="'.$loginTbUser.'" data-nivel="'.$nvTbUser.'" data-ativo="'.$statusTbuser.'"><i class="fas fa-edit"></i></a>&nbsp; &nbsp;&nbsp;
                                                                                        <a href="../processo/delete_user.php?id='.$idTbUser.'&login-user='.$loginTbUser.'&nivel-user='.$nivelTbUser.'"><i class="fas fa-trash-alt"></i></a>&nbsp; &nbsp;&nbsp;
                                                                                        <a href="../processo/reset_passwd.php?id='.$idTbUser.'&login-user='.$loginTbUser.'&nivel-user='.$nivelTbUser.'"><i class="fas fa-key"></i></a>&nbsp; &nbsp;&nbsp;
                                                                                        <a href="../processo/resend_email.php?id-user='.$idTbUser.'&login-user='.$loginTbUser.'&email-user='.$emailTbUser.'&nome-user='.$nomeTbUser.'&nivel='.$nvTbUser.'"><i class="fas fa-reply"></i></a>&nbsp;&nbsp;<a href="../processo/lock_user.php?id='.$idTbUser.'&login-user='.$loginTbUser.'&nome-user='.$nomeTbUser.'&nivel='.$nvTbUser.'"><i class="fas fa-user-unlock"></i></a>
                                                                                    </td>';
                                                                                }
                                                                            echo '</tr>';

                                                                        }
                                                                        echo '</tbody>';
                                                                ?>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="card card-secondary card-outline">
                                                    <div class="card-header">
                                                    <h3 class="card-title">Novo usuário</h3>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <form action="../processo/cadastrar_user.php" name="frm_cad_user"  method="POST" onsubmit="return valida_campos_new_user();">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <label for="nome-usuario" class="col-sm-12 col-form-label">Nome usuário</label>
                                                                    <div class="col-sm-12">
                                                                        <input type="text" class="form-control form-control-sm" id="nome-usuario" name="nome-usuario" <?php if(!empty($_SESSION['tempNome'])){echo "value='".$_SESSION['tempNome']."'";}?> onkeyup="click_campos_new_user('nome-usuario');">
                                                                        <span id="span-nome" style="color:#ff0000;"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label for="email-usuario" class="col-sm-12 col-form-label">E-mail usuário</label>
                                                                    <div class="col-sm-12">
                                                                        <input type="text" class="form-control form-control-sm" id="email-usuario" name="email-usuario" <?php if(!empty($_SESSION['tempNome'])){echo "value='".$_SESSION['tempEmail']."'";}?> onkeyup="click_campos_new_user('email-usuario');valida_email('email-usuario');">
                                                                        <span id="span-email" style="color:#ff0000;"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label for="nome-login" class="col-sm-12 col-form-label">Login usuário</label>
                                                                    <div class="col-sm-12">
                                                                        <input type="text" class="form-control form-control-sm" id="nome-login" name="nome-login" <?php if(!empty($_SESSION['tempNome'])){echo "value='".$_SESSION['tempLogin']."'";}?> maxlength="10" onkeyup="click_campos_new_user('nome-login');">
                                                                        <span id="span-login" style="color:#ff0000;"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label for="cb_nivel" class="col-sm-12 col-form-label">Nível</label>
                                                                    <div class="col-sm-12">
                                                                        <select id="cb_nivel" name="cb_nivel" class="form-control form-control-sm" onkeyup="click_campos_new_user('cb_nivel');" onclick="click_campos_new_user('cb_nivel');">
																			<option>Selecione...</option>
																			<?php while($rowComboNivel = mysqli_fetch_array($queryComboNivel)){ ?>
																			<option value="<?php print $rowComboNivel['nivel']?>"><?php print $rowComboNivel['descricao']?></option><?php }?>
                                                                        </select>
                                                                        <span id="span-nivel" style="color:#ff0000;"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label for="cb_status" class="col-sm-12 col-form-label">Status</label>
                                                                    <div class="col-sm-12">
                                                                        <select id="cb_status" name="cb_status" class="form-control form-control-sm" onkeyup="click_campos_new_user('cb_status');" onclick="click_campos_new_user('cb_status');">
																			<option>Selecione...</option>
																			<?php while($rowComboStatus = mysqli_fetch_array($queryComboStatus)){ ?>
																			<option value="<?php print $rowComboStatus['status']?>"><?php print $rowComboStatus['descricao']?></option><?php }?>
                                                                        </select>
                                                                        <span id="span-status" style="color:#ff0000;"></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <br />
                                                                    <div class="col-sm-6">
                                                                        <a href="">
                                                                            <button type="submit" class="btn btn-outline-primary" >Salvar</button>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="dadosAlertas">
                                        <div class="card card-secondary card-outline collapsed-card">
                                            <div class="card-header">
                                            <h3 class="card-title">Disparar alarmes quando</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <form id="" name="" action="../processo/gravar_alertas.php" method="POST">
                                                    <div class="row text-sm">
                                                        <div class="col-sm-10">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <a href="">
                                                                <button type="submit" class="btn btn-outline-primary" >Salvar</button>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-7">
                                                            <div class="custom-control custom-checkbox col-sm-12">
                                                                <input class="custom-control-input" type="checkbox" id="dias-cupons-acumulados" name="dias-cupons-acumulados" value="1">
                                                                <label for="dias-cupons-acumulados" class="custom-control-label col-sm-12">houver cupons acumulados há mais de</label>
                                                                <div class="col-sm-2">
                                                                    <input class="form-control form-control-sm col-sm-12" type="number" id="n-dias-cupons-acumulados" name="n-dias-cupons-acumulados" style="border-left: none;border-right: none; border-top: none;" value="5" min="5">
                                                                </div> 
                                                                <div class="col-sm-4">
                                                                    <label for="n-dias-cupons-acumulados" class="col-form-label">dias</label>
                                                                </div> 
                                                            </div>
                                                        </div>
                                                        <!-- Número de Cupons acumulados -->
                                                        <div class="col-sm-7">
                                                            <div class="custom-control custom-checkbox col-sm-12">
                                                                <input class="custom-control-input" type="checkbox" id="num-cupons-acumulados" name="num-cupons-acumulados" value="1">
                                                                <label for="num-cupons-acumulados" class="custom-control-label col-sm-12"> houver um número de cupons acumulados na memória maior que</label>
                                                                <div class="col-sm-2">
                                                                    <input class="form-control form-control-sm col-sm-12" type="number" id="n-cupons-acumulados" name="n-cupons-acumulados" style="border-left: none;border-right: none; border-top: none;" value="1000" min="1000">
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <label for="n-cupons-acumulados" class="col-form-label">cupons</label>
                                                                </div>  
                                                            </div>
                                                        </div>
                                                        <!-- Nível de bateria -->
                                                        <div class="col-sm-9">
                                                            <div class="custom-control custom-checkbox col-sm-12">
                                                                <input class="custom-control-input" type="checkbox" id="nivel-de-bateria" name="nivel-de-bateria" value="1">
                                                                <label for="nivel-de-bateria" class="custom-control-label col-sm-12"> o nível de bateria estiver</label>
                                                                <div class="col-sm-4">
                                                                    <label for="nivel-de-bateria" class="col-form-label">baixo</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Dias Vecimento certificado -->
                                                        <div class="col-sm-7">
                                                            <div class="custom-control custom-checkbox col-sm-12">
                                                                <input class="custom-control-input" type="checkbox" id="vencimento-certificado" name="vencimento-certificado" value="1">
                                                                <label for="vencimento-certificado" class="custom-control-label col-sm-12"> o tempo restante para o vencimento do certificado atingir</label>
                                                                <div class="col-sm-2">
                                                                    <input class="form-control form-control-sm col-sm-12" type="number" id="n-vencimento-certificado" name="n-vencimento-certificado" style="border-left: none;border-right: none; border-top: none;" value="30" min="30">
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <label for="n-vencimento-certificado" class="col-form-label">dias</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Dias Vecimento certificado -->
                                                        <div class="col-sm-7">
                                                            <div class="custom-control custom-checkbox col-sm-12">
                                                                <input class="custom-control-input" type="checkbox" id="dias-comun-sefaz" name="dias-comun-sefaz" value="1">
                                                                <label for="dias-comun-sefaz" class="custom-control-label col-sm-12"> a falta de comunicação com a SEFAZ atingir</label>
                                                                <div class="col-sm-2">
                                                                    <input class="form-control form-control-sm col-sm-12" type="number" id="n-dias-comun-sefaz" name="n-dias-comun-sefaz" style="border-left: none;border-right: none; border-top: none;" value="5" min="5">
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <label for="n-dias-comun-sefaz" class="col-form-label">dias</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Variação do relógio -->
                                                        <div class="col-sm-7">
                                                            <div class="custom-control custom-checkbox col-sm-12">
                                                                <input class="custom-control-input" type="checkbox" id="relogio-sat" name="relogio-sat" value="1">
                                                                <label for="relogio-sat" class="custom-control-label col-sm-12"> a variação do relógio interno e NTP for maior que</label>
                                                                <div class="col-sm-2">
                                                                    <input class="form-control form-control-sm col-sm-12" type="number" id="n-relogio-sat" name="n-relogio-sat" style="border-left: none;border-right: none; border-top: none;" value="5" min="5">
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <label for="n-relogio-sat" class="col-form-label">minutos</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Mudança no estado do bloqueio -->
                                                        <div class="col-sm-9">
                                                            <div class="custom-control custom-checkbox col-sm-12">
                                                                <input class="custom-control-input" type="checkbox" id="estado-bloqueio" name="estado-bloqueio" value="1">
                                                                <label for="estado-bloqueio" class="custom-control-label col-sm-12"> houver mudança no estado de operação</label>
                                                                <div class="col-sm-2">
                                                                    <label for="estado-bloqueio" class="col-form-label">bloqueado</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Status Porta WAN -->
                                                        <div class="col-sm-9">
                                                            <div class="custom-control custom-checkbox col-sm-12">
                                                                <input class="custom-control-input" type="checkbox" id="estado-wan" name="estado-wan" value="1">
                                                                <label for="estado-wan" class="custom-control-label col-sm-12">  o estado da WAN estiver</label>
                                                                <div class="col-sm-2">
                                                                    <label for="estado-wan" class="col-form-label">desligado</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Status Loja Block -->
                                                        <div class="col-sm-9">
                                                            <div class="custom-control custom-checkbox col-sm-12">
                                                                <input class="custom-control-input" type="checkbox" id="loja-block" name="loja-block" value="1">
                                                                <label for="loja-block" class="custom-control-label col-sm-12">  loja com possível</label>
                                                                <div class="col-sm-2">
                                                                    <label for="loja-block" class="col-form-label">bloqueio</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-1">
                                                            <div class="form-group">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="card card-secondary card-outline collapsed-card">
                                            <div class="card-header">
                                                <h3 class="card-title">Forma de recebimento alarmes</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row text-sm">
                                                    <div class="col-sm-6">
                                                        <div class="card card-info card-outline">
                                                            <div class="card-header">
                                                                <h3 class="card-title">E-mails Alertas</h3>
                                                                <div class="card-tools">
                                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="table-responsive-sm">
                                                                    <table id="tbEmailAlerta" class="table table-sm table-bordered table-hover">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>E-mail</th>
                                                                                <th>Status</th>
                                                                                <th>Ações</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <?php
                                                                            $sqlAlertas = "SELECT id, email, status_email, descricao_status FROM cn_email_alarmes";
                                                                            $queryAlertas = mysqli_query($conn,$sqlAlertas);
                                                                            echo '<tbody>';
                                                                            while($rowAlertas = mysqli_fetch_array($queryAlertas)){
                                                                                $idAlertas = $rowAlertas['id'];
                                                                                $emailAlertas = $rowAlertas['email'];
                                                                                $statusEmail = $rowAlertas['descricao_status'];

                                                                                echo '<tr>';
                                                                                    echo '<td>'.$emailAlertas.'</td>';
                                                                                    echo '<td>'.$statusEmail.'</td>';
                                                                                    echo '<td><a href="../processo/delete_email_alerta.php?id='.$idAlertas.'&email='.$emailAlertas.'"><i class="fas fa-trash-alt"></i></a></td>';
                                                                                echo '</tr>';

                                                                            }
                                                                            echo '</tbody>';
                                                                        ?>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="card card-secondary card-outline">
                                                            <div class="card-header">
                                                            <h3 class="card-title">Novo E-mail</h3>
                                                                <div class="card-tools">
                                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                            <form action="../processo/cadastrar_email.php" name="frm_cad_email"  method="POST" onsubmit="return valida_campos_new_alarme();">
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <label for="nivel" class="col-sm-4 col-form-label">E-mail</label>
                                                                            <div class="col-sm-12">
                                                                                <input type="email" class="form-control form-control-sm" id="email-alarme" name="email-alarme" onkeyup="click_campos_email_alertas('email-alarme');valida_campos_email_alertas('email-alarme');">
                                                                            </div>
                                                                            <span id="span-email-alarme" style="color:#ff0000;"></span>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <label for="cb_active_email" class="col-sm-6 col-form-label">Status E-mail</label>
                                                                            <div class=" col-sm-12">
                                                                                <select id="cb_active_email" name="cb_active_email" class="form-control form-control-sm" onkeyup="click_campos_email_alertas('cb_active_email');valida_campos_email_alertas('cb_active_email');">
                                                                                    <option>Selecione...</option>
                                                                                    <option value="1">Habilitada</option>
                                                                                    <option value="0">Desabilitada</option>
                                                                                </select>
                                                                                <span id="span-active-email" style="color:#ff0000;"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="col-sm-6">
                                                                                <label for="cb_nivel" class="col-sm-6 col-form-label"></label>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <a href="">
                                                                                    <button type="submit" class="btn btn-outline-primary" >Salvar</button>
                                                                                </a>
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
                                        <div class="card card-secondary card-outline collapsed-card">
                                            <div class="card-header">
                                                <h3 class="card-title">Forma de recebimento alarmes Exceção</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row text-sm">
                                                    <div class="col-sm-6">
                                                        <div class="card card-info card-outline">
                                                            <div class="card-header">
                                                                <h3 class="card-title">E-mails Exception</h3>
                                                                <div class="card-tools">
                                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="table-responsive-sm">
                                                                    <table id="tbEmailAlertaException" class="table table-sm table-bordered table-hover">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>E-mail</th>
                                                                                <th>Status</th>
                                                                                <th>Ações</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <?php
                                                                            $sqlAlertasException = "SELECT id, email, status_email, descricao_status FROM cn_email_alarmes_exception";
                                                                            $queryAlertasException = mysqli_query($conn,$sqlAlertasException);
                                                                            echo '<tbody>';
                                                                            while($rowAlertasException = mysqli_fetch_array($queryAlertasException)){
                                                                                $idAlertasException = $rowAlertasException['id'];
                                                                                $emailAlertasException = $rowAlertasException['email'];
                                                                                $statusEmailException = $rowAlertasException['descricao_status'];

                                                                                echo '<tr>';
                                                                                    echo '<td>'.$emailAlertasException.'</td>';
                                                                                    echo '<td>'.$statusEmailException.'</td>';
                                                                                    echo '<td><a href="../processo/delete_email_alerta_exception.php?id='.$idAlertasException.'&email='.$emailAlertasException.'"><i class="fas fa-trash-alt"></i></a></td>';
                                                                                echo '</tr>';

                                                                            }
                                                                            echo '</tbody>';
                                                                        ?>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="card card-secondary card-outline">
                                                            <div class="card-header">
                                                            <h3 class="card-title">Novo E-mail Exception </h3>
                                                                <div class="card-tools">
                                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                            <form action="../processo/cadastrar_email_exception.php" name="frm_cad_email_exception"  method="POST" onsubmit="return valida_campos_new_alarme_exception();">
                                                                    <div class="row">
                                                                        <div class="col-sm-12">
                                                                            <label for="nivel" class="col-sm-4 col-form-label">E-mail</label>
                                                                            <div class="col-sm-12">
                                                                                <input type="email" class="form-control form-control-sm" id="email-alarme-exception" name="email-alarme-exception" onkeyup="click_campos_email_alertas_exception('email-alarme-exception');valida_campos_email_alertas_exception('email-alarme-exception');">
                                                                            </div>
                                                                            <span id="span-email-alarme-exception" style="color:#ff0000;"></span>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <label for="cb_active_email_exception" class="col-sm-6 col-form-label">Status E-mail</label>
                                                                            <div class=" col-sm-12">
                                                                                <select id="cb_active_email_exception" name="cb_active_email_exception" class="form-control form-control-sm" onkeyup="click_campos_email_alertas_exception('cb_active_email_exception');valida_campos_email_alertas_exception('cb_active_email_exception');">
                                                                                    <option>Selecione...</option>
                                                                                    <option value="1">Habilitada</option>
                                                                                    <option value="0">Desabilitada</option>
                                                                                </select>
                                                                                <span id="span-active-email-exception" style="color:#ff0000;"></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-12">
                                                                            <div class="col-sm-6">
                                                                                <label for="cb_nivel" class="col-sm-6 col-form-label"></label>
                                                                            </div>
                                                                            <div class="col-sm-6">
                                                                                <a href="">
                                                                                    <button type="submit" class="btn btn-outline-primary" >Salvar</button>
                                                                                </a>
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
                                    <div class="tab-pane" id="dadosSistema">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="card card-info card-outline">
                                                    <div class="card-header">
                                                    <h3 class="card-title">Lojas Monitor</h3>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive-sm">
                                                            <table id="tbAltaSistema" class="table table-sm table-striped table-bordered table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Loja</th>
                                                                        <th>Envio</th>
                                                                        <th>Instalação</th>
                                                                        <th>Situação</th>
                                                                        <th>Ações</th>
                                                                    </tr>
                                                                </thead>
                                                                <?php
                                                                    $sqlTbInstallMonitor = "SELECT id, shop, tpvs, DATE_FORMAT(date_install, '%d/%m/%Y') AS data_install , DATE_FORMAT(date_send, '%d/%m/%Y') AS data_send, status, ativo, descricao_status, ntpvs, descricao_st_shop FROM cn_install_monitor_sat";
                                                                    $queryTbInstallMonitor = mysqli_query($conn,$sqlTbInstallMonitor);
                                                                    echo '<tbody>';
                                                                        while($rowTbInstallMonitorSAT = mysqli_fetch_array($queryTbInstallMonitor)){
                                                                            $idTbInstallMonitor         = $rowTbInstallMonitorSAT['id'];
                                                                            $shopTbInstallMonitor       = $rowTbInstallMonitorSAT['shop'];
                                                                            $sendTbInstallMonitor       = $rowTbInstallMonitorSAT['data_send'];
                                                                            $installTbInstallMonitor    = $rowTbInstallMonitorSAT['data_install'];
                                                                            $statusTbInstallMonitor     = $rowTbInstallMonitorSAT['status'];
                                                                            $situacaoTbInstallMonitor   = $rowTbInstallMonitorSAT['descricao_status']; 
                                                                            $tpvsTbInstallMonitor       = $rowTbInstallMonitorSAT['tpvs'];    
                                                                            $nTpvsTbInstallMonitor      = $rowTbInstallMonitorSAT['ntpvs'];
                                                                            $ativoTbInstallMonitor      = $rowTbInstallMonitorSAT['ativo'];
                                                                            $statusShopTbInstallMonitor = $rowTbInstallMonitorSAT['descricao_st_shop'];
                                                                            echo '<tr>';
                                                                                echo '<td>'.$shopTbInstallMonitor.'</td>';
                                                                                echo '<td>'.$sendTbInstallMonitor.'</td>';
                                                                                echo '<td>'.$installTbInstallMonitor.'</td>';
                                                                                echo '<td>'.$situacaoTbInstallMonitor.'</td>';
                                                                                echo '<td><a href="#" data-toggle="modal" data-target="#viewLojaAtiva" data-id="'.$idTbInstallMonitor.'" data-shop="'.$shopTbInstallMonitor.'" data-send="'.$sendTbInstallMonitor.'" data-install="'.$installTbInstallMonitor.'" data-status="'.$statusTbInstallMonitor.'" data-caixas="'.$tpvsTbInstallMonitor.'" data-status_loja="'.$statusShopTbInstallMonitor.'"><i class="fas fa-info"></i></a>&nbsp;&nbsp;&nbsp;<a href="#"><i class="fas fa-power-off"  data-toggle="modal" data-target="#modalStopService" data-ncaixas="'.$nTpvsTbInstallMonitor.'" data-nshop="'.$shopTbInstallMonitor.'"></i></a>&nbsp;&nbsp;&nbsp;<a href="#"><i class="fas fa-ban" data-toggle="modal" data-target="#modalRemoveService" data-ncaixas="'.$nTpvsTbInstallMonitor.'" data-nshop="'.$shopTbInstallMonitor.'"></i></a>&nbsp;&nbsp;&nbsp;<a href="#"><i class="fas fa-sync" data-toggle="modal" data-target="#modalRestartService" data-ncaixas="'.$nTpvsTbInstallMonitor.'" data-nshop="'.$shopTbInstallMonitor.'"></i></a>&nbsp;&nbsp;&nbsp;<a href="#"><i class="far fa-file-alt"></i>&nbsp;&nbsp;&nbsp;</a><a href="#"><i class="fas fa-question" data-toggle="modal" data-target="#modalStatusService" data-ncaixas="'.$nTpvsTbInstallMonitor.'" data-nshop="'.$shopTbInstallMonitor.'"></i>&nbsp;&nbsp;&nbsp;<a href="../processo/delete_shop.php?id='.$idTbInstallMonitor.'&status='.$ativoTbInstallMonitor.'&ntpvs='.$nTpvsTbInstallMonitor.'&shop='.$shopTbInstallMonitor.'"><i class="fas fa-trash-alt"></td>';
                                                                            echo '</tr>';

                                                                        }
                                                                        echo '</tbody>';
                                                                ?>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="card card-secondary card-outline">
                                                    <div class="card-header">
                                                    <h3 class="card-title">Atualizar/Ativar Loja</h3>
                                                        <div class="card-tools">
                                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <form action="../processo/ativar_lojas.php" name="frm_cad_ativar_loja"  method="POST" onsubmit="return valida_campos_ativar_lojas();">
                                                            <div class="row">
                                                                <div class="col-sm-4">
                                                                    <label for="num-shop" class="col-form-label">Loja</label>
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <input type="number" maxlength="5" class="form-control form-control-sm" id="num-shop" name="num-shop" onkeyup="click_campos_ativar_lojas('num-shop');">
                                                                        </div>
                                                                    </div>
                                                                    <span id="span-num-shop" style="color:#ff0000;"></span>
                                                                </div>
                                                                <div class="col-sm-8">
                                                                    <label for="cb_nivel" class="col-sm-8 col-form-label"></label>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label for="dt-envio" class="col-form-label">Data Envio</label>
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <div class="input-group date" data-provide="datepicker" name="data_envio" id="data_envio">
                                                                                <input type="text" class="form-control form-control-sm" id="date-send" name="date-send" onkeyup="click_campos_ativar_lojas('date-send');" readonly>
                                                                                <div class="input-group-addon">
                                                                                    <span class="glyphicon glyphicon-th"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <span id="span-date-send" style="color:#ff0000;"></span>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label for="dt-envio" class="col-form-label">Data Instalação</label>
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <div class="input-group date" data-provide="datepicker" name="data_instalacao" id="data_instalacao">
                                                                                <input type="text" class="form-control form-control-sm" id="date-install" name="date-install" onkeyup="click_campos_ativar_lojas('date-install');" readonly>
                                                                                <div class="input-group-addon">
                                                                                    <span class="glyphicon glyphicon-th"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <span id="span-date-install" style="color:#ff0000;"></span>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label for="cb_active" class="col-sm-12 col-form-label">Status</label>
                                                                    <select id="cb_active" name="cb_active" class="form-control form-control-sm">
                                                                        <option>Selecione...</option>
                                                                        <option value="1">Habilitada</option>
                                                                        <option value="0">Desabilitada</option>
                                                                    </select>
                                                                    <span id="span-status" style="color:#ff0000;"></span>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label for="cb_active" class="col-sm-12 col-form-label">Status Loja</label>
                                                                    <select id="cb_status_lojas" name="cb_status_loja" class="form-control form-control-sm">
                                                                        <option>Selecione...</option>
                                                                        <option value="0">Aberta</option>
                                                                        <option value="1">Fechada</option>
                                                                    </select>
                                                                    <span id="span-status" style="color:#ff0000;"></span>
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <div class="col-sm-6">
                                                                        <label for="cb_nivel" class="col-sm-6 col-form-label"></label>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <a href="">
                                                                            <button type="submit" class="btn btn-outline-primary" >Salvar</button>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="importCSV">
                                        <div class="card card-secondary card-outline collapsed-card">
                                            <div class="card-header">
                                            <h3 class="card-title">Import CSV Lojas x CR</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                    </button>
                                                    <a href="../import/import_dados_cr.csv" class="btn btn-tool btn-sm">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <form id="frmImportLojasCR" name="frmImport" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <label for="">Input file csv</label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" name="file">
                                                            <label class="custom-file-label">Upload</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label"></label>
                                                        <div class="col-sm-12">
                                                            <div class="progress progress-striped active">
                                                                <div class="progress-bar" style="width: 0%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Import</button>
                                                </form>
                                            </div>
                                            <div class="card-footer">
                                            </div>
                                        </div>
                                        <div class="card card-secondary card-outline collapsed-card">
                                            <div class="card-header">
                                            <h3 class="card-title">Import CSV Ativar Loja</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                    </button>
                                                    <a href="../import/import_ativar_lojas.csv" class="btn btn-tool btn-sm">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <form id="frmImportAtivarLojas" name="frmImport" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <label for="">Input file csv</label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" name="file">
                                                            <label class="custom-file-label">Upload</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label"></label>
                                                        <div class="col-sm-12">
                                                            <div class="progress progress-striped active">
                                                                <div class="progress-bar" style="width: 0%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Import</button>
                                                </form>
                                            </div>
                                            <div class="card-footer">
                                            </div>
                                        </div>
                                        <div class="card card-secondary card-outline collapsed-card">
                                            <div class="card-header">
                                            <h3 class="card-title">Import CSV Dados Fiscais Lojas</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
                                                    </button>
                                                    <a href="../import/import_dados_fiscais_lojas.csv" class="btn btn-tool btn-sm">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <form id="frmImportDadosFiscais" name="frmImport" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <label for="">Input file csv</label>
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input" name="file">
                                                            <label class="custom-file-label">Upload</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-sm-2 control-label"></label>
                                                        <div class="col-sm-12">
                                                            <div class="progress progress-striped active">
                                                                <div class="progress-bar" style="width: 0%">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary upload" id="btn-import-csv-dadosofiscais">Import</button>
                                                </form>
                                            </div>
                                            <div class="card-footer">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="dadosParam">
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
                    <form action="../processo/editar_user.php" method="POST">
                        <div class="modal fade" id="editaDadosUsuario" tabindex="-1" role="dialog" aria-labelledby="editaDadosUsuario" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center">Dados Usuário</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form id="editaDadosUsuarioForm">
                                            <div class="form-group">
                                                <label for="text">ID </label>
                                                <input for="text" class="form-control form-control-sm" id="id" name="id" readonly>
                                                <label for="text">Nome </label>
                                                <input type="text" class="form-control form-control-sm" id="nome" name="nome" onkeyup="click_campos_new_user('nome');">
                                                <span id="nome-edit"></span>
                                                <label for="email">E-mail </label>
                                                <input type="email" class="form-control form-control-sm" id="email" name="email" onkeyup="click_campos_new_user('email');">
                                                <label for="text">Login </label>
                                                <input type="text" class="form-control form-control-sm" id="login" name="login" onkeyup="click_campos_new_user('login');">
                                                <label for="cb_nivel">Nível</label>
                                                <select id="nivel" name="cb_nivel" class="form-control form-control-sm" onkeyup="click_campos_new_user('nivel');">
                                                    <?php while($rowComboNivelEdit = mysqli_fetch_array($queryComboNivelEdit)){ ?>
                                                    <option value="<?php print $rowComboNivelEdit['nivel']?>"><?php print $rowComboNivelEdit['descricao']?></option><?php }?>
                                                </select>
                                                <label for="cb_nivel">Status</label>
                                                <select id="status" name="cb_status" class="form-control form-control-sm" onkeyup="click_campos_new_user('status');">
                                                    <?php while($rowComboStatusEdit = mysqli_fetch_array($queryComboStatusEdit)){ ?>
                                                    <option value="<?php print $rowComboStatusEdit['status']?>"><?php print $rowComboStatusEdit['descricao']?></option><?php }?>
                                                </select>
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
                    <form action="../processo/editar_nivel.php" method="POST">
                        <div class="modal fade" id="editaDadosNivel" tabindex="-1" role="dialog" aria-labelledby="editaDadosNivel" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center">Dados Nível</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form id="editaDadosNivel">
                                            <div class="form-group">
                                                <label for="text">ID </label>
                                                <input for="text" class="form-control form-control-sm" id="id" name="id" readonly>
                                                <label for="text">Descrição </label>
                                                <input type="text" class="form-control form-control-sm" id="nome" name="nome" onkeyup="click_campos_new_user('nome');">
                                                <span id="nome-edit"></span>
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
                    <form name="frm_view_user" action="#" method="POST">
                        <div class="modal fade" id="viewDadosUsuario" tabindex="-1" role="dialog" aria-labelledby="viewDadosUsuario" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center">Dados Usuário</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form>
                                            <div class="form-group">
												<div class="row">
													<div class="col-sm-12">
														<label for="text" class="col-sm-4">ID </label>
														<input for="text" class="form-control form-control-sm col-sm-4" id="id" name="id" readonly>
													</div>
													<div class="col-sm-4">
														<label for="text" class="col-sm-12">Data Criação </label>
														<input type="text" class="form-control form-control-sm col-sm-12" id="dtcriacao" name="dtcriacao" readonly>
													</div>
													<div class="col-sm-4">
														<label for="text" class="col-sm-12">Data Modificação </label>
														<input type="text" class="form-control form-control-sm col-sm-12" id="dtmodificacao" name="dtmodificacao" readonly>
													</div>
													<div class="col-sm-12">
														<label for="text" class="col-sm-12">Nome </label>
														<input type="text" class="form-control form-control-sm col-sm-12" id="nome" name="nome" readonly>
													</div>
													<div class="col-sm-12">
														<label for="email" class="col-sm-12">E-mail </label>
														<input type="email" class="form-control form-control-sm col-sm-12" id="email" name="email" readonly>
													</div>
													<div class="col-sm-8">
														<label for="text" class="col-sm-12">Login </label>
														<input type="text" class="form-control form-control-sm col-sm-12" id="login" name="login" readonly>
													</div>
													<div class="col-sm-6">
														<label for="text" class="col-sm-12">Nível </label>
														<input type="text" class="form-control form-control-sm col-sm-12" id="nivel" name="nivel" readonly>
													</div>
													<div class="col-sm-6">
														<label for="text"  class="col-sm-12">Status </label>
														<input type="text" class="form-control form-control-sm col-sm-12" id="status" name="status" readonly>
													</div>
													<div class="col-sm-6">
														<label for="text"  class="col-sm-12">E-mail verificado </label>
														<input type="text" class="form-control form-control-sm col-sm-12" id="email-verificado" name="email-verificado" readonly>
													</div>
													<div class="col-sm-6">
														<label for="text"  class="col-sm-12">Data verificação e-mail </label>
														<input type="text" class="form-control form-control-sm col-sm-12" id="data-email-verificado" name="data-email-verificado" readonly>
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
                    <form action="#" method="POST">
                        <div class="modal fade" id="viewLojaAtiva" tabindex="-1" role="dialog" aria-labelledby="viewLojaAtiva" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center">Dados Loja</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form id="viewLojaAtivaForm">
                                            <div class="form-group">
                                                <label for="text">ID </label>
                                                <input for="text" class="form-control form-control-sm" id="id" name="id" readonly>
                                                <label for="text">Loja </label>
                                                <input type="text" class="form-control form-control-sm" id="shop" name="shop" readonly>
                                            </div>
                                            <label for="dt-envio-modal" class="col-form-label">Data Envio</label>
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-sm" id="date-send-modal" name="date-send-modal" readonly>
                                            </div>
                                            <label for="dt-install-modal" class="col-form-label">Data Instalação</label>
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-sm" id="date-install-modal" name="date-install-modal" readonly>
                                            </div>
                                            <label for="st-install-modal" class="col-form-label">Status</label>
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-sm" id="st-install-modal" name="st-install-modal" readonly>
                                            </div>
                                            <label for="st-loja-install-modal" class="col-form-label">Status Loja</label>
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-sm" id="st-loja-install-modal" name="st-loja-install-modal" readonly>
                                            </div>
                                        </form>        
                                    </div>        
                                    <div class="modal-footer">       
                                    </div>      
                                </div> 
                            </div>
                        </div>
                    </form>
                    <form action="../api/processo/processa_service.php?id=4" method="POST" onsubmit="return valida_check_stop_service();">
                        <div class="modal fade" id="modalStopService" tabindex="-1" role="dialog" aria-labelledby="viewLojaAtiva" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center" id="title-stop-service"></h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form id="modalStopServiceForm">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6 class="text-center">Selecione os PDVs :</h6>
                                                    <input for="text" class="form-control" id="num-shop" name="num-shop" style="display: none;" readonly>
                                                    <input for="text" class="form-control" id="num-pdvs" name="num-pdvs" style="display: none;" readonly>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="stop-pdv-1" name="stop-pdv-1" value="1">
                                                        <label for="stop-pdv-1" class="custom-control-label col-sm-12"> PDV 1</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="stop-pdv-2" name="stop-pdv-2" value="2">
                                                        <label for="stop-pdv-2" class="custom-control-label col-sm-12"> PDV 2</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="stop-pdv-3" name="stop-pdv-3" value="3">
                                                        <label for="stop-pdv-3" class="custom-control-label col-sm-12"> PDV 3</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="stop-pdv-4" name="stop-pdv-4" value="4">
                                                        <label for="stop-pdv-4" class="custom-control-label col-sm-12"> PDV 4</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_5">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="stop-pdv-5" name="stop-pdv-5" value="5">
                                                        <label for="stop-pdv-5" class="custom-control-label col-sm-12"> PDV 5</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_6">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="stop-pdv-6" name="stop-pdv-6" value="6">
                                                        <label for="stop-pdv-6" class="custom-control-label col-sm-12"> PDV 6</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_7">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="stop-pdv-7" name="stop-pdv-7" value="7">
                                                        <label for="stop-pdv-7" class="custom-control-label col-sm-12"> PDV 7</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="pdv_8">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="stop-pdv-8" name="stop-pdv-8" value="8">
                                                        <label for="stop-pdv-8" class="custom-control-label col-sm-12"> PDV 8</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="pdv_9">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="stop-pdv-9" name="stop-pdv-9" value="9">
                                                        <label for="stop-pdv-9" class="custom-control-label col-sm-12"> PDV 9</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>        
                                    </div>        
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-outline-danger">Stop</button>
                                    </div>      
                                </div> 
                            </div>
                        </div>
                    </form>
                    <form action="../api/processo/processa_service.php?id=1" method="POST" onsubmit="return valida_check_status_service();">
                        <div class="modal fade" id="modalStatusService" tabindex="-1" role="dialog" aria-labelledby="viewLojaAtiva" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center" id="title-status-service"></h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form id="modalStatusServiceForm">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6 class="text-center">Selecione os PDVs :</h6>
                                                    <input for="text" class="form-control" id="num-shop" name="num-shop" style="display: none;" readonly>
                                                    <input for="text" class="form-control" id="num-pdvs" name="num-pdvs" style="display: none;" readonly>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="status-pdv-1" name="status-pdv-1" value="1">
                                                        <label for="status-pdv-1" class="custom-control-label col-sm-12"> PDV 1</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="status-pdv-2" name="status-pdv-2" value="2">
                                                        <label for="status-pdv-2" class="custom-control-label col-sm-12"> PDV 2</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="status-pdv-3" name="status-pdv-3" value="3">
                                                        <label for="status-pdv-3" class="custom-control-label col-sm-12"> PDV 3</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="status-pdv-4" name="status-pdv-4" value="4">
                                                        <label for="status-pdv-4" class="custom-control-label col-sm-12"> PDV 4</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_5">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="status-pdv-5" name="status-pdv-5" value="5">
                                                        <label for="status-pdv-5" class="custom-control-label col-sm-12"> PDV 5</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_6">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="status-pdv-6" name="status-pdv-6" value="6">
                                                        <label for="status-pdv-6" class="custom-control-label col-sm-12"> PDV 6</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_7">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="status-pdv-7" name="status-pdv-7" value="7">
                                                        <label for="status-pdv-7" class="custom-control-label col-sm-12"> PDV 7</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="pdv_8">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="status-pdv-8" name="status-pdv-8" value="8">
                                                        <label for="status-pdv-8" class="custom-control-label col-sm-12"> PDV 8</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="pdv_9">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="status-pdv-9" name="status-pdv-9" value="9">
                                                        <label for="status-pdv-9" class="custom-control-label col-sm-12"> PDV 9</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>        
                                    </div>        
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-outline-info">Status</button>
                                    </div>      
                                </div> 
                            </div>
                        </div>
                    </form>
                    <form action="../api/processo/processa_service.php?id=2" method="POST" onsubmit="return valida_check_start_service();">
                        <div class="modal fade" id="modalStartService" tabindex="-1" role="dialog" aria-labelledby="viewLojaAtiva" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center" id="title-start-service"></h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form id="modalStartServiceForm">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6 class="text-center">Selecione os PDVs :</h6>
                                                    <input for="text" class="form-control" id="num-shop" name="num-shop" style="display: none;" readonly>
                                                    <input for="text" class="form-control" id="num-pdvs" name="num-pdvs" style="display: none;" readonly>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="start-pdv-1" name="start-pdv-1" value="1">
                                                        <label for="start-pdv-1" class="custom-control-label col-sm-12"> PDV 1</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="start-pdv-2" name="start-pdv-2" value="2">
                                                        <label for="start-pdv-2" class="custom-control-label col-sm-12"> PDV 2</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="start-pdv-3" name="start-pdv-3" value="3">
                                                        <label for="start-pdv-3" class="custom-control-label col-sm-12"> PDV 3</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="start-pdv-4" name="start-pdv-4" value="4">
                                                        <label for="start-pdv-4" class="custom-control-label col-sm-12"> PDV 4</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_5">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="start-pdv-5" name="start-pdv-5" value="5">
                                                        <label for="start-pdv-5" class="custom-control-label col-sm-12"> PDV 5</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_6">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="start-pdv-6" name="start-pdv-6" value="6">
                                                        <label for="start-pdv-6" class="custom-control-label col-sm-12"> PDV 6</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_7">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="start-pdv-7" name="start-pdv-7" value="7">
                                                        <label for="start-pdv-7" class="custom-control-label col-sm-12"> PDV 7</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="pdv_8">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="start-pdv-8" name="start-pdv-8" value="8">
                                                        <label for="start-pdv-8" class="custom-control-label col-sm-12"> PDV 8</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="pdv_9">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="start-pdv-9" name="start-pdv-9" value="9">
                                                        <label for="start-pdv-9" class="custom-control-label col-sm-12"> PDV 9</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>        
                                    </div>        
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-outline-success">Start</button>
                                    </div>      
                                </div> 
                            </div>
                        </div>
                    </form>
                    <form action="../api/processo/processa_service.php?id=3" method="POST" onsubmit="return valida_check_restart_service();">
                        <div class="modal fade" id="modalRestartService" tabindex="-1" role="dialog" aria-labelledby="viewLojaAtiva" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center" id="title-start-service"></h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form id="modalRestartServiceForm">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6 class="text-center">Selecione os PDVs :</h6>
                                                    <input for="text" class="form-control" id="num-shop" name="num-shop" style="display: none;" readonly>
                                                    <input for="text" class="form-control" id="num-pdvs" name="num-pdvs" style="display: none;" readonly>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="restart-pdv-1" name="restart-pdv-1" value="1">
                                                        <label for="restart-pdv-1" class="custom-control-label col-sm-12"> PDV 1</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="restart-pdv-2" name="restart-pdv-2" value="2">
                                                        <label for="restart-pdv-2" class="custom-control-label col-sm-12"> PDV 2</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="restart-pdv-3" name="restart-pdv-3" value="3">
                                                        <label for="restart-pdv-3" class="custom-control-label col-sm-12"> PDV 3</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="restart-pdv-4" name="restart-pdv-4" value="4">
                                                        <label for="restart-pdv-4" class="custom-control-label col-sm-12"> PDV 4</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_5">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="restart-pdv-5" name="restart-pdv-5" value="5">
                                                        <label for="restart-pdv-5" class="custom-control-label col-sm-12"> PDV 5</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_6">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="restart-pdv-6" name="restart-pdv-6" value="6">
                                                        <label for="restart-pdv-6" class="custom-control-label col-sm-12"> PDV 6</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_7">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="restart-pdv-7" name="restart-pdv-7" value="7">
                                                        <label for="restart-pdv-7" class="custom-control-label col-sm-12"> PDV 7</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="pdv_8">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="restart-pdv-8" name="restart-pdv-8" value="8">
                                                        <label for="restart-pdv-8" class="custom-control-label col-sm-12"> PDV 8</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="pdv_9">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="restart-pdv-9" name="restart-pdv-9" value="9">
                                                        <label for="restart-pdv-9" class="custom-control-label col-sm-12"> PDV 9</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>        
                                    </div>        
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-outline-warning">Restart</button>
                                    </div>      
                                </div> 
                            </div>
                        </div>
                    </form>
                    <form action="../api/processo/processa_service.php?id=5" method="POST" onsubmit="return valida_check_remove_service();">
                        <div class="modal fade" id="modalRemoveService" tabindex="-1" role="dialog" aria-labelledby="viewLojaAtiva" aria-hidden="true">    
                            <div class="modal-dialog" role="document">          
                                <!-- Modal content-->      
                                <div class="modal-content">        
                                    <div class="modal-header">          
                                        <h4 class="modal-title texto-modal text-center" id="title-remove-service"></h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>  
                                    </div>        
                                    <div class="modal-body">
                                        <form id="modalRemoveServiceForm">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <h6 class="text-center">Selecione os PDVs :</h6>
                                                    <input for="text" class="form-control" id="num-shop" name="num-shop" style="display: none;" readonly>
                                                    <input for="text" class="form-control" id="num-pdvs" name="num-pdvs" style="display: none;" readonly>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="remove-pdv-1" name="remove-pdv-1" value="1">
                                                        <label for="remove-pdv-1" class="custom-control-label col-sm-12"> PDV 1</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="remove-pdv-2" name="remove-pdv-2" value="2">
                                                        <label for="remove-pdv-2" class="custom-control-label col-sm-12"> PDV 2</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="remove-pdv-3" name="remove-pdv-3" value="3">
                                                        <label for="remove-pdv-3" class="custom-control-label col-sm-12"> PDV 3</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_4">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="remove-pdv-4" name="remove-pdv-4" value="4">
                                                        <label for="remove-pdv-4" class="custom-control-label col-sm-12"> PDV 4</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_5">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="remove-pdv-5" name="remove-pdv-5" value="5">
                                                        <label for="remove-pdv-5" class="custom-control-label col-sm-12"> PDV 5</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_6">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="remove-pdv-6" name="remove-pdv-6" value="6">
                                                        <label for="remove-pdv-6" class="custom-control-label col-sm-12"> PDV 6</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" style="display: none;" id="pdv_7">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="remove-pdv-7" name="remove-pdv-7" value="7">
                                                        <label for="remove-pdv-7" class="custom-control-label col-sm-12"> PDV 7</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="pdv_8">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="remove-pdv-8" name="remove-pdv-8" value="8">
                                                        <label for="remove-pdv-8" class="custom-control-label col-sm-12"> PDV 8</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4" id="pdv_9">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="remove-pdv-9" name="remove-pdv-9" value="9">
                                                        <label for="remove-pdv-9" class="custom-control-label col-sm-12"> PDV 9</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>        
                                    </div>        
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-outline-danger">Remover</button>
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
        <!-- DataTables -->
        <script src="../plugins/datatables/jquery.dataTables.js"></script>
        <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
        <!-- Datepicker -->
        <script src="../plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="../plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.pt-BR.min.js"></script>
        <!-- overlayScrollbars -->
        <script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- bs-custom-file-input -->
        <script src="../plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
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
        <script>
            $('#frmImportDadosFiscais').on('submit', function(e){
                e.preventDefault();

                $form = $('#frmImportDadosFiscais');
                var formdata = new FormData($form[0]);
                var request = new XMLHttpRequest();

                request.upload.addEventListener('progress', function(e){
                    var percent = Math.round(e.loaded / e.total * 100);
                    $form.find('.progress-bar').width(percent + '%').html(percent + '%');
                });

                request.addEventListener('load', function(e){
                    $form.find('.progress-bar').addClass('bg-success').html('upload completo...');
                    setTimeout("window.open(self.location, '_self');", 1000); 
                });

                request.open('post','../processo/import_csv_dados_fiscais_lojas.php');
                request.send(formdata);
                alert(formdata)
            });
        </script>
        <script>
            $('#frmImportAtivarLojas').on('submit', function(e){
                e.preventDefault();

                $form = $('#frmImportAtivarLojas');
                var formdata = new FormData($form[0]);
                var request = new XMLHttpRequest();

                request.upload.addEventListener('progress', function(e){
                    var percent = Math.round(e.loaded / e.total * 100);
                    $form.find('.progress-bar').width(percent + '%').html(percent + '%');
                });

                request.addEventListener('load', function(e){
                    $form.find('.progress-bar').addClass('bg-success').html('upload completo...');
                    setTimeout("window.open(self.location, '_self');", 1000); 
                });

                request.open('post','../processo/import_csv_ativar_lojas.php');
                request.send(formdata);
                alert(formdata)
            });
        </script>
        <script>
            $('#frmImportLojasCR').on('submit', function(e){
                e.preventDefault();

                $form = $('#frmImportLojasCR');
                var formdata = new FormData($form[0]);
                var request = new XMLHttpRequest();

                request.upload.addEventListener('progress', function(e){
                    var percent = Math.round(e.loaded / e.total * 100);
                    $form.find('.progress-bar').width(percent + '%').html(percent + '%');
                });

                request.addEventListener('load', function(e){
                    $form.find('.progress-bar').addClass('bg-success').html('upload completo...');
                    setTimeout("window.open(self.location, '_self');", 1000); 
                });

                request.open('post','../processo/import_csv_cr.php');
                request.send(formdata);
                alert(formdata)
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function(){
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 1
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('dias-cupons-acumulados').checked = true;
                        }else{
                            document.getElementById('dias-cupons-acumulados').checked = false;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 2
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('n-dias-cupons-acumulados').value = data;
                        }else{
                            document.getElementById('n-dias-cupons-acumulados').value = 5;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 3
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('num-cupons-acumulados').checked = true;
                        }else{
                            document.getElementById('num-cupons-acumulados').checked = false;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 4
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('n-cupons-acumulados').value = data;
                        }else{
                            document.getElementById('n-cupons-acumulados').value = 1000;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 5
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('nivel-de-bateria').checked = true;
                        }else{
                            document.getElementById('nivel-de-bateria').checked = false;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 6
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('vencimento-certificado').checked = true;
                        }else{
                            document.getElementById('vencimento-certificado').checked = false;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 7
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('n-vencimento-certificado').value = data;
                        }else{
                            document.getElementById('n-vencimento-certificado').value = 30;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 8
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('dias-comun-sefaz').checked = true;
                        }else{
                            document.getElementById('dias-comun-sefaz').checked = false;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 9
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('n-dias-comun-sefaz').value = data;
                        }else{
                            document.getElementById('n-dias-comun-sefaz').value = 5;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 10
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('relogio-sat').checked = true;
                        }else{
                            document.getElementById('relogio-sat').checked = false;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 11
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('n-relogio-sat').value = data;
                        }else{
                            document.getElementById('n-relogio-sat').value = 5;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 12
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('estado-bloqueio').checked = true;
                        }else{
                            document.getElementById('estado-bloqueio').checked = false;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 12
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('estado-wan').checked = true;
                        }else{
                            document.getElementById('estado-wan').checked = false;
                        }
                    }
                })
                $.ajax({
                    url: "../processo/get_alarmes.php",
                    type: "POST",
                    data: 
                        {
                            cod: 14
                        },
                    success:function(data){
                        if(data >= 1){
                            document.getElementById('loja-block').checked = true;
                        }else{
                            document.getElementById('loja-block').checked = false;
                        }
                    }
                })
            })
        </script>
        <script type="text/javascript">
            $(document).ready(function () {
            bsCustomFileInput.init();
            });
        </script>
        <script type="text/javascript">
            $(".applyDatepicker").datepicker({ forceParse: false });		
            $('#data_envio').datepicker({
            //format: 'DD-MM-YYYY',
            //startDate: '+0',
            autoclose: true,
            language: 'pt-BR'
            });
        </script>
        <script type="text/javascript">		
            $('#data_instalacao').datepicker({
            //format: "dd-mm-yyyy",
            startDate: '-30',
            autoclose: true,
            language: "pt-BR"
            });
        </script>
        <script type="text/javascript">		
            $('#data_envio_modal').datepicker({
            //format: 'DD-MM-YYYY',
            //startDate: '+0',
            autoclose: true,
            language: 'pt-BR'
            });
        </script>
        <script type="text/javascript">		
            $('#data_install_modal').datepicker({
            //format: "dd-mm-yyyy",
            startDate: '-30',
            autoclose: true,
            language: "pt-BR"
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
            function valida_campos_new_user(){
                var msg = 'Campo obrigatório';
                var texto = msg.bold();
                
                if(document.getElementById('nome-usuario').value == ''){
                    document.getElementById('span-nome').innerHTML = texto;
                    document.getElementById('nome-usuario').style.border='1px solid red';
                    document.getElementById('nome-usuario').focus();
                    return false;
                }
                if(document.getElementById('email-usuario').value == ''){
                    document.getElementById('span-email').innerHTML = texto;
                    document.getElementById('email-usuario').style.border='1px solid red';
                    document.getElementById('email-usuario').focus();
                    return false;
                }
                if(document.getElementById('nome-login').value == ''){
                    document.getElementById('span-login').innerHTML = texto;
                    document.getElementById('nome-login').style.border='1px solid red';
                    document.getElementById('nome-login').focus();
                    return false;
                }
                if(document.getElementById('cb_nivel').selectedIndex == 0){
                    document.getElementById('span-nivel').innerHTML = texto;
                    document.getElementById('cb_nivel').style.border='1px solid red';
                    document.getElementById('cb_nivel').focus();
                    return false;
                }
                if(document.getElementById('cb_status').selectedIndex == 0){
                    document.getElementById('span-status').innerHTML = texto;
                    document.getElementById('cb_status').style.border='1px solid red';
                    document.getElementById('cb_status').focus();
                    return false; 
                }
            }
        </script>
        <script type="text/javascript">
            function click_campos_new_user(id){
                document.getElementById(id).style.border='1px solid green';
                document.getElementById('span-nome').innerHTML = '';
                document.getElementById('span-email').innerHTML = '';
                document.getElementById('span-login').innerHTML = '';
                document.getElementById('span-nivel').innerHTML = '';
                document.getElementById('span-status').innerHTML = '';
                document.getElementById('nome-edit').innerHTML = '';
            }
            function valida_email(email){
                var msg = 'E-mail inválido [email@dominio.com].';
                var texto = msg.bold();
                if(document.getElementById(email).value.indexOf('@')==-1 || document.getElementById(email).value.indexOf('.')==-1){
                    document.getElementById('span-email').innerHTML = texto;
                    document.getElementById('email-usuario').style.border='1px solid red';
                    document.getElementById('email-usuario').focus();
                    return false;
                }
            }
        </script>
        <script type="text/javascript">
            function valida_campos_new_alarme(){
                var msg = 'Campo obrigatório';
                var texto = msg.bold();
                
                if(document.getElementById('email-alarme').value == ''){
                    document.getElementById('span-email-alarme').innerHTML = texto;
                    document.getElementById('email-alarme').style.border='1px solid red';
                    document.getElementById('email-alarme').focus();
                    return false;
                }

                if(document.getElementById('cb_active_email').selectedIndex == 0){
                    document.getElementById('span-active-email').innerHTML = texto;
                    document.getElementById('cb_active_email').style.border='1px solid red';
                    document.getElementById('cb_active_email').focus();
                    return false; 
                }
            }
           
            function valida_campos_email_alertas(id){
                var msg = 'E-mail inválido [email@dominio.com].';
                var texto = msg.bold();

                if(document.getElementById(id).value.indexOf('@')==-1 || document.getElementById(id).value.indexOf('.')==-1){
                    document.getElementById('span-email-alarme').innerHTML = texto;
                    document.getElementById(id).style.border='1px solid red';
                    document.getElementById(id).focus();
                    return false;
                }
            }

            function click_campos_email_alertas(id){
                document.getElementById(id).style.border='1px solid green';
                document.getElementById('span-email-alarme').innerHTML = '';
                document.getElementById('span-active-email').innerHTML = '';
            }
        </script>
        <script type="text/javascript">
            function valida_campos_new_alarme_exception(){
                var msg = 'Campo obrigatório';
                var texto = msg.bold();
                
                if(document.getElementById('email-alarme-exception').value == ''){
                    document.getElementById('span-email-alarme-exception').innerHTML = texto;
                    document.getElementById('email-alarme-exception').style.border='1px solid red';
                    document.getElementById('email-alarme-exception').focus();
                    return false;
                }

                if(document.getElementById('cb_active_email_exception').selectedIndex == 0){
                    document.getElementById('span-active-email-exception').innerHTML = texto;
                    document.getElementById('cb_active_email_exception').style.border='1px solid red';
                    document.getElementById('cb_active_email_exception').focus();
                    return false; 
                }
            }
           
            function valida_campos_email_alertas_exception(id){
                var msg = 'E-mail inválido [email@dominio.com].';
                var texto = msg.bold();

                if(document.getElementById(id).value.indexOf('@')==-1 || document.getElementById(id).value.indexOf('.')==-1){
                    document.getElementById('span-email-alarme-exception').innerHTML = texto;
                    document.getElementById(id).style.border='1px solid red';
                    document.getElementById(id).focus();
                    return false;
                }
            }

            function click_campos_email_alertas_exception(id){
                document.getElementById(id).style.border='1px solid green';
                document.getElementById('span-email-alarme-exception').innerHTML = '';
                document.getElementById('span-active-email-exception').innerHTML = '';
            }
        </script>
        <script type="text/javascript">
            function valida_campos_ativar_lojas(){
                var msg = 'Campo obrigatório';
                var texto = msg.bold();

                if(document.getElementById('num-shop').value == ''){
                    document.getElementById('span-num-shop').innerHTML = texto;
                    document.getElementById('num-shop').style.border='1px solid red';
                    document.getElementById('num-shop').focus();
                    return false;
                }

                if(document.getElementById('date-send').value == ''){
                    document.getElementById('span-date-send').innerHTML = texto;
                    document.getElementById('date-send').style.border='1px solid red';
                    document.getElementById('date-send').focus();
                    return false;
                }

                if(document.getElementById('date-install').value == ''){
                    document.getElementById('span-date-install').innerHTML = texto;
                    document.getElementById('date-install').style.border='1px solid red';
                    document.getElementById('date-install').focus();
                    return false;
                }
            }
        </script>
        <script type="text/javascript">
             function click_campos_ativar_lojas(id){
                document.getElementById(id).style.border='1px solid green';
                document.getElementById('span-num-shop').innerHTML = '';
                document.getElementById('span-date-send').innerHTML = '';
                document.getElementById('span-date-install').innerHTML = '';
            }
        </script>
        <script type="text/javascript">
            function valida_check_stop_service(){
                var valor = 0
                for(var i = 1 ; i<=9;i++){
                    if(document.getElementById('stop-pdv-'+i).checked){
                        valor = valor +1;
                    }
                }
                if(valor==0){
                    alert('Não foi selecionado nenhum PDV!');
                    return false;
                }
            }
        </script>
        <script type="text/javascript">
            function valida_check_status_service(){
                var valor = 0
                for(var i = 1 ; i<=9;i++){
                    if(document.getElementById('status-pdv-'+i).checked){
                        valor = valor +1;
                    }
                }
                if(valor==0){
                    alert('Não foi selecionado nenhum PDV!');
                    return false;
                }
            }
        </script>
        <script type="text/javascript">
            function valida_check_start_service(){
                var valor = 0
                for(var i = 1 ; i<=9;i++){
                    if(document.getElementById('start-pdv-'+i).checked){
                        valor = valor +1;
                    }
                }
                if(valor==0){
                    alert('Não foi selecionado nenhum PDV!');
                    return false;
                }
            }
        </script>
        <script type="text/javascript">
            function valida_check_restart_service(){
                var valor = 0
                for(var i = 1 ; i<=9;i++){
                    if(document.getElementById('restart-pdv-'+i).checked){
                        valor = valor +1;
                    }
                }
                if(valor==0){
                    alert('Não foi selecionado nenhum PDV!');
                    return false;
                }
            }
        </script>
        <script type="text/javascript">
            function valida_check_remove_service(){
                var valor = 0
                for(var i = 1 ; i<=9;i++){
                    if(document.getElementById('remove-pdv-'+i).checked){
                        valor = valor +1;
                    }
                }
                if(valor==0){
                    alert('Não foi selecionado nenhum PDV!');
                    return false;
                }
            }
        </script>
        <script type="text/javascript">
            $('#viewLojaAtiva').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var idShopEdit = button.data('id')
                var shopEdit = button.data('shop')
                var dataSend = button.data('send')
                var dataInstall = button.data('install')
                var status = button.data('status')
                var ncaixas = button.data('caixas')
                var statusLoja = button.data('status_loja')

                var modal = $(this)
                modal.find('#id').val(idShopEdit)
                modal.find('#shop').val(shopEdit)
                modal.find('#date-send-modal').val(dataSend)
                modal.find('#date-install-modal').val(dataInstall)
                modal.find('#st-install-modal').val(status)
                modal.find('#st-loja-install-modal').val(statusLoja)

            })

            $('#editaDadosUsuario').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var idUserEdit = button.data('id')
                var nomeEditUser = button.data('nome')
                var emailEditUser = button.data('email')
                var loginEdituser = button.data('login')
                var nivelEditUser = button.data('nivel')
                var statusEditUser = button.data('ativo')

                var modal = $(this)
                modal.find('#id').val(idUserEdit)
                modal.find('#nome').val(nomeEditUser)
                modal.find('#email').val(emailEditUser)
                modal.find('#login').val(loginEdituser)
                modal.find('#nivel').val(nivelEditUser)
                modal.find('#status').val(statusEditUser)
            })

            $('#editaDadosNivel').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var idNivelEdit = button.data('id')
                var nomeEditNivel = button.data('nome')
                
                var modal = $(this)
                modal.find('#id').val(idNivelEdit)
                modal.find('#nome').val(nomeEditNivel)
            })
                                                        
            $('#viewDadosUsuario').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var idUserView = button.data('id')
                var nomeViewUser = button.data('nome')
                var dtCriacao = button.data('dtcriacao')
                var emailViewuser = button.data('email')
                var loginViewUser = button.data('login')
                var nivelViewUser = button.data('nivel')
                var statusViewUser = button.data('ativo')
                var dtModif = button.data('dtmodif')
				var statusEmailUser = button.data('stemail')
				var dtEmailUser = button.data('dtemail')

                var modal = $(this)
                modal.find('#id').val(idUserView)
                modal.find('#nome').val(nomeViewUser)
                modal.find('#dtcriacao').val(dtCriacao)
                modal.find('#email').val(emailViewuser)
                modal.find('#login').val(loginViewUser)
                modal.find('#nivel').val(nivelViewUser)
                modal.find('#status').val(statusViewUser)
                modal.find('#dtmodificacao').val(dtModif)
				modal.find('#email-verificado').val(statusEmailUser)
				modal.find('#data-email-verificado').val(dtEmailUser)
            })

            $('#modalStopService').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var ncaixas = button.data('ncaixas')
                var nshop = button.data('nshop')
                var msg = 'Stop Service - [ ' + nshop + ' ]'

                var modal = $(this)
                modal.find('#title-stop-service').html(msg)
                modal.find('#num-shop').val(nshop)
                modal.find('#num-pdvs').val(ncaixas)
                for(var x = 1 ; x <= 9; x++){
                    if(x<=ncaixas){
                        modal.find('#pdv_'+x).css("display","block")
                    }else{
                        modal.find('#pdv_'+x).css("display","none")
                    }
                }
            })

            $('#modalStatusService').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var ncaixas = button.data('ncaixas')
                var nshop = button.data('nshop')
                var msg = 'Status Service - [ ' + nshop + ' ]'

                var modal = $(this)
                modal.find('#title-status-service').html(msg)
                modal.find('#num-shop').val(nshop)
                modal.find('#num-pdvs').val(ncaixas)
                for(var x = 1 ; x <= 9; x++){
                    if(x<=ncaixas){
                        modal.find('#pdv_'+x).css("display","block")
                    }else{
                        modal.find('#pdv_'+x).css("display","none")
                    }
                }
            })

            $('#modalStartService').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var ncaixas = button.data('ncaixas')
                var nshop = button.data('nshop')
                var msg = 'Start Service - [ ' + nshop + ' ]'

                var modal = $(this)
                modal.find('#title-start-service').html(msg)
                modal.find('#num-shop').val(nshop)
                modal.find('#num-pdvs').val(ncaixas)
                for(var x = 1 ; x <= 9; x++){
                    if(x<=ncaixas){
                        modal.find('#pdv_'+x).css("display","block")
                    }else{
                        modal.find('#pdv_'+x).css("display","none")
                    }
                }
            })

            $('#modalRestartService').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var ncaixas = button.data('ncaixas')
                var nshop = button.data('nshop')
                var msg = 'Restart Service - [ ' + nshop + ' ]'

                var modal = $(this)
                modal.find('#title-restart-service').html(msg)
                modal.find('#num-shop').val(nshop)
                modal.find('#num-pdvs').val(ncaixas)
                for(var x = 1 ; x <= 9; x++){
                    if(x<=ncaixas){
                        modal.find('#pdv_'+x).css("display","block")
                    }else{
                        modal.find('#pdv_'+x).css("display","none")
                    }
                }
            })

            $('#modalRemoveService').on('show.bs.modal', function(event){
                var button = $(event.relatedTarget)
                var ncaixas = button.data('ncaixas')
                var nshop = button.data('nshop')
                var msg = 'Remove Service - [ ' + nshop + ' ]'

                var modal = $(this)
                modal.find('#title-remove-service').html(msg)
                modal.find('#num-shop').val(nshop)
                modal.find('#num-pdvs').val(ncaixas)
                for(var x = 1 ; x <= 9; x++){
                    if(x<=ncaixas){
                        modal.find('#pdv_'+x).css("display","block")
                    }else{
                        modal.find('#pdv_'+x).css("display","none")
                    }
                }
            })
        </script>
        <script>
            $(function () {
                $('#tbNiveis').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": true,
                "pageLength": 3
                });
                $('#tbUsuarios').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": true,
                "pageLength": 3
                });
                $('#tbAltaSistema').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "pageLength": 5
                });
                $('#tbAEmailAlarmes').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "pageLength": 5
                });
                $('#tbAEmailAlarmesException').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "pageLength": 5
                });
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