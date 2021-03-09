<style>
	body { font:inherit; font-size: 12px; }
	
</style>
<body>
	<div>
		<h1 id="titulo"></h1>
		<div id="status"></div>
		<div id="div_progress" style="display:none">
			<progress id="progressbar" max="100"></progress>
			<span id="ahora"></span>/<span id="Total"></span> bytes
			<a id="cancelar_descarga" href="javascript:{}">Cancelar</a>
		</div>
	</div>
	<iframe id="control_descarga" style="border:0;width:0;height:0; display:none"></iframe>
	<span id="s_file_cancelar" style="display:none"></span>
</body>
<script>
	var pb=document.getElementById("progressbar");
	var ahora=document.getElementById("ahora");
	var Total=document.getElementById("Total");
	var cancela_descarga=0;
	var local_url="<?php echo $_SERVER['PHP_SELF'];?>";
</script>

<?php
set_time_limit(0); 
ob_implicit_flush(true);
ob_end_flush();

foreach($_POST as $k => $d) { $$k=$d;}
foreach($_GET as $k => $d) { $$k=$d;}

function ERROR($texto) { return "<p style='color:red;font-weight: bold;'>".$texto."</p>"; }
function TRAZA($texto,$id=NULL) {
	if ($id)
		echo "<script>document.getElementById('".$id."').innerHTML='".$texto."';</script>";
	else
		echo "<span style='font-size:12px;'>".$texto."</span>";
}

$lista_funciones=array("ssh2_connect","ssh2_sftp");
foreach($lista_funciones as $d)
	if (!function_exists($d))
   	die(ERROR('Function '.$d.' not found, you cannot use it here'));

if (empty($opcion)) {
	die (ERROR("ERROR INTERNO: no hay definida accion..."));
}

if ($opcion=="cancel_download") {
	TRAZA("<b>Descarga cancelada!!</b>","status");
	file_put_contents($fichero_cancel,"Cancelar");
//	unlink($fichero_cancel);
	exit;
}

TRAZA("Estableciendo conexion con la direccion IP ".$IP."... ","status");
if(!($con = ssh2_connect($IP, $port)))
	die(ERROR("ERROR: No se puede conectar con la direccion IP ".$IP));

TRAZA("Autorizando la conexion... ","status");
if(!ssh2_auth_password($con, "root", "root"))
	die(ERROR("Fallo de autentificación del POS..."));

switch($opcion) {
	case "download_bbdd":
		TRAZA("DESCARGANDO FICHERO: ".$fichero,"status");
			//Ejecución del comando
		$file_local=tempnam("/home/soporteweb/tmp/", "download_bbdd_");
		$sftp = ssh2_sftp($con);
		$sftp_remote = "ssh2.sftp://".$sftp.$dir.$fichero;
		if (!($remote = fopen($sftp_remote, 'rb')))
			die(ERROR("ERROR: no se ha podido abrir el origen: ".$fichero." (".$sftp_remote.")"));
		$total=filesize($sftp_remote);

		if (!($local = fopen($file_local, 'w')))
			die(ERROR("ERROR: no se ha podido abrir el destino: ".$file_local));
			
		$leido=0;

		echo '
		<script>
			pb.value=0;
			document.getElementById("div_progress").style="display:block;";
		</script>';
		while(!feof($remote) && !file_exists($file_cancelar)) {
			echo (file_exists($file_cancelar)?"true":"false");
			echo '
				<script>
					pb.value='.round($leido/$total*100,0).';
					ahora.innerHTML="'.$leido.'";
					Total.innerHTML="'.$total.'";
				</script>';
			$leido+=fwrite($local, fread($remote, 8192));
		}

		fclose($local);
		fclose($remote);
		TRAZA("<b>Descarga OK</b>","status");
		
		break;
}
//					if (cancela_descarga == 1) {
//						document.write("Cancelando descarga...");
//						document.getElementById("control_descarga").src=local_url+"?opcion=cancel_download&fichero_cancel='.$file_cancelar.'";
//					}

exit;
?>