<?php
require("./comun_dashboard.php");

$nume_lista=0;
$detalle="";

function get_detalle($fichero,&$detalle,$cuenta_especial="") {
	$tmp_contador=0;
	if (file_exists($fichero)) {
		if (filesize($fichero) > 0) {
			$tmp=file_get_contents($fichero);
			$tmp1=explode("\n",$tmp);
			$detalle="";
			foreach($tmp1 as $k => $d) {
				if ($d) {
					$d_tmp=str_replace("#", "\t", $d);
					if (empty($cuenta_especial)) {
						$tmp_contador++;
						$detalle.=$d_tmp.PHP_EOL;
					} else {
						if (preg_match("/".$cuenta_especial."/", $d)) {
							$tmp_contador++;
							$detalle.="<b style='color:red'>".$d_tmp."</b>".PHP_EOL;
						}
/*						else 
							$detalle.=$d_tmp.PHP_EOL;*/
					}
				}
			}
		}
	}
	if ($tmp_contador==0)
		$detalle="<div class='NO_HAY_DATOS'>No hay datos</div>";
	return $tmp_contador;
}
function get_detalle_nume($fichero,&$detalle,$cabecera,$tama,$cuenta_especial="") {
	$tmp_contador=0;
	$tmp=file_get_contents($fichero);
	if (!empty($tmp)) {
		$tmp1=explode("\n",$tmp);
		$detalle="<table class='TABLA2' style='width:$tama'><tr>";
		foreach($cabecera as $k => $d) {
			$detalle.="<th>".$d."</th>";
		}
		$detalle.="</tr>";
		$tmp_contador=count($tmp1);
		foreach($tmp1 as $k => $d) {
			if ($d) {
				$d_tmp=explode(",",$d);
				$detalle.="<tr>";
				foreach($d_tmp as $k1 => $d1) {
					$detalle.="<td>".intval($d1)."</td>";
				}
				$detalle.="</tr>";
			}
		}
		$detalle.="</table>";
	}
	if ($tmp_contador==0)
		$detalle="<div class='NO_HAY_DATOS'>No hay datos</div>";
	return $tmp_contador;
}

switch($_GET["opcion"]) {
	case "mal_uso_etiquetas":
		$fichero=$dir_oper.$dir_datos."/mal_uso_etiquetas.dat";
		$texto_titulo="ETIQUETAS: mal uso";
		$nume_lista=get_detalle($fichero,$detalle, "MAL USO");
		$ayuda="Mal uso de las etiquetas: si la fecha de la ultima generación automática es menor a hoy menos tres días.";
		break;

	case "mal_uso_escalones":
		$fichero=$dir_oper.$dir_datos."/mal_uso_escalones.dat";
		$texto_titulo="ESCALONES: mal uso";
		$nume_lista=get_detalle($fichero,$detalle, "MAL USO");
		$ayuda="Mal uso de los escalones: si la fecha del último uso es menor a hoy menos tres días.";
		break;

	case "check_etiquetas":
		$fichero=$dir_oper.$dir_datos."/etiquetas1.dat";
		$texto_titulo="INICIO DIA: Etiquetas mal";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Muestra si ha habido errores en la generacion de etiquetas, a partir de transacciones de ETL (trx.22).&#10;Si hay un valor mayor que cero, hay que avisar a SEDE";
		break;

	case "check_entregas_desaparecidas":
		$fichero=$dir_oper.$dir_datos."/entregas_desaparecidas.dat";
		$texto_titulo="Entregas Desaparecidas";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Determina qué movimientos se han eliminado la noche en que se hacen inventarios.&#10;Se instaló un HOTFIX para solucionarlo el 18/12/2017, se mantiene esta sonda hasta el 28/02/2018";
		break;

	case "check_franjas_prioritarias":
		$fichero=$dir_oper.$dir_datos."/franjas_prioritarias.dat";
		$texto_titulo="Franjas prioritarias";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Un pedido que se ejecuta en VELA, pero no llega a almacén (dos franjas prioritarias).&#10;
				Se lanza en VELA, aumenta las pendiente de servir, y como no llega a almacen, éste no sirve la mercancía y  por lo que las PS no se decrementan.&#10;
				&#10;Corrección manual por parte de soporte remoto nivel 3, hasta la implantación de VELA 2.&#10;";
		break;
	
	case "check_transacciones_ETL":
		$fichero=$dir_oper.$dir_datos."/transacciones_ETL.dat";
		$texto_titulo="INICIO DIA: trx. status 4";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Muestra si ha habido algunas transacciones con estatus 4 durante el proceso del ETL (error)";
		break;

	case "check_cambio_precios":
		$fichero=$dir_oper.$dir_datos."/cambio_precios.dat";
		$texto_titulo="PROBLEMA CAMB.PRECIOS";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Cuando no tenemos tarifa futura.";
		break;

	case "check_precios_mal_fechas":
		$fichero=$dir_oper.$dir_datos."/precios_mal.dat";
		$texto_titulo="PRECIOS MAL (FECHAS)";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="<b>Si existen registros en el que el END_DATE es menor al BEGIN_DATE.</b> Ademas se queda sin precio pasado al día siguiente.";
		break;
		
	case "check_tiendas_vela":
		$fichero=$dir_oper.$dir_datos."/tiendas_diff.dat";
		$texto_titulo="CONFIG.TIENDAS VELA";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Muestra las tiendas que estan mal configuradas&#10;
			<ul>
				<li>ERROR CONFIG. TPVs: en VELA la tienda está OK, pero en la tienda, la master está mal configurada&#10;</li>
				<li>TIENDA NO EXISTE EN BBDD TIENDAS: la tienda está de alta en VELA, pero no en AS/400&#10;</li>
				<li>ERROR CONFIG. BBDD VELA: la TPV está correctamente configurada, pero en VELA aún está como PREVELA&#10;</li>
				<li>TIENDA NO EXISTE EN BBDD VELA: la tienda está de alta en AS/400, pero no está de alta en VELA.&#10</li>
			</ul>&#10
			<p><b><i>NOTA IMPORTANTE:</i></b> se han excluido las tiendas CASH&CARRY de esta comprobacion</p>";
		break;

	case "check_previsiones_venta_altas":
		$fichero=$dir_oper.$dir_datos."/previsiones_venta_alta.dat";
		$texto_titulo="PREVISIONES VTA ALTAS";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Previsiones de venta muy altas (desbordamiento > 4.3)";
		break;

	case "check_previsiones_venta_cash":
		$fichero=$dir_oper.$dir_datos."/previsiones_venta_cash.dat";
		$texto_titulo="PREVISIONES VTA CASH";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Previsiones de venta muy altas para la tienda CASH & CARRY";
		break;

	case "check_previsiones_venta_negativas":
		$fichero=$dir_oper.$dir_datos."/previsiones_venta_negativa.dat";
		$texto_titulo="PREVISIONES VTA NEGAT.";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Previsiones de venta negativas";
		break;

	case "check_pendientes_servir_altas":
		$fichero=$dir_oper.$dir_datos."/pendientes_servir_altas.dat";
		$texto_titulo="PENDIENTES SERV. ALTAS";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Pendientes de servir muy altas (desbordamiento > 5,3)";
		break;

	case "check_stocks_muy_altos":
		$fichero=$dir_oper.$dir_datos."/stocks_muy_altos.dat";
		$texto_titulo="STOCK MUY ALTOS";
		$nume_lista=get_detalle($fichero,$detalle);
		$ayuda="Stocks muy altos (desbordamiento > 5,3)";
		break;

	case "check_varios_articulos_surtido":
		$fichero=$dir_oper.$dir_datos."/varios_articulos_surtido.dat";
		$texto_titulo="VARIOS ART. SURTIDO";
		$ancho=300; $cabecera=array('Articulo','Tienda (B.Unit)','Cantidad');
		$nume_lista=get_detalle_nume($fichero,$detalle,@$cabecera,@$ancho);
		$ayuda="<b>SONDA DE DESARROLLO VELA</b>&#10;<p>Aquí se identifican los artículos que tienen varios registros en la vista de artículos de surtido.	
			Esta situación es crítica para etiquetas, y para mas procesos que estén esperando un solo registro por artículo.</p>";
		break;
		
	case "diff_tiendas":
	case "stock_negativo":
	case "franjas_duplicadas":
	case "ficheros_venta":
		header("location:".$DIR_VELA."/".$_GET["opcion"].".php?detalles=".@$_GET["detalles"]."&refresco=".@$_GET["refresco"]."&correo=".@$_GET["correo"]);
		exit;

	default: die("ERROR");
}

$array_refresco=array("3" => "Cada 5 minutos", "2" => "Cada hora", "1" => "1 vez al día");
$array_refresco_class=array("3" => "cinco_min", "2" => "cada_hora", "1" => "una_vez");

$hora_fichero=(file_exists($fichero)?date("d/m/y H:i:s",filemtime($fichero)):"Fichero no disponible");
if (!empty($ayuda)) {
	$refresco=(!empty($_GET["refresco"])?$array_refresco[$_GET["refresco"]]:"N/D");	
	$ayuda.="&#10;&#10;<br>FRECUENCIA ACTUALIZACION DATOS: ".$refresco."&#10;Ultima revision: ".$hora_fichero;
}

if(!empty($_GET['detalles'])) {
	$class_refresco="";
	if ($nume_lista > 0) {
		$file_temporal=$DIR_TMP.basename($fichero);
		file_put_contents($DOCUMENT_ROOT.$file_temporal,file_get_contents($fichero));
	}
}
else
	$class_refresco=(!empty($_GET["refresco"])?$array_refresco_class[$_GET["refresco"]]:"");

$url_local=get_url_from_local(__FILE__);

$red1=($nume_lista>0?"red":"");
?>
<div class="<?php echo $class_refresco; ?>" title="<?php if(empty($_GET['detalles'])) echo strip_tags(@$ayuda); ?>">
	<i class="hora_fichero"><?php echo @$hora_fichero; ?>  <?php if (!empty($_GET["correo"])) { echo "<i class='fas fa-envelope'></i>";} ?></i>
	<table style="padding:0; margin:0 0 auto; width:100%">
		<tr>		
			<td><span class="titulo_cuadro"><?php echo $texto_titulo; ?></span></td>
			<td><span class="num_grande <?php echo $red1; ?>"><?php echo $nume_lista; ?></span></td>
		</tr>
	</table>
	<?php
		if (!empty($_GET["detalles"])) {
			echo "<i style='font-size:12px'>".str_replace("&#10;","<br>",$ayuda)."</i>";
			echo "<hr>";
			if (!empty($file_temporal)) echo "<button id='descargar'>Descargar fichero</button><div id='dummy'></div>";
			if (!empty($ancho))
				echo "<div class='of_y' style='height:510px; margin:1em 0 0 1em; width:".($ancho+10)."'>".$detalle."</div>";
			else
				echo "<div class='of_y' style='font-size:10px;height:575px'><pre>".$detalle."</pre></div>";
			if (!empty($file_temporal)) {
				echo "<script>
					$('#descargar').on('click',function() {
						window.open('".$file_temporal."','_blank');
						});
					</script>";
			}
		}
	?>
</div>