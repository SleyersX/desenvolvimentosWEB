<?php
require("./comun_dashboard.php");

function get_count_files($file,&$lista) {
	$count_tmp=0;
	$list=array();
	$tmp=file_get_contents($file);
	if ($tmp) {
		$tmp1=explode("\n",$tmp);
		foreach($tmp1 as $k => $d) {
			if ($d) {
				list($lista[$count_tmp]["tienda"],$lista[$count_tmp]["fichero"],$lista[$count_tmp]["size"],$lista[$count_tmp]["mes"],$lista[$count_tmp]["dia"],$lista[$count_tmp]["hora"],$dummy	) = explode(" ",$d);
// -rw-r--r-- 1 esbadmin ftp400 52K sep 15 15:30 ./98015/A9801546V
				//$lista[]=array($fichero,$size,$mes,$dia,$hora);
				$count_tmp++;
			}
		}
	}
	return $count_tmp;
}

$red1=$red2=$red3="";
$count_files_venta_no_subidos=get_count_files($dir_oper.$dir_datos."files_venta_no_subidos.dat", $lista_venta_no_subidos);
$count_files_venta_error=get_count_files($dir_oper.$dir_datos."files_venta_error.dat", $lista_venta_error);
$count_files_pedidos_error=get_count_files($dir_oper.$dir_datos."files_pedidos_error.dat", $lista_pedidos_error);

$fichero=$dir_oper.$dir_datos."files_venta_no_subidos.dat";

$ayuda="Reporta errores en las comunicaciones:&#10;
	a) Ficheros de venta no subidos en el último gestor, denegados por ETL.&#10;
	b) Ficheros de venta con error, contenido con errores.&#10;
	c) Pedidos con error, que no pueden ser enviados. Para este caso, hay una alarma por correo al equipo de Nivel 3 de soporte para arreglar y subir el fichero de nuevo.";

function Pinta_Ficheros($lista,$titulo) {
	if (count($lista)>0) {
		echo '
		<div class="of_y" style="font-size:14px;height:575px">
			<h3>'.$titulo.'</h3>
			<pre>';
		foreach($lista as $k => $d)
			echo 'Tienda '.$d["tienda"].'	Fichero: '.$d["fichero"].' (size '.$d["size"].')	Fecha: '.$d["dia"].'/'.$d["mes"].' '.$d["hora"].PHP_EOL;
		echo '
			</pre>
		</div>';
	}
}

?>

<div title="<?php if(empty($_GET['detalles'])) echo @$ayuda; ?>" class="cinco_min">
	<i class="hora_fichero"><?php echo (file_exists($fichero)?date("d/m/y H:i:s",filemtime($fichero)):"No hay fichero"); ?>  <?php if ($_GET["correo"]) { echo "<i class='fas fa-envelope'></i>";} ?></i>
	<table style="padding:0; margin:0 0 auto; width:100%">
		<tr>
			<td><span class="titulo_cuadro">COMUNICACIONES:</span></td>
			<td width="50%">
			<table style="font-size:12px">
				<tr><td>Ventas con error:</td><td><b class="<?php echo $red1;?>"><?php echo $count_files_venta_error;?></b></td></tr>
				<tr><td>Ventas no subidas:</td><td><b class="<?php echo $red2;?>"><?php echo $count_files_venta_no_subidos;?></b></td></tr>
				<tr><td>Pedidos con error:</td><td><b class="<?php echo $red3;?>"><?php echo $count_files_pedidos_error;?></b></td></tr>
			</table>
		</tr>
	</table>
	<?php
		if (!empty($_GET["detalles"])) {
			echo "<i style='font-size:12px'>".str_replace("&#10;","<br>",$ayuda)."</i>";
			echo '<hr>';
			if (($count_files_venta_no_subidos+$count_files_venta_error+$count_files_pedidos_error)>0) {
				Pinta_Ficheros($lista_venta_no_subidos, 'Ficheros de venta no subidos ('.$count_files_venta_no_subidos.')');
				Pinta_Ficheros($lista_venta_error, 'Ficheros de venta con error ('.$count_files_venta_error.')');
				Pinta_Ficheros($lista_pedidos_error, 'Ficheros de pedidos con error ('.$count_files_pedidos_error.')');
			}
		}
	?>
</div>