<?php
	$IP=@$con_tda->GetIP();
	$Puerto=@$con_tda->GetPort();

$Tienda=@$_GET["Tienda"]; $Caja=@$_GET["Caja"];
//$IP=$_GET["IP"]; $Puerto=$_GET["Puerto"];

if (!empty($_POST["Accion_Diagnostico"])) {
$Accion_Diagnostico=$_POST["Accion_Diagnostico"];
$Tienda=$_POST["T"];
$Caja=$_POST["C"];
$tmpsalida=$_SERVER["DOCUMENT_ROOT"].$_POST["tmpsalida"];
switch ($Accion_Diagnostico) {
/*
	case "Run BAT 555":
		_ECHO("<pre><p>STARTING RUN BAT 555.</p>");
		_ECHO($con_tda->cmdExec("echo | (touch /var/lib/nfs/xtab /var/lib/nfs/etab /var/lib/nfs/rmtab; /etc/rc.d/init.d/nfs stop; /etc/rc.d/init.d/nfs start)"));
		_ECHO('<p>RUN SUCESSFULL.</p></pre>');
		break;

	case "Delete file idenerro.err":
		_ECHO("<pre><p>REMOVING FILE IDENERRO.ERR.</p>");
		_ECHO("<p>".$con_tda->cmdExec("rm /confdia/bin/idenerro.err -vf")."</p>");
		_ECHO('<p>REMOVE SUCESSFULL.</p></pre>');
		break;

	case "Restore communication database":
		_ECHO("<pre><p>STARTING RESTORE OF COMMUNICATIONS DATABASE.</p>");
		_ECHO("<p>".$con_tda->cmdExec("cd /confdia/bdcomu/; tar xzvf bdcomu.tgz")."</p>");
		_ECHO('<p>RESTORE SUCESSFULL.</p></pre>');
		break;
*/
	default:
		break;
}
exit;
}

if (empty($tmpsalida)) {
	$tmpsalida=tempnam("/tmp", "diagnosis_salida");
	file_put_contents("/home/soporteweb".$tmpsalida, "resultado");
}
		
?>
<style>
	.opc_nok { font-style: italic; color: gray;}
	.paises { content: "Solo paises"; }
</style>

<fieldset id="opciones_diagnosis">
<legend>HERRAMIENTAS DE CHEQUEO DE TPV</legend>
<p><b>Herramientas comunes:</b>
	<ul>
		<li><a class="opc_ok" id="LAN_Stress" href='javascript:{}' title="Proceso para realizar una comprobación del estado de la LAN de la tienda">LAN Stress</a></li>
		<li><a class="opc_ok" id="elementos_tienda" href='javascript:{}'>Check Elementos Tienda</a></li>
		<li><a class="opc_ok" id="check_conexion_servidores" href='javascript:{}'>Check Conexion Servidores</a></li>
		<li><a class="opc_nok" id="ping_balanza_seccion" href='javascript:{}'>Ping Balanza Seccion <i>(Trabajando en su solucion)</i></a></li>
		<li><a class="opc_nok" id="instalaciones_pendientes" href='javascript:{}'>Instalaciones Pendientes</a></li>
		<li><a class="opc_ok" id="tailDE" href='javascript:{}'>Tail D. Electronico</a></li>
	</ul>
</p>
<p><b>Solo Espa&ntilde;a:</b>
	<ul>
		<li><a class="opc_ok" id="tailog" href='javascript:{}'>Tailog</a></li>
	</ul>
</p>
<p><b>Resto de paises:</b>
	<ul>
		<li><a class="opc_nok" id="BAT 555" href='javascript:{}'>BAT 555</a></li>
		<li><a class="opc_nok" id="delete_idenerro.err" href='javascript:{}'>Delete file idenerro.err</a></li>
		<li><a class="opc_nok" id="restore_comm_database" href='javascript:{}'>Restore communication database</a></li>
	</ul>
</p>
</fieldset>