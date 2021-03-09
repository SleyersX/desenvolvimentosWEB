<?php
require($_SERVER['DOCUMENT_ROOT']."/config.php");
$SERVER=$_SERVER['SERVER_ADDR'];
$Tienda=$_GET['Tienda'];
$DIR_VENTAS="$DIR_TMP/USB_$Tienda";
$LOG="/".$DOCUMENT_ROOT."/".$DIR_VENTAS."/".$Tienda.".log";

function GRABA_LOG($Texto) {
	global $LOG;
	$Texto_Error=date("Y-m-d H:i:s")." - ".$Texto."\n";
	file_put_contents($LOG, $Texto_Error, FILE_APPEND);
}
function GRABA_ECHO($Texto) {
	_ECHO($Texto); GRABA_LOG($Texto);
}

function ERROR_SALVADO($Texto) {
	GRABA_LOG($Texto);
	GRABA_LOG("**************************************************************************************");
	die("<p><b style='color:red'>".$Texto."<b></p>");
}

_ECHO('
<div style="border:1px solid gray; background-color: lightgray; margin-top:1em; font-size:12px">
<h2>PROCESO DE EXTRACCION DE VENTAS.</h2>
<ul>');

$Res=shell_exec("mkdir -p /$DOCUMENT_ROOT/$DIR_VENTAS && chmod 777 /$DOCUMENT_ROOT/$DIR_VENTAS && echo OK");
if (!preg_match("/OK/",$Res)) {
	ERROR_SALVADO("ERROR al crear directorio ".$DIR_VENTAS.": ".$Res);
}

GRABA_LOG("Iniciando salvado de ventas por el usuario: ".$_SESSION['usuario']." desde IP: ".$_SERVER['REMOTE_ADDR']);

_ECHO("<li>ENVIANDO HERRAMIENTAS NECESARIAS...");
$Dir_Orig=$DOCUMENT_ROOT.$DIR_RAIZ."/Estado_Monitorizacion/Conexion_Tienda/scripts/";
$Dir_Dest="/root/";
$F_Salvar="Salvar_Ventas_N2A.sh";
$Res=shell_exec("sudo ssh2 $Tienda 1 \"scp -q soporte@$SERVER:$Dir_Orig/$F_Salvar $Dir_Dest/$F_Salvar && echo 1\"");
if ($Res != 1)
	ERROR_SALVADO("ERROR: ".$Res);
_ECHO("</li>");

_ECHO("<li>EJECUTANDO HERRAMIENTA PARA EXTRAER VENTAS (NO CANCELAR NI APAGAR TPV)...");
$Res=shell_exec("sudo ssh2 $Tienda 1 \"cd $Dir_Dest; sh $F_Salvar\"");
if (!preg_match("/SALVADO CORRECTO/",$Res))
	ERROR_SALVADO($Res);
_ECHO("</li>");

_ECHO("<li>TRANSFIRIENDO FICHEROS GENERADOS ULTIMO SALVADO AL SERVIDOR...");
$cmd="sudo ssh2 $Tienda 1 \"cd /tmp/; scp -q M*.[VSL]GZ soporte@$SERVER:/$DOCUMENT_ROOT/$DIR_VENTAS/ && rm M*.VGZ M*.SGZ M*.LGZ -f && echo 1\"";
$Res=shell_exec($cmd);
if ($Res != 1)
	ERROR_SALVADO("ERROR al transferir ficheros al servidor ($Res)");

/* VGZ >>> Ventas.vta*/
$F_VTA="Ventas.vta";
shell_exec("zcat $DOCUMENT_ROOT/$DIR_VENTAS/M".$Tienda."00.VGZ > $DOCUMENT_ROOT/$DIR_VENTAS/$F_VTA");
$Tama_VTA=@filesize("$DOCUMENT_ROOT/$DIR_VENTAS/$F_VTA");

/* SGZ >>> SUN */
$F_SUN="M".$Tienda."00.SUN";
shell_exec("zcat $DOCUMENT_ROOT/$DIR_VENTAS/M".$Tienda."00.SGZ > $DOCUMENT_ROOT/$DIR_VENTAS/$F_SUN");
$Tama_SUN=@filesize("/$DOCUMENT_ROOT/$DIR_VENTAS/$F_SUN");

/* LGZ (Diario Electronico) */
$F_LGZ="M".$Tienda."00.LGZ";
$Tama_LGZ=@filesize("/$DOCUMENT_ROOT/$DIR_VENTAS/$F_LGZ");

if ($Tama_VTA > 0) _ECHO(shell_exec("sudo ssh2 $Tienda 1 \"cd /tmp/; rm -f M*.VGZ M*.SGZ M*.LGZ\""));

_ECHO("</li></ul><p><b>PROCESO TERMINADO!!</b></p>
<p>Ficheros generados, pulsar en el link para descargar:</p>
<ul>");

if (!$Tama_VTA && !$Tama_SUN && !$Tama_LGZ) _ECHO("<li><b>NO HAY FICHEROS</b></li>");

if ($Tama_VTA > 0) _ECHO("<li><a href='http://$SERVER/$DIR_VENTAS/$F_VTA'>$F_VTA</a> - Tamaño: $Tama_VTA bytes</li>");
if ($Tama_SUN > 0) _ECHO("<li><a href='http://$SERVER/$DIR_VENTAS/$F_SUN'>$F_SUN</a> - Tamaño: $Tama_SUN bytes</li>");
if ($Tama_LGZ > 0) _ECHO("<li><a href='http://$SERVER/$DIR_VENTAS/$F_LGZ'>$F_LGZ</a> - Tamaño: $Tama_LGZ bytes</li>");
_ECHO("</ul></div>");
Graba_Historico("SALVADO DE VENTAS EFECTUADO POR HSR");
GRABA_LOG("Finalizado proceso de salvado de ventas. Resultado:");
GRABA_LOG($F_VTA.": ".$Tama_VTA." bytes");
GRABA_LOG($F_SUN.": ".$Tama_SUN." bytes");
GRABA_LOG($F_LGZ.": ".$Tama_LGZ." bytes");
GRABA_LOG("**************************************************************************************");
Graba_Historico("SALVADO DE VENTAS POR HSR");

?>
<script>
	$("#Generar_Fichero").hide();
</script>
