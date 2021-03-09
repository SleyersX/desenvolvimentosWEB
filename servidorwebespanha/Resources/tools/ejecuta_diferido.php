<?php
$Modo_Lite=true;
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once($DOCUMENT_ROOT.$DIR_RAIZ."/tools/tools.php");
foreach($_POST as $k => $d) { $$k=$d;}

$Idioma=$_SESSION['Idioma'];

function _ERROR($texto) { _ECHO("<font color=red>$texto</font>"); }
function _DIE($texto) {  die(_ERROR($texto)); }
function _OK($texto) { _ECHO("<font color=blue>$texto</font>"); }

switch ($OPCION) {
	case "UPDATE_DATA":
		_ECHO("Iniciando proceso...<br><i>Starting proccess...</i><br>");
		if (empty($DIR_TMP)) $DIR_TMP="/tmp/";
		$Tienda=sprintf("%05d",$Tienda);
		file_put_contents("/tmp/error.log",date("Y-m-d H:i:s")." - Peticion de actualizacion para la tienda $Tienda ($Pais) desde la direccion IP ".$_SERVER['REMOTE_ADDR']."\n", FILE_APPEND);
		_ECHO("Obteniendo informacion...<br><i>Getting information...</i><br>");
		$cmd="cd /home/MULTI; sudo bash Actualiza \"$Tienda\" \"$Pais\"";
		shell_exec($cmd);
		if ($Idioma="ENG") _OK("<p>Proccess successfuly done!</p>");
		else _OK("<p>Datos actualizados correctamente.<br>Pulse en CERRAR para recargar.</p>");
		break;

	case "CHANGE_IP":
		if (!filter_var($Nueva_IP, FILTER_VALIDATE_IP)) {
			echo "<font color=red>IP no valida: <b>$Nueva_IP</b>...</font>";  exit(1);
		}
		$Where="numerotienda='$Tienda' and Centro='$Centro'";
		shell_exec("sudo mysql soporteremotoweb -e \"UPDATE tiendas SET IP='$Nueva_IP' where $Where; INSERT INTO Historico_ESP VALUES($Tienda,1,NOW(),'Se ha cambiado la IP de la tienda a $Nueva_IP.')\" -h 10.208.162.6 -u root");
		echo "<font color=blue>IP Cambiada con exito...</font>";
		die();
		break;

	case "ADD_SHOP":
		require_once(getcwd()."/mysql.php");
// 		myQUERY("UPDATE tiendas SET IP='$Nueva_IP' where $Where");
// 		myQUERY("INSERT INTO Historico_ESP VALUES($Tienda,0,NOW(),'Se ha cambiado la IP de la tienda a $Nueva_IP.')");
		echo "<font color=blue>Tienda creada con exito...$newTienda</font>"; exit(0);
		break;

	case "FTP_CONCENTRADOR":
		$conn_id = ftp_connect($ftp_server) or _DIE("No se pudo conectar a $ftp_server");
		ftp_login ($conn_id, "lares/usertpvsop", "al59e1q6") or _DIE("Authentication failed on the server!!");
		ftp_chdir($conn_id, "FichAut") or _DIE("Directory not exists!!");
		$Lista_Files=explode("#",$ficheros);
		foreach($Lista_Files as $k => $d) {
			 if (!empty($d)) {
				$base=basename($d);
				_ECHO("Transfiriendo fichero $base... ");
				if (file_exists($d))
					if (ftp_put($conn_id, $base, $d, FTP_BINARY))
						_OK("Successfull!!<br>");
					else
 						_ERROR("Transfer failed!!<br>");
				else
 					_ERROR("Not exists!!<br>");
			}
		}
		ftp_close($conn_id);
		break;

	case "UPLOAD_FILE":
		print_r($_FILES);
		echo "Subiendo el fichero $Fichero a la carpeta $Path_Destino...";
		break;

	case "GRABA_HISTORICO":
		$txt=$_GET["txt"]; $tienda=$_GET["tienda"]; $caja=$_GET["caja"];  
//		$usuario=$_GET["usuario"]; $IP_usuario=$_SERVER["REMOTE_ADDR"];
//		echo "INSERT INTO Historico VALUES(".$tienda.",".$caja.",NOW(),'".$txt."')";
		require_once($DOCUMENT_ROOT.$DIR_RAIZ."/tools/mysql.php");
		myQUERY_remoto("10.208.162.6", "INSERT INTO Historico VALUES(".$tienda.",".$caja.",NOW(),'".$PAIS_SERVER."','".$txt."')",true);
		CLOSE_BBDD();
		break;
}

?>
