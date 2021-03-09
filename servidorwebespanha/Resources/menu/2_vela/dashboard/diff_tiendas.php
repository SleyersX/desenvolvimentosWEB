<?php
//require("../../cabecera_vistas.php");

$dir="/home/soporteweb/tools/VELA/datos/";

$filename=$dir."diff_articulos.dat";
if (file_exists($filename)) {
	$tmp=file_get_contents($filename);
	if ($tmp) {
		list($solo_TPV,$solo_VELA,$diff_precios)=explode(",",$tmp);
	}
	$red1=($solo_TPV>0?"red":"");
	$red2=($solo_VELA>0?"red":"");
	$red3=($diff_precios>0?"red":"");
}

$ayuda="
Aquí se reflejan discrepancias de precios entre TPVs y VELA.&#10;
	a) Artículos que solo existen en TPVs.&#10;
	b) Artículos que solo existen en VELA.&#10;
	c) Artículos que están en ambos sistemas, pero tienen diferencias de precios.&#10;";

$detalle="";
if (!empty($_GET["detalles"])) {
	$filename=$dir."diff_articulos_files.dat";
	if (file_exists($filename)) {
		$lista_files_precios=$lista_files_tpv=$lista_files_vela="";
		$tmp=file_get_contents($filename);
		if ($tmp) {
			$lista=explode("\n",$tmp);
			foreach($lista as $k => $d) {
				if (!empty($d)) {
					list($tienda,$fichero,$nume_arti) = explode(",",$d);
					$tmp_lista="<tr class='row'><td>".$tienda."</td><td>".$fichero."</td><td>".$nume_arti."</td></tr>";
					if (preg_match("/Precios/",$fichero) ) $lista_files_precios.=$tmp_lista;
					if (preg_match("/TPV/",$fichero) ) $lista_files_tpv.=$tmp_lista;
					if (preg_match("/VELA/",$fichero) ) $lista_files_vela.=$tmp_lista;
				}				
			}
		}
		$detalle="
			<style>
				.altura_detalles { height:550px }
				#info_detalles caption {
					font-weight:bold; padding:0; background-color:lightgray; font-size:12px;
				}
			</style>
			<div id='info_detalles'>
				<a id='descarga_fichero' href='' target='new'></a>
				<table style='width:100%'>
					<tr style='vertical-align:top'>
						<td>
							<div class='of_y altura_detalles'>
								<table class='TABLA2'><caption>Articulos solo en TPVs</caption><tr><th>Tienda</th><th>Fichero</th><th>Articulos</th></tr>".$lista_files_tpv."</table>
							</div>
						</td>
						<td>
							<div class='of_y altura_detalles'>
								<table class='TABLA2'><caption>Articulos solo en VELA</caption><tr><th>Tienda</th><th>Fichero</th><th>Articulos</th></tr>".$lista_files_vela."</table>
							</div>
						</td>
						<td>
							<div class='of_y altura_detalles'>
								<table class='TABLA2'><caption>Diferencias entre TPVs y VELA</caption><tr><th>Tienda</th><th>Fichero</th><th>Articulos</th></tr>".$lista_files_precios."</table>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<script>
				$('.row').on('click',function(){
					var tienda=$(this).children().eq(0).text();
					var fichero=$(this).children().eq(1).text();
					window.open( '/tmp/VELA/precios/remoto/'+tienda+'/'+fichero, '_blank');
				});
			</script>
		";
	}
}

/*
<div>
	<h2>Tiendas con diferencias TPV/VELA</h2>
	<table width="100%" style="padding:0; margin:0 0 auto;">
		<tr>
			<td><div class="num_grande <?php echo $red1; ?>" style="font-size:200%"><?php echo $solo_TPV; ?></div><div class="info_bottom"><b>Solo TPVs</b></div></td>
			<td><div class="num_grande <?php echo $red2; ?>" style="font-size:200%"><?php echo $solo_VELA; ?></div><div class="info_bottom"><b>Solo VELA</b></div></td>		
			<td><div class="num_grande <?php echo $red3; ?>" style="font-size:200%"><?php echo $diff_precios; ?></div><div class="info_bottom"><b>Diff. precios</b></div></td>
		</tr>
	</table>
	<?php echo $detalle; ?>
</div>
*/
?>
<div title="<?php if(empty($_GET['detalles'])) echo @$ayuda; ?>" class="cada_hora">
	<i class="hora_fichero"><?php echo (file_exists($filename)?date("d/m/y H:i:s",filemtime($filename)):"No hay fichero"); ?></i>
	<table style="padding:0; margin:0 0 auto; width:100%">
		<tr>
			<td><span class="titulo_cuadro">Tiendas con diferencias TPV/VELA (Precios)</span></td>
			<td width="40%">
			<table style="font-size:12px">
				<tr><td>Solo TPVs:</td><td><div style="font-weight:bold" class="<?php echo $red1; ?>"><?php echo $solo_TPV; ?></div></td></tr>
				<tr><td>Solo VELA:</td><td><div style="font-weight:bold" class="<?php echo $red2; ?>"><?php echo $solo_VELA; ?></div></td></tr>
				<tr><td>Diff. precios:</td><td><div style="font-weight:bold" class="<?php echo $red3; ?>"><?php echo $diff_precios; ?></div></td></tr>
			</table>
			</td>
		</tr>
	</table>
	<?php
		if (!empty($_GET["detalles"])) {
			echo "<i style='font-size:12px'>".str_replace("&#10;","<br>",$ayuda)."</i>";
			echo "<hr>".$detalle;
		}
	?>
</div>