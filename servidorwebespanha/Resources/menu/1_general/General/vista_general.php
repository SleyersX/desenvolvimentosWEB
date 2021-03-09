<title>GENERAL</title>
<?php
require("../../cabecera_vistas.php");


function ROJO($Texto) {  return "CONCAT('<font color=red><b>',$Texto,'</b></font>')"; }
function VERDE($Texto) { return "CONCAT('<font color=darkgreen><b>',$Texto,'</b></font>')"; }

$Vers_Conex="CONCAT('(',$Conexion,') ',IF($Table.Version IS NULL, 'Nunca accedido', $Table.Version))";

$Queries["FIDE_ONLINE"]=array ( "FIDEL.ONLINE",
	array("STATUS", "SUBTIPO", "Fichero","Cantidad"),
	"select a.DAT5,IF(c.subtipo='EL ARBOL','EL ARBOL','OTRO'),b.DAT_ADIC1,count(*) from $Table a join Solo_DATS b ON a.tienda=b.tienda and a.caja=b.caja join tiendas c ON b.tienda=c.numerotienda where a.conexion=1 group by 1,2,3",
	NULL,
	"");

// $md5sum=exec("md5sum /home/MULTI/files_to_copy/root/102_createPopulationDailyDashboard.sh_to_initd | cut -b1-5");
// $Queries["WSD"]=array ( "Version WSD",
// 	array("Version", "Cantidad"), "select DAT_ADIC1,count(DAT_ADIC1) from Solo_DATS group by DAT_ADIC1", NULL, "");
if (GetPais() == "ESP") {

//	$Queries["DAT_ADIC5"]=array ( "ENCUESTAS A CLIENTES",
//		array("Status", "Cantidad"), "select CASE Status WHEN 0 THEN 'NO ENVIO' WHEN 1 THEN 'SOLO CABECERA' WHEN 2 THEN 'TICKET COMPLETO' ELSE 'N/D' END, count(*) from EncuestasClientes group by 1", NULL, "ESP","");

$Queries["Actualiz1"]=array ( "PEND.ACTUAL.", array("Actual", "bat200", "Auto","Masters","Esclavas","Total"),
	"select
		b.version, a.PKG, a.PKG_Auto, SUM(CASE WHEN a.Caja=1 THEN 1 ELSE 0 END), SUM(CASE WHEN a.Caja<>1 THEN 1 ELSE 0 END), concat('<b>',count(*),'</b>')
		from Paquetes a JOIN $Table b on b.tienda=a.tienda and b.caja=a.caja where b.Version <> '' group by 1,2,3", NULL, "");

$Queries["HUBS"]=array ("HUB/SWITCH",
	array("Tipo","Cantidad"),
	"select hub 'HUB/SWITCH',count(*) from ChecksESP where caja=1 group by 1",
	NULL,
	"");

$Queries["ADELANTO_VENTAS"]=array ("FASE ADELANTO VENTAS",
	array("Version","Fase","Tiendas"),
	"select version,fase,count(*) from Fase_Adelanto_Ventas a join tmpTiendas b on a.tienda=b.numerotienda and conexion and pais='ESP' group by version,fase",
	NULL,
	"");

	$Queries["FIDELIZACION"]=array ( "FIDELIZACION",
		array("Status", "Masters","Esclavas"),
			"select IF(Fide_Online,'Online','Offline'),SUM(CASE WHEN caja=1 THEN 1 ELSE 0 END) 'Masters', SUM(CASE WHEN caja<>1 THEN 1 ELSE 0 END) 'Esclavas' from Fidelizacion group by 1",
		NULL, "","");

	$Queries["PINPADS"]=array ("PINPADS",
		array("Modelo", "Version SW PINPAD","Cantidad"),
		"CREATE OR REPLACE VIEW tmpPINPAD as SELECT SUBSTRING_INDEX(PINPAD,'/',1) as Modelo, SUBSTRING_INDEX(PINPAD,'/',-1) as Version from $Table where Conexion=1;
		SELECT
			IF(Modelo IS NULL,'<hr><b>TOTAL PINPADS</b>', IF(Version IS NULL,'',Modelo)),
			IF(Modelo IS NULL,'',IFNULL(Version, 'SUBTOTAL MODELO')),
			count(*)
		from tmpPINPAD group by Modelo, Version WITH ROLLUP",
		NULL,
		"",
		"NUMERO DE PINPADS POR MODELO\n\nEsta vista nos muestra una clasificacion por modelo y firmware de los PINPADs de la red\n");

}

$Queries["MISMA_IP"]=array ("Tiendas con mismo IP",
	array("IP","Tienda"), "select  ip, GROUP_CONCAT(numerotienda SEPARATOR ' - ') from tiendas where IP like '10.%' and Pais='".GetPais()."' group by ip HAVING COUNT(ip)>=2", NULL, GetPais());

$Queries["TIENDAS_ONLINE"]=array ( "CLASIF. FIDELIZACION",
	array("Tipo", "Cantidad"), 
		"select if(DAT5='0','OFFLINE',DAT5),count(*) from $Table WHERE caja=1 group by 1",
		"", "ARG BRA POR CHI","Este listado muestra las tiendas (solo cajas master) con el tipo de fidelizacion: ONLINE o OFFLINE");

if (GetPais() == "BRA") {

	$Queries["IMPRESORAS"]=array ( "IMPRESORAS",
		array("Tipo", "Cantidad"), "select DAT_ADIC3,count(*) from Solo_DATS group by DAT_ADIC3", NULL, "BRA");

	$Queries["Kernel"]=array ( "Version Kernel",
		array("Kernel","cantidad"), "select DAT2,count(DAT2) from $Table group by DAT2", NULL, "BRA");

	$Queries["cups"]=array ( "Version cups-1.1.14-18",
		array("cups-1.1.14-18","Version SA","Cantidad"), "select IF(DAT_ADIC2=1,'INSTALADO','NO INSTALADO'), b.version, count(*) from Solo_DATS a join tmpTiendas b on a.tienda=b.numerotienda where a.caja=1 group by 1,2", NULL, "BRA");

	$Queries["CONTINGENCIA_SAT"]=array ("CONTINGENCIA SAT<br>Cantidad > 1",
                array("Tienda", "Caja", "Cantidad"), "select tienda, caja, SAT from Contingencia where SAT>1", NULL, "BRA");
	$Queries["CONTINGENCIA_NFCe"]=array ("CONTINGENCIA NFCe<br>Cantidad > 1",
                array("Tienda", "Caja", "Cantidad"), "select tienda, caja, NFCe from Contingencia where NFCe>1", NULL, "BRA");
}

if ( GetPais() == "POR" ) {
	$Queries["CALCULADO"]=array ("CALCULADO",
		array("Tienda","Fecha<>0","Fecha=0"), "select Tienda, DAT_ADIC3,DAT_ADIC4 from Solo_DATS where caja=1 and DAT_ADIC3+DAT_ADIC4 > 0", NULL, "");
}

$B_1_1="<b style=\"font-size:1.1em\">";
$Queries["MODELOS_TPV"]=array ("TPVs x MODELO",
	array("Modelo","Cantidad"),
	"select Modelo,count(*) from $Table where conexion=1 and modelo is not NULL  group by Modelo order by 2 desc;
	 select '$B_1_1\TOTAL</b>',concat('$B_1_1',count(*),'</b>') from $Table where conexion=1 and modelo is not NULL",
	NULL,
	"ALL");


/*
$sum_fichero=md5_file("/home/pendserv/scripts/200_actu_pend_serv_v2.sh");
$Queries["200_pend"]=array ("TIENDAS PREPARADAS<br>".substr($sum_fichero,0,8)."...",
	array("ESTADO","Cantidad"),
	"select IF(DAT_ADIC5='$sum_fichero','PREPARADA','NO PREPARADA'),count(*) from Solo_DATS where caja=1 group by 1",
	NULL,
	"ESP",
	"Numero de tiendas con version pendiente de instalar");
*/


/*

*/


/* ----------------------------------------------------------------------------------------------------------------------------- */

$tmpVersiones=array(); $Tabla_Subcentros_x_Versiones=array(); $sumVersiones=""; $sep="'<hr>'"; $sumVersionesTotal="";
if ($Pais=="ESP" || $Pais=="PAR") {
	$corte="06.90.04-p1";
	$tmpVersiones[0]="<$corte";
	$sumVersiones=",SUM(CASE WHEN version<'".$corte."' THEN 1 ELSE 0 END)";
	$sumVersionesTotal=",SUM(CASE WHEN version<'".$corte."' THEN 1 ELSE 0 END)";
} else {
	$corte="";
}
$tmp=myQUERY("select distinct(version) from tmpTiendas where version>='$corte' AND centro <> 'SEDE' order by version;");

foreach($tmp as $k => $d) {
	$sumVersiones.=",SUM(CASE WHEN version='".$d[0]."' THEN 1 ELSE 0 END)";
	$sumVersionesTotal.=",CONCAT('<b>',SUM(CASE WHEN version='".$d[0]."' THEN 1 ELSE 0 END),'</b>')";
	$sep.=",'<hr>'";
	$tmpVersiones[$k+1]=$d[0];
}
$sumVersiones.=", CONCAT('<b class=\"total\">', count(centro), '</b>')";
$sumVersionesTotal.=", CONCAT('<b class=\"total\">', count(centro), '</b>')";
$sep.=",'<hr>'";

$Tabla_Versiones_x_Subcentro=myQUERY("
	select centro ".$sumVersiones."  from tmpTiendas where Pruebas=0 group by centro;
	select '<b>TOTAL CENTROS</b>' ".$sumVersionesTotal."  from tmpTiendas where Pruebas=0;");

$Tabla_Vers_Subc_Tipo_Subt=myQUERY("select centro,tipo,subtipo ".$sumVersiones."  from tmpTiendas where pruebas=0 roup by 1,2,3;");

$php_info_general="/Resources/menu/1_general/info_general.php";
$php_resultado_busqueda="/Resources/Estado_Monitorizacion/Conectar_Tienda/resultado_busqueda.php";

/* ----------------------------------------------------------------------------------------------------------------------------- */
$vista_general_versiones_x_tienda='<div class="PANEL"><table id="vista_general_versiones" class="tabla2 nueva_vista">
	<caption><a class="tips_general" rel="'.$php_info_general.'?opcion=info_1">TIENDAS x VERSION x CENTRO</a></caption>
	<thead><tr>';
foreach(array_merge(array("CENTRO"),$tmpVersiones,array("TOTAL TDAs")) as $k => $d) {
	$vista_general_versiones_x_tienda.="<th class='versiones'>$d</th>";
}
$vista_general_versiones_x_tienda.='</tr></thead>';

foreach($Tabla_Versiones_x_Subcentro as $k => $d) {
	$vista_general_versiones_x_tienda.="<tr>";
	$tmp=myQUERY("select tipo ".$sumVersiones." from tmpTiendas where centro='".$d[0]."' AND Pruebas = 0 group by tipo");
	foreach($d as $k1 => $d1) $vista_general_versiones_x_tienda.='<td class="'.($k1==0?"centros":"tiendas").'">'.$d1.'</td>';
	$vista_general_versiones_x_tienda.="</tr>";
}
$vista_general_versiones_x_tienda.='</table></div>';

/* ----------------------------------------------------------------------------------------------------------------------------- */
if (1 == 0) {
	$tmp=myQUERY("
	select centro,sum(case when DAT_ADIC1=1 then 1 else 0 end) as Activo,sum(case when DAT_ADIC1<>1 then 1 else 0 end) as Inactivo, count(*) from Solo_DATS a join tmpTiendas b on a.tienda=b.numerotienda where centro<>'sede' group by 1;
	select 'TOTAL',sum(case when DAT_ADIC1=1 then 1 else 0 end) as Activo,sum(case when DAT_ADIC1<>1 then 1 else 0 end) as Inactivo, count(*) from Solo_DATS a join tmpTiendas b on a.tienda=b.numerotienda where centro<>'sede'");
	$vista_acti_devo_desc='<div class="PANEL"><table id="vista_acti_devo_desc" class="tabla2 nueva_vista">
		<caption><a class="tips_general" rel="'.$php_info_general.'?opcion=info_2">ACTIVACION DEVOL. DESCUENTOS (CAJAS)</a></caption>
		<thead><tr><th>CENTRO</th><th>ACTIVADAS</th><th>PENDIENTES</th><th>TOTAL</th></tr></thead>';

	foreach($tmp as $k => $d) {
		$vista_acti_devo_desc.='<tr>';
		foreach($d as $k1 => $d1) $vista_acti_devo_desc.='<td class="'.($k1==0?"centros":"tiendas").'">'.$d1.'</td>';
		$vista_acti_devo_desc.='</tr>';
	}
	$vista_acti_devo_desc.='</table></div>';
} else
	$vista_acti_devo_desc='';

/* ----------------------------------------------------------------------------------------------------------------------------- */

if (1 == 0) {
	$tmp=myQUERY("
	select Error,count(*) from EBD a join tmpTiendas b on a.tienda=b.numerotienda where date(fecha)=CURRENT_DATE group by 1;
	select 'TOTAL',count(*) from EBD a join tmpTiendas b on a.tienda=b.numerotienda where date(fecha)=CURRENT_DATE;");
	$vista_EBD='<div class="PANEL"><table id="vista_acti_devo_desc" class="tabla2 nueva_vista">
		<caption><a class="tips_general" rel="'.$php_info_general.'?opcion=info_2">EBD</a></caption>
		<thead><tr><th>ERROR</th><th style="display:none">Tienda</th><th style="display:none">CENTRO</th><th>CANTIDAD</th></tr></thead>';

	foreach($tmp as $k => $d) {
		$vista_EBD.='<tr>';
		foreach($d as $k1 => $d1) $vista_EBD.='<td class="'.($k1==0?"centros":"tiendas").'">'.$d1.'</td>';
		$vista_EBD.='</tr>';
	}
	$vista_EBD.='</table></div>';
} else
	$vista_EBD="";

$Queries["Clasif_por_subtipo_tienda"]=array("CLASIFICACION POR SUBTIPO<br>(MAESTRO DE TIENDAS)",
	array("Subtipo","Propias","Franquicias","Total"),
	"select subtipo, sum(case when tipo='PROPIA' THEN 1 ELSE 0 END) as 'PROPIAS', sum(case when tipo='FRANQUICIA' THEN 1 ELSE 0 END) as 'FRANQUICIAS',count(*) as 'TOTAL' from tmpTiendas where centro<>'SEDE' group by 1",
	NULL,
	"ALL");


$Version = "CONCAT(Version, '(',$Conexion,')'";
$Queries["Clasif_por_tipo_vers"]=array("CLASIFICACION POR TIPO/VERSION",
	array("TIPO","VERSION","Cantidad"),
	"select CONCAT(tipo,'-',subtipo), $Vers_Conex, count(*) from tiendas, $Table where Numerotienda=Tienda and Caja=1 and Pais='$Pais' group by 1,2 order by 1,2",
	NULL,
	"ALL");

?>

<style>
	.nueva_vista { font-family: sans-serif, arial; margin:0px; }
	.nueva_vista CAPTION { background-color: lightcyan; border-radius: 5px 5px 0 0;}
	.nueva_vista CAPTION a { text-decoration:none; font-weight: bold; font-size:110%; }
	.nueva_vista td { text-align:right; border-left: 1px solid #999; padding-right:1em; }
	.nueva_vista td:first-child { text-align:left; }
	.nueva_vista td:last-child { background-color: honeydew; font-weight: bold;}
	.nueva_vista tr:last-child { background-color: honeydew; font-weight: bold;}
	.nueva_vista th { border-left: 1px solid #999; padding: 3px;}
	.tiendas:hover { background-color: lightgreen !important; }
	#resultado_vista_general { width:100%; height:90%; max-width:100%; padding:0px; top:50px;}
	#resultado_vista_general .modal-body { margin-top:1em; height:94%; }
</style>

<?php
	if (!empty($vista_general_versiones_x_tienda)) echo $vista_general_versiones_x_tienda;
	if (!empty($vista_acti_devo_desc)) echo $vista_acti_devo_desc;
	if (!empty($vista_EBD)) echo $vista_EBD;
	if (isset($Queries)) foreach ($Queries as $key => $dato) { Show_data2($key, $dato); echo PHP_EOL; }
?>
<div class="modal" id="resultado_vista_general" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"></div></div></div></div>

<script>
	var click;
	var url_php_resultado_busqueda="<?php echo $php_resultado_busqueda;?>";
//	var usuario="<?php echo $_SESSION['usuario']; ?>";
	function get_centro(x) {
		return x.parentElement.firstElementChild.textContent;
	}
	function get_version(x) {
		return x.offsetParent.tHead.children[0].cells[x.cellIndex].textContent;
	}
	
	function carga_body_modal(url) {
		if (hay_login()) {
			$("#resultado_vista_general .modal-body").empty();
			$("#resultado_vista_general .modal-body").html('<div id="Cargando"><img src="/img/wait.gif"/></div>');
			$("#resultado_vista_general .modal-body").load(url);
			$("#resultado_vista_general").modal();
		}
	}

	$("#vista_general_versiones .centros").on("click",function(x) {
		var centro = get_centro($(this)[0]);
		if (centro != "TOTAL CENTROS") {
			carga_body_modal(url_php_resultado_busqueda+"?Viene_de_general=1"+"&Busca_Centro="+encodeURI(centro))
		}
	});

	$("#vista_general_versiones .versiones").on("click",function(x) {
		var version = get_version($(this)[0]);
		if (version != "CENTRO" && version != "TOTAL TDAs") {
			carga_body_modal(url_php_resultado_busqueda+"?Viene_de_general=1"+"&Busca_Version="+encodeURI(version)+"&Busca_Centro=");
		}
	});

	$("#vista_general_versiones .tiendas").on("click",function(x) {
		click=$(this);
		var centro = get_centro($(this)[0]);
		var version = get_version($(this)[0]);
		carga_body_modal(url_php_resultado_busqueda+"?Viene_de_general=1"+"&Busca_Version="+encodeURI(version)+"&Busca_Centro="+encodeURI(centro));
	});
	$(".tips_general").cluetip({ width: 350, showTitle: false, arrows: true, dropShadow: true, sticky: false, mouseOutClose: true, closeText:"" });
</script>
