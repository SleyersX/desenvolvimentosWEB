<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
$SERVER=$_SERVER['SERVER_ADDR'];
$Tienda=$_GET['Tienda'];
$FECHA=date("Y-m-d");
$Flag_Ejec="/root/Comunicacion_a_fichero_$FECHA";
$F_Tienda=sprintf("%05d",$Tienda);
$DIR_TMP=$_SESSION['DIR_TMP'];

echo '
<div style="border:1px solid gray; background-color: lightgray; margin-top:1em;">
<h2>PROCESO DE EXTRACCION DE VENTAS.</h2>
<ul>';

_ECHO("<li>EJECUTANDO HERRAMIENTA PARA EXTRAER VENTAS (NO CANCELAR NI APAGAR TPV)...");
$Dir_Orig=$DOCUMENT_ROOT."/tools/";
$Dir_Dest="/root/";
$F_Salvar="Comunicacion_a_fichero";

if (!file_exists("$Dir_Orig/$F_Salvar"))
	die ("FICHERO $Dir_Orig/$F_Salvar no existe!!");

if (!SoyYo()) shell_exec("rm -f /$DOCUMENT_ROOT/$DIR_TMP/M$F_Tienda*");

$comando="
mkdir -p /root/ventas_GN
cd /root/ventas_GN
if [ -f $Flag_Ejec ] ; then
	echo 'ERROR: salvado ya ejecutado hoy...'
else
	scp -q soporte@$SERVER:$Dir_Orig/$F_Salvar $Dir_Dest/$F_Salvar
	if [ ! -s $Dir_Dest/$F_Salvar ]; then
		echo 'ERROR: al transferir la herramienta de salvado de datos'
	else 
		service nfs stop >/dev/null 2>&1
		tar czf BBDD.tgz /confdia/ctrdatos/maepara1.* /confdia/ficcaje/maeemis* /confdia/ficcaje/intdocu1.dat /confdia/ficcaje/hisdocu1.dat >/dev/null 2>&1
		chmod 755 /root/Comunicacion_a_fichero
		cd /confdia/bin
		. ./functions >/dev/null 2>&1
		. ./setvari >/dev/null 2>&1
		/root/Comunicacion_a_fichero $Tienda >/dev/null 2>&1 | tee -a /tmp/Comunicacion_a_fichero.$FECHA.log
		nohup service nfs start >/dev/null 2>&1 &
		cd /tmp
		if [ ! -z \"`dir M$F_Tienda* 2>/dev/null`\" ] ;then
			tar cz M$F_Tienda* | ssh soporte@$SERVER \"cd /$DOCUMENT_ROOT/$DIR_TMP/; tar xzf -\" 2>/dev/null
			for f in M$F_Tienda*; do
				mv \$f /root/ventas_GN/\$f.$FECHA
			done
			touch $Flag_Ejec
			echo \"SALVADO CORRECTO\"
		else
			echo \"ERROR: No se han generado ficheros de ventas...\"
		fi
	fi
fi";

file_put_contents("/$DOCUMENT_ROOT/$DIR_TMP/salvado_$F_Tienda",$comando);

$comando="
cat /$DOCUMENT_ROOT/$DIR_TMP/salvado_$F_Tienda | sudo ssh2 $Tienda 1 \"cat - > /root/salvado_ventas.sh; bash /root/salvado_ventas.sh\"; sync;";

// if ($Pais=="BRA" && SoyYo()) echo "$comando";

$Res=shell_exec($comando);

if (preg_match("/ERROR/",@$Res))
	die("<p><b style='color:red'>$Res</b></p>");
_ECHO("</li></ul><p><b>PROCESO EN TPV TERMINADO!!</b></p>");

$conn_id = ftp_connect($ftp_server);
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
ftp_chdir($conn_id, 'FichAut'); 

_ECHO("<hr><h3>Ficheros generados</h3><i>(Pulsar en el link para descargar en caso necesario)</i><ul>");

$Files_Descargados=scandir($DOCUMENT_ROOT.$DIR_TMP);
$encontrado=false;
foreach($Files_Descargados as $k => $d) {
	if (preg_match("/M$F_Tienda/",$d)) {
		$tama=filesize($DOCUMENT_ROOT.$DIR_TMP.$d);
		_ECHO("<li>");
		_ECHO("<a href=\"http://$SERVER/$DIR_TMP/$d\" target=\"_blank\">$d</a> - Tamaño: $tama bytes");
		if (ftp_put($conn_id, $d, $DOCUMENT_ROOT.$DIR_TMP.$d, FTP_ASCII)) {
			_ECHO(" (Se ha subido con éxito al concentrador $ftp_server)");
		} else {
			_ECHO(" (<b style='color:red'>ERROR en la subida del fichero a $ftp_server</b>)");
		}
		_ECHO("</li>");
		$encontrado=true;
	}
}
if (!$encontrado) _ECHO("<li><b>NO HAY FICHEROS</b></li>");

Graba_Historico("SALVADO DE VENTAS EFECTUADO POR HSR");

ftp_close($conn_id);

_ECHO("</ul></div>");
?>
<script>
	$("#Generar_Fichero").hide();
</script>