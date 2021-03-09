<?php
require("../../cabecera_vistas.php");
if (SoyYo()) require($DOCUMENT_ROOT.$DIR_LIBRERIAS."/chart.php");
$local_url=get_url_from_local(__FILE__);
$tmp=file_get_contents("/home/soporteweb/tools/VELA/datos/listado_tiendas_vela_completo.dat");
$listado_tiendas_vela=array();
if ($tmp) {
	$res=explode("\n",$tmp);
	foreach($res as $k => $d) {
		if ($d) {
			$dd=explode(",",$d);
			$listado_tiendas_vela[$dd[1]]=$dd[0];
		}
	}
}

$fecha_inicial=$fecha_actual=date("Y-m-d");
$fecha_actual=(empty($_GET["fecha_actual"])?date("Y-m-d"):$_GET["fecha_actual"]);
$fecha_inicial=(empty($_GET["fecha_inicial"])?date("Y-m-d"):$_GET["fecha_inicial"]);

$recarga_url_total_tiendas=$local_url."?opcion=total_tiendas&fecha_inicial=\"+$('#fecha_desde').val()+\"&fecha_actual=\"+$('#fecha_hasta').val()";
$recarga_url_info_tiendas=$local_url."?opcion=info_tienda&tienda=\"+tienda+\"&fecha_inicial=\"+$('#fecha_desde').val()+\"&fecha_actual=\"+$('#fecha_hasta').val()";
$recarga_url_info_tiendas_pendientes=$local_url."?opcion=info_tienda_pendientes&tienda=\"+tienda";
$recarga_url_csv=$local_url."?opcion=export_to_csv&fecha_inicial=\"+$('#fecha_desde').val()+\"&fecha_actual=\"+$('#fecha_hasta').val()";
$recarga_url_pendientes=$local_url."?opcion=ver_pendientes\"";

$filename="/home/cesionesonline/resultados/ultima_revision";
if (file_exists($filename))
	$ultima_revision=file_get_contents($filename);
else
	$ultima_revision="N/D";

if (!empty($_GET["opcion"])) {
	switch($_GET["opcion"]) {
		case "export_to_csv":
			$data = myQUERY("select * from seguimiento_cesiones where tienda<98000 and (date(fecha_salida)>=date('".$fecha_inicial."') AND date(fecha_salida)<=date('".$fecha_actual."'))");
			array_unshift($data,array("Tienda", "Cesion", "Fecha Salida", "Fecha Entrada"));
			download_send_headers("seguimiento_cesiones.csv");
			echo array2csv($data);
			exit;

		case "info_tienda":
		case "info_tienda_pendientes":
			$tienda=@$_GET["tienda"];
			if ($_GET["opcion"]=="info_tienda_pendientes") {
				$error_conexion="--- CESIONES PENDIENTES ---";
				$tmp=myQUERY("select *,timediff(NOW(),fecha_salida) from seguimiento_cesiones where tienda=".$tienda." and fecha_entrada is null order by fecha_salida");
				$class_error="pendientes";
			} else {
				$error_conexion="Sin problemas de acceso"; $class_error="";
				$filename="/home/cesionesonline/resultados/".sprintf("%05d",$tienda).".error_conexion";
				if (file_exists($filename)) {
					if (preg_match("/Connection timed out/",file_get_contents($filename))) {
						$error_conexion="Error de conexion con la tienda.";
						$res_ip=myQUERY("select IP from tmpTiendas where numerotienda=".$tienda);
						$error_conexion.="<br>IP acceso: ".$res_ip[0][0];
						$class_error="pendientes";
					}
				}
				$tmp=myQUERY("select *,timediff(IFNULL(fecha_entrada,NOW()),fecha_salida) from seguimiento_cesiones where tienda=".$tienda." and (date(fecha_salida)>=date('".$fecha_inicial."') AND date(fecha_salida)<=date('".$fecha_actual."')) order by fecha_salida");
			}
			
			$t_tabla="<table class='tabla2 nueva_vista'><thead><tr><th>Tienda</th><th>Cesion</th><th>Fecha Salida</th><th>Fecha Entrada</th><th>Tiempo</th></thead>";
			foreach($tmp as $k => $d) {
				list($tienda,$cesion,$fecha_salida,$fecha_entrada,$correo,$diff)=$d;
				$t_tabla.="<tr class='".($fecha_entrada==""?"pendientes":"")."'>";
				$t_tabla.="<td >".$tienda."</td>";
				$t_tabla.="<td class='cesiones'>".$cesion."</td>";
				$t_tabla.="<td class='cesiones'>".$fecha_salida."</td>";
				$t_tabla.="<td class='cesiones'>".$fecha_entrada."</td>";
				$clase_diff="";
				if ($diff >= "01:00:00") $clase_diff="diff_yellow";			
				if ($diff >= "03:00:00") $clase_diff="diff_red";
				$t_tabla.="<td class='cesiones $clase_diff'>$diff</td>";
				$t_tabla.="</tr>";
			}
			$t_tabla.="</table>";
			echo "<div class='res_conexion $class_error'>".$error_conexion."</div>";
			echo "<div class='res_info_tienda'>".$t_tabla."</div>";
			exit;
		
		case "total_tiendas":
		case "ver_pendientes":
			$total_pendientes=$total_entregadas=0;
			if ($_GET["opcion"] == "ver_pendientes") {
				//$tmp=myQUERY("select * from seguimiento_cesiones where fecha_entrada is null and tienda<98000");
				$tmp=myQUERY("select
				tienda
				,centro, tipo, subtipo
				,sum(case when fecha_entrada is null then 1 else 0 end) as Pendientes
				,sum(case when fecha_entrada is NOT null then 1 else 0 end) as Entregadas
				from seguimiento_cesiones a join tmpTiendas b on a.tienda=b.numerotienda
				WHERE tienda<98000 and fecha_entrada is null group by tienda");
			}
			else {
			$tmp=myQUERY("select
				tienda
				,centro, tipo, subtipo
				,sum(case when fecha_entrada is null then 1 else 0 end) as Pendientes
				,sum(case when fecha_entrada is NOT null then 1 else 0 end) as Entregadas
				from seguimiento_cesiones a join tmpTiendas b on a.tienda=b.numerotienda
				WHERE tienda<98000 and (date(fecha_salida)>=date('".$fecha_inicial."') AND date(fecha_salida)<=date('".$fecha_actual."'))
				group by tienda");
			}
			if (count($tmp)>0) {
				$t_tabla_total="<div style='height:730px; overflow-y:auto;'><table class='tabla2 nueva_vista'><thead><tr><th>Tienda</th><th>Centro</th><th>Tipo-Subtipo</th><th>VELA/TPV</th><th>Pendientes</th><th>Entregados</th><th>TOTAL</th></thead>";
				foreach($tmp as $k => $d) {
					list($tienda,$centro, $tipo, $subtipo,$p,$e)=$d;
					if (array_key_exists($tienda,$listado_tiendas_vela))
						$es_vela="BO.VELA";
					else 
						$es_vela="VELA POS";
					$t_tabla_total.="<tr class='tienda ".($p?"pendientes":($es_vela=="BO.VELA"?"es_vela":""))."'>";
					$t_tabla_total.="<td class='tienda'>".$tienda."</td>";
					$t_tabla_total.="<td class='tienda centro'>".$centro."</td>";
					$t_tabla_total.="<td class='tienda centro'>".$tipo."-".$subtipo."</td>";
					$t_tabla_total.="<td class='tienda centro'>".$es_vela."</td>";
					$t_tabla_total.="<td class='cesiones'>".$p."</td>"; @$total_pendientes+=$p;
					$t_tabla_total.="<td class='cesiones'>".$e."</td>"; @$total_entregadas+=$e;
					$t_tabla_total.="<td class='".($p==0?"totales":"")."'>".($p+$e)."</td>";
					$t_tabla_total.="</tr>";
				}
				$t_tabla_total.="</table></div>";
			} else {
				$t_tabla_total="<div id='error_no_registros'><p>No hay registros para esas fechas</p></div>";
			}
			$t_total_total="
			<table style='width: 100%; border: 1px solid gray; border-radius: 3px; background-color: white;'>
				<tr>
					".($_GET["opcion"] != "ver_pendientes"?"
					<td class='info_cesiones fechas'>
						<table style=''>
							<tr>
								<td><label>Fecha desde:</label><input id='fecha_desde' type='date' value='$fecha_inicial'></td>
							</tr>
							<tr>
								<td><label>Fecha hasta:</label><input id='fecha_hasta' type='date' value='$fecha_actual'></td>
							</tr>
						</table>
					</td>":"")."
					<td class='info_cesiones'>Ultima Revision:</br><span>".$ultima_revision."</span></td> 
					<td class='info_cesiones'>Pendientes<span class='numero_cesiones ".($total_pendientes?"c_red":"")."'>$total_pendientes</span></td>
					".($_GET["opcion"] != "ver_pendientes"?"<td class='info_cesiones'>Entregados<span class='numero_cesiones'>$total_entregadas</span></td>":"")."
					".($_GET["opcion"] != "ver_pendientes"?"<td class='info_cesiones'>TOTAL<span class='numero_cesiones'>".($total_pendientes+$total_entregadas)."</td>":"")."
				</tr>
				<tr>
					<td colspan='5' class='info_cesiones' style='height:auto;'>
						<table style='width:100%'>
							<tr>
								<td><button id='export_to_csv'>Exportar a CSV</button><div id='carga_csv' style='display:none;'></div></td>
								<td><button id='refresh' style='font-size:12px; margin-left.1em'>Refresh</button></td>
								<td><button id='ver_pendientes' style='font-size:12px; margin-left.1em'>Ver pendientes</button></td>
								<td  style='font-size:12px; text-align:right'><i>Pulse en una tienda para ver los detalles<i></td>
							</tr>
						</table>
				</tr>
			</table>";
			echo $t_total_total;
			echo $t_tabla_total;

			echo '
			<script>
				$(".nueva_vista tr").on("click",function () {
					var tienda=$(this).find("td:first")[0].textContent;
			'.($_GET["opcion"] != "ver_pendientes"?'$("#info_tienda").empty().load("'.$recarga_url_info_tiendas.');':'$("#info_tienda").empty().load("'.$recarga_url_info_tiendas_pendientes.');').'
					$("#info_tienda").show();
				})
				$("#fecha_desde").on("change",function (){ $("#info_total").load("'.$recarga_url_total_tiendas.'); $("#info_tienda").hide(); });
				$("#fecha_hasta").on("change",function (){ $("#info_total").load("'.$recarga_url_total_tiendas.'); $("#info_tienda").hide(); });
				$("#export_to_csv").on("click",function() {
					window.open("'.$recarga_url_csv.');
				});
				$("#refresh").on("click",function() {
					$("#info_tienda").empty().hide();
					Recarga_Info();
				});
				$("#ver_pendientes").on("click",function() {
					$("#info_total").load("'.$recarga_url_pendientes.');
				});
			</script>
			';
		exit;
	}
	exit;
}

?>

<style>
	.info_cesiones {
			font-family: sans-serif, arial; text-align: center; font-size: 12;
			background-color: white;
			color: gray;
			height: 50px;
			 
			border:1px solid gray; border-radius: 3px;
	}
	.numero_cesiones {
		display: block;
	   padding: 0 1em 0 1em;
    	font-weight: bold;
    	font-size: 20;
    }
	.nueva_vista { font-family: sans-serif, arial; margin:0px; }
	.nueva_vista CAPTION { background-color: lightcyan; border-radius: 5px 5px 0 0;}
	.nueva_vista CAPTION a { text-decoration:none; font-weight: bold; font-size:110%; }
	.nueva_vista td { text-align:right; border-left: 1px solid #999; padding-right:1em; }
	.nueva_vista .centro { text-align:center; padding:0 0.5em 0 0.5em; }
	.nueva_vista th { border-left: 1px solid #999; padding: 3px; text-align: center;}
	.cesiones:hover { background-color: lightgreen !important; }
	.nueva_vista tr:hover { color: black; }
	.pendientes { background-color: red; color:white; }
	.totales { background-color: honeydew; font-weight: bold; }
	#resultado_vista_general { width:100%; height:90%; max-width:100%; padding:0px; top:50px;}
	#resultado_vista_general .modal-body { margin-top:1em; height:94%; }
	.res_conexion { font-family: sans-serif; text-align: center; margin-bottom: 1em; font-weight: bold;}
	.res_info_tienda { height: 750px !important; overflow-y: auto;}
	.diff_red { background-color: red; color:white;}
	.diff_yellow { background-color: yellow; color:black;}
	.c_red { color: red; }
	.es_vela { background-color: lightblue;}
	#error_no_registros {
		font-family: sans-serif, arial; background-color: floralwhite; height: 100; text-align: center;
    	border: 1px solid gray; margin-top: 1em; border-radius: 3px; font-size: 1.25em;
	}
	#fecha_desde , #fecha_hasta { border: 0; font-family: sans-serif, arial;text-align:center; }
	.info_cesiones label { font-size: 10px; }
	.info_cesiones button { font-size:12px; margin-left.1em; }
</style>

<div class="PANEL" id="info_total"></div>
<div class="PANEL" id="info_tienda" style="display:none"></div>


<script>
	function Pinta_Grafico() {
		console.log("Pinta_Grafico");
		
	}
	function Recarga_Info() {
		$("#info_total").load("<?php echo $local_url.'?opcion=total_tiendas'; ?>");
		Pinta_Grafico();
	}
	$("#info_total").ready(function () {
		Recarga_Info();
	})
	
</script>