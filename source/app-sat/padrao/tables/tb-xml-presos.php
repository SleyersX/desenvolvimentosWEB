<?php
    session_start();
    require_once("../../security/seguranca.php");
    protegePagina();
    require_once("../../security/connect.php");

    if(!empty($_SESSION['usuarioIDDashSAT']) && $_SESSION['usuarioNivelDashSAT'] != 4 ){
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
        require_once("../../processo/processa_log.php");
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

    unset($_SESSION['tempNome']);
	unset($_SESSION['tempEmail']);
    unset($_SESSION['tempLogin']);
    
    //Conexão ao banco de dados, exclusiva para monitorar inatividade do usuário
    $idLoginTemp = $_SESSION['idLoginTempDashSAT'];
    $conexao = new PDO('mysql:host=database;dbname=srvremoto',"root","8wFml6golmmbuKPv");
    $usuarioLogado = $conexao->prepare("SELECT * FROM tb_sessoes_login_dashsat WHERE id = '$idLoginTemp'");
    $usuarioLogado->execute();
    $fech = $usuarioLogado->fetchAll();

    include "../../config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>App SAT | Tracking </title>
        <link rel="icon" href="../../favicon.ico" />
        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="../../plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
        <link rel="stylesheet" href="../../dist/css/style.css">
        <!-- DataTables -->
        <!--<link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.css">-->
        <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" href="../../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
        <!-- Google Font: Source Sans Pro -->
        <link href="../../plugins/fonts-google/fontgoogle.css" rel="stylesheet">
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
            $.post("../../processo/processa_vis.php", {contar: '',}, function(data){
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
                        <a href="../home.php?token=<?php echo $token;?>" class="nav-link">
                            Home
                        </a>
                    </li>
                </ul>
                <!-- Right navbar links -->
                <?php
                    require_once("../../notificacao/alertas.php");
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
                            <a class="dropdown-item" href="../../security/sair.php">
                                <i class="fas fa-sign-out-alt"></i> Log Out</a>
                        </div>
                    </li>
                </ul>   
            </nav>
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <a href="../home.php?token=<?php echo $token;?>" class="brand-link">
                    <img src="../../dist/img/logo-dia.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                    <span class="brand-text font-weight-light">Admin SAT</span>
                </a>
                <div class="sidebar">
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <?php
                                echo  "<img src='../$avatar' class='img-circle elevation-2' alt='User Image'>";
                            ?>
                        </div>
                    <div class="info">
                        <a href="#" class="d-block"><?php print $_SESSION['usuarioNomeDashSAT']?></a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item has-treeview">
                            <a href="../search.php?token=<?php echo $token;?>" class="nav-link">
                                <i class="fas fa-search nav-icon"></i>
                                <p>Search</p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href="../estrutura.php?token=<?php echo $token;?>" class="nav-link">
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
                                    <a href="../dashboard.php?token=<?php echo $token;?>" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard S&#64;T</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item has-treeview menu-open">
                            <a href="table.php?token=<?php echo $token;?>" class="nav-link active">
                                <i class="nav-icon fas fa-table"></i>
                                <p>
                                    Tables
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="table.php?token=<?php echo $token;?>" class="nav-link">
                                        <i class="fas fa-server nav-icon"></i>
                                        <p>Todos os S&#64;Ts</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="tb-sats-inativos.php?token=<?php echo $token;?>" class="nav-link">
                                        <i class="fas fa-wifi-slash nav-icon"></i>
                                        <p>S&#64;Ts Inativos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="tb-comun-sefaz.php?token=<?php echo $token;?>" class="nav-link">
                                        <i class="fas fa-satellite-dish nav-icon"></i>
                                        <p>Comunicação SEFAZ</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="tb-xml-presos.php?token=<?php echo $token;?>" class="nav-link active">
                                        <i class="fas fa-hdd nav-icon"></i>
                                        <p>XMLs Pendentes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="tb-erro-config.php?token=<?php echo $token;?>" class="nav-link">
                                        <i class="fas fa-cogs nav-icon"></i>
                                        <p>Erros de Configurações</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="tb-sats-falha.php?token=<?php echo $token;?>" class="nav-link">
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
                                <h1>XMLs Pendentes</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="../home.php?token=<?php echo $token;?>">Home</a></li>
                                    <li class="breadcrumb-item active">XMLs Pendentes</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="load" style="display: block;" id="div-preloader"><img src="../../dist/img/preloader.gif" width="48" height="48"></div>
                            <div class="card" id="div-pendentes" style="display: none;">
                                <div class="card-header">
                                    <h3 class="card-title">S&#64;Ts com XMLs pendentes em memória de envio a SEFAZ</h3>
                                    <div class="card-tools">
                                        <a href="../../export/export_sat_xml_pendentes.php?errxml=sats_c_xmls_pendentes" class="btn btn-tool btn-sm">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="table-responsive-sm">
                                        <table id="tbXmlPresos" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Loja</th>
                                                    <th>Caixa</th>
                                                    <th>S&#64;T</th>
                                                    <th>Disco Usado</th>
                                                    <th>IP S&#64;T</th>
                                                    <th>IP Loja</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyXmlPresos"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        require_once("../../share/update_passwd.php");
                    ?>
                    <form name="frm_dados_pessoais" action="../../processo/atualizar_usuario.php" method="POST">
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
                                                    <label class="form-check-label"><img src="../../dist/img/avatar.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar2" name="avatar2">
                                                    <label class="form-check-label"><img src="../../dist/img/avatar2.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar3" name="avatar3">
                                                    <label class="form-check-label"><img src="../../dist/img/avatar3.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar4" name="avatar4">
                                                    <label class="form-check-label"><img src="../../dist/img/avatar4.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="avatar5" name="avatar5">
                                                    <label class="form-check-label"><img src="../../dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image" width="48" height="48"></label>
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
            <!-- /.control-sidebar -->
        </div>
        <!-- ./wrapper -->

        <!-- jQuery -->
        <script src="../../plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- DataTables -->
        <script src="../../plugins/datatables/jquery.dataTables.js"></script>
        <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
        <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
        <script src="../../plugins/jszip/jszip.min.js"></script>
        <script src="../../plugins/pdfmake/pdfmake.min.js"></script>
        <script src="../../plugins/pdfmake/vfs_fonts.js"></script>
        <script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
        <script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
        <script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
        <!-- AdminLTE App -->
        <script src="../../dist/js/adminlte.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="../../dist/js/demo.js"></script>
        <!-- page script -->
        <script type="text/javascript" src="../../plugins/pace-master/pace.min.js"></script>
        <script text="text/javascript">
            $(document).ready(function(){
                var data = '<?php echo $dtaActual;?>';
                $.ajax({
                    url: "../../processo/get_tables.php",
                    type: "POST",
                    dataType: "json",
                    data: {idTable:6},
                    success: function(data){
                        if(data){
                            $('#div-preloader').css("display","none")
                            $('#div-pendentes').css("display","block")
                            for(var i=0;data.length>i;i++){
                                $('#tbodyXmlPresos').append('<tr><td>'+(i+1)+'</td><td>DIA-'+data[i].loja+'</td><td>'+data[i].caixa+'</td><td><a href="../getsat.php?token=<?php echo $token;?>&idsat='+data[i].sat+'&page=a7e01f4f5af6c3a7d169f07f098023d5">'+data[i].sat+'</a></td><td>'+data[i].disco_usado+'</td><td>'+data[i].ipsat+'</td><td>'+data[i].iploja+'</td></tr>')
                            }

                            $(function () {
                                $('#tbXmlPresos').DataTable({
                                    "paging": true,
                                    "lengthChange": true,
                                    "searching": true,
                                    "ordering": true,
                                    "info": true,
                                    "autoWidth": false,
                                    "paginate": {
                                        "first": "First page",
                                        "last": "Last page",
                                    },
                                });
                            });
                        }else{
                            $(function () {
                                $('#tbXmlPresos').DataTable({
                                    "paging": true,
                                    "lengthChange": true,
                                    "searching": true,
                                    "ordering": true,
                                    "info": true,
                                    "autoWidth": false,
                                    "paginate": {
                                        "first": "First page",
                                        "last": "Last page",
                                    },
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
    </body>
    <?php
        echo '
        <script type="text/javascript">
            $(document).ready(function(){
                function atualizaTempo(){
                    $.ajax({
                        url: "../../processo/atualiza_tempo.php",
                        type: "POST",
                        data: {id:'.$fech[0]["id"].'},
                        success: function(data){
                            if(data == 1){
                                location.href="../../processo/encerra_sessao.php";
                            }
                        }
                    }); 
                }setInterval(atualizaTempo,10000);
            });
            $("#limpaTempo").on("click", function(){
                $.ajax({
                    url: "../../processo/limpa_tempo.php",
                    type: "POST",
                    data: {id:'.$fech[0]["id"].'}
                });
            });
        </script>';
    ?>
</html>