<title>VELA - DASHBOARD</title>
<?php
require_once("./comun_dashboard.php");

/*
		<div class="flota paneles v_info sombra_normal">
			<div class="info info_peque" src="<?php echo $DIR_VELA;?>/franjas_duplicadas.php" detalles="?detalles=true" refresco="2"></div>
		</div>

*/

$array_sondas=array(
	 "check_etiquetas" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
	,"mal_uso_etiquetas" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
	,"mal_uso_escalones" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
	,"check_cambio_precios" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
	,"check_tiendas_vela" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
//	,"check_franjas_prioritarias" => array( "detalles" => "true", "refresco" => 1, "correo" => false)
	,"check_precios_mal_fechas" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
 	,"check_previsiones_venta_altas" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
	,"check_previsiones_venta_cash" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
	,"check_previsiones_venta_negativas" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
	,"check_pendientes_servir_altas" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
	,"check_stocks_muy_altos" => array( "detalles" => "true", "refresco" => 1, "correo" => false )
	,"stock_negativo" => array("detalles"=>"true", "refresco" => 1, "correo" => false)
	,"check_varios_articulos_surtido" => array("detalles"=>"true", "refresco" => 1, "correo" => false)
	
	,"diff_tiendas" => array("detalles"=>"true", "refresco" => 2, "correo" => false)
	,"franjas_duplicadas" => array("detalles"=>"true", "refresco" => 2, "correo" => false)
//	,"check_entregas_desaparecidas" => array("detalles"=>"true", "refresco" => 2, "correo" => false)
//	,"check_franjas_prioritarias" => array("detalles"=>"true", "refresco" => 2, "correo" => false)

	,"ficheros_venta" => array("detalles"=>"true", "refresco" => 3, "correo" => true)
);

$txt_sondas=array();
foreach($array_sondas as $k => $d) {
	@$txt_sondas[$d["refresco"]].='<div class="flota paneles v_info sombra_normal"><div class="info info_peque" src="'.$DIR_VELA.'/general.php?opcion='.$k.'" detalles="&detalles='.$d["detalles"].'" refresco="'.$d["refresco"].'" correo="'.$d["correo"].'"></div></div>';
}
?>

<style>
	.una_vez { background-color:#A9CCE3; }
	.cinco_min { background-color:#76D7C4 }
	.cada_hora { background-color:#F9E79F }

	.general {
		width: <?php echo $anchura_vista; ?> !important; height: <?php echo $altura_vista; ?>; max-height: <?php echo $altura_vista; ?> !important;
		font-family: "Lato", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-smoothing: antialiased;
	}
	.v_info {
			margin: 2px;
			background: linear-gradient(#eeefef, #ffffff 20%);
			border:1px solid gray;
			border-radius: 3px;
			padding: 3px;
			cursor: pointer;
	}
	.sombra_normal {
		box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 3px 7px rgba(0, 0, 0, 0.3), inset 0 1px rgba(255,255,255,1), inset 0 -3px 2px rgba(0,0,0,0.25);
	}
	.v_info:hover {
		background: linear-gradient(#ffffff,#eeefef 20%);
	}
	.float { float: left; }
	.info_grande { width: 100%; height: 400px; }
	.info_peque  {	width: 100%; }
	.info_arriba { width: 1000px; font-size: 14px; }
	
	.titulo_cuadro { font-size: 14px; font-weight: bold; text-align: center; text-transform: uppercase; }
	.num_grande { font-weight: bold; font-size: 30px; text-align: center; margin-top: 15px; }
	.info_bottom { font-size: 12px; text-align: center; }
	
	.t1 {
			width: 100%;
	}
	.t1 td { text-align:center; }
	.t1 td:first-child { text-align:left; padding-left:1em; }
	.t1 th:first-child { text-align:left; padding-left:1em; }
	.v_info ul li { font-weight: normal; margin-bottom: 1em; }
	
	.detalles {
		font-size:10px; cursor: pointer; color:blue;
	}
	.info_adicional { float:right; text-align:right; margin-right: 10px; }
	#b_volver { float: right; margin:5px 5px 0 0; }	
	.of_y     { overflow-y: auto; }

	.b_descargar_fichero {
		font-size: 10px; margin-left: 1em; margin-top: 1em;
		border:1px solid black;
	}
	.b_descargar_fichero:hover { background-color: white; }
	
	.tabla_general {
		border:1px solid black;
		border-radius: 3px;
		background: linear-gradient(#eeefef, #ffffff 20%);
		width:100%; height:95%
	}
	#info_detalle {
		height:700px; 
	}
	.info_descarga { width:100%; }
	.t_a_right  { text-align:right; }	
	.t_a_left   { text-align:left; }
	.t_a_center { text-align:center; }
	.red { color: red; }
	.ico_ayuda {
		float:left;
		margin-right:2em;
	}
	.flota {
			float:left;
			position: relative;
			width: 300;
	}
	#izquierda {
		width: 625px;
	}
	#derecha {
		float:left;
		position: relative;
		width: 550px;
		
	}
	#izquierda, #derecha .v_info {
		height: 800px;
	}
	.info_detalle_2 {
		overflow-y: auto;
		height:600px;
	}
	.hora_fichero { font-size:10px;float:left; position:absolute; top:1; left:1; }
	
	.NO_HAY_DATOS {
			font-size: 20px; font-weight: bold; text-align: center;
			border:1px solid black; margin:0 0 auto;
	}
</style>

<div id="v_general" class="general">
	<div id="izquierda" class="flota v_info sombra_normal">
		<table width="100%" style="text-align:center; font-size:12px;">
			<tr><td width="33%" class="cinco_min">Cada cinco minutos</td><td width="33%" class="cada_hora">Cada hora</td><td width="33%" class="una_vez">Una vez al dia (07:00)</td></tr>
		</table>
		<hr>
		<?php echo $txt_sondas[3]; ?>
		<?php echo $txt_sondas[2]; ?>
		<?php echo $txt_sondas[1]; ?>

	</div>
	<div id="derecha" class="sombra_normal">
		<div class="paneles v_info">
				<div id="dashboard1" class="info info_grande" src="<?php echo $DIR_VELA;?>/procesos_etl.php" detalles="#" refresco="0" correo="false"></div>
		</div>
	</div>
</div>
<div id="v_detalles" class="general" style="display:none;">
	<div class="tabla_general">
		<button id='b_volver' onclick="Muestra_dashboard();">Volver al dashboard...</button>
		<div id="info_detalle" style="display:block"></div>
	</div>
</div>


<script>
	var Activo=false;
	var myActualiza_datos=null;
	
	function Carga_url(x) {
		var url=$(x).attr("src");
		$(x).load(url);
	}
	
	$(".paneles .info").each(function () {
		var url=$(this).attr("src");
		var detalles=$(this).attr("detalles");
		if (detalles) {
			var url_detalles=url+detalles;
		}
	});
	$(".paneles .info").on("click",function () {
		if ($(this).attr("detalles") != "#") {
			var url_detalles=$(this).attr("src")+$(this).attr("detalles")+"&refresco="+$(this).attr("refresco");
			Muestra_detalles(url_detalles);
		}
	})

	function Ir_Detalle_Dashboard() {
		Muestra_detalles($("#dashboard1").attr("src")+"?detalles=true");
	}

	function Actualiza_datos() {
		$(".paneles .info").each(function () {
			var url=$(this).attr("src");
			var refresco=$(this).attr("refresco");
			var correo=$(this).attr("correo");
			if (url) {
				if (url.match("general.php") != null ) {
					url = url + "&refresco="+refresco+"&correo="+correo;
				}
				l1=url.split('/').length;
				if (refresco != 0) $(this).load(url);
			}
		});
		
		if (Activo === true) {
			if (myActualiza_datos) clearTimeout(myActualiza_datos);
			myActualiza_datos=setTimeout(Actualiza_datos, 5000);
		}
	}
	function Muestra_dashboard() {
		Activo=true;
		$("#v_general").show(); $("#v_detalles").hide();
		Actualiza_datos();
		if (typeof(drawCharts_etl) === "function" ) google.charts.setOnLoadCallback(drawCharts_etl);
	}
	function Muestra_detalles(x) {
		Activo=false;
		$("#v_general").hide(); $("#v_detalles").show();
		$("#info_detalle").empty().load(x);
		
	}

	Muestra_dashboard();
	$("#dashboard1").load($("#dashboard1").attr("src"));

	$("#CUERPO").css("height","0");
</script>
