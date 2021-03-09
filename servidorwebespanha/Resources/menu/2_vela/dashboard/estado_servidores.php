<?php
require("../../Monitorizacion/cabecera_vistas.php");

$estado_FTM="Running...";
$estado_VELA="Running...";
$estado_ETL="Running...";
?>
<div>
	<h2>ESTADO DE SERVICIOS</h2>
	<table class="t1">
		<tr>
			<th>Servicio</th><th>Estado</th>
		</tr>
		<tr><td>FTM</td><td><span><?php echo $estado_FTM; ?></span></td></tr>
		<tr><td>VELA</td><td><span><?php echo $estado_VELA; ?></span></td></tr>
		<tr><td>ETL</td><td><span><?php echo $estado_ETL; ?></span></td></tr>
	</table>
	<hr>
	<h2>ALARMAS VARIAS:</h2>
	<div>
		<ul>
			<li></li>
		</ul>
	</div>
</div>

<script>
	
</script>