<?php

if (empty($_SERVER['DOCUMENT_ROOT'])) {
	$DOCUMENT_ROOT="/home/soporteweb/";
	require_once($DOCUMENT_ROOT."/tools/mysql.php");
}

$File_Export=$DOCUMENT_ROOT."/Incidencias/export.txt";

if (!file_exists($File_Export))
	die("ERROR: El fichero '$File_Export' no existe...");

echo "Leyendo informacion del fichero...".PHP_EOL;
$Incidencias=file($File_Export);

$Total=count($Incidencias);
$contador=0;
echo "Generando informacion...".PHP_EOL;
foreach($Incidencias as $k => $d) {
	$contador++;
	list($ID, $Titulo, $FechGrab, $FechReso, $CodiReso, $ElemProd, $TipoProbl, $DiaNiveMax, $Prioridad, $DiaNiveActu, $Asignado, $Estado, $VersInst,$Defecto) = explode("\t", $d);
	@list($date,$hora) = explode(" ",$FechGrab); @list($dia,$mes,$ano)=explode("/",$date);
	$FechGrab="$ano/$mes/$dia $hora";
	@list($date,$hora) = explode(" ",$FechReso); @list($dia,$mes,$ano)=explode("/",$date);
	$FechReso="$ano/$mes/$dia $hora";


	myQUERY("REPLACE Incidencias VALUES('$ID','$Titulo','$FechGrab', '$FechReso', '$CodiReso', '$ElemProd', '$TipoProbl', $DiaNiveMax, $Prioridad, $DiaNiveActu, '$Asignado', '$Estado', '$VersInst','$Defecto');");
	echo "Progreso: ".round($contador/$Total*100,2).PHP_EOL;
}
echo "<p>Fin de generacion de informacion.</p>".PHP_EOL;

?>
