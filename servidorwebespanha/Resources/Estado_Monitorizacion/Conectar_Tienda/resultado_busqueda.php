<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

//<div class="Aviso" style="width:95%; height: 830 !important; overflow-y: auto;">

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }

$Boton_Atras = '<input id="Busca_Atras" class="button" type="button" Value="Atras..."/>';

//$SubQuery="Pais=$Pais&Busca_Tienda=$Busca_Tienda&Busca_Centro=$Busca_Centro&Busca_Tipo=$Busca_Tipo&Busca_Subtipo=$Busca_Subtipo&Busca_Poblacion=$Busca_Poblacion&Busca_Provincia=$Busca_Provincia&Busca_Version=$Busca_Version";

$Query_Centro="";

if ($Busca_Centro == "SEDE") {
	$Check_Pais="XXX";
	$tmpTiendas="tmpTiendasSEDE";
}
else {
	$Check_Pais=$Pais;
	$tmpTiendas="tmpTiendas";
}
if (!empty($Viene_de_general)) {
	if (!empty($Busca_Version)) {
		$Busca_Version=urldecode($Busca_Version);
		if ($Busca_Version[0]=="<")
			$Busca_Version="<'".str_replace("<","",$Busca_Version)."'";
		else 
			$Busca_Version="='".$Busca_Version."'";
	}
	if (!empty($Busca_Centro)) {
		if ($Busca_Centro=="TOTAL CENTROS") {
			$tmp=myQUERY("select distinct(centro) from tmpTiendas where centro<>'SEDE'");
			$Query_Centro="t.Centro in (''"; foreach($tmp as $k => $d) $Query_Centro.=",'".$d[0]."'"; $Query_Centro.=")";
		}
		else 
			$Query_Centro="t.Centro='".$Busca_Centro."'";
	}
}
else {
	if (!empty($Busca_Centro)) $Query_Centro="t.Centro='".$Busca_Centro."'";
}

$query="
	select
		CONCAT('CONNECT;',ifnull(t.numerotienda,''),';',IFNULL(t.Centro,''),';',ifnull(t.Conexion,'')),
		CAST(t.Numerotienda AS UNSIGNED),
		if(t.Conexion,'SI','NO'),
		t.Version,
		t.Centro,
		t.Tipo,
		t.Subtipo,
		t.Direccion,
		t.Poblacion,
		CP.CP,
		t.Provincia,
		t.Telefono,
		t.IP,
		t.VELA

	from $tmpTiendas t LEFT JOIN CP ON t.numerotienda=CP.tienda

	WHERE t.Pais IN ('".$Check_Pais."') "
	.(!empty($Busca_Tienda)?" AND t.numerotienda='$Busca_Tienda'":"")
	.(!empty($Busca_Centro)?" AND $Query_Centro":"")
	.(!empty($Busca_Tipo)?" AND t.Tipo='$Busca_Tipo'":"")
	.(!empty($Busca_Subtipo)?" AND t.Subtipo='$Busca_Subtipo'":"")
	.(!empty($Busca_Poblacion)?" AND t.Poblacion='$Busca_Poblacion'":"")
	.(!empty($Busca_Provincia)?" AND t.Provincia='$Busca_Provincia'":"")
	.(!empty($Busca_VELA)?" AND t.VELA='$Busca_VELA'":"")
//	.(!empty($Busca_Version)?($Busca_Version=="NO DISPON."?"AND c.Conexion=0":" AND t.Version='$Busca_Version'"):"")
	.(!empty($Busca_Version)?" AND t.Version $Busca_Version":"")
	." order by 2";
//if (SoyYo()) echo $query;
$Res=myQUERY($query);

function Imprime_Filtro() {
	global $Busca_Tienda, $Busca_Centro, $Busca_Tipo, $Busca_Subtipo, $Busca_Poblacion, $Busca_Provincia, $Busca_Version, $Busca_VELA;
	return '<b>FILTRO APLICADO / FILTER APPLIED:</b><br>
			<ul>'
			.(!empty($Busca_Tienda)?"<li><b>TIENDA / SHOP:</b> $Busca_Tienda</li>":"")
			.(!empty($Busca_Centro)?"<li><b>CENTRO / CENTER:</b> $Busca_Centro</li>":"")
			.(!empty($Busca_Tipo)?"<li><b>TIPO / TYPE:</b> $Busca_Tipo</li>":"")
			.(!empty($Busca_Subtipo)?"<li><b>SUBTIPO / SUBTYPE: </b> $Busca_Subtipo</li>":"")
			.(!empty($Busca_Poblacion)?"<li><b>POBLACION / TOWN:</b> $Busca_Poblacion</li>":"")
			.(!empty($Busca_Provincia)?"<li><b>PROVINCIA / PROVINCE:</b> $Busca_Provincia</li>":"")
			.(!empty($Busca_VELA)?"<li><b>VELA / VELA:</b> $Busca_VELA</li>":"")
			.(!empty($Busca_Version)?"<li><b>VERSION / VERSION:</b> $Busca_Version</li>":"")
		.'</ul>';
}

if ($Res == NULL) {
	require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/not_in_bbdd.php");
	die();
}

$cuerpo_tabla="";
foreach($Res as $k1 => $linea) {
	list($Stuff,$Tienda,$Centro,$Cnx)=explode(";",$linea[0]);
	if ($Cnx==1) $Class="ON"; else $Class="OFF";
	$ON=($Cnx==1);
	$VELA=$linea[13];
	if ($VELA=="SI") { $Class=($ON?"VELA_ON":"VELA_OFF"); }
	
	$url=$PHP_PRECONECTAR.'?Tienda='.$Tienda.'&Centro='.$Centro;
	$cuerpo_tabla.='<tr class="'.$Class.'" id="'.$Tienda.'" value="'.$url.'">';

	foreach ($linea as $k2 => $campo) {
		if ($k2>0) {
			$Adic_Class=""; 
			if ($k2==1) { $Adic_Class="A_RIGHT"; }
			$cuerpo_tabla.="<td class='$Class $Adic_Class'>$campo</td>";
		}
	}
	$cuerpo_tabla.='</tr>';
}


if (!empty($Busca_Tienda)) {
	$url=$PHP_PRECONECTAR.'?Tienda='.$Busca_Tienda.'&Centro='.$Centro;
	echo "<script>window.open('$url', \"_blank\");</script>";
}

?>

<style>
	.VELA_ON { color:green; font-weight:bold; }
	.VELA_OFF { color:green;  }
	#count { font-style: italic; }
</style>

<body>
<div class="Aviso" style="width:95%; overflow-y: auto; <?php if (!empty($_GET['Viene_de_general'])) echo 'height:100%'; ?>">
	<table class="TABLA2">
		<caption>
			<div id="Leyenda_Izquierda"><?php echo Imprime_Filtro(); ?></div>
			<div style="position:absolute; left:40%;">
				<h2 style="font-weight:bold;">Resultado de la b&uacute;squeda <span id="count">(<?php echo count($Res); ?> registros)</span></h2>
				<i>(Pulse en la fila para acceder a la informaci&oacute;n)</i>
			</div>
			<div id="Leyenda_Derecha">
				<b>LEYENDA:</b><br>
				<b style="color:blue">Color azul, indica caja master en linea</b><br>
				<b style="color:gray">Color gris, indica caja master desconectada</b><br>
				<b style="color:green">Color verde, indica tienda en VELA</b><br>
			</div>
		</caption>
		<thead>
			<th style="display:none">?</th>
			<th>TIENDA</th>
			<th>CONEX.</th>
			<th>VERSION</th>
			<th>CENTRO</th>
			<th>TIPO</th>
			<th>SUBTIPO</th>
			<th>DIRECCION</th>
			<th>LOCALIDAD</th>
			<th>CP</th>
			<th>PROVINCIA</th>
			<th>TELEFONO</th>
			<th>IP</th>
			<th>VELA</th>
		</thead>
		<tbody id="cuerpo_tabla_resultado_busqueda"><?php echo $cuerpo_tabla; ?></tbody>
	</table>
</div>
</body>
<script>
	$("#cuerpo_tabla_resultado_busqueda tr").on("click",function() {
		window.open( $(this).attr("value") , "_blank");
	});
</script>