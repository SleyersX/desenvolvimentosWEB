<?php
$Idioma=$_SESSION['Idioma'];
switch ($Idioma) {
	case "ESP":
		$Texto1="Lo sentimos, pero debe ingresar en el sistema para poder utilizar esta opcion.";
		$Texto2=">> Pulse aqui para validar su acceso <<";
		break;
	case "ENG":
		$Texto1="We apologize, but you must login into system to use this option.";
		$Texto2=">> Click here to login <<";
		break;
}
?>

<div class='Aviso Aviso_Rojo'>
	<img class="icono_aviso" src="/img/error.png" />
	<div id="texto_aviso">
		<h2><center><?php echo $Texto1; ?></center></h2>
		<p><center>
			<a id='a_login' href='javascript:{}' onclick="$('#user_login').dialog('open');">
			<?php echo $Texto2; ?>
			</a>
		</center></p>
	</div>
</div>
<!--<script>
	jQuery(document).ready(function () {
		$("#a_login").on("click",function() {
			$('#user_login').dialog('open');
		});
	});
</script>-->