<title>PRECIOS VELA</title>
<?php
require("../Monitorizacion/cabecera_vistas.php");

$dir="/home/soporteweb/tools/VELA/precios/remoto";
$dir_web="VELA/precios/remoto";
$cmd="cd ".$dir." && sudo find . -type f -ls";
//echo $cmd;
//$files_res=shell_exec($cmd); var_dump($files_res); 
$files_res=glob($dir."/*");
$lista_total="";
//print_r($files_res);
foreach($files_res as $d) {
	if (!empty($d)) {
		$tienda=basename($d);
		$files_precios=glob($d."/*");
		foreach($files_precios as $d1) {
			$f=basename($d1);
			$href="/tmp/".$dir_web."/".$tienda."/".$f;
			$lista_total.="<tr><td><a href='$href' title='Pulsa aquí para descargar' download>$tienda - $f</a></td><td>".filesize($d1)."</td></tr>";
		}
	}
}

find precios/remoto/ -type f | grep -v -i historico | grep -v "TPV98" | grep "Diff_Unicos_TPV" | wc -l
?>
<style type="text/css">
	.lista_files { float:left; background-color:whitesmoke; border:1px solid black; border-radius:3px; padding:1em; }
	.capt1 { background-color: white;}
</style>

<div class="lista_files">
	<table class='TABLA2'>
		<caption class="capt1"></caption>
		<tr><th>Fichero</th><th>Size</th></tr>
		<?php echo $lista_total; ?>
	</table>
</div>