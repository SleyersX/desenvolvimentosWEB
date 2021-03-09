<?php
set_time_limit(0); 
ob_implicit_flush(true);
ob_end_flush();

foreach($_POST as $k => $d) { $$k=$d;}
foreach($_GET as $k => $d) { $$k=$d;}
foreach($_SESSION as $k => $d) { $$k=$d;}
foreach($_SERVER as $k => $d) { $$k=$d;}

function ERROR($texto) { return "<p style='color:red;font-weight: bold;'>".$texto."</p>"; }
function TRAZA($texto,$id=NULL) {
	if ($id)
		echo "<script>document.getElementById('".$id."').innerHTML='".$texto."';</script>";
	else
		echo "<span style='font-size:12px;'>".$texto."</span>";
}

switch(@$opcion) {
	case "inicia":
		echo $QUERY_STRING;

		$ch1 = curl_init();
		$url=$HTTP_HOST.$PHP_SELF."?".$QUERY_STRING."&opcion=download";
		curl_setopt($ch1, CURLOPT_URL, $url); curl_setopt($ch1, CURLOPT_HEADER, 0);

//		$ch2 = curl_init();
//		$url=$HTTP_HOST.$PHP_SELF."?".$QUERY_STRING."&opcion=cancel_download";
//		curl_setopt($ch2, CURLOPT_URL, $url); curl_setopt($ch2, CURLOPT_HEADER, 0);

		$mh = curl_multi_init();
		curl_multi_add_handle($mh,$ch1);
//		curl_multi_add_handle($mh,$ch2);
		$active = null;
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		while ($active && $mrc == CURLM_OK) {
			if (curl_multi_select($mh) != -1) {
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		curl_multi_remove_handle($mh, $ch1);
//		curl_multi_remove_handle($mh, $ch2);
		curl_multi_close($mh);
		exit;
	
	case "cancel_download":
		echo "Descarga cancelada!!";
		file_put_contents($file_cancelar,"Cancelar");
//		unlink($fichero_cancel);
		exit;

	case "download":
		echo '
			<div>
				<h1 id="titulo"></h1>
				<div id="status"></div>
				<div id="div_progress" style="display:none">
					<progress id="progressbar" max="100"></progress>
					<span id="ahora"></span>/<span id="Total"></span> bytes
					<a id="cancelar_descarga" href="javascript:{}">Cancelar</a>
				</div>
			</div>
			<script>
				var pb=document.getElementById("progressbar");
				var ahora=document.getElementById("ahora");
				var Total=document.getElementById("Total");
			</script>
		';

		TRAZA("Estableciendo conexion con la direccion IP ".$IP."... ","status");
		if(!($con = ssh2_connect($IP, $port)))
			die(ERROR("ERROR: No se puede conectar con la direccion IP ".$IP));

		TRAZA("Autorizando la conexion... ","status");
		if(!ssh2_auth_password($con, "root", "root"))
			die(ERROR("Fallo de autentificación del POS..."));

		TRAZA("DESCARGANDO FICHERO: ".$fichero,"status");
	
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
		exit;
}

$file_local=tempnam($DOCUMENT_ROOT.$_SESSION['DIR_TMP'], "download_bbdd_");
$file_cancelar=$file_local.".cancel";
$options_download=$QUERY_STRING."&file_local=".$file_local."&file_cancelar=".$file_cancelar;
$options_cancelar="&file_local=".$file_local."&file_cancelar=".$file_cancelar;

?>
<body onload="callScripts();">
	<span id="status2"></span>

	<div id="callDownload"></div><div id="callCancel"></div>
	<a id="control_descarga" href="javascript:{ Cancela_Descarga(); }">Cancelar descarga...</a>
</body>

<script src="/Resources/js/jquery.min.js"></script>

<script>
	var Descarga=document.getElementById("callDownload");
	var Cancelar=document.getElementById("i_cancelar");
	var options_download="<?php echo $options_download; ?>";

	callScripts=function () {
		document.getElementById("callDownload").innerHTML = '<iframe id="descarga" src="./control_descarga.php?opcion=inicia&'+options_download+'" style="float:left;"></iframe>';
		document.getElementById("callCancel").innerHTML = '<iframe id="i_cancelar" src="./control_descarga.php?opcion=cancel_download<?php echo $options_cancelar;?>" style="height:50px; width:100px; float:right;"></iframe>';
	}

	function Cancela_Descarga() {
		document.getElementById("descarga").stop();
//		document.getElementById("callCancel").innerHTML = '<iframe id="i_cancelar" src="./control_descarga.php?opcion=cancel_download<?php echo $options_cancelar;?>" style="height:50px; width:100px; float:right;"></iframe>';
//		Cancelar.src="./control_descarga.php?opcion=cancel_download"+"<?php echo $options_cancelar;?>";
	}
//	$("#i_cancelar").attr("src","./control_descarga.php?opcion=cancel_download"+"<?php echo $options_cancelar; ?>");
//	$("#descarga").attr("src","./control_descarga.php?opcion=inicia&"+options_download);

</script>