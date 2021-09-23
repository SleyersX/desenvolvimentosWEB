<!DOCTYPE html>
<?php
    use models\DB;
    require_once "../models/connection.php";
    $db = new DB();
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Money Management </title>
    <link rel="icon" href="../favicon.ico" />
    <!-- Google Font: Source Sans Pro -->
    <link href="../plugins/fonts-google/fontgoogle.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- daterange picker -->
    <link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="../plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="../plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="../plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
    <!-- BS Stepper -->
    <link rel="stylesheet" href="../plugins/bs-stepper/css/bs-stepper.min.css">
    <!-- dropzonejs -->
    <link rel="stylesheet" href="../plugins/dropzone/min/dropzone.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../dist/css/style.css">
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__wobble" src="../dist/img/money_management.png" alt="PreLoaDer" height="60" width="60">
    </div>
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-dark">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">
                        Home
                    </a>
                </li>
            </ul>
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
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-sign-out-alt"></i> Log Out</a>
                    </div>
                </li>
            </ul>
        </nav>
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="#" class="brand-link">
                <img src="../dist/img/money_management_3.png" alt="Money Management Logo" class="brand-image img-circle elevation-3"
                     style="opacity: .8">
                <span class="brand-text font-weight-light">Money Management</span>
            </a>
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="../dist/img/root.png" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">Walter Moura</a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item has-treeview">
                            <a href="index.php" class="nav-link">
                                <i class="fas fa-home nav-icon"></i>
                                <p>Home</p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview">
                            <a href="lancamentos.php" class="nav-link active">
                                <i class="fas fa-money-check-alt nav-icon"></i>
                                <p>Lançamentos</p>
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
                            <h1>Lançamentos</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Lançamentos</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <section class="content">
                <?php
                /*$sql="SELECT * FROM tb_entries";
                $stmt = $db->conn->prepare("$sql");
                $stmt->execute();
                print_r($stmt->fetch());
                */
                ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h4>Novos lançamentos</h4>
                                </div>
                                <form>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label for="idLancamento">ID</label>
                                                    <input type="text" class="form-control" id="idLancamento">
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="form-group">
                                                    <label for="cliente">Cliente</label>
                                                    <select class="form-control">
                                                        <option></option>
                                                        <option>0000000001 - Walter Moura</option>
                                                        <option>0000000002 - Darla Lino</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label for="tipoLancamento">Tipo</label>
                                                    <select class="form-control">
                                                        <option></option>
                                                        <option>Receita</option>
                                                        <option>Despesa</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label>Data Lançamento</label>
                                                <div class="input-group date" id="reservationdate" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input" data-target="#reservationdate"/>
                                                    <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label for="tipoLancamento">Centor de Custo</label>
                                                    <select class="form-control">
                                                        <option></option>
                                                        <option>Financeiro</option>
                                                        <option>Compras</option>
                                                        <option>Informática</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="tipoLancamento">Classificação</label>
                                                    <select class="form-control">
                                                        <option></option>
                                                        <option>Salários</option>
                                                        <option>Beneficiários</option>
                                                        <option>Outros</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="tipoLancamento">Plano de Contas</label>
                                                    <select class="form-control">
                                                        <option></option>
                                                        <option>Pagamento</option>
                                                        <option>Adiantamento</option>
                                                        <option>Férias</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <label for="valorPagamento">Valor Pagamento</label>
                                                    <input type="text" class="form-control" id="valorPagamento">
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <label>Pagamento Hoje</label>
                                                <div class="input-group date" id="pagamentoHoje" data-target-input="nearest">
                                                    <input type="text" class="form-control datetimepicker-input" data-target="#pagamentoHoje"/>
                                                    <div class="input-group-append" data-target="#pagamentoHoje" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label>Observações</label>
                                                    <textarea class="form-control" rows="5" placeholder="Observações..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card card-danger">
                                <div class="card-header">
                                    <h4>Saldos</h4>
                                </div>
                                <form>
                                    <div class="card-body">
                                        <div class="col-sm-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-warning"><i class="fas fa-arrow-circle-left"></i>  <i class="fas fa-dollar-sign"></i></span>

                                                <div class="info-box-content">
                                                    <span class="info-box-text">Saldo Mês Anterior</span>
                                                    <span class="info-box-number" id="idSaldoMesAnterior">R$ 0,00</span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                            <!-- /.info-box -->
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-success"><i class="fas fa-arrow-circle-up"></i>  <i class="fas fa-dollar-sign"></i></span>

                                                <div class="info-box-content">
                                                    <span class="info-box-text">Saldo Mês Atual</span>
                                                    <span class="info-box-number" id="idSaldoMesAtual">R$ 0,00</span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                            <!-- /.info-box -->
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-primary"><i class="fas fa-dollar-sign"></i></span>

                                                <div class="info-box-content">
                                                    <span class="info-box-text">Receitas do Ano</span>
                                                    <span class="info-box-number" id="idReceitasDoAno">R$ 0,00</span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                            <!-- /.info-box -->
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-danger"><i class="fas fa-dollar-sign"></i></span>

                                                <div class="info-box-content">
                                                    <span class="info-box-text">Despesas do Ano</span>
                                                    <span class="info-box-number" id="idDespesasDoAno">R$ 0,00</span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                            <!-- /.info-box -->
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="info-box">
                                                <span class="info-box-icon bg-orange"><i class="fas fa-dollar-sign"></i></span>

                                                <div class="info-box-content">
                                                    <span class="info-box-text">Acumulado Ano</span>
                                                    <span class="info-box-number" id="idAcumuladoAno">R$ 0,00</span>
                                                </div>
                                                <!-- /.info-box-content -->
                                            </div>
                                            <!-- /.info-box -->
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h4>Tabela lançamentos</h4>
                                </div>
                                <form>
                                    <div class="card-body">
                                        <div class="table-responsive-sm">
                                            <table id="tbLancamentos" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Data Lançamento</th>
                                                        <th>Centro de Custo</th>
                                                        <th>Classificação</th>
                                                        <th>Plano de Contas</th>
                                                        <th>Cliente</th>
                                                        <th>Status</th>
                                                        <th>Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbBodyLancamentos"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.control-sidebar -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2020-2021 <a href="#">Developed by SLEYERSX </a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 0.01
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
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="../plugins/select2/js/select2.full.min.js"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="../plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
    <!-- InputMask -->
    <script src="../plugins/moment/moment.min.js"></script>
    <script src="../plugins/inputmask/jquery.inputmask.min.js"></script>
    <!-- date-range-picker -->
    <script src="../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap color picker -->
    <script src="../plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Bootstrap Switch -->
    <script src="../plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- BS-Stepper -->
    <script src="../plugins/bs-stepper/js/bs-stepper.min.js"></script>
    <!-- dropzonejs -->
    <script src="../plugins/dropzone/min/dropzone.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
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
    <!-- Page specific script -->
    <script>
        $(function () {
            //Date picker
            $('#reservationdate').datetimepicker({
                format: 'DD/MM/YYYY'
            });
            $('#pagamentoHoje').datetimepicker({
                format: 'DD/MM/YYYY'
            })
        })
    </script>
    <script type="text/javascript">
        // Recupera tabela lançamentos
        $('#tbBodyLancamentos').empty();
        $.ajax({
            url: "../controllers/getEntries.php",
            type: "GET",
            data : {},
            dataType: "json",
            success: function (data) {
                if(data){
                    for(let i=0;data.length>i;i++) {
                        $('#tbBodyLancamentos').append(`<tr><td>${i}</td><td>${data[i].data_lancamento}</td><td>${data[i].cn_custo_descricao}</td><td>${data[i].classificacao}</td><td>${data[i].plano_de_contas}</td><td>${data[i].nome}</td><td>${data[i].status}</td><td>${parseFloat(data[i].valor).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'})}</td></tr>`);
                    }
                    $('#tbLancamentos').DataTable({
                        "retrieve": true,
                        "reponsive": true,
                        "paging": true,
                        "lengthChange": false,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "pageLength": 5
                    });
                }else{
                    $('#tbLancamentos').DataTable({
                        //"retrieve": true,
                        "language": {
                            "emptyTable": "Sem dados disponiveis atualmente"
                        }
                    });
                }
            }
        });

        // Recupera saldos
        $.ajax({
            url: "../controllers/getSumMonthPrevious.php",
            type: "GET",
            data: "",
            dataType: "json",
            success: function (data){
                if(data){
                    document.getElementById('idSaldoMesAnterior').innerHTML = parseFloat(data.saldoMesAnterior).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                    document.getElementById('idSaldoMesAtual').innerHTML = parseFloat(data.saldoMesAtual).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                    document.getElementById('idReceitasDoAno').innerHTML = parseFloat(data.somaReceitasAno).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                    document.getElementById('idDespesasDoAno').innerHTML = parseFloat(data.somaDespesasAno).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                    document.getElementById('idAcumuladoAno').innerHTML = parseFloat(data.saldoAcumuladoAno).toLocaleString('pt-br',{style: 'currency', currency: 'BRL'});
                }
            }
        });
    </script>
</body>
</html>