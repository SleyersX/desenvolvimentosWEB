<?php
require("./comun_dashboard.php");
$nume_lista=0;
$detalle="";

function get_detalle($fichero,&$detalle) {
	$tmp_contador=0;
	if (file_exists($fichero)) {
		if (filesize($fichero) > 0) {
			$tmp=file_get_contents($fichero);
			$tmp1=explode("\n",$tmp);
			$detalle="
				<div class='of_y' style='font-size:10px;height:575px'>
				<pre>"; $count_tmp=0;
			foreach($tmp1 as $k => $d) {
				if ($d) {
					$d_tmp=str_replace("#", "\t", $d);
					$detalle.=$d_tmp.PHP_EOL;
					$tmp_contador++;
				}
			}
			$detalle.="</pre></div>";
		}
	}
	return $tmp_contador;
}

switch($_GET["opcion"]) {
	case "mal_uso_etiquetas":
		$fichero=$dir_oper.$dir_datos."/mal_uso_etiquetas.dat";
		$texto_titulo="ETIQUETAS: mal uso";
		break;
	case "check_etiquetas":
		$fichero=$dir_oper.$dir_datos."/etiquetas1.dat";
		$texto_titulo="INICIO DIA: Etiquetas mal";
		break;
	default: die("ERROR");
}

if (!empty($fichero))
	$nume_lista=get_detalle($fichero,$detalle);

$red1=($nume_lista>0?"red":"");
?>
<div>
	<i class="hora_fichero"><?php echo (file_exists($fichero)?date("d/m/y H:i:s",filemtime($fichero)):""); ?></i>
	<table style="padding:0; margin:0 0 auto; width:100%">
		<tr>		
			<td><span class="titulo_cuadro"><?php echo $texto_titulo; ?></span></td>
			<td><span class="num_grande <?php echo $red1; ?>"><?php echo $nume_lista; ?></span></td>
		</tr>
	</table>
	<?php if (!empty($_GET["detalles"])) echo "<hr>".$detalle; ?>
</div>