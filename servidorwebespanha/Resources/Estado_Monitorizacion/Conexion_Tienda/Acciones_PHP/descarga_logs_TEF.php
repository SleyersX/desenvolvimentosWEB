<?php
$file_TEF="$Tienda-$Caja-TEF.tgz";
$con_tda->cmdExec("cd /usr/local/n2a/var/log; tar czf /tmp/$file_TEF tef/;");
$con_tda->receiveFile("/tmp/$file_TEF", $DOCUMENT_ROOT.$DIR_TMP.$file_TEF);
$Res="<p>Informacion de TEF recogida con exito!</p><br>"	;
$Res.='<center><a class="button" href="'.$DIR_TMP.$file_TEF.'" target="_blank" style="text-decoration:none">Pulse aqui para descargar...</a></center>';
echo Pinta_Resultado("DESCARGA LOGS DE TEF",$Res);
?>