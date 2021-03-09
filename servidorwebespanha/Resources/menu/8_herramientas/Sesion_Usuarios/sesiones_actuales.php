<?php

$dir="/var/lib/php5/";
$sesiones=glob($dir."*");

$tabla="<table class='TABLA2'>";
$tabla.="<thead><tr><th>USUARIO</th><th>NOMBRE USUARIO</th><th>IP ORIGEN</th><th>GRUPO SOPORTE</th><th>FECHA INICIO</th></tr></head>";
foreach($sesiones as $k => $d) {
	$tmp=file_get_contents($d);
	$t=split(";",$tmp);
	foreach($t as $k1 => $d1) {
		$t1=split(":",$d1);
		@list($v,$dummy,$val)=$t1;
		$var=str_replace("|s","",$v);
		$$var=str_replace("\"","",$val);
	}
	if ($usuario!="Invitado")
		$tabla.="<tr><td>".$usuario."</td><td>".$nombre_usuario."</td><td>".$UserIP."</td><td>".$nombre_grupo."</td><td>".$F_Inicio."</td></tr>";
	else
		$tabla.="<tr class='Invitado'><td>INVITADO</td><td>INVITADO</td><td>".$UserIP."</td><td>INVITADO</td><td>".$F_Inicio."</td></tr>";
}
$tabla.="</table>";
?>
<link rel="stylesheet" type="text/css" href="/Resources/css/tabla2.css"/>
<style type="text/css">
	.Invitado {
		background-color: lightcyan;
	}
</style>

<div>
	<?php echo $tabla; ?>
</div>