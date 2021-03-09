<?php
  	session_start();
	$txt_usuario = (isset($_COOKIE['cookieUsuario'])) ? base64_decode($_COOKIE['cookieUsuario']) : '';
	$txt_senha = (isset($_COOKIE['cookieSenha'])) ? base64_decode($_COOKIE['cookieSenha']) : '';
	$lembreme = (isset($_COOKIE['cookieLembreme'])) ? base64_decode($_COOKIE['cookieLembreme']) : '';
	$checked = ($lembreme == 'rememberme') ? 'checked' : '';
	require_once("security/valida.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>App SAT | Log in</title>
	<link rel="icon" href="favicon.ico" />
	<!-- Tell the browser to be responsive to screen width -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
	<!-- SweetAlert2 -->
	<link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
	<!-- Toastr -->
	<link rel="stylesheet" href="plugins/toastr/toastr.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="plugins/icons/ionicons.min.css">
	<!-- icheck bootstrap -->
	<link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="dist/css/adminlte.min.css">
	<link rel="stylesheet" href="dist/css/style.css">
	<!-- Google Font: Source Sans Pro -->
	<link href="plugins/fonts-google/fontgoogle.css" rel="stylesheet">
</head>
<body class="hold-transition login-page">
	<div class="login-box">
		<?php
        $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
        if(isset($token)){
            if($token == 'debb99b4791aa7763045224bf2bc98ca3b0e164eca8c9f092b3674bf964eee0c'){
                echo '<div class="alert alert-warning" role="alert">';
                echo "<i class='fas fa-exclamation-circle nav-icon mr-2'></i><strong>Ops! Sessão encerrada inatividade</strong>";
                echo "</div>";
            }
            $_SESSION['InputGetToken'] = $token;
        }
        if(isset($_SESSION['InputGetToken'])){
            unset($_SESSION['InputGetToken']);
        }
		?>
		<div class="login-logo">
			<img src="dist/img/logo-dia.png" alt="AdminLTE Logo" class="brand-image" style="opacity: .8">
		</div>
		<!-- /.login-logo -->
		<div class="card">
			<div class="card-body login-card-body">
				<p class="login-box-msg">Faça login para iniciar sua sessão</p>
				<form action="index.php" method="POST" id="quickForm" onsubmit="return valida_campos();">
					<div class="input-group mb-3">
						<input type="text" class="form-control" value="<?=$txt_usuario?>" name="txt_usuario" id="txt_usuario" placeholder="Login">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-user"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" class="form-control" maxlength="50" value="<?=$txt_senha?>" name="txt_senha" id="txt_senha" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-eye" id="show_password" style="border: none; background-color: transparent ;"></span>
                            </div>
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
					</div>
					<div class="row">
                        <div class="col-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="lembrete" value="rememberme" <?=$checked?>> Remember me
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- jQuery -->
	<script src="plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- SweetAlert2 -->
	<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
	<!-- Toastr -->
	<script src="plugins/toastr/toastr.min.js"></script>
	<!-- AdminLTE App -->
	<script src="dist/js/adminlte.min.js"></script>
	<!-- AdminLTE for demo purposes -->
	<script src="dist/js/demo.js"></script>
	<script type="text/javascript">
		function valida_campos(){
			if(document.getElementById('txt_usuario').value == ''){
				const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000
				})
				$(document).Toasts('create', {
					class: 'bg-warning', 
					title: 'Atenção',
					body: 'Por favor informe o seu login!',
					autohide: true,
					delay: 1500
				})
				document.getElementById('txt_usuario').focus();
				return false;
			}
			if(document.getElementById('txt_senha').value == ''){
				const Toast = Swal.mixin({
				toast: true,
				position: 'top-end',
				showConfirmButton: false,
				timer: 3000
				})
				$(document).Toasts('create', {
					class: 'bg-warning', 
					title: 'Atenção',
					body: 'Por favor informe o sua senha!',
					autohide: true,
					delay: 1500
				})
				document.getElementById('txt_senha').focus();
				return false;
			}
		}
		jQuery(document).ready(function($){
			$('#show_password').click(function(e){
				if($('#txt_senha').attr('type') == 'password'){
					$('#txt_senha').attr('type', 'text');
					$('#show_password').attr('class', 'fas fa-eye-slash');
				}else{
					$('#txt_senha').attr('type','password');
					$('#show_password').attr('class', 'fas fa-eye');
				}
			})
		})
	</script>
	<script type="text/javascript" src="plugins/pace-master/pace.min.js"></script>
</body>
</html>
