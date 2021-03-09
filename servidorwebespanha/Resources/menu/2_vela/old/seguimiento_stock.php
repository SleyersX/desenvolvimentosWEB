<title>ERRORES FTM</title>
<?php
require("../Monitorizacion/cabecera_vistas.php");

// if (SoyYo()) { echo '<pre>'; print_r($_SESSION); print_r($_COOKIE); print_r($_SERVER); echo '</pre>'; }

$Queries["Item_6423"]=array (
	"Seguimiento ITEM 6423<br>(100 ultimos registros)",
	array("Fecha", "Stock","UbSW","PendServ","Ventas"),
	"select Fecha,Stock,u_b_s_w,PendServ,Ventas FROM VELA_9851_ITEM WHERE Item=6423 order by Fecha desc limit 100","", "ESP");

$Queries["Item_21977"]=array (
	"Seguimiento ITEM 21977<br>(100 ultimos registros)",
	array("Fecha", "Stock","UbSW","PendServ","Ventas"),
	"select Fecha,Stock,u_b_s_w,PendServ,Ventas FROM VELA_9851_ITEM WHERE Item=21977 order by Fecha desc limit 100","", "ESP");

	$files_res=glob("/home/soporteweb/tmp/seguimiento_stock/VELA_9851_TOTAL*");
	$lista_total="";
	foreach($files_res as $d) {
		if (!empty($d)) {
			$f=basename($d);
			$lista_total.="<tr><td><a href='/tmp/seguimiento_stock/$f' title='Pulsa aquí para descargar' download>$f</a></td><td>".filesize($d)."</td></tr>";
		}
	}

	$files_res=glob("/home/soporteweb/tmp/seguimiento_stock/VELA_9851_ITEM*");
	$lista_item="";
	foreach($files_res as $d) {
		if (!empty($d)) {
			$f=basename($d);
			$lista_item.="<tr><td><a href='/tmp/seguimiento_stock/$f' title='Pulsa aquí para descargar' download>$f</a></td><td>".filesize($d)."</td></tr>";
		}
	}


if (isset($Queries))
	foreach ($Queries as $key => $dato) {
		Show_data2($key, $dato); echo PHP_EOL;
	}
if (isset($Queries_s))
	foreach ($Queries_s as $key => $dato) {
		Show_data_sin_query($key, $dato);
	}
?>
<style type="text/css">
	.lista_files { float:left; background-color:whitesmoke; border:1px solid black; border-radius:3px; padding:1em; }
	.capt1 { background-color: white;}
</style>

<div class="lista_files">
	<table class='TABLA2'>
		<caption class="capt1">TODOS LOS ARTICULOS, CADA 30 MINUTOS</caption>
		<tr><th>Fichero</th><th>Size</th></tr>
		<?php echo $lista_total; ?>
	</table>
</div>

<div class="lista_files">
	<table class='TABLA2'>
		<caption class="capt1">ARTICULOS 2463 y 21977, CADA 5 MINUTOS</caption>
		<tr><th>Fichero</th><th>Size</th></tr>
		<?php echo $lista_item; ?>
	</table>
</div>
