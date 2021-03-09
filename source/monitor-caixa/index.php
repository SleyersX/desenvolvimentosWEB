<?php

use CoffeeCode\DataLayer\Connect;
//use PDOException;
//use Source\Models\MonitorCaixas;

require "vendor/autoload.php";
require "source/Config.php";

//$data = new MonitorCaixas();
$sql = "SELECT * FROM tb_monitoramento_abertura_de_caixas WHERE data_atualizacao BETWEEN (NOW()+ INTERVAL -(2) DAY) AND NOW() ORDER BY shop, data_abertura, pos ASC";
$query = Connect::getInstance()->prepare($sql);
$query->execute();
$dados = $query->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Abertura de Caixas | Tracking </title>
        <link rel="icon" href="favicon.ico" />
        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="dist/css/adminlte.min.css">
        <!-- Select2 -->
        <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
        <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
        <!-- Tempusdominus Bbootstrap 4 -->
        <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
        <!-- DataTables -->
        <!--<link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.css">-->
        <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
        <!-- daterange picker -->
        <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
        <!-- Bootstrap Color Picker -->
        <link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
        <!-- Bootstrap4 Duallistbox -->
        <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
        <!-- Google Font: Source Sans Pro -->
        <link href="plugins/fonts-google/fontgoogle.css" rel="stylesheet">
    </head>
    <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
        <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="#?" class="nav-link">
                            Home
                        </a>
                    </li>
                </ul>   
            </nav>
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item has-treeview">
                            <a href="#?" class="nav-link">
                                <i class="fas fa-search nav-icon"></i>
                                <p>Search</p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href=".#?" class="nav-link">
                                <i class="fas fa-code-branch nav-icon"></i>
                                <p>Estrutura</p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview menu-open">
                            <a href="#?" class="nav-link active">
                                <i class="nav-icon fas fa-table"></i>
                                <p>Tables</p>
                            </a>
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
                                <h1>Titulo</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#?>">Home</a></li>
                                    <li class="breadcrumb-item active">Titulo</li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-secondary card-outline collapsed-card">
                                <div class="card-header">
                                    <h3 class="card-title">Filtro avançado</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" id="frmFiltroAvancado" action="">
                                        <div class="form-group">
                                            <label for="">Data Inicio</label>
                                            <div class="input-group col-sm-4">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="far fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control float-right" id="reservation">
                                            </div>
                                        </div>
                                    </form>
                                    <!--<button type="submit" class="btn btn-outline-primary" id="btnPesquisar">Pesquisar</button>-->
                                    <button type="submit" onclick="Pesquisar(1)" class="btn btn-outline-primary" id="btnPesquisar">Pesquisar</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"> Abertura de Caixas </h4>
                                    <div class="card-tools">
            
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive-sm" style="display: block;" id="div-abertura-caixa">
                                        <table id="tbAberturaDeCaixas" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Shop</th>
                                                    <th>Pos</th>
                                                    <th>Registration</th>
                                                    <th>Aperture Date</th>
                                                    <th>Aperture Hour</th>
                                                    <th>Aperture Value</th>
                                                </tr>
                                            </thead>
                                            <?php
                                                echo "<tbody>";
                                                if(count($dados)>=1){
                                                    $i =1;
                                                    while($i < count($dados)){
                                                        echo "<tr>";
                                                            echo "<td>".$i."</td>";
                                                            echo "<td>DIA-".$dados[$i]->shop."</td>";
                                                            echo "<td>".$dados[$i]->pos."</td>";
                                                            echo "<td>".$dados[$i]->matricula."</td>";
                                                            echo "<td>".$dados[$i]->data_abertura."</td>";
                                                            echo "<td>".$dados[$i]->hora_abertura."</td>";
                                                            echo "<td>".$dados[$i]->valor_abertura."</td>";
                                                        echo "</tr>";
                                                        $i++;
                                                    }
                                                }
                                                echo "</tbody>";
                                            ?>
                                        </table>
                                    </div>
                                    <div class="table-responsive-sm" style="display: none;" id="div-filtro-avancado">
                                        <table id="tbFiltroAvancado" class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Shop</th>
                                                    <th>Pos</th>
                                                    <th>Registration</th>
                                                    <th>Aperture Date</th>
                                                    <th>Aperture Hour</th>
                                                    <th>Aperture Value</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyFiltroAvancado"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <footer class="main-footer">
                    <strong>Copyright &copy; 2020-2020 <a href="#">Developed by TPVs</a>.</strong>
                    All rights reserved.
                    <div class="float-right d-none d-sm-inline-block">
                    <b>Version</b> 1.0
                    </div>
                </footer>
            </div>        
            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
        </div>
        <!-- ./wrapper -->
        <script src="dist/js/pages/dashboard2.js"></script>
        <!-- jQuery -->
        <script src="plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Select2 -->
        <script src="plugins/select2/js/select2.full.min.js"></script>
        <!-- Bootstrap4 Duallistbox -->
        <script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
        <!-- InputMask -->
        <script src="plugins/moment/locales.js"></script>
        <script src="plugins/moment/moment.min.js"></script>
        <script src="plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
        <!-- DataTables -->
        <script src="plugins/datatables/jquery.dataTables.js"></script>
        <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
        <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
        <script src="plugins/jszip/jszip.min.js"></script>
        <script src="plugins/pdfmake/pdfmake.min.js"></script>
        <script src="plugins/pdfmake/vfs_fonts.js"></script>
        <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
        <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
        <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
        <!-- Tempusdominus Bootstrap 4 -->
        <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
        <!-- date-range-picker -->
        <script src="plugins/daterangepicker/daterangepicker.js"></script>
        <script src="plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.pt-BR.min.js"></script>
        <!-- bootstrap color picker -->
        <script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
        <!-- ChartJS -->
        <script src="plugins/chart.js/Chart.min.js"></script>
        <!-- overlayScrollbars -->
        <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/adminlte.min.js"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="dist/js/demo.js"></script>
        <!-- page script -->
        <script>
            $(function (){
                $('#reservation').daterangepicker({
                    locale: 'pt-br'           
                });
                //Date range picker with time picker
            })
        </script>
        <script>      
            $(function(){
                $('#tbAberturaDeCaixas').DataTable({
                    //"retrieve": true,
                    "reponsive": true,
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": true,
                    "pageLength": 10,
                });    
            });
        </script>
        <script type="text/javascript">
            function cleanTable(){
                $('#tbodyFiltroAvancado').empty();
            }
            function ajustTable(){
                $('#tbFiltroAvancado').DataTable({
                    "retrieve": true,
                    "reponsive": true,
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "pageLength": 10,
                });
            }
            //$('#btnPesquisar').on('click', function(){
            function Pesquisar(x){
                var stringData = document.getElementById('reservation').value ;
                var arrayData = stringData.split("-",2);
                
                var splitData1 = arrayData[0]
                var splitData2 = arrayData[1]
                
                var replaceData1 = splitData1.replace("/","-")
                replaceData1 = replaceData1.replace("/","-")
                var replaceData2 = splitData2.replace("/","-")
                replaceData2 = replaceData2.replace("/","-")

                $('#div-abertura-caixa').css("display","none");
                $('#div-filtro-avancado').css("display","block");
                cleanTable();
                $.ajax({
                    url: "getdata.php",
                    type: "POST",
                    dataType: "json",
                    data: {idTable:x,dataInicial:replaceData1,dataFinal:replaceData2},
                    success: function(data){
                        if(data){
                            //alert(data)
                            for(var i=0;data.length>i;i++){
                                $('#tbodyFiltroAvancado').append('<tr><td>'+(i+1)+'</td><td>DIA-'+data[i].shop+'</td><td>'+data[i].pos+'</td><td>'+data[i].matricula+'</td><td>'+data[i].data_abertura+'</td><td>'+data[i].hora_abertura+'</td><td>'+data[i].valor_abertura+'</td></tr>')
                            }
                            $('#tbFiltroAvancado').DataTable({
                                "retrieve": true,
                                "reponsive": true,
                                "paging": true,
                                "lengthChange": true,
                                "searching": true,
                                "ordering": true,
                                "info": true,
                                "autoWidth": false,
                                "pageLength": 10,
                            });
                        }else{
                            $('#tbFiltroAvancado').DataTable({
                                "retrieve": true,
                                "language": {
                                    "emptyTable": "Sem dados disponiveis atualmente"
                                },
                                "reponsive": true,
                                "paging": true,
                                "lengthChange": true,
                                "searching": true,
                                "ordering": true,
                                "info": true,
                                "autoWidth": false,
                                "pageLength": 10,
                            });
                        }
                    }
                });
            }
        </script>
    </body>
</html>