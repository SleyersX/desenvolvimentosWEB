<?php
if (count($_GET)>0) {
	foreach($_GET as $k => $d) $$k=$d;
}

	$DIR_DESTINO="/home/BBDD_Tiendas/";
	$DIR_BBDD    = $DIR_DESTINO;
	$DIR_VENTAS  = $DIR_DESTINO."Ventas_GN/";
	$DIR_LOGS    = $DIR_DESTINO."logs/";
	$DIR_OTROS   = $DIR_DESTINO."otros/";
	$DIR_CONTROL = $DIR_DESTINO."/control_descargas/";

if (!empty($file_datos_descarga)) {
	if (file_exists($file_datos_descarga)) {
		$tmp=file_get_contents($file_datos_descarga);
		$tmp1=explode(";",$tmp);
		foreach($tmp1 as $k => $d) {
			if ($d) {
				list($var,$valor) = explode("=",$d);
				$$var=$valor;
			}
		}
	}
}

function Comprueba_Conexion($tienda) {
	echo "Comprobando conexion con la tienda $tienda... ";
	$res=exec('sudo ssh2 '.$tienda.' '.$caja.' "hostname -i | cut -f1 -d\' \'" || echo 0');
	if ($res == 0 ) {
		echo "<p style='color:red'>ERROR: no hay acceso a la tienda...</p>";
		exit;
	} else {
		echo "<span id='IP_origen' style='display:none'>".$res."</span>";
	}
	echo "<b>OK</b><br>";	
}
	
function Crea_Tunel($tienda,$caja=1,$pais="ESP") {
//	echo "Creando tunel con la tienda $tienda... ";
	$res=shell_exec('sudo bash /usr/bin/tda_tunnel '.$tienda.' '.$caja.' '.$pais." | tr -d '\n';");
//	echo "<pre>".$res."</pre>";
	echo "<span id='dir_tunel' style='display:none'>".$res."</span>";
//	echo "<b>OK</b><br>";
}

function Crea_Lista($lista, $titulo, $pc=false, $dir_destino="") {
	global $DIR_DESTINO;
	if ($pc) {
		$link_descarga = str_replace($DIR_DESTINO, "", $dir_destino);
	}
	else {
		$link_descarga="";
	}
	echo "<table class='tabla2' style='font-size: 12px;'>";
	echo '<caption><div id="info1" class="informacion borde_redondo"><h3>'.$titulo.'</h3></div></caption>';
	echo "<tr><th>Nombre fichero</th><th>Size</th><th>Fecha Creacion</th></tr>";
	foreach($lista as $d) {
		if (!is_dir($d)) {
			$b_file=basename($d); $size=filesize($d); $date=date("d/m/Y H:i:s.", filectime($d));
			echo "<tr class='row_file' dir_destino='".$dir_destino."' link_descarga='".$link_descarga."' title='&#128432  PULSE PARA DESCARGAR EL ARCHIVO  '>";
				echo "<td><span id='i_file_name' class='file_name'>".$b_file."</span></td>";
				echo "<td><span id='i_file_size'class='file_size'>".$size."</span> bytes (".round($size/1024/1014,0)." MB)</td>";
				echo "<td><span id='i_file_date'>".$date."</span></td>";
			echo "</tr>";
		}
	}
	echo "</table>";
}

function sort_by_mtime($file1,$file2) {
    $time1 = filemtime($file1);
    $time2 = filemtime($file2);
    if ($time1 == $time2) {
        return 0;
    }
    return ($time1 < $time2) ? 1 : -1;
}

if (!empty($opcion) ) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();

	$tienda=sprintf("%05d",(empty($tienda)?0:$tienda));
	$caja=(empty($caja)?1:$caja);
	$pais=(empty($pais)?'ESP':$pais);

	switch($_GET["opcion"]) {
		case "ayuda1":
			echo "<p>Con esta opción, usted podrá descargar cualquier fichero permitido al servidor de soporte</p>
				<p><input id='check_ayuda' type='checkbox'>No volver a mostrar esta ayuda</input></p>";
			exit;

		case "copia1":
		
			$filename_destino=basename($destino);
			
			$a_grabar="DIR_DESTINO=".$DIR_DESTINO.";";
			$a_grabar.="DIR_CONTROL=".$DIR_CONTROL.";";
			$a_grabar.="DIR_BBDD=".$DIR_DESTINO.";";
			$a_grabar.="DIR_VENTAS=".$DIR_DESTINO."Ventas_GN/".";";
			$a_grabar.="DIR_LOGS=".$DIR_DESTINO."logs/".";";
			$a_grabar.="DIR_OTROS=".$DIR_DESTINO."otros/".";";
			$a_grabar.="fichero=".$fichero.";";
			$a_grabar.="destino=".$destino.";";
			$a_grabar.="tienda=".$tienda.";";
			$a_grabar.="caja=".$caja.";";
			$a_grabar.="file_descarga=".$file_descarga.";";
			$a_grabar.="file_control=".$DIR_CONTROL.".control.".$filename_destino.";";
			$a_grabar.="file_porc=".$DIR_CONTROL.".porc.".$filename_destino.";";

			file_put_contents($file_datos_descarga, $a_grabar);

			echo "Iniciando descarga del fichero $fichero...<br>";  flush(); @ob_flush();
			$f_origen=fopen($fichero,"r");
			echo "Preparando fichero destino $destino...<br>";  flush(); @ob_flush();
			if (!$f_origen) { echo "Imposible abrir origen $fichero..."; exit; }
			$f_destino=fopen($destino,"w");
			if (!$f_destino) { echo "Imposible abrir destino $destino..."; exit; }
			$c_actual=0;
			$size_total=filesize($fichero);
			while(!feof($f_origen)) {
				$buff=fread($f_origen, 10240);
				$c_actual+=strlen($buff);
				$porc=$c_actual*100/$size_total;
				echo "Copiados $c_actual bytes de $size_total...<br>";  flush(); @ob_flush();
				echo $porc; flush(); @ob_flush();
				fwrite($f_destino, $buff);
			}
			fclose($f_origen);
			fclose($f_destino);
			
			exit;			
	
		case "listado_remoto":
			//Crea_Tunel($tienda, $caja, $pais);
			$DIR_TUNNEL="/tmp/tunnel_".$tienda."/";
			$lista_files=glob($DIR_TUNNEL.$dir_origen."/*",GLOB_BRACE);
			if (empty($lista_files)) {
				$dir_origen=str_replace("usr/local/n2a/var", "pesados/n2a.var/", $dir_origen);
				$lista_files=glob($DIR_TUNNEL.$dir_origen."/*",GLOB_BRACE);
			}
			//echo "$DIR_TUNNEL/$dir_origen/*";
			if (empty($lista_files)) {
				echo "NO SE HAN ENCONTRADO RESULTADOS...";
			}
			else {

				usort($lista_files,"sort_by_mtime");
				Crea_Lista($lista_files, $titulo, false, $dir_origen);
				echo "<span id='dir_origen' style='display:none'>".$dir_origen."</span>";
				echo "<span id='dir_destino' style='display:none'>".$dir_destino."</span>";
			}
			exit;
					
		case "listado_local":
			$patron=$tienda."_*";
			$res=glob($DIR_BBDD.$patron,GLOB_BRACE);
			usort($res,"sort_by_mtime");
			if (!empty($res)) Crea_Lista($res,"BASES DE DATOS DESCARGADAS", true, $DIR_BBDD);

			$res=glob($DIR_VENTAS.$patron,GLOB_BRACE);
			usort($res,"sort_by_mtime");
			if (!empty($res)) Crea_Lista($res,"COMUNICACIONES DESCARGADAS", true, $DIR_VENTAS);

			$res=glob($DIR_LOGS.$patron,GLOB_BRACE);
			usort($res,"sort_by_mtime");
			if (!empty($res)) Crea_Lista($res,"LOGS DESCARGADOS</h3>", true, $DIR_LOGS);

			$res=glob($DIR_OTROS.$patron,GLOB_BRACE);
			usort($res,"sort_by_mtime");
			if (!empty($res)) Crea_Lista($res,"OTROS FICHEROS DESCARGADOS", true, $DIR_OTROS);

			exit;

		case "fin_descarga":
			echo (file_exists($file_descarga)?0:1);
			exit;

		case "descarga":
			$filename_destino=basename($destino);
			
			$a_grabar="DIR_DESTINO=".$DIR_DESTINO.";";
			$a_grabar.="DIR_CONTROL=".$DIR_CONTROL.";";
			$a_grabar.="DIR_BBDD=".$DIR_DESTINO.";";
			$a_grabar.="DIR_VENTAS=".$DIR_DESTINO."Ventas_GN/".";";
			$a_grabar.="DIR_LOGS=".$DIR_DESTINO."logs/".";";
			$a_grabar.="DIR_OTROS=".$DIR_DESTINO."otros/".";";
			$a_grabar.="fichero=".$fichero.";";
			$a_grabar.="destino=".$destino.";";
			$a_grabar.="tienda=".$tienda.";";
			$a_grabar.="caja=".$caja.";";
			$a_grabar.="file_descarga=".$file_descarga.";";
			$a_grabar.="file_control=".$DIR_CONTROL.".control.".$filename_destino.";";
			$a_grabar.="file_porc=".$DIR_CONTROL.".porc.".$filename_destino.";";

			file_put_contents($file_datos_descarga, $a_grabar);

			$URL_IFRAME="http://".$_SERVER["SERVER_ADDR"].":8085/?comandoTerminal=download_file_tunel&file_datos_descarga=".$file_datos_descarga;
			require_once("/home/soporteweb/Resources/library/jquery.php");
			echo "<iframe id='i_descarga' src='".$URL_IFRAME."' style='display:block; border:0; width:450px;'></iframe>";
			exit;
			
		case "cancela_descarga":
			unlink($file_control);
			unlink($file_porc);
			unlink($file_datos_descarga);
		//	file_put_contents($file_porc,"0");
			exit;

		case "porc_descarga":																						
			if (file_exists($file_porc)) {
				if (file_exists($file_control)) {
					$tmp=array_pop(file($file_porc));
					echo "downloading;".$tmp;
				}
				else {
					echo "stopped;;0;;";
					unlink($file_porc);
					unlink($file_datos_descarga);
				}
			}
			else
				echo "no_exist;;0;;";
			exit;

		case "crea_tunel":
			echo "<p>Tunel...<br>
				<ul>
					<li>Tienda: $tienda</li>
					<li>Caja: $caja</li>
					<li>Pais: $pais</li>
				</ul></p>
			";
			$res=shell_exec('sudo bash -vx /usr/bin/tda_tunnel '.$tienda.' '.$caja.' '.$pais." | tr -d '\n';");
			echo $res;
			if (!empty($res)) {
				if (file_exists($res."/root/param.dat")) {
					echo " <b>OK</b>";
					echo "<span id='dir_tunel' style='display:block'>".$res."</span>";
				} else 
					echo "<b style='color:red'>ERROR!!</b>";
			}
			exit;

		default:
			exit;
	}
}

$perm_Base_Datos=$perm_Comunicaciones=$perm_Logs=$perm_Capturador=1;
//if ($_SERVER['REMOTE_ADDR'] == "10.208.185.5") { print_r($_SESSION); $perm_Base_Datos=$perm_Comunicaciones=$perm_Logs=0; }
if ($_SESSION["nombre_grupo"] == "APPs") {
	$perm_Base_Datos=$perm_Comunicaciones=$perm_Logs=0;
}
?>
<head>
	<link rel="stylesheet" type="text/css" href="/Resources/css/tabla2.css">
	<link rel="stylesheet" type="text/css" href="/Resources/css/w3.css">
</head>

<style>
	#div_descargas { background-color:white; border:1px solid black;  }
	#div_descargas { font-family: sans-serif; font-size: 10px;  }
	#over{ text-align: center; }
	#over img { display: inline-block; }
	.tabla2 > * { font-size: 12px; }

	.informacion { border:1px solid black; background-color: lightskyblue; font-size: 1em;}
	.borde_redondo { border-radius: 3px; }
	#resultado { border:1px solid green; width:99%; height:600px; overflow:scroll; padding:2px; }
	#bbdd_local { border:1px solid blue; width:100%; height:600px }
	#opciones {  border:1px solid black; width:100%; }
	#t_general { width:100%;border:1px solid black;vertical-align:top; }
	#t_general a { cursor:pointer; }
		#t_general a:hover { background-color: lightgray; font-weight: bold;}
	#t_opciones { width:100%; height:220px !important; font-size: 12px; }
	#ayuda { height:220px !important; width:100%; background-color: lightgray;}
	.disable { color: gray; font-style: italic; }
	.enable { color: blue; font-weight: bold;}
</style>

<div id="div_descargas" class="borde_redondo">
<div id="info" style="display:none"></div>
<table id="t_general">
	<tr>
		<td colspan="2">
			<div id="opciones" class="borde_redondo">
			<table id="t_opciones" class="border_redondo">
			<tr>
				<td width="25%" valign="top">
					<?php
						echo '
							<b>BASES DE DATOS</b>
							<ul>
								<li><a perm="'.$perm_Base_Datos.'" class="accion '.(!$perm_Base_Datos?"disable":"enable").'" dir_origen="/usr/local/n2a/var/data/database/backup/endDay/" dir_destino="'.$DIR_BBDD.'">Fin de dia (tras comunicaciones)</a></li>
								<li><a perm="'.$perm_Base_Datos.'" class="accion '.(!$perm_Base_Datos?"disable":"enable").'" dir_origen="/usr/local/n2a/var/data/database/backup/beforeCommunication/" dir_destino="'.$DIR_BBDD.'">Fin de dia (antes comunicaciones)</a></li>
								<li><a perm="'.$perm_Base_Datos.'" class="accion '.(!$perm_Base_Datos?"disable":"enable").'" dir_origen="/usr/local/n2a/var/data/database/backup/closeWorkstation/" dir_destino="'.$DIR_BBDD.'">Tras el cierre caja</a></li>
							</ul>';
						echo '
							<b>COMUNICACIONES:</b>
							<ul>
								<li><a perm="'.$perm_Comunicaciones.'" class="accion '.(!$perm_Comunicaciones?"disable":"enable").'" dir_origen="/usr/local/n2a/var/data/communications/historyIn/" dir_destino="'.$DIR_VENTAS.'">Recibidos</a></li>
								<li><a perm="'.$perm_Comunicaciones.'" class="accion '.(!$perm_Comunicaciones?"disable":"enable").'" dir_origen="/usr/local/n2a/var/data/communications/historyOut/" dir_destino="'.$DIR_VENTAS.'">Emitidos</a></li>
							</ul>';
					?>
				</td>
				<td width="25%" valign="top">
					<?php
						echo '
							<b>LOGS DE LA TPV:</b>
							<ul>
								<li><a perm="'.$perm_Logs.'" class="accion  '.(!$perm_Logs?"disable":"enable").'" dir_origen="/var/log/" dir_destino="'.$DIR_LOGS.'">Logs sistema</a></li>
								<li><a perm="'.$perm_Logs.'" class="accion  '.(!$perm_Logs?"disable":"enable").'" dir_origen="/usr/local/n2a/var/log/" dir_destino="'.$DIR_LOGS.'">Logs aplicacion</a></li>
								<li>
									<a perm="'.$perm_Capturador.'" class="accion  '.(!$perm_Capturador?"disable":"enable").'" dir_origen="/usr/share/guc/log/" dir_destino="'.$DIR_LOGS.'" title="Permite descargar logs de uso de los capturadores, así como su conexion">Logs del capturador</a>
								</li>

							</ul>';
						echo '
							<b>FICHEROS CAPTURADOR:</b>
							<ul>
								<li><a perm="'.$perm_Capturador.'" class="accion  '.(!$perm_Capturador?"disable":"enable").'" dir_origen="/usr/local/n2a/var/data/devices/dataCapturer/history/" dir_destino="'.$DIR_OTROS.'">Historico (<i>history</i>)</a></li>
								<li><a perm="'.$perm_Capturador.'" class="accion  '.(!$perm_Capturador?"disable":"enable").'" dir_origen="/usr/local/n2a/var/data/devices/dataCapturer/in/" dir_destino="'.$DIR_OTROS.'">Entrada a TPV (<i>in</i>)</a></li>
								<li><a perm="'.$perm_Capturador.'" class="accion  '.(!$perm_Capturador?"disable":"enable").'" dir_origen="/usr/local/n2a/var/data/devices/dataCapturer/out/" dir_destino="'.$DIR_OTROS.'">Salida al capturador (<i>out</i>)</a></li>
								<li><a perm="'.$perm_Capturador.'" class="accion  '.(!$perm_Capturador?"disable":"enable").'" dir_origen="/usr/local/n2a/var/data/devices/dataCapturer/transformed/" dir_destino="'.$DIR_OTROS.'">Transformados (<i>transformed</i>)</a></li>
							</ul>';
					?>
				</td>
				<td width="25%" valign="top"></td>
				<td width="25%" valign="top">
					<div id="ayuda" class="borde_redondo"></div>
				</td>
			</tr>
			</table>
			</div>
		</td>
	</tr>
	<tr><td>
		<span id='file_datos_descarga' style="display:block"></span>
		<span id='file_descarga' style="display:none"></span>
		<div id='s_fin_descarga' style="display:none"></div>
		<span id="info_tunel">Estado de la descarga: </span>
		</td></tr>
	<tr>
		<td width="50%">
			<div id="resultado" class="borde_redondo"></div>
		</td>
		</td>
		<td style="vertical-align:top;">
			<div id="bbdd_local" class="borde_redondo"></div>
		</td>
	</tr>
</table>
</div>

<?php
//if (empty($Tienda)) {
	$file_datos_descarga=tempnam("/tmp","file_datos_descarga.");

//}
if (empty($con_tda)) {
	require_once("/home/soporteweb/Resources/library/jquery.php");
	require_once("/home/soporteweb/Resources/library/sweetalert.php");
}

?>
<script type="text/javascript">
	var file_datos_descarga='<?php echo @$file_datos_descarga; ?>';  
	var local_url="/Resources/Estado_Monitorizacion/Conexion_Tienda/Acciones_PHP/menu_descarga_ficheros.php";
	var tienda='<?php echo sprintf("%05d",(empty($Tienda)?$_GET["tienda"]:$Tienda)); ?>';
	var caja='<?php echo (empty($Caja)?1:$Caja); ?>';
	var pais='<?php echo (empty($Pais)?"ESP":$Pais); ?>';
	var datos_tienda="&tienda="+tienda+"&caja="+caja+"&pais="+pais;
	var ya_descargando=false;
	var file="";
	var gif_espera="<div id='over'><img src='/img/Loading-data.gif'/></div>";
	var fin_de_descarga=null;
	var file_descarga=null;
	var revisa_estado=null;

	var texto_pulse_abajo='';
	var texto_pulse_derecha="<p>Pulse en un fichero descargado para descargar a su PC. &#9658</p>";

	var porcentaje=0;
	var interv_porc=null;

	function actu_porc(para_porcentaje) {
		console.log(interv_porc,porcentaje);
		if (para_porcentaje) clearInterval(interv_porc);
		function exe_actu_porc() {
			var url2=local_url+'?opcion=porc_descarga&file_datos_descarga='+file_datos_descarga;
			$.get(url2,function(res) {
				res=res.replace("\n","");
				estado=res.split(";")[0];
				bytes=res.split(";")[1];
				porcentaje=res.split(";")[2];
				rate=res.split(";")[3];
				eta=res.split(";")[4];
				console.log(interv_porc, res, porcentaje);

				if (estado == "stopped") {
					clearInterval(interv_porc);
					swal({ title:"Descarga completada...", html:elem1.innerHTML, timer: 3000 });
				} else { 
					elem=document.getElementById("porc2");
					elem1=document.getElementById("valor");
					elem.style.width = porcentaje;
					elem1.innerHTML = "<i style='font-size:12px'>"+bytes+" bytes ("+porcentaje+") - Rate trasnfer: "+rate+", ETA: "+eta+"</i>";
					if (!para_porcentaje) interv_porc=setTimeout(function(){ exe_actu_porc(); } , 1000 );
				}
			});
		}
		exe_actu_porc();
	}

	function downloadURL(url) {
		var hiddenIFrameID = 'hiddenDownloader',
			iframe = document.getElementById(hiddenIFrameID);
		if (iframe === null) {
			iframe = document.createElement('iframe');
			iframe.id = hiddenIFrameID;
			iframe.style.display = 'none';
			document.body.appendChild(iframe);
		}
		iframe.src = "/Resources/tools/download_from_server.php?file="+url;
	};

	function descargar() {
		var dir_destino=$("#dir_destino").html();
		var dir_origen=$("#dir_origen").html();
		var tunel=$("#dir_tunel").html();
		var destino=dir_destino+"/"+tienda+"_"+file;
		var fichero=tunel+"/"+$("#dir_origen").html()+file;
		file_descarga=$("#dir_destino").html()+tienda+".download_tmp"; fin_de_descarga=0;
		var texto=local_url+"?opcion=descarga&tienda="+tienda+"&caja=1&fichero="+fichero+"&destino="+destino+"&file_descarga="+file_descarga+"&file_datos_descarga="+file_datos_descarga;
		$("#otro2").attr("src",texto);
		actu_porc(false);
	}

	function Ayuda() {
//	Con esta opci&oacute;n podr&aacute; descargar cualquier fichero desde la TPV al servidor
//	
		swal({
			title: "",
			html: true,
			text: "<iframe style='border:0; width:500;' src='"+local_url+"?opcion=ayuda1'></iframe>",
			imageUrl: "/img/interrogacion.gif",
			confirmButtonText: "Close"
		},function () {
			console.log($("#check_ayuda"));
		});
	}

	function dialog_descarga_desde_tpv(x) {
		file = x.find("#i_file_name").html(); size = x.find("#i_file_size").html(); date = x.find("#i_file_date").html();

		swal({
				title: "Descargar al server",
				html:
				 	'<p>Desea descargar el siguiente fichero al servidor?</p>' +
				 	'<p><b>'+file+'</b>?</p><p style="font-size:80%"><b>Size:</b> '+size+'<br><b>Fecha:</b> '+date+'</p>',
				showCancelButton: true,
				allowEscapeKey:false,
				confirmButtonText: "Descargar",
		}).then((result) => {
			if (result.value) {
				swal({
					title:"Descarga",
					html:
						'<i>Espere por favor, descargando fichero al servidor</i>'+
						'<p style="font-size:12px;">Fichero: '+file+'</p>'+
						'<div id="cont_porc2" class="w3-light-grey w3-round-large">'+
							'<div id="porc2" class="w3-green w3-round-large w3-center" style="height:21; width:0"></div>'+
							'<span id="valor" style="float:left; position:absoluto; top:0"></span>'+
						'</div>'+
						'<iframe id="otro2" style="display:none; width:450px; height:200px; border:1;"></iframe>',
					showConfirmButton: false,
					showCancelButton: true,
					cancelButtonText: "Cancelar",
					onOpen: () => {
						descargar();
					}
				}).then((result) => {
					 if (result.dismiss === 'cancel') {
					 	swal({
					 		title: "Cancelando desgarga...",
					 		timer: 3000,
					 		onOpen: () => {
								swal.showLoading();
								actu_porc(true); // Paramos porcentaje
								$.get(local_url+"?opcion=cancela_descarga&file_datos_descarga="+file_datos_descarga);
							}
					 	})
					 }
				})
      	}
		})	
	}

	function refresca_ficheros_locales() {
		$("#bbdd_local").load(local_url+"?opcion=listado_local&tienda="+tienda,function () {
			$("#bbdd_local .row_file").on("click",function () {
				x=$(this);
				var file = x.find("#i_file_name").html();
				var size = x.find("#i_file_size").html();
				var date = x.find("#i_file_date").html();
				var link_descarga = x.attr("link_descarga");
				swal({
					title: "Descargar al PC",
					html:"<p>Desea descargar el siguiente fichero a su PC?</p><p><b>"+file+"</b>?</p><p style='font-size:80%'><b>Size:</b> "+size+"<br><b>Fecha:</b> "+date+"</p>",
					showCancelButton: true,
					confirmButtonText: "Download"
				}).then((result) => {
					if (result.value) {
						downloadURL("/tmp/BBDD_Tiendas/"+link_descarga+"/"+file);
					}
				});
			});
		});
		setTimeout(function(){ refresca_ficheros_locales(); } , 5000 );
	}

	function refresca_conexion_tunel() {
		$("#ayuda").load(local_url+"?opcion=crea_tunel"+datos_tienda);
		setTimeout(function(){ refresca_conexion_tunel(); } , 5000 );
	}
	
	refresca_conexion_tunel();
	refresca_ficheros_locales();
		
	$(".accion").on("click",function () {
		var perm = $(this).attr("perm");
		if (perm == 0) {
			swal("NO POSIBLE!","No tiene permisos para realizar esta accion","warning");
		} else {
			var opcion="listado_remoto";
			var dir_origen=$(this).attr("dir_origen");
			var dir_destino=$(this).attr("dir_destino");
			var url=local_url+"?opcion="+opcion+"&tienda="+tienda+"&dir_origen="+dir_origen+"&dir_destino="+dir_destino+"&titulo="+encodeURIComponent($(this).html());
			$("#info1").show();
			$("#resultado").html(gif_espera).load(url,function () {
				$("#resultado .row_file").on("click",function () {
					dialog_descarga_desde_tpv($(this) );
				});
			});
		}
	});

	$("#informacion").html("<p>&#9664 Pulse en una opci&oacute;n elegida en la parte izquierda.</p>");
	
//	Ayuda();
</script>


