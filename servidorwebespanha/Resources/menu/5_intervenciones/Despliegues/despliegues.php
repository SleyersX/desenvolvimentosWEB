<?php
require("../../cabecera_vistas.php");

$local_url=get_url_from_local(__FILE__);

if (!empty($_GET["opcion"])) {
	switch($_GET["opcion"]) {
		case "get_info_total":
			$NO_NO=" CAJA SIN ACTUALIZAR - CAJA SIN PAQUETE DISTRIBUIDO";
			$NO_SI=" CAJA SIN ACTUALIZAR - CAJA CON PAQUETE DISTRIBUIDO";
			$SI_NO=" CAJA ACTUALIZADA - CAJA SIN PAQUETE DISTRIBUIDO";
			$SI_SI=" CAJA ACTUALIZADA - CAJA CON PAQUETE DISTRIBUIDO";
			$Leyenda="<table class='leyenda'>
				<tr class='NO_NO'><td>NO/NO</td><td>$NO_NO</td></tr>
				<tr class='NO_SI'><td>NO/SI</td><td>$NO_SI</td></tr>
				<tr class='SI_NO'><td>SI/NO</td><td>$SI_NO</td></tr>
				<tr class='SI_SI'><td>SI/SI</td><td>$SI_SI</td></tr>
			</table>";

			$Botones="
				<button id='refrescar'>Refrescar</button>
				<button id='filtro1'>Pendientes distribuir</button>
				<button id='filtro2'>Distribuidas</button>
					<script>
						$('#refrescar').on('click',function() { $('#info_total').load('".$local_url."?opcion=get_info_total'); });
						$('#filtro1').on('click',function() { $('#info_total').load('".$local_url."?opcion=get_info_total&filtro=1'); });
						$('#filtro2').on('click',function() { $('#info_total').load('".$local_url."?opcion=get_info_total&filtro=2'); });
					</script>";

			$CAJAS=" sum( case when a.caja=1 then (version='38.001.16.BRA-0001')+((md5sum='5b8d-5b8d-5b8d-5b8d-5b8d-5b8d-5b8d-')*10) else 0 end) as caja1";
			for($i=2;$i<=9;$i++) {
				$CAJAS.=",sum( case when a.caja=$i then (version='38.001.16.BRA-0001')+((md5sum='5b8d-5b8d-5b8d-5b8d-5b8d-5b8d-5b8d-')*10) else 0 end) as caja$i";
			}
			$FILTRO="";
			$txt_filtro="Información total";
			if (!empty($_GET["fitro"])) {
				switch($_GET["filtro"]) {
					case 1: // PENDIENTES DE DISTRIBUIR
						$txt_filtro="Pendientes de distribuir";
						$FILTRO="where md5sum<>'5b8d-5b8d-5b8d-5b8d-5b8d-5b8d-5b8d-'"; break;
					case 2: // DISTRIBUIDAS
						$txt_filtro="Ya distribuidas";
						$FILTRO="where md5sum='5b8d-5b8d-5b8d-5b8d-5b8d-5b8d-5b8d-'"; break;
						break;
				} 
			}
			$data = myQUERY("select a.tienda, b.NTPVS, $CAJAS from Versiones_Futuras a join ChecksBRA b on a.tienda=b.tienda and a.caja=b.caja $FILTRO group by 1");
			$cabecera=array("Tienda", "N.TPVS","Caja1","Caja2","Caja3","Caja4","Caja5","Caja6","Caja7","Caja8","Caja9");
			$t_tabla="<table class='tabla2 nueva_vista'><caption>$txt_filtro</caption><thead><tr>";
			foreach($cabecera as $k => $d) $t_tabla.="<th>$d</th>";
			$t_tabla.="</tr></thead><tbody>";
			
			foreach($data as $k => $d) {
				list($Tienda,$NTPVS,$Caja1,$Caja2,$Caja3,$Caja4,$Caja5,$Caja6,$Caja7,$Caja8,$Caja9) = $d;
				$Cajas="";
				for($c=1;$c<10;$c++) {
					$v="Caja$c";
					if ($c > $NTPVS)
						$Cajas.="<td></td>";
					else {
						switch ($$v) {
							case "0": $Cajas.="<td class='NO_NO' title='".str_replace("-","\n",$NO_NO)."'>NO/NO</td>"; $GLOBAL="NO_NO"; break;
							case "1": $Cajas.="<td class='SI_NO' title='".str_replace("-","\n",$NO_SI)."'>SI/NO</td>"; $GLOBAL="NO_SI";break;
							case "10": $Cajas.="<td class='NO_SI' title='".str_replace("-","\n",$SI_NO)."'>NO/SI</td>"; $GLOBAL="SI_NO";break;
							case "11": $Cajas.="<td class='SI_SI' title='".str_replace("-","\n",$SI_SI)."'>SI/SI</td>"; $GLOBAL="SI_SI";break;
							default: "<td></td>"; $GLOBAL=""; break;
						}
					}
				}
				$t_tabla.="<tr><td>$Tienda</td><td>$NTPVS</td>".$Cajas."</tr>";
			}
			
			$t_tabla.="</tbody></table>";
			echo "<div><table><tr><td>$Leyenda</td><td>$Botones</td></tr></table></div>";
			echo "<div class='res_info_tienda'>".$t_tabla."</div>";
			exit;
	}
	exit;
}

?>

<style>
	.leyenda, .info_cesiones {
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
    .NO_NO { background-color: red; color:white;}
    .NO_SI { background-color: orange; color:black;}
    .SI_NO { background-color: yellow; color:black;}
    .SI_SI { background-color: green; color:black;}
    
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
	function Recarga_Info(filtro) {
		$("#info_total").load("<?php echo $local_url.'?opcion=get_info_total'; ?>"+"&filtro="+filtro);
	}
	$("#info_total").ready(function () {
		Recarga_Info("sin_filtro");
	})
	
</script>