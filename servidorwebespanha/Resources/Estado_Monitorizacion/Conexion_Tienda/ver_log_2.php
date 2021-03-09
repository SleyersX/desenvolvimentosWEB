<?php
$Fichero=$_GET['file'];
$FichBase=basename($Fichero);
$Tipo=@$_GET['Tipo'];

$Quitar=array(
 "CAJERA" => array("INFO  - [mainn2a] - OPERATOR DISPLAY ---->","VISOR CAJERA: ")
,"SCANNER" => array("INFO  - [WNScanner-EventThread] - [JPosBarCodeReaderWrapper] - scanner read this (getScanData):","SCANNER:")
,"TECLADO" => array("INFO  - [mainn2a] - Key pressed","TECLA PULSADA")
,"JOURNAL" => array("INFO  - [mainn2a] - ------>[JournalIso8859Wrapper]- Writting line to the JOURNAL:","IMPRESORA-DE:","IMPRESORA-DE")
,"LLAVE" => array("INFO  - [DIAJpos - EventQueue] - KeyLock at position:","LLAVERO:")
);

$Teclas_Code=array(
	 " (standard mode)"
	,"Code=0001, Id=4097 (non echoable)"
	,"Code=0008"
	,"Code=0009, Id=4109 (non echoable)"
	,"Code=0011, Id=4111 (non echoable)"
	,"Code=0015, Id=4112 (non echoable)"
	,"Code=0018, Id=4117 (non echoable)"
	,"Code=0025, Id=4127 (non echoable)"
	,"Code=0028, Id=4131 (non echoable)"
	,"Code=0032, Id=4132 (non echoable)"
	,"Code=0033, Id=4135 (non echoable)");
$Teclas_Human=array(
	""
	,"[CONSULTA]"
	,"[ALTA_CAJERA]"
	,"[GESTION_PEDIDOS]"
	,"[C]"
	,"[NO]"
	,"[FUNCIONES_DE_INSPECTOR]"
	,"[FUNCIONES_DE_ENCARGADA]"
	,"[CIERRE_FINDIA_DESCANSO]"
	,"[SI]"
	,"[TOTAL]");

function Reemplazar($Tipo, $data) {
	global $Quitar, $Teclas_Code, $Teclas_Human;
	$tmp=str_replace(htmlspecialchars($Quitar[$Tipo][0]),$Quitar[$Tipo][1],$data);
	if ($Tipo=="TECLADO") $tmp=str_replace($Teclas_Code,$Teclas_Human,$tmp);
	if ($Tipo=="LLAVE") $tmp=str_replace("INFO  - [mainn2a] - KeyLock at position:","LLAVERO:",$tmp);
	return substr($tmp,6,2)."/".substr($tmp,4,2)."/".substr($tmp,0,4)." ".substr($tmp,8,2).":".substr($tmp,10,2).":".substr($tmp,12,2)." - ".$tmp;
}

// function Reemplazar_Teclado($data) {
// 	
// 	$tmp=str_replace("INFO  - [mainn2a] - Key pressed",,$data);"TECLADO" => array("","TECLADO")
// }

function parser_log($data, $parser=false) {
	if (!$parser) return $data;
	if (preg_match("/OPERATOR DISPLAY/", $data))
		return "<b><font color='blue'>".Reemplazar("CAJERA",$data)."</font></b>";
	if (preg_match("/scanner read this/", $data))
		return "<b><font color='green'>".Reemplazar("SCANNER",$data)."</font></b>";
	if (preg_match("/Key pressed/", $data))
		return "<b><font color='#CC2EFA'>".Reemplazar("TECLADO",$data)."</font></b>";
	if (preg_match("/Writting line to the JOURNAL/", $data))
		return "<b><font color='#FF8000'>".Reemplazar("JOURNAL",$data)."</font></b>";
	if (preg_match("/S_KEY_POSITION/", $data))
		return "<b><font color='red'>".Reemplazar("LLAVE",$data)."</font></b>";
// 	if (preg_match("/- ERROR -/", $data))         return "<b><font color='red'>".$data."</font></b>";
	return $data;
}

function parser_de($data) {
	if (!preg_match("/^<|^>/",$data)) return $data; else return "";
}

function DIE_IF($Cond, $Texto) {
	if ($Cond)
		die("<big>".$Texto."</big><p><i>Por favor, pongase en contacto con el administrador o Ventas Nacional</i></p>");
}

function _open($File, $GZ) {
	DIE_IF(!file_exists($File), "El fichero $File no existe en el servidor...");
	if ($GZ) $src=gzopen($File, "r"); else $src=fopen($File, "r");
	DIE_IF(!$src, "Fallo al abrir el fichero $File...");
	return $src;
}
function _eof($src, $GZ)   {
	DIE_IF(!$src, "Fallo al trabajar con el fichero ".$GLOBALS['FichBase']."...");
	if ($GZ) return gzeof($src); else return feof($src); }
function _gets($src, $GZ)  {
	DIE_IF(!$src, "Fallo al leer del fichero ".$GLOBALS['FichBase']."...");
	if ($GZ) return gzgets($src); else return fgets($src); }
function _close($src, $GZ) {
	DIE_IF(!$src, "Fallo al cerrar el fichero ".$GLOBALS['FichBase']."...");
	if ($GZ) return gzclose($src); else return fclose($src); }

echo '<pre>';
$GZ=pathinfo( $Fichero, PATHINFO_EXTENSION ) == "gz";
$src=_open($Fichero, $GZ);
while (!_eof($src, $GZ)) {
	if ($Tipo=="DE") echo parser_de(_gets($src,$GZ));
	if ($Tipo=="LOG") echo parser_log(htmlspecialchars(_gets($src,$GZ)),true);
	flush(); @ob_flush();
}
_close($src,$GZ);

?>
</pre>