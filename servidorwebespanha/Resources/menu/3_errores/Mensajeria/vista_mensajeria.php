<title>VISTA MENSAJERIA/MODOS LOCALES</title>
<?php
$No_Carga_ssh2=true;
require($_SERVER['DOCUMENT_ROOT']."/config.php");

$tmp=myQUERY("Select max(caja) from MESSAGE_LOCAL_OUT_ESP");
$Numero_Maximo_Cajas=$tmp[0][0];
$tmp_query=""; $cabecera[0]="Tienda";
$msg="CONCAT('<span title=\"',Mensaje,'\">',
	(concat(
		  if(a.msg_null>0,concat('<span style=\"color:red;\">',a.msg_null,'</span>') , a.msg_null)
		, concat('(<span style=\"color:blue\">', a.msg_total,'</span>)')
	)),'</span>')";
for ($i=1; $i<=$Numero_Maximo_Cajas; $i++) {
	$tmp_query.="IFNULL(MAX(CASE WHEN a.caja=$i THEN ".$msg." END),'') 'Caja $i'";
	$cabecera[$i]="Caja $i";
	if ($i<$Numero_Maximo_Cajas) $tmp_query.=",";
}

// $query_total="select tienda, $tmp_query from MESSAGE_LOCAL_OUT_ESP where tienda in (7939,9829,16057,8121,60718) group by tienda having (sum(MSG)>0)";
$query_total="
	select
		a.tienda,
		$tmp_query
	from MESSAGE_LOCAL_OUT_ESP a
		JOIN ChecksESP b on a.tienda=b.tienda and a.caja=b.caja
	/* WHERE SUBSTRING(b.Version,1,8)='06.30.02' */
	GROUP BY a.tienda
	HAVING (sum(a.msg_null)>0) OR (sum(a.msg_total)>500)";
$Queries["MESSAGE_LOCAL_OUT"]=array("MENSAJES LOCAL OUT BLOQUEADOS (<b style=\"color:red\">ROJO=Mensajes contenido NULL</b>; AZUL=Total mensajes bloqueados)",
	$cabecera,
	$query_total,
	NULL,
	"ESP",
	"");

$tmp=myQUERY("Select max(caja) from MESSAGE_OUT_ESP"); $Numero_Maximo_Cajas=$tmp[0][0];
$tmp=myQUERY("Select max(msg) from MESSAGE_OUT_ESP"); $Numero_Maximo_MSG=$tmp[0][0];
$msg=html_entity_decode("CONCAT('<span title=\"',Mensaje,'\">',MSG,'</span>')");
$tmp_query=""; $cabecera[0]="Tienda";
for ($i=1; $i<=$Numero_Maximo_Cajas; $i++) {
	$tmp_query.="IFNULL(MAX(CASE WHEN caja=$i THEN ".$msg." END),'') 'Caja $i'";
	$cabecera[$i]="Caja $i";
	if ($i<$Numero_Maximo_Cajas) $tmp_query.=",";
}

$query_total=myQUERY("select tienda, $tmp_query from MESSAGE_OUT_ESP group by tienda having (sum(MSG)>500)");


$Queries_s["MESSAGE_OUT"]=array("MENSAJES OUT BLOQUEADOS",
	$cabecera,
	$query_total,
	NULL,
	"ESP",
	"");


if (isset($Queries))
	foreach ($Queries as $key => $dato) {
		Show_data2($key, $dato); echo PHP_EOL;
	}
if (isset($Queries_s))
	foreach ($Queries_s as $key => $dato) {
		Show_data_sin_query($key, $dato);
	}
if (isset($Queries_T))
	foreach ($Queries_T as $key => $dato) {
		Show_data2($key, $dato,true); echo PHP_EOL;
	}

?>
