<?php
//session_set_cookie_params('60'); // 1 minutes.
//session_regenerate_id(true); 
@session_start();

require($_SERVER['DOCUMENT_ROOT'].'/config.php');
require($_SERVER['DOCUMENT_ROOT'].$DIR_TOOLS.'head_1.php');
require($_SERVER['DOCUMENT_ROOT'].$DIR_RAIZ."/Usuario/usuario_login.php");

if (strtoupper(@$usuario) == "VMA001ES") $SoyYo=true; else $SoyYo=false;
//if (!$SoyYo) { require_once("/home/soporteweb/Resources/Portal/mantenimiento.php"); die(); }


//$Alerta="<div class='Aviso' style='background-color: lightgreen; position:relative; top:100; text-align:center;'><h2>Debido a tareas de mantenimiento, este servidor estar&aacute; inactivo de 15:00 a 16:00.</h2><h3>Por favor, rogamos perdonen las molestias.</h3></div>";
//$Alerta="<div class='Aviso' style='background-color: lightred; position:relative; top:90; text-align:center;'><h2>EL SERVIDOR TIENE ALGUNOS PROBLEMAS. ROGAMOS DISCULPEN LAS MOLESTIAS.</h3></div>";
$Alerta="";

$usuarios_vela = "JSF010ES|VMA001ES|APG018ES|JMC006ES|EPO001ES|JVV001ES|CAT001ES|PTV002ES|DVP001ES|AGR013ES|IAA001ES|PEA001ES|SFG007ES|JHA001ES|SFM001ES";

$INFO_SISTEMA='Server Id: '.$PAIS_SERVER.'-'.$SERVER_ADDR.'-'.$VERSION_SERVER;
if ( !empty($grupo_usuario) && $grupo_usuario==2 )
//	$CARGA_SISTEMA='var interval_info_server=en_background("#INFO_SERVER", StatServ+"?Opcion=CARGA_SISTEMA", 5000);';
//else
	$CARGA_SISTEMA="";

$Menu_General=1;
	$Ver_Pend_Serv=0;
	$Ver_Ofertas_Pelotazos=0; 
	$Ver_Amazon=0;
	$Ver_Peluches=0;
	$Ver_Ofertas_Nuevo=0;
	$Ver_PCs=0;
	$Ver_VELA=0;
	$Ver_PV=0;
	$Ver_Amazon=0;
	$Ver_Desarrollo=0;
	
$Menu_VELA=0;
	$Ver_Dashboard_VELA=0;

$Menu_Errores=1;

$Menu_Listados=1;
	$Ver_Capturadores=0;

$Menu_Hist_Movim=1;

$Menu_S_Cupones=1;

$Menu_Conect_Tienda=1;

$Menu_Herramientas=1;
	$Ver_Incidencias=0;
	$Ver_Incidencias_VELA=0;

switch($Pais) {
	case "ESP":
		$Ver_Capturadores=$Ver_Amazon=$Ver_Ofertas_Nuevo=$Ver_PCs=$Ver_Capturadores=$Ver_Incidencias=1;
		if (preg_match("/SFM001ES|JCG012ES|JSF010ES|VMA001ES|FSS003ES|RVP001ES|APG018ES/", strtoupper(@$usuario))) $Ver_Kibana=1;
		if (preg_match("/JSF010ES|VMA001ES|JVV001ES|PTV002ES|ERC002ES/", strtoupper(@$usuario))) {
			$Ver_Desarrollo=$Ver_VELA=$Ver_Incidencias_VELA=1;
		}
		if (preg_match("/".$usuarios_vela."/", strtoupper(@$usuario))) {
			$Menu_VELA=1;			
		}
		$Ver_Dashboard_VELA=preg_match("/CAT001ES|JSF010ES|VMA001ES|SFM001ES|LFG001ES|ECG003ES|JVV001ES|EPO001ES|APG018ES|AGR013ES|PTV002ES|SFG007ES/", strtoupper(@$usuario));
		break;
	case "ARG":
		if ($SoyYo)
			$Ver_PV=1;
		break;
	case "POR": break;
	case "CHI": break;
	case "BRA": break;
	case "PAR":
		$Menu_Hist_Movim=$Menu_S_Cupones=0;
		break;
}

$PHP_SERVIDOR_CUPONES="Servidor_Cupones/vista_servidor_cupones_".$PAIS_SERVER.".php";

$T_Menu=array(
	"GENERAL"    => array( "ESP" => "GENERAL", "ENG" => "MAIN" ),
		"GENERAL_GENERAL"  => array( "ESP" => "GENERAL", "ENG" => "MAIN" ),
		"GENERAL_OFERTAS"  => array( "ESP" => "OFERTAS", "ENG" => "OFFERS" ),
		"GENERAL_AMAZON"  => array( "ESP" => "PROY.DRON", "ENG" => "PROT.DRON" ),
		"GENERAL_PELUCHES"  => array( "ESP" => "PELUCHES", "ENG" => "PUPPIES" ),
		"GENERAL_PASO_VELA"  => array( "ESP" => "PASO A VELA", "ENG" => "CHANGE TO VELA" ),
	"ERRORES"    => array( "ESP" => "ERRORES", "ENG" => "ERRORS" ),
		"PS" => array( "ESP" => "PENDIENTES DE SERVIR", "ENG" => "PENDING TO SERVE" ),
		"FOTO" => array( "ESP" => "INFORME REGULAR.", "ENG" => "REGULAR. RESUME" ),
		"DESARROLLO" => array( "ESP" => "DESARROLLO", "ENG" => "DEVELOPMENT" ),
		"MENSAJERIA" => array( "ESP" => "MENSAJERIA", "ENG" => "MESSAGES" ),
	"LISTADOS"   => array( "ESP" => "LISTADOS", "ENG" => "LISTS" ),
		"LIST_TOTAL"  => array( "ESP" => "LIST.TOTAL", "ENG" => "WHOLE LIST" ),
		"HARDWARE"    => array( "ESP" => "HARDWARE", "ENG" => "HARDWARE" ),
		"CAPTURADORES"=> array( "ESP" => "CAPTURADORES", "ENG" => "PDA-READERS" ),
	"HIST_MOVIM" => array( "ESP" => "INTERVENCIONES", "ENG" => "INTERVENTIONS" ),
		"HIST_HOY" => array( "ESP" => "Intervenciones HOY", "ENG" => "Interventions of TODAY" ),
		"HIST_TODOS" => array( "ESP" => "Historico Intervenciones", "ENG" => "WHOLE HISTORICAL Interventions" ),
	"S_CUPONES" => array( "ESP" => "S.CUPONES", "ENG" => "COUPON SERVER" ),
		"s_cupones" => array( "ESP" => "Servidor de cupones", "ENG" => "Coupons server"),
		"all_cupon" => array( "ESP" => "Todos los servidores", "ENG" => "All servers"),
		"hist_cupon" => array( "ESP" => "Historico servidor", "ENG" => "Historical"),
	"CONECT_TIENDA" => array( "ESP" => "CONECT.TIENDA", "ENG" => "CONNECT STORE" ),
	"HERRAMIENTAS" => array( "ESP" => "HERRAMIENTAS", "ENG" => "TOOLS" ),
		"INCIDENCIAS" => array( "ESP" => "INCIDENCIAS", "ENG" => "ISSUES" ),
		"ADMINISTRACION" => array( "ESP" => "ADMINISTRACION", "ENG" => "ADMINISTRATION" ),
		"SERVIDORES" => array( "ESP" => "SERVIDORES", "ENG" => "SERVERS INFO" )
);

function Get_Centro() {
	global $Centro, $Tiendas_Subcentro,$Pais;

	$Textos=array(
		"Todos" => array(
			"ESP" => "-- Todos los centros --",
			"ENG" => "-- All centers --"),
		"Quitar" => array(
			"ESP" => "Quitar Filtro",
			"ENG" => "Remove Filter")
	);

	$Tiendas_Subcentro="";
	if (!empty($_SESSION['FILTRO_CENTRO'])) {
		$Centro=$_SESSION['FILTRO_CENTRO'];
		$tmp=myQUERY("select numerotienda from tmpTiendas where Centro like '%$Centro%' AND Pais='$Pais'");
		if ($tmp) $_SESSION['Tiendas_Subcentro']=",".convert_multi_array($tmp).",";
	} else {
		unset($_SESSION['Tiendas_Subcentro']); unset($_SESSION['FILTRO_CENTRO']);
	}

	$tmp=myQUERY("SELECT DISTINCT(CENTRO) from tmpTiendas where PAIS in ('$Pais','XXX') order by 1");
	$Res='<select id="b_Selector_Centro" name="myCentro"><option value="">'.$Textos["Todos"][$_SESSION['Idioma']].'</option>';
	foreach($tmp as $d)
		$Res.='<option value="'.$d[0].'" '.($d[0] === @$Centro?'selected="selected"':'').'>'.$d[0].'</option>';
	$Res.='</select>';
	$Res.='<input class="button" name="Quitar_Filtro" type="button" id="b_Quitar_Filtro" value="'.$Textos["Quitar"][$_SESSION['Idioma']].'"/>';
	return $Res;
}

if ($Menu_General) {
	$MENU_GENERAL='
	<a>'.$T_Menu["GENERAL"][$Idioma].'<span class="flecha">&#9660</span></a>
	<ul>
		<li><a src="'.$url_general.'/General/vista_general.php" target="CUERPO" time=30>'.$T_Menu["GENERAL_GENERAL"][$Idioma].'</a></li>'
		.($Ver_PV?'<li><a src="/vista_PV.php" target="CUERPO" time=10>INFO.PV.</a></li>':'')	
		.($Ver_Capturadores?'<li><a src="'.$url_general.'/Conexion_Capt/vista_capturadores.php" target="CUERPO" time=30>CONEXION CAPT.</a></li>':'')
		.($Ver_Ofertas_Nuevo?'<li><a id="oferta_nuevo" src="Monitorizacion/vista_ofertas_nuevo.php" target="CUERPO" time=0>OFERTAS NUEVO</a></li>':'')
		.($Ver_Ofertas_Pelotazos?'<li><a src="Monitorizacion/vista_ofertas.php" target="CUERPO" time=30>'.$T_Menu["GENERAL_OFERTAS"][$Idioma].'</a></li>':'')
		.($Ver_Amazon?'<li><a id="dron" src="Monitorizacion/vista_amazon.php" target="CUERPO" time=0>'.$T_Menu["GENERAL_AMAZON"][$Idioma].'</a></li>':'')
		.($Ver_Peluches?'<li><a id="peluches" src="Monitorizacion/vista_peluches.php" target="CUERPO" time=60>'.$T_Menu["GENERAL_PELUCHES"][$Idioma].'</a></li>':'')
		.($Ver_PCs?'<li><a id="PCs" src="Monitorizacion/vista_PCs.php" target="CUERPO" time=0>PCs</a></li>':'')
		.($Ver_Desarrollo?
			'<li><a src="Paso_VELA/vista_principal_vela.php" target="CUERPO" time=0>'.$T_Menu["GENERAL_PASO_VELA"][$Idioma].' (PRUEBAS)</a></li>'.
			'<li><a src="Monitorizacion/vista_general_2.php" target="CUERPO" time=0>Pruebas nuevo general (PRUEBAS)</a></li>'.
			'<li><a src="Monitorizacion/VELA.php" target="CUERPO" time=0>VELA</a></li>':'')
	.'</ul>';
} else { $MENU_GENERAL=''; }

$MENU_VELA='
<a>VELA<span class="flecha">&#9660</span></a>
<ul>'
	.($Ver_Dashboard_VELA?'<li><a src="'.$url_vela.'/dashboard.php" target="CUERPO">DASHBOARD</a></li>':'')
.'</ul>
';
if (!$Menu_VELA) $MENU_VELA="";

$MENU_ERRORES='
<a>'.$T_Menu["ERRORES"][$Idioma].'<span class="flecha">&#9660</span></a>
<ul>'
	.'<li><a id="a_inicio" src="'.$url_errores.'/Errores/vista_Errores.php" target="CUERPO" time=30>'.$T_Menu["ERRORES"][$Idioma].'</a></li>'
	.($Ver_Pend_Serv?
		 '<li><a src="PS/vista_PS.php" target="CUERPO" time=0>'.$T_Menu["PS"][$Idioma].'</a></li>'
		.'<li><a src="PS/informe_foto.php" target="CUERPO" time=0>'.$T_Menu["FOTO"][$Idioma].'</a></li>':'')
	.'<li><a src="'.$url_errores.'/Mensajeria/vista_mensajeria.php" target="CUERPO" time=30>'.$T_Menu["MENSAJERIA"][$Idioma].'</a></li>'
	.($SoyYo?'<li><a src="'.$url_errores.'/Errores/vista_Errores_2.php" target="CUERPO" time=0>ERRORES 2</a></li>':"")
.'</ul>';
if (!$Menu_Errores) $MENU_ERRORES="";

$MENU_LISTADOS='
<a>'.$T_Menu["LISTADOS"][$Idioma].'<span class="flecha">&#9660</span></a>
<ul>
	<li><a src="'.$url_listados.'/List_Total/listado_total.php"        target="CUERPO" time=0>'.$T_Menu["LIST_TOTAL"][$Idioma].'</a></li>
	<li><a src="'.$url_listados.'/Hardware/listado_hardware.php"     target="CUERPO" time=0>'.$T_Menu["HARDWARE"][$Idioma].'</a></li>
	<li><a src="'.$url_listados.'/Capturadores/listado_capturadores.php" target="CUERPO" time=0>'.$T_Menu["CAPTURADORES"][$Idioma].'</a></li>
</ul>';
if (!$Menu_Listados) $MENU_LISTADOS="";
//********************************************************************************************************************************************************************
if (!$Menu_Hist_Movim)
	$MENU_HISTORICO="";
else {
	$MENU_HISTORICO='
		<a>'.$T_Menu["HIST_MOVIM"][$Idioma].'<span class="flecha">&#9660</span></a>
			<ul>
				<li><a src="'.$url_intervenciones.'/Historico_HOY/vista_Historico.php"  target="CUERPO" time=0>'.$T_Menu["HIST_HOY"][$Idioma].'</a></li>
				<li><a src="'.$url_intervenciones.'/Historico_Intervenciones/listado_historico.php" target="CUERPO" time=0>'.$T_Menu["HIST_TODOS"][$Idioma].'</a></li>
			</ul>';
}
//********************************************************************************************************************************************************************
$MENU_CUPONES='
<a>'.$T_Menu["S_CUPONES"][$Idioma].'<span class="flecha">&#9660</span></a>
<ul>
	<li><a id="id_servidor_cupones" src="'.$PHP_SERVIDOR_CUPONES.'" target="CUERPO" time=30>'.$T_Menu["s_cupones"][$Idioma].'</a></li>
	<li><a src="Servidor_Cupones/vista_todos_servidores_cupones_2.php" target="CUERPO" time=0>'.$T_Menu["all_cupon"][$Idioma].'</a></li>
	<li><a id="id_historico_cupones" src="Servidor_Cupones/vista_historico_servidores_cupones.php" target="CUERPO" time=0>'.$T_Menu["hist_cupon"][$Idioma].'</a></li>'
	.($Ver_Desarrollo?'<li><a src="Servidor_Cupones/nuevo_servidor_cupones.php" target="CUERPO" time=0>Nuevo</a></li>':'')
	.'
</ul>';
if (!$Menu_S_Cupones) $MENU_CUPONES="";
//********************************************************************************************************************************************************************
$MENU_CONECTAR='<a src="Conectar_Tienda/busca_tienda.php" target="CUERPO" time=0>'.$T_Menu["CONECT_TIENDA"][$Idioma].'</a>';
if (!$Menu_Conect_Tienda) $MENU_CONECTAR="";

if (!$Menu_Herramientas)
	$MENU_HERRAMIENTAS="";
else {
	$MENU_HERRAMIENTAS='
		<a>'.$T_Menu["HERRAMIENTAS"][$Idioma].'<span class="flecha">&#9660</span></a>
			<ul>'
				.($Ver_Incidencias?'<li><a src="'.$url_herramientas.'/Incidencias/vista_incidencias.php" target="CUERPO" time=0>'.$T_Menu["INCIDENCIAS"][$Idioma].'</a></li>':'')
				.($Ver_Incidencias_VELA?'<li><a src="'.$url_herramientas.'/Incidencias/vista_incidencias_3.php" target="CUERPO" time=0>INCIDENCIAS (VELA)</a></li>':'')
				.($SoyYo?'<li><a src="'.$url_herramientas.'/PARAGUAY/estado_impresora.php" target="CUERPO" time=0>ESTADO IMPRESORA</a></li>':'')
				.($SoyYo?'<li><a src="'.$url_herramientas.'/Administracion/multi_pais.php" target="CUERPO" time=0>MULTI PAIS</a></li>':'')
				.'<li><a src="'.$url_herramientas.'/Administracion/gestion.php" target="CUERPO" time=0>'.$T_Menu["ADMINISTRACION"][$Idioma].'</a></li>'
				.'<li><a src="'.$url_herramientas.'/Servidores/servidores.php" target="CUERPO" time=0>'.$T_Menu["SERVIDORES"][$Idioma].'</a></li>'
			.'</ul>';
}
$total_tiendas=shell_exec("sudo cat /home/MULTI/tmp/partes/listado_tiendas.txt | wc -l");
//********************************************************************************************************************************************************************
?>

<script>
var StatServ=DIR_RAIZ+'/tools/estado_servidor.php';
var Repaginar, interval_datos_cabecera,interval_info_server;
var timer=null;
var timestamp='<?=time();?>';
var usuario, nombre_usuario, grupo_usuario;
var auto_link="<?php echo @$_GET["LINK"]; ?>";

function checkTime(i) {
	if (i < 10) {
		i = "0" + i;
	}
	return i;
}
    //-->
var months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
var myDays = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];

function updateTime(){
	var date = new Date(timestamp*1000);
	//.toLocaleString();
	var curr_hour = checkTime(date.getHours());
	var curr_minute = checkTime(date.getMinutes());
	var curr_second = checkTime(date.getSeconds());
	var day = date.getDate();
	var month = date.getMonth();
	var thisDay = date.getDay(), thisDay = myDays[thisDay];
	var yy = date.getYear();
	var year = (yy < 1000) ? yy + 1900 : yy;

	$('#RELOJ_MONITOR').html(curr_hour + " : " + curr_minute + " : " + curr_second);
	timestamp++;
}

function updateSystem() {
	$.ajax({
		url: StatServ + "?Opcion=PROGRESO",
		timeout: 10000,
		success: function(result){
			var total_tiendas=<?=$total_tiendas;?>;
			var porc=Math.round(result/total_tiendas*100);
			$("#progreso").html("Progreso: "+result+"/ "+total_tiendas+" ("+ porc +"%)");
			$("#barra_progreso").progressbar({ value: porc });
		 }
	});
}

function updateUser() {
	usuario="<?=@$_SESSION['usuario']; ?>";
	nombre_usuario="<?=@$_SESSION['nombre_usuario'];?>";
	grupo_usuario="<?=@$_SESSION['grupo_usuario']; ?>";

	var txtHtml='Usuario/a: <span id="d_usuario_id"></span><br>Nombre: <span id="d_usuario_nombre"></span><br>Grupo: <span id="d_usuario_grupo"></span><br>';

	$("#DIV_USUARIO").html(txtHtml);
	
	if (usuario) {
		$("#d_usuario_id").html(usuario);
		$("#d_usuario_nombre").html(nombre_usuario);
		$("#d_usuario_grupo").html(grupo_usuario);
		$("#DIV_USUARIO").html(txtHtml + "<button id='b_logout'>Logout</button>");
	}
	else {
		$("#d_usuario_id").html("Invitado");
		$("#d_usuario_nombre").html("Invitado");
		$("#d_usuario_grupo").html("Invitado");
		$("#DIV_USUARIO").html(txtHtml + "<button id='b_login'>Login</button>");
	}
}

jQuery(document).ready(function () {
	if (typeof interval_datos_cabecera !== 'undefined') { clearInterval(interval_datos_cabecera); }
	if (typeof Repaginar !== 'undefined')               clearInterval(Repaginar);
	if (typeof interval_info_server !== 'undefined')    clearInterval(interval_info_server);

	updateTime(); setInterval(updateTime, 1000);
	updateSystem(); setInterval(updateSystem, 5000);
	updateUser();

	$("#b_Quitar_Filtro").on("click",function() {
		Put_SESSION("UNSET_SESSION","FILTRO_CENTRO", "");
		location.reload();
	});

	$("#b_Selector_Centro").change(function() {
		if (!$(this).val()) $("#b_Quitar_Filtro").click();
		Put_SESSION("CHG_SESSION","FILTRO_CENTRO", $(this).val());
		location.reload();
	});

	$("#head-nav a").on("click",function(a) {
		tmp_src=$(this).attr("src");
		if (tmp_src) {
			tmp_target="#"+$(this).attr("target");
			timer=$(this).attr("time")*1000;
			$(tmp_target).html('<div id="Cargando"><img src="/img/wait.gif"/></div>');
//			console.log("ANTES:" + Repaginar);	
			clearInterval(Repaginar);
			extra="?x=<?php echo md5(time()); ?>";
			// console.log(tmp_src, extra);
//			Repaginar=en_background(tmp_target, tmp_src+extra, timer);
			Repaginar=en_background(tmp_target, tmp_src, timer);
//			console.log("DESPUES:" + Repaginar);
// 			alert("Estamos sufriendo algunos problemas en el servidor.\n\nPor favor, rogamos cierren todas las sesiones activas y las ventanas.\n\nSe va a proceder a reiniciar el servidor");
		}
	});
	var p=$("#DIV_CABECERA").height();
	$("#CUERPO" ).offset({ top: p+5});

	if (usuario == "servcupo")
		$('#id_servidor_cupones').click();
	else 
		$('#a_inicio').click();

	$("#CUERPO").before("<?php echo $Alerta; ?>");
});
</script>

</head>
<style>
	#barra_progreso { height: 14px; text-align: center; }
	#progreso { width: 100%; float: left; font-size: 11px;}
	#t_monitor {
		width: 100%;
		height: 100%;
		vertical-align:top;
	}
	#t_monitor * {
		font-family: sans-serif;
	}
	#RELOJ_MONITOR {
		border-bottom: 1px solid gray;
		color: black;
		text-align: center;
	}
	#Panel_izquierdo {
		border: 1px solid black;
		background-color: whitesmoke;
		border-radius: 3px;
		width: 205px;
		height: 100%;
	}
	#Zona_Trabajo {
		width: 100% !important; height: 100%;
	}
	#barra_progreso { height: 14px; text-align: center; }
	#progreso { width: 100%; float: left; font-size: 11px;}
	#DIV_PAIS {
		background: rgb(212,37,29);
		align-content:center;
		border-radius:3px; 
	}
	#DIV_USUARIO {
		font-family: sans-serif;
		font-size: 12px;
	}
	#DIV_INFO_SISTEMA {
		color:black;
	}
</style>

<body>
	<table id="t_monitor">
		<tr><td>
			<div id="Panel_izquierdo">
				<table width="100%">
					<tr><td id="DIV_PAIS" width="20%">
							<img id="ICONO_PAIS" src="/favicon.ico">
							<a href="/" ><img src="/img/newlogo.png" title="Pagina del Portal" height="50px"></a>
					</td></tr>

					<tr><td id="RELOJ_MONITOR"></td></tr>

					<tr><td id="DIV_INFO_SISTEMA">
						<div id="RELOJ_MONITOR"></div>
						<div id="INFO_SERVER">
							<span id="INFO_SISTEMA"><?php echo $INFO_SISTEMA; ?></span><br>
							<div id="barra_progreso" style="height:14px"><div id='progreso'></div></div>
						</div>
					</td></tr>

					<tr><td id="DIV_USUARIO"></td></tr>

					<tr><td id="DIV_CENTRO" ><?php echo Get_Centro(); ?></td></tr>

				</table>
			</div>
		</td>
		<td>
			<div id="Zona_Trabajo">
				Zona Trabajo
			</div>
		</td></tr>
	</table>
</body>
<?php
/*	

	<div id="DIV_CABECERA">
		<div id="CAB1">
			<table width="100%">
			<tr>
				<td id="DIV_PAIS" width="20%">
					<img id="ICONO_PAIS" src="/favicon.ico" ></img>
					<a href="/" ><img src="/img/logo_dia2.gif" title="Pagina del Portal" height="50px"></a>
				</td>
				<td width="20%" id="DIV_CENTRO" ><?php echo Get_Centro(); ?></td>
				<td id="DIV_USUARIO"></td>
				<td>
					<div id="RELOJ_MONITOR"></div>
					<div id="INFO_SERVER">
						<span id="INFO_SISTEMA"><?php echo $INFO_SISTEMA; ?></span><br>
						<div id="barra_progreso" style="height:14px"><div id='progreso'></div></div>
					</div>
				</td>
			</tr>
			</table>

			<nav id="head-nav" class="navbar navbar-fixed-top">
				<ul class="nav">
					<li><?php echo $MENU_GENERAL; ?></li>
					<li><?php echo $MENU_VELA; ?></li>
					<li><?php echo $MENU_ERRORES; ?></li>
					<li><?php echo $MENU_LISTADOS; ?></li>
					<li><?php echo $MENU_HISTORICO; ?></li>
					<li><?php echo $MENU_CUPONES; ?></li>
					<li><?php echo $MENU_CONECTAR; ?></li>
					<li><?php echo $MENU_HERRAMIENTAS; ?></li>
				</ul>
				<div class="navbar-inner clearfix"><ul class="nav"></ul></div>
			</nav>

			</div>
		</div>
	<div id="CUERPO">
	</div>

</body>
</html>
*/
?>