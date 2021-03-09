<html>
<?php
@session_start();

$Tipo=@$_GET['Tipo'];
$Fichero=$_GET['file']; 

$FichBase=basename($Fichero);

$Dir_Tmp=$_SERVER['DOCUMENT_ROOT'].$_SESSION['DIR_TMP']."tmp_".substr($FichBase, 0, 7);

$TITULO=$FichBase; $Sin_CSS=true;

$Modo_Lite=true;
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once($DOCUMENT_ROOT.$DIR_TOOLS.'head_1.php');
require_once($DOCUMENT_ROOT.$DIR_TOOLS.'tools.php');

echo '<body onunload="borra_tmp(\''.$Dir_Tmp.'\');">';

function DIE_IF($Cond, $Texto) {
	if ($Cond)
		die("<big>".$Texto."</big><p><i>Por favor, pongase en contacto con el administrador o Ventas Nacional</i></p>");
}

function is_gz($Fichero) { return (pathinfo( $Fichero, PATHINFO_EXTENSION ) == "gz"); }
	

$File_tmp="file_tmp";
if (is_gz($FichBase)) $CAT="zcat"; else $CAT="cat";

exec("rm -Rf $Dir_Tmp 2>/dev/null");
exec("mkdir -p $Dir_Tmp 2>/dev/null");
exec("$CAT $Fichero > $Dir_Tmp/$File_tmp");

$Size_Fichero=filesize($Fichero); $Size_tmp=filesize("$Dir_Tmp/$File_tmp");
$Lineas_Fichero=wc_l("$Dir_Tmp/$File_tmp");
$tama_parte=intval($Lineas_Fichero / 8.0)+1;

$tmp=exec("
	cd $Dir_Tmp; 
	split -d -l$tama_parte $File_tmp parte_ ;
	for i in parte_*; do
		awk '/^2/ { printf substr(\$1,1,8) \"-\" substr(\$1,9,6) \"-\" FILENAME \" \"; exit;}' \$i;
	done;");
$Lista_Files=explode(" ",$tmp);
foreach($Lista_Files as $k => $f) {
	list($Fech, $Hora, $File)=explode("-",$f);
	$Lista_Total[$File]=array($Fech,$Hora);
}

echo '
<table style="background-color:white">
	<tr><td id="Cabecera_Ver_Logs" style="border-bottom:2px solid black">';

// <div id="Cabecera_Ver_Logs" style="position:absolute; top:0; left:0; width:100%;  background-color:white;">';
echo '<p><b>Nombre del fichero: </b>'.$FichBase.'</p>';
if (is_gz($FichBase ))
	echo '<p><b>Tamaño del fichero GZ:</b> '.form_size($Size_Fichero,"KB").' <b>(Descomprimido:</b> '.form_size($Size_tmp,"KB").'<b>)</b></p>';
else
	echo '<p><b>Tamaño del fichero: </b>'.form_size($Size_Fichero,"KB").'</p>';
echo '<ul>';
//// FICHERO COMPLETO ////
$myURL='ver_log_2.php?file='.$Fichero.'&Tipo=LOG';
echo '<li>Ver fichero completo <a href="javascript:{}" onclick="Carga_Datos(\'visor_log\', \''.$myURL.'\');">aqu&#237;</a></li>';
//// FICHERO POR TRAMOS ////
echo '<li>Ver fichero por tramos horarios:<br>';
foreach ($Lista_Total as $k => $d) {
	$myURL='ver_log_2.php?file='.$Dir_Tmp."/".$k.'&Tipo=LOG';
	echo '<a href="javascript:{}" onclick="Carga_Datos(\'visor_log\', \''.$myURL.'\');">'.form_fecha($d[0])."-".form_hora($d[1]).'</a> ';
}
echo '</li>';
echo '</ul>';
// echo '<hr>';
// echo '</div>';
echo '</td></tr>';
echo '<tr><td>';
// echo '<div style="top: 160;position: absolute;width: 99%; height:auto;">';
echo '<object type="text/html" id="visor_log" style="height: 740;width: 100%;"></object>';
echo '</td></tr>';
// echo '</div>';
?>
</pre>
</body>
</html>