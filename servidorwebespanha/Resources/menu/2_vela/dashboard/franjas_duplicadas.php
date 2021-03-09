<?php
require("./comun_dashboard.php");

$dir_descarga="/tmp/datos_VELA/";
$dir_franjas="datos/franjas_duplicadas";

$detalle=$red1="";
$nume_lista=0;
$tmp1=glob($dir_oper.$dir_franjas."/franjas_duplicadas*");
$fichero=$dir_oper.$dir_datos."/franjas_duplicadas.ultima_consulta";

if (!empty($tmp1)) {
	rsort($tmp1);
	$tmp=$tmp1[0];
	$res=file_get_contents($tmp);
	$tmp=explode("\n",$res);
	$nume_lista=count($tmp)-1;
	if ($nume_lista>0) {
		$red1="red";
		$lista_files="";
		if (!empty($_GET["detalles"])) {
			foreach($tmp as $k => $d) {
				if (!empty($d)) {
					list($id,$order,$begin_date,$seq,$transm)=explode(",",$d);
					$lista_files.="<tr><td>$id</td><td>$order</td><td>$begin_date</td><td>$seq</td><td>$transm</td></tr>";
				}
			}
		
			$detalle="
			<hr>
			<style>
				.altura_detalles { height:570px }
			</style>
			<div id='info_detalles'>
				<a id='descarga_fichero' href='' target='new'></a>
				<div class='of_y altura_detalles'>
					<table class='TABLA2'><tr><th>B.Unit</th><th>O.Item.Type</th><th>Begin.Date</th><th>Sequence</th><th>Transm.Date</th></tr>".$lista_files."</table>
				</div>
			</div>
			<script>
				$('.row').on('click',function(){
					var fichero=$(this).children().eq(0).text();
					window.open( '/tmp/datos_VELA/'+fichero, '_blank');
				});
			</script>
		";
		}
	}	
}
$red1=($nume_lista>0?"red":"");

?>
<div class="cada_hora">
	<i class="hora_fichero"><?php echo (file_exists($fichero)?date("d/m/y H:i:s",filemtime($fichero)):"No hay fichero"); ?></i>
	<table style="padding:0; margin:0 0 auto; width:100%">
		<tr>
			<td><span class="titulo_cuadro">Franjas duplicadas</span></td>
			<td><span class="num_grande <?php echo $red1; ?>"><?php echo $nume_lista; ?></span></td>
		</tr>
	</table>
	<?php echo $detalle; ?>
</div>