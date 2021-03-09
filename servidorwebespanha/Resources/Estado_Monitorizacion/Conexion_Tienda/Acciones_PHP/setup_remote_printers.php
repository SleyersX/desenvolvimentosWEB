<?php
if ($con_tda->caja != 1) {
	echo '<div class="Aviso Aviso_Rojo" style="width: 80%; ">
		<p>Esta opci&oacute;n solo est&aacute disponible para cajas master</p>
		<p><i>(This option is only available for Master POS)</i></p>
		</div>';
} else {

if (isset($Copia_Ficheros_Remoto)) {

echo '<script>Desbloqueo();</script>';
echo '<div class="Aviso" style="width: 60%;">';
$error=false; 
echo 'Sending file "hosts" to POS-Master..... <span id="p1"></span><br>';
$con_tda->putFile_2("/home/Versiones_Tienda/Setup_Remote_Printers/hosts", "/tmp/hosts.new_hsr", "p1") or $error=true;
echo 'Sending file "printers.conf" to POS-Master..... <span id="p2"></span><br>';
$con_tda->putFile_2("/home/Versiones_Tienda/Setup_Remote_Printers/printers.conf", "/tmp/printers.conf.new_hsr", "p2") or $error=true;

	if (!$error) {
		echo '<hr><p>Setting up files... ';
		$Res=$con_tda->cmdExec("
 			cd /etc; [ -f /tmp/hosts.new_hsr ] && (cp hosts host.bck_hsr -f; cp /tmp/hosts.new_hsr hosts -f);
 			cd /etc/cups; [ -f /tmp/printers.conf.new_hsr ] && (cp printers.conf printers.conf.bck_hsr -f; cp /tmp/printers.conf.new_hsr printers.conf -f; chmod 600 printers.conf)");
		echo 'Done!!</p>';
	}
	echo '</div>';
} else {
	echo '
	<div class="Aviso" style="width: 80%;">
		<p>Esta opci&oacute;n permite modificar los ficheros de configuracion de red para el acceso a impresoras sin utilizar NETPORT.</p>
		<p><i>(This option lets you modify the configuration files to access network printers without using NETPORT.)</i></p>
		<input type=submit name=Copia_Ficheros_Remoto value="Modificar ficheros"/>
	</div>';
	}
}
?>