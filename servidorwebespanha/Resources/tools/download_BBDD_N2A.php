<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

<script type="text/javascript">
 
    function getTimeAJAX() {
 
        //GUARDAMOS EN UNA VARIABLE EL RESULTADO DE LA CONSULTA AJAX    
 
        var time = $.ajax({
 
            url: 'time.php', //indicamos la ruta donde se genera la hora
                dataType: 'text',//indicamos que es de tipo texto plano
                async: false     //ponemos el parámetro asyn a falso
        }).responseText;
 
        //actualizamos el div que nos mostrará la hora actual
        document.getElementById("myWatch").innerHTML = "La fecha que hemos obtenido de time.php vía AJAX es: "+time;
    }
    
	function lanzacomando() {
		var res = $.ajax({
			url: 'p1.php', //indicamos la ruta donde se genera la hora
                dataType: 'text',//indicamos que es de tipo texto plano
                async: true     //ponemos el parámetro asyn a falso
        }).responseText;
	}
 
    //con esta funcion llamamos a la función getTimeAJAX cada segundo para actualizar el div que mostrará la hora
	lanzacomando();
	getTimeAJAX();
	setInterval(getTimeAJAX,1000);
 
</script>
 
<html>
    <body>
 
    <div id='myWatch'></div>
    </body>
</html>
El código se encuentra completamente comentado, Este ejemplo es un poco “tonto” ya que si lo que queremos es obtener la fecha actual, hay v

<?php
/*
<script language="javascript">
	function ACTIVA_OPCION(Etiqueta, Valor) {
		INPUT_HIDDEN(Etiqueta, Valor, 'myForm');
		INPUT_HIDDEN('myAcciones','<?php echo $myAcciones; ?>','myForm'); SUBMIT('myForm');
	}
</script>


$Modo_Lite=true;

require_once("/home/soporteweb/config.php");

switch ($TipoBBDD) {
	case "Actual":
		echo "Descargando BBDD actual de la tienda $Tienda\n";
// 		echo shell_exec('sudo ssh -p10001 -lroot -i /home/MULTI/id_rsa.ESP -t 10.90.133.7 "mysqldump n2a | pv | gzip > dump.sql.gz"');
		flush(); ob_flush();
		break;
	default:
		echo "No existe accion.\n"; break;
}
	$DIR="/usr/local/n2a/var/data/database/backup/endDay/";
	if (@$Fichero_BBDD) {
		$tmp_file = $DIR_TMP.$Tienda."-".$Caja."-".basename($Fichero_BBDD);
		$local_file = $DOCUMENT_ROOT.$tmp_file;

		if (basename($Fichero_BBDD) === "Actual.sql.gz") {
			$con_tda->cmdExec("mysqldump n2a | gzip > $Fichero_BBDD");
		}
		$con_tda->receiveFile($Fichero_BBDD, $local_file);
		flush(); ob_flush();
	}

	echo Establece_Ayuda("../ayuda/ayuda_descargar_bbdd.html");

	$Result=$con_tda->cmdExec("cd $DIR; ls -lHta b*gz | awk '{printf \"%s#%dKB\\n\",\$9,\$5/1024}';");
	$Lista_Ficheros=explode("\n",$Result);
	$Lista_Ficheros[]="Actual.sql.gz#N/A";
	rsort($Lista_Ficheros);

	$Res="<table id='lista_bbdd' class='lista_ficheros' style='text-decoration:none;'>   <thead><tr><th>Fichero</th><th>Tamanio</th><th>Opciones</th></tr></thead>   <tbody>";

	foreach ($Lista_Ficheros as $k => $d) {
		if (!empty($d)) {
			list($File, $Size) = explode("#",$d);
			$tmp_file=$DIR_TMP.$Tienda."-".$Caja."-".$File;
			$local_file=$DOCUMENT_ROOT.$tmp_file;
			$OnClick = "INPUT_HIDDEN('Fichero_BBDD','".$DIR.$File."','myForm'); INPUT_HIDDEN('myAcciones','$myAcciones','myForm'); SUBMIT('myForm');";
			$Res.="<tr><td id='td_Fichero'>".basename($File)."</td>";
			$Res.="<td id='td_Tamanio'>".$Size."</td>";
			$Res.='<td id="td_Opciones">';
			if (file_exists($local_file)) $Icono="recargar.png"; else $Icono="download_to_server.gif";
			$Res.='<a class="button b_download" onclick="'.$OnClick.'" title="Descargar fichero al servidor"><img src="'.$DIR_IMAGE.'/'.$Icono.'"/></a>';
			if (file_exists($local_file)) {
				$Res.='<a class="button b_download" href="'.$tmp_file.'" title="Descargar a PC" target="_blank"><img src="'.$DIR_IMAGE.'/download_to_pc.gif" /></a>';
			}
		}
		$Res.="</td>";
		$Res.="</tr>";
	}
}
$Res.="</table>";
echo FIELDSET_DATOS("DESCARGA DE BBDDs ".$Pais,$Res);*/
?>
