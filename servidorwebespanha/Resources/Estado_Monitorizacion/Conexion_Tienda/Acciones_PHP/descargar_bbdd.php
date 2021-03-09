<?php

/*if (!empty($argv)) {
	parse_str(implode('&', array_slice($argv, 1)), $_GET);
	print_r($_GET);
}*/

// TROZO DE CODIGO QUE SE EJECUTA CON AJAX.
if (!empty($_GET["ACCION"])) {
	$Tienda=$_GET["tienda"];
	$Caja=$_GET["caja"];
	$IP=$_GET["IP"];
	switch($_GET["ACCION"]) {
		case "download":
				$tempfile=tempnam("/home/soporteweb/tmp", "download_bbdd_");
				$fichero=$_GET["fichero"];
				$destino=$_GET["destino"];
				echo "Descargando fichero ".$fichero." de la tienda ".$Tienda." (IP: ".$IP.") - Caja ".$Caja."..."."<br>";
//				$cmd="scp -vvv -P23 root@".$IP.":/root/".$fichero." ".$tempfile;
				$cmd="ssh2 ".$Tienda." ".$Caja." \"du -b ".$fichero." | cut -f1\"";
				echo $cmd."<br>";
				
				$tama=shell_exec("sudo ".$cmd);
				if ($tama > 0) {
//					$cmd="bash -vx ssh2 ".$Tienda." ".$Caja." \"cat ".$fichero."\" > ".$tempfile;
					$cmd="ssh2 ".$Tienda." ".$Caja." \"cat ".$fichero."\" > ".$tempfile;
					echo $cmd;
					echo shell_exec("sudo ".$cmd);
				} else {
					echo "ERROR EN DESCARGA: ".$tama;
				}
				break;
	}
	exit;
}

if (!SoyYo()) {
	echo "<script>Desbloqueo();</script>";
	echo "<h1>OPCION DESACTIVADA TEMPORALMENTE.</h1>";
	exit;
}

if ($con_tda->caja != 1) {
	echo '<script>javascript:Desbloqueo();</script>';
	die(Alert("warning","SOLO SE PERMITE DESCARGAR BBDD DE CAJAS MASTER DESDE ESTA HERRAMIENTA"));
}
/*
if ($con_tda->SA==1) {
	$DIR_OTROS="/confdia/logscomu/";

	if (@$Fichero_BBDD) {
		$tmp_file = $DIR_TMP.$Tienda."-".$Caja."-".basename($Fichero_BBDD);
		$local_file = $DOCUMENT_ROOT.$tmp_file;

		if (basename($Fichero_BBDD) === "BBDD_Actual.tgz")
			$con_tda->BBDD_Actual($local_file);
		else 
			$con_tda->receiveFile($Fichero_BBDD, $local_file);
		flush(); @ob_flush();
	}

	$Result=$con_tda->cmdExec("cd $DIR_OTROS; ls -lsita *Backup* | awk '{printf \"%s#%dKB\\n\",\$11,\$7/1024}';");
	$Lista_Ficheros=explode("\n",$Result);
	$Lista_Ficheros[]="BBDD_Actual.tgz#N/A";
	rsort($Lista_Ficheros);

	$Res="<table id='lista_bbdd' class='lista_ficheros' style='text-decoration:none;'>   <thead><tr><th>Fichero</th><th>Tamanio</th><th>Opciones</th></tr></thead>   <tbody>";

	foreach ($Lista_Ficheros as $k => $d) {
		if (!empty($d)) {
			list($File, $Size) = explode("#",$d);
			$tmp_file=$DIR_TMP.$Tienda."-".$Caja."-".$File;
			$local_file=$DOCUMENT_ROOT.$tmp_file;
			$OnClick = "ACTIVA_OPCION('Fichero_BBDD','$DIR_OTROS$File')";
			$Res.="<tr><td id='td_Fichero'>".basename($File)."</td>";
			$Res.="<td id='td_Tamanio'>".$Size."</td>";
			$Res.='<td id="td_Opciones">';
			if (file_exists($local_file)) $Icono="recargar.png"; else $Icono="download_to_server.gif";
			$Res.='<a class="button b_download" onclick="'.$OnClick.'" title="Descargar fichero al servidor"><img src="'.$DIR_IMAGE.'/'.$Icono.'"/></a>';
			if (file_exists($local_file)) {
				$Res.='<a class="button b_download" href="'.$tmp_file.'" title="Descargar a PC" target="_blank"><img src="'.$DIR_IMAGE.'/download_to_pc.gif" /></a>';
				$OnClick = "ACTIVA_OPCION('Descarga_USB','$tmp_file')";
				$Res.='<a class="button b_download" href="bbdd_to_usb_sa.php?FILE='.$tmp_file.'" title="Grabar a USB" target="_blank"><img src="'.$DIR_IMAGE.'/i1/media-flash.png" /></a>';
			}
			$Res.="</td>";
			$Res.="</tr>";
		}
	}
}
else {
*/
_ECHO("<script>Desbloqueo();</script>");
_ECHO('<div class="Aviso">');

$DIR="/usr/local/n2a/var/data/database/backup/endDay/";
//$DIR="/root/";

if (@$Fichero_BBDD) {
	$tmp_file = $DIR_TMP.$Tienda."-".$Caja."-".basename($Fichero_BBDD);
	$local_file = $DOCUMENT_ROOT.$tmp_file;

	if (basename($Fichero_BBDD) === "Actual.sql.gz") {
		_ECHO("<p>Building database dump... ");
		$con_tda->cmdExec("mysqldump n2a | gzip > $Fichero_BBDD");
			_ECHO("Done!!</p>");
//			$con_tda->dump_n2a();
	}
 	$con_tda->receiveFile($Fichero_BBDD, $local_file);
// 	_ECHO("<p>"); $con_tda->get_file_from_tpv($Fichero_BBDD, $local_file); _ECHO("</p>");
}

$Result=$con_tda->cmdExec("cd ".$DIR."; ls -lHta *gz | awk '{printf \"%s#%d\\n\",\$9,\$5}';");
$Lista_Ficheros=explode("\n",$Result);
rsort($Lista_Ficheros);

$lista_ficheros=""; $l_ficheros="";
foreach ($Lista_Ficheros as $k => $d) {
	if (!empty($d)) {
		list($File, $Size) = explode("#",$d);
		$tmp_file=$DIR_TMP.$Tienda."-".$Caja."-".$File;
		$local_file=$DOCUMENT_ROOT.$tmp_file;
		$b_name=basename($File);
		$l_ficheros.="['".$b_name."',".$Size.",'N/D'],";
	}
}

$url_php="Acciones_PHP/".basename(__FILE__);
?>

<table style="width:100%">
	<tr><td><div id="div_lista_ficheros"></div></td></tr>
	<tr><td><input type="button" id="b_descargar" value="Descargar"></td></tr>
	<tr><td><iframe id="resultado_descarga" style="border:0;width:100%; height:100;display:none"></iframe></td></tr>
</table>

<script>
	var nombre_fichero = "";
	var url="<?php echo 'http://'.$SERVER_ADDR.':8080/'; ?>";

	$("#b_descargar").on("click",function () {
		if (nombre_fichero == "") {
			alert("Debe seleccionar un fichero para descargar");
		} else {
				var options = "?"+
					"comandoTerminal=download_file" +
					"&tienda=<?php echo $Tienda; ?>"+
					"&caja=<?php echo $Caja; ?>"+
					"&IP=<?php echo $con_tda->GetIP(); ?>"+
					"&fichero=<?php echo $DIR; ?>"+nombre_fichero+
					"&destino=/tmp/prueba";

				$("#resultado_descarga").attr("src",url + options);
				$("#resultado_descarga").show();
		}
	});
	
	function selectHandler(e) {
		var selection = lista_ficheros.getSelection();
		nombre_fichero = data1.getValue(selection[0].row,0);
		var size_fichero = data1.getValue(selection[0].row,1);

		$("#b_descargar").attr("value", "Descargar "+nombre_fichero);

		if (en_descarga==true) {
			$("#ini1").hide();
			$("#dialog_status").html("Ya hay un fichero en descarga. Espere por favor...").show();
		}
		else {
			$("#dialog_filename").html(nombre_fichero);
			$("#dialog_size").html(size_fichero);
			$("#ini1").show();
		}
		$("#ifrm_resultado").hide();

		$("#dialog_descarga").dialog("open");
	}

	var data1;
	var lista_ficheros;

	function drawTables() {
		data1 = new google.visualization.DataTable();
		data1.addColumn('string','Fichero');
		data1.addColumn('number','Size');
		data1.addColumn('string','Fecha');
		data1.addRows([ <?php echo $l_ficheros; ?> ]);
		lista_ficheros = new google.visualization.Table(document.getElementById('div_lista_ficheros'));
		lista_ficheros.draw(data1, { title: 'BASES DE DATOS' });
		google.visualization.events.addListener(lista_ficheros, 'select', selectHandler );
	}


$('#prog').progressbar({ value: 0 });

	var d_tienda="<?php echo $Tienda; ?>";
	var d_caja="<?php echo $Caja; ?>";
	var url_php="<?php echo $url_php; ?>";
	$("#lista_bbdd tr").on("click",function(e) {
		var t1=url_php+"?ACCION=download&tienda="+d_tienda+"&caja="+d_caja+"&fichero="+$(this).text()+"&destino=/tmp/kk1";
		var parametros={ "ACCION":"download", "tienda": d_tienda, "caja": d_caja, "fichero": $(this).text(), "destino": "/tmp/kk1", "IP":"<?php echo $IP; ?>"}

		var interval_refresca_download=en_background("#ACTU_DOWNLOAD", url_php, 500);

		console.log($(this));
		console.log($("#tama").text());
				
		$.ajax({
/*			beforeSend: function(XMLHttpRequest) {
			//Upload progress
				XMLHttpRequest.upload.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {  
						var percentComplete = evt.loaded / evt.total;
						//Do something with upload progress
					}
				}, false); 
			//Download progress
				XMLHttpRequest.addEventListener("progress", function(evt){
					if (evt.lengthComputable) {  
						var percentComplete = evt.loaded / evt.total;
						//Do something with download progress
					}
				}, false); 
			},*/
			cache:false,
			url: t1,
			data: parametros,
			success:function (x) {
				$("#res_download").html(x);
			},
			error : function(xhr, status) {
				alert('Disculpe, existió un problema');
			}
		});

	});
	google.charts.setOnLoadCallback(drawTables);
</script>
