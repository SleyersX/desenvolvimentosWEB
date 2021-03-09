<?php
$Idioma=$_SESSION['Idioma'];

switch ($Idioma) {
	case "ESP":
echo '
<div id="Error_de_conexion" class="Aviso Aviso_Rojo">
	<h2>Error de conexion</h2>
	<p id="Error_Cnx">'.$con_tda->getLastError().'</span>
	<p>No es posible conectarnos a la caja solicitada. </p>
	<p>Esto es debido a:</p>
	<ul>
		<li>La caja est&aacute; desconectada de la red.</li> Si el problema persiste, tomen medidas como: avisar servicio tecnico, revisar las conexiones de red, comprobar IP, etc.
		<li>Problemas en el disco duro de la caja: errores fisicos, disco lleno, etc.</li> Esto se puede revisar en la pantalla principal de errores. Si el problema persiste, avisen a servicio tecnico para sustitucion de disco duro.
	</ul>
</div>';
	break;

	case "ENG":
echo '
<div id="Error_de_conexion" class="Aviso Aviso_Rojo">
	<h2>Connection error</h2>
	<p id="Error_Cnx">'.$con_tda->getLastError().'</span>
	<p>It is not possible to connect to the POS.</p>
	<p>This is due to:</p>
	<ul>
		<li>The POS is disconnected from the network.</li> If the problem persists, take measures such as warning technical service, check network connections, check IP, etc.
		<li> Issues on the hard disk of the box. Physical errors, disk full, etc.</li> This can be checked on the main screen of the remote support tool. If the problem persists, reminded a technical service for replacement hard drive.
	</ul>
</div>';
	break;
}
?>