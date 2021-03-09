<?php
if (!isset($DOCUMENT_ROOT)) require($_SERVER["DOCUMENT_ROOT"]."/config.php");

if (isset($Get_bat25)) {

function _DIE($txt) {
	Actu_json($txt);
	die();
}

function Actu_json($txt) {
	global $fp;
	fputs($fp, "<br>".$txt);
 	fflush($fp);
}
function Establece_Conexion($ip, $port, $user, $pass) {
	if(!($con = @ssh2_connect($ip, $port))) _DIE('No se puede conectar con la máquina '.$ip);
	if(!@ssh2_auth_password($con, "root", "root")) _DIE('Fallo de autentificación en la máquina '.$ip);
	return $con;
}
function Ejecuta_Comando($con, $comando, $verbose) {
	if(!($stream = ssh2_exec($con, $comando, "xterm")) ){
		_DIE('Fallo de ejecución de comando '.$comando.' en la máquina '.$ip."<br>");
	} else {
		stream_set_blocking( $stream, true );
		$data = "";
		while( $buf = fgets($stream)){
			$data .= $buf;
			Actu_json($buf.PHP_EOL);
		}
		fclose($stream_out); fclose($stream);
	}
	return $data;
}

function Ejecuta_Comando_PV($con, $comando) {
	if(!($stream = ssh2_exec($con, $comando, true)) ){
		_DIE('Fallo de ejecución de comando '.$comando.' en la máquina '.$ip."<br>");
	} else {
		$errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		stream_set_blocking( $errorStream , true );
		
		$data = "";
		while( $buf = fgets($errorStream)){
			$data .= $buf;
			flush();
			Actu_json($buf.PHP_EOL);
		}
		fclose($errorStream );
	}
	return $data;
}

function Ejecuta_Comando_Shell($con, $comando) {
	$sshStream = ssh2_shell($con, 'xterm', null, 120, 24, SSH2_TERM_UNIT_CHARS); 
	fwrite($sshStream, $comando . PHP_EOL);
	sleep(1);
	stream_set_blocking($sshStream, true);
	$sshUntarRetval = "";
	while ($buf = fgets($sshStream)) {
		flush();
		$sshUntarRetval .= $buf;
		Actu_json($buf.PHP_EOL);
	}
	fclose($sshStream);
}

function put_file($con, $srcFile, $dstFile, $verbose=0) {
	// Create SFTP session
	($verbose==1) && Actu_json("Iniciando transferencia...".PHP_EOL);
	$sftp = ssh2_sftp($con);
	$sftpStream = @fopen('ssh2.sftp://'.$sftp.$dstFile, 'w');
	try {
		if (!$sftpStream) {
			throw new Exception("Could not open remote file: $dstFile");
		}
		$size_to_send = filesize($srcFile);
		$data_to_send = @file_get_contents($srcFile);
		if ($data_to_send === false) {
			throw new Exception("Could not open local file: $srcFile.");
		}
		if (@fwrite($sftpStream, $data_to_send) === false) {
			throw new Exception("Could not send data from file: $srcFile.");
		}
		fclose($sftpStream);
	} catch (Exception $e) {
		error_log('Exception: ' . $e->getMessage());
		fclose($sftpStream);
	}
	($verbose==1) && Actu_json("Transferencia OK...".PHP_EOL);
}

foreach($_POST as $k => $d) { $$k=$d;}

$fp=fopen($DOCUMENT_ROOT."/".$f_tmp,"w");

Actu_json("Iniciando conexion con IP: $ip, puerto: $port".PHP_EOL);
$con=Establece_Conexion($ip, $port, "root", "root");

Actu_json("Copiando herramientas backup...".PHP_EOL);
put_file($con, "/home/soporteweb/Resources/Estado_Monitorizacion/Conexion_Tienda/scripts/backup25","/tmp/backup25",2);
// $res=Ejecuta_Comando($con, "scp soporte@10.208.162.6:/home/soporteweb/Resources/Estado_Monitorizacion/Conexion_Tienda/scripts/backup25 /tmp/");

Actu_json("Ejecutando backup: ");
Ejecuta_Comando($con, "mysqldump n2a | pv -fbte -i1  2>/dev/stdout > /tmp/test | tr '\r' '\n'");

// $res=Ejecuta_Comando($con, "bash /tmp/backup25");

Actu_json("OPERACION FINALIZADA!!");

fclose($fp);

/*	$ssh2='sudo ssh2 '.$Tienda.' '.$Caja;
	echo "<h2>Salvando base de datos en tienda $Tienda - Caja $Caja</h2>";
	echo "<ul>";
	echo "<li>Volcando herramientas para bat 25...</li>";
	$cmd='cat ../scripts/backup25 | '.$ssh2.' "cat - > /tmp/backup25; md5sum /tmp/backup25 | awk \'{print $1}\'"';
	echo(shell_exec($cmd));
	echo "<li>Salvando datos...</li>";
	echo "<pre style='font-size:10px'>";
	$cmd=$ssh2.' "cd /tmp; bash backup25"'; echo $cmd.PHP_EOL;
	echo(shell_exec($ssh2.' "mysql n2a -e \"select * from CUPON\""'));
// 	_ECHO(shell_exec($cmd));
	echo "</pre>";*/
	die();
}

$DIR_FILES_OUT="/usr/local/n2a/var/data/communications/out";

function DIE_ERROR($Texto) {
	die(_ECHO("<div class='Aviso Aviso_Rojo'><b style='color:red'>$Texto</b></div>"));
}

	$FILE_BASE="M".sprintf("%05d",$con_tda->tienda)."00";
	$FECHHORA_ACTUAL=date("Ymd-Hi");
	$FECHA_ACTUAL=date("Ymd");

	$DIR_VENTAS="/root/ventas_GN";
	$DIR_TMP_VENTAS="$DIR_VENTAS/$FECHA_ACTUAL";

	$FILE_VGZ=$DIR_VENTAS.'/'.$FILE_BASE.'.VGZ';
	$FILE_SGZ=$DIR_VENTAS.'/'.$FILE_BASE.'.SGZ';
	$FICHEROTAR=$FECHA_ACTUAL.'_'.$FILE_BASE.'.tgz';

// require_once($DOCUMENT_ROOT.$DIR_CONEXION_TIENDA."Acciones_PHP/Salvado_Ventas/Salvado_Ventas_SA.php");

	$File1="$DOWNLOAD_SERVER?file=$DIR_TMP$FILE_BASE.VGZ";
	$File2="$DOWNLOAD_SERVER?file=$DIR_TMP$FILE_BASE.SGZ";
	$FTP_Files=$DOCUMENT_ROOT.$DIR_TMP.$FILE_BASE.".VGZ"."#".$DOCUMENT_ROOT.$DIR_TMP.$FILE_BASE.".SGZ";


if (isset($Opcion_Salvado_Ventas)) {
	_ECHO("<script>Desbloqueo();</script>");

	require_once($DOCUMENT_ROOT.$DIR_CONEXION_TIENDA."Acciones_PHP/Salvado_Ventas/$F_Salvar");

	echo '
	<hr>
	<p>Elija modo de transmision</p>
	<ul style="list-style-type: none;">
		<li><input type=radio name=Metodo value="USB" />PC/USB
			<div id="info_usb">
				- Si lo va a grabar en un USB, recuerde comprobar que el dispositivo USB tiene espacio suficiente.<br>
				- Utilizar el USB para volcar los ficheros en la aplicaci&oacute;n AS/400 correspondiente.<br>
				<ul style="margin-top:1em">
				<li>Pulsar <a href="'.$File1.'">[aqu&iacute;]</a> para grabar el fichero de ventas.</li>
				<li>Pulsar <a href="'.$File2.'">[aqu&iacute;]</a> para grabar el fichero de fidelizaci&oacute;n.</li>
				</ul>
			</div>
		</li>
		<li><input type=radio name=Metodo value="CONCENTRADOR" />CONCENTRADOR
			<div id="info_conc">
				NOTA: Esta opcion nos permite enviar directamente los dos ficheros al concentrador.
				<input type=button name="FTP_Concentrador" value="Enviar a concentrador"/>
			</div>
			<div id="d_ftp_concentrador" style="display:none">
				<p>Transfiriendo ficheros al concentrador </p>
				<span id="resultado_ftp_concentrador"></span>
			</div>
		</li>
	</ul>
	<script>
		$(":radio:eq(0)").click(function(){ $("#info_usb").show(); $("#info_conc").hide(); });
		$(":radio:eq(1)").click(function(){ $("#info_usb").hide(); $("#info_conc").show(); });
		$("#d_ftp_concentrador").dialog({
			autoOpen: false, modal: true, width: "auto", height: 400, resizable: false,
			open: function() {
				var parametros={ OPCION:"FTP_CONCENTRADOR", ftp_server:"'.$ftp_server.'", ficheros:"'.$FTP_Files.'" };
				$.ajax({ data:  parametros, url:DIR_RAIZ+"/tools/ejecuta_diferido.php", type:  "post",
					success:  function (response) { $("#resultado_ftp_concentrador").html(response); }
				});
			},
			buttons: {
				"Cerrar": function() {
					$(this).dialog("close");
				}
			}
		});
		$("input[name=FTP_Concentrador]").on("click",function(e){
			$("#d_ftp_concentrador").dialog("open");
		});
	</script>
	';
	
	echo '</div>';
// 	Graba_Historico("SALVADO DE VENTAS POR HSR");
}
else
{
$local_file=str_replace("/home/soporteweb/","/",__FILE__);
echo '
<div class="Aviso" style="width: 80%; ">
<p>Esta opci&oacute;n permite salvar la base de datos actual de la caja.<br><i>Por favor, rogamos no interrumpan el proceso, podria da&ntilde;ar la informacion extraida.</i>
</p>
';
$Iframe='<iframe scrolling="auto" id="iframe_consola" src="'.$local_file.'?Get_bat25=true&Tienda='.$con_tda->tienda.'&Caja='.$con_tda->caja.'">
</iframe>';
}

echo '
<script>
var stop=true;
function updateStatus( target ){
	$("#"+target).load("/tmp/status.json");
	if (stop==false)
		t = setTimeout("updateStatus(\'"+target+"\')", 500);
}

function startprocess()
{
	$("#div_salvado").html();
	$.ajax( {
		url:"'.$local_file.'",
		data:{
			Get_bat25:"true",
			ip:"'.$con_tda->GetIP().'",
			port:23,
			f_tmp:"tmp/status.json"
		},
		complete:function(){ stop=true; }
	});
	stop=false;
	t = setTimeout("updateStatus(\'div_salvado\')", 500);
}
</script>';
echo '</ul> <input id="Generar_bat25" type=button value="Generar fichero de ventas?" onclick="startprocess();"/>
	<div id="div_salvado">
	</div>
</div>';

// 		url:"'.$local_file.'?Get_bat25=true&Tienda='.$con_tda->tienda.'&Caja='.$con_tda->caja.'",
// $("#Generar_bat25").on("click",function() {
// 	$("#div_salvado").html(\'<img src="/img/ventana_espera.gif"/>\');
// 	$("#div_salvado").load("'.$local_file.'?Get_bat25=true&Tienda='.$con_tda->tienda.'&Caja='.$con_tda->caja.'");
// });

?>
