<?php

//if (1==1 && strtoupper(@$_SESSION["usuario"]) == "VMA001ES") {
$VERSION_SWAL="7.0.5";
//if (SoyYo())
//	$VERSION_SWAL="7.17.0";
$PATH_TO_SWEETALERT=$DIR_RAIZ."/library/sweetalert2/".$VERSION_SWAL."/dist";
//$CSS_SWEETALERT=$PATH_TO_SWEETALERT."/sweetalert2.min.css";
$JS_SWEETALERT=$PATH_TO_SWEETALERT."/sweetalert2.all.min.js";


//echo '<link rel="stylesheet" type="text/css" href="'.$CSS_SWEETALERT.'?v='.filemtime($DOCUMENT_ROOT.$CSS_SWEETALERT).'" />'.PHP_EOL;
echo '<script language="JavaScript" src="'.$JS_SWEETALERT.'?v='.filemtime($DOCUMENT_ROOT.$JS_SWEETALERT).'"></script>'.PHP_EOL;

$Idioma=$_SESSION['Idioma'];

if ($Idioma == "ESP") {
	$TextoDespedida="<p>Su sesion ha sido cerrada.</p><p><small><i>Este dialogo se cerrar&aacute; en 5 segundos...</i></small></p>";
} else {
	$TextoDespedida="<p>Your session has been closed.</p><p><small><i>This dialog will be closed in 5 seconds...</i></small></p>";
}

if (!empty($VERSION_SWAL)) {
	$swal_1='html:"'.@$TextoDespedida.'" }).then( function() { location.reload(true); }, function(dismiss) { location.reload(true); }';
} else {
	$swal_1='text:"'.@$TextoDespedida.'", html:true }, function() { location.reload(true); }';
}

?>

<script>
	function swal_logout() {
		swal({
			title: "Logout",
			showConfirmButton: true,
			timer: 5000,
			html:"<p style='font-family:sans, arial'>Su sesion ha sido cerrada.</p><p><small><i>Este dialogo se cerrar&aacute; en 5 segundos...</i></small></p>"
		}).then(function() {	location.reload(true); }, function(dismiss) { location.reload(true); });
	}
</script>
