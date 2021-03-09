<?php
require("./comun_dashboard.php");

$dir_descarga="/tmp/datos_VELA/";
$file_umbral="umbral_stock_negativo.dat";
$file_totales="stock_negativo.dat";
$fichero=$dir_oper.$dir_datos.$file_totales;

$tmp=file_get_contents($dir_oper.$file_umbral);
list($dummy,$umbral_stock_negativo)=explode("=",$tmp);

$ayuda="Esta pagina muestra aquellas tiendas que tienen al menos 1 artículo que tenga menos de ".$umbral_stock_negativo." unidades/kilos de stock"; 

$nume_lista=0;
	
if (file_exists($fichero)) {
	$tmp=file_get_contents($fichero);

	if ($tmp) {
		$tmp1=explode("\n",$tmp);
		$nume_lista=count($tmp1);

		if (!empty($_GET["detalles"])) {
			if ($nume_lista > 0) {
				echo "
			<style>
				#info_detalles td {
					vertical-align:top;
				}
				.altura_detalles { height:600px }
				#detalle_por_tienda { width:600px; }
			</style>
			
			<h3>Tiendas con stock negativo: ".$nume_lista."</h3>
			<i style='font-size:12px'>".str_replace("&#10;","<br>",$ayuda)."</i>
			<hr>
			<div id='info_detalles'>
				<div class='v_info float info_descarga' style='width:250px'>
					<a class='b_descargar_fichero' href='".$dir_descarga.$file_totales."'>Descargar fichero...</a>
					<hr>
					<div class='of_y altura_detalles'>
						<table class='TABLA2'>
							<tr>
								<th class='t_a_center'>Tienda</th>
								<th class='t_a_center'>Numero de articulos</th>
							</tr>";
			foreach($tmp1 as $k => $d) {
				if ($d) {
					list($Tienda,$Stock) = explode(",",$d);
					echo "<tr class='row'>
								<td class='t_a_right'>".$Tienda."</td>
								<td class='t_a_right'>".$Stock."</td>
							</tr>";
				}
			}
			echo "
						</table>
					</div>
				</div>
				<div id='detalle_por_tienda' class='v_info float '>
					<b>&#9664 Pulse en una tienda para ver el detalle</b>
				</div>
			</div>
			<script>
				$('.row').on('click',function(){
					console.log('".$DIR_VELA.basename(__FILE__)."' + '?detalle_por_tienda='+$(this).children().first().text());
					$('#detalle_por_tienda').load('".$DIR_VELA.basename(__FILE__)."?detalle_por_tienda='+$(this).children().first().text());
				});
			</script>
			";
		}
		die();
	}

	if (!empty($_GET["detalle_por_tienda"])) {
		$file=$dir_oper.$dir_datos."stock_negativo_detalle/".$_GET["detalle_por_tienda"].".dat";
		if (file_exists($file)) {
			$tmp=file_get_contents($file);
			$tabla=explode("\n",$tmp);
			$file_descarga=$dir_descarga."stock_negativo_detalle/".$_GET["detalle_por_tienda"].".dat";
			echo "
			<a class='b_descargar_fichero' href='$file_descarga'>Descargar fichero...</a>
			<hr>
			<div class='of_y altura_detalles'>
				<table class='TABLA2'>
					<tr>
						<th>Tienda</th>
						<th>Articulo</th>
						<th>Descripcion</th>
						<th>Stock</th>
					</tr>";
			foreach($tabla as $k => $d) {
				if ($d) {
					list($Tienda,$Item,$Desc,$Stock) = explode("#",$d);
					echo "
					<tr class='row'>
						<td>".$Tienda."</td>
						<td>".$Item."</td>
						<td>".$Desc."</td>
						<td style='text-align:right;'>".$Stock."</td>
					</tr>";
				}
			}
			echo "
				</table>
			</div>
			";
		}
		die();
	}
}
}
$red1=($nume_lista>0?"red":"");
?>

<div title="<?php if(empty($_GET['detalles'])) echo @$ayuda; ?>" class="una_vez">
	<i class="hora_fichero"><?php echo (file_exists($fichero)?date("d/m/y H:i:s",filemtime($fichero)):"No hay fichero"); ?></i>
	<table style="padding:0; margin:0 0 auto; width:100%">
		<tr>
			<td><span class="titulo_cuadro">Tdas.Stock Negativo</span></td>
			<td><span class="num_grande <?php echo $red1; ?>"><?php echo $nume_lista; ?></span></td>
		</tr>
	</table>
	<?php
		if (!empty($_GET["detalles"])) {
			echo "<i style='font-size:12px'>".str_replace("&#10;","<br>",$ayuda)."</i>";
			echo "<hr>".$detalle;
		}
	?>
</div>