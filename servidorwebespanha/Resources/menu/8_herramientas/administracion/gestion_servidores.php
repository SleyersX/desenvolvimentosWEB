<?php

/*
+------------------+-------------+------+-----+---------+-------+
| Field            | Type        | Null | Key | Default | Extra |
+------------------+-------------+------+-----+---------+-------+
| ip_server        | varchar(20) | NO   | PRI | NULL    |       |
| name_server      | varchar(20) | YES  |     | NULL    |       |
| pais             | varchar(3)  | YES  |     | NULL    |       |
| ldap_host        | varchar(20) | YES  |     | NULL    |       |
| DOMINIO          | varchar(10) | YES  |     | NULL    |       |
| base_dn          | varchar(20) | YES  |     | NULL    |       |
| ftp_concentrador | varchar(20) | YES  |     | NULL    |       |
| ftp_user_name    | varchar(30) | YES  |     | NULL    |       |
| ftp_user_pass    | varchar(30) | YES  |     | NULL    |       |
| Idioma           | varchar(3)  | YES  |     | NULL    |       |
+------------------+-------------+------+-----+---------+-------+
*/

set_time_limit(0); 
ob_implicit_flush(true);
ob_end_flush();

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!empty($_GET["guardar_datos"])) {
	$c_query="";
	foreach($_POST as $k => $d) { 
		if ($k!="c_ip_server") {
			$c_query.=",";
		}
		$c_query.="'".$d."'";
	}
	var_dump($c_query);
	$cmd='sudo mysql soporteremotoweb -e "REPLACE info_servers VALUES ('.$c_query.')" -u root -h 10.208.162.6';
	exec($cmd);
	echo "Registro creado/modificado correctamente.";
	exit;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!empty($_GET["Borrar"])) {
	$A_borrar=$_GET["c_ip_server"];
	$cmd='sudo mysql soporteremotoweb -e "DELETE FROM info_servers WHERE ip_server = \''.$A_borrar.'\'" -u root -h 10.208.162.6';
	exec($cmd);
	echo "Registro borrado correctamente.";
	exit;
}

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once($DOCUMENT_ROOT.$DIR_RAIZ."/styles_js/head_1.php");
//require("./comun_administracion.php");

if ($Pais != "ESP")
	die("Solo permitido en España!!");

if (isset($Exportar)) {
	$F_INFO="info_servers.sql.gz";

	echo "<pre>";
	echo "PREPARANDO COPIA DE TABLAS:\n";
		exec("sudo mysqldump soporteremotoweb info_servers | gzip > /tmp/".$F_INFO);

	echo "COPIANDO A SERVIDORES\n";
	$Servidores= myQUERY("SELECT * from info_servers where pais not in ('ESP','CHI') order by name_server");
	foreach ($Servidores as $d) {
		echo "COPIANDO A SERVIDOR ".$d[1]." (IP: ".$d[0].")\n";
			exec("sudo scp -q /tmp/".$F_INFO." soporte@".$d[0].":/tmp/");
		echo "ACTUALIZANDO TABLAS EN REMOTO:\n";
			exec('sudo ssh -lsoporte '.$d[0].' "zcat /tmp/'.$F_INFO.' | mysql soporteremotoweb -f');
	}
	echo "PROCESO OK!!\n";
	echo "<pre>";
	exit;
}

$Servidores= myQUERY("SELECT * from info_servers order by name_server");

if (count($Servidores)>0) {
	$l_servers="";
	foreach ($Servidores as $reg) {
		$l_servers.="[";
		foreach ($reg as $k => $d) { $l_servers.="'".$d."',"; }
		$l_servers.="],";
	}
//	$l_servers.='</table>';
} else
	$l_servers=Alert("error", "No se han encontrado registros...");
?>

<body style="background-color:white">

<a href="./gestion.php">Inicio</a>

<h1>Gestion de servidores</h1>

<div style="border:1px solid black; padding:1em">
	<div id="botones">
		<input type="button" id="Nuevo" value="Nuevo" />
		<input type="button" id="Modificar" value="Modificar"/>
		<input type="button" id="Borrar" value="Borrar" />
		<input type="button" id="Exportar" value="Exportar..."/>
	</div>
	<p><iframe id="proceso" frameborder="0" width="100%" height="200" style="display:none"></iframe></p>
	<hr>
	<div id="div_lista_servers">
		<center><img src="/img/wait.gif"/></center>
	</div>

</div>
	
<div id="captura_datos" style="display:none">
	<table>
		<tr><td>ip_server:</td><td><input type="text" name="c_ip_server" value="Introduzca un valor"/></td></tr>
		<tr><td>name_server:</td><td><input type="text" name="c_name_server" value="Introduzca un valor"/></td></tr>
		<tr><td>pais:</td><td><input type="text" name="c_pais" value="Introduzca un valor"/></td></tr>
		<tr><td>ldap_host:</td><td><input type="text" name="c_ldap_host" value="Introduzca un valor"/></td></tr>
		<tr><td>DOMINIO:</td><td><input type="text" name="c_DOMINIO" value="Introduzca un valor"/></td></tr>
		<tr><td>base_dn:</td><td><input type="text" name="c_base_dn" value="Introduzca un valor"/></td></tr>
		<tr><td>ftp_concentrador:</td><td><input type="text" name="c_ftp_concentrador" value="Introduzca un valor"/></td></tr>
		<tr><td>ftp_user_name:</td><td><input type="text" name="c_ftp_user_name" value="Introduzca un valor"/></td></tr>
		<tr><td>ftp_user_pass:</td><td><input type="text" name="c_ftp_user_pass" value="Introduzca un valor"/></td></tr>
		<tr><td>Idioma:</td><td><input type="text" name="c_Idioma" value="Introduzca un valor"/></td></tr>
		<div id="resultado"></div>
	</table>
</div>
	
</body>

<script>
	var data1;
	var lista_servers;
	var selection;
	var local_url="<?php echo basename($SCRIPT_FILENAME); ?>";
	var cabeceras=new Array ("ip_server","name_server","pais","ldap_host","DOMINIO","base_dn","ftp_concentrador","ftp_user_name","ftp_user_pass","Idioma");
	var tama_cabe=9;

	$("#captura_datos" ).dialog({
		autoOpen: false, resizable: false, height: "auto", width: "auto", modal: true,
		buttons: {
			"Aceptar": function() {
				var parametros={};
				for (v=0;v<=tama_cabe;v++) {
					var campo="c_"+cabeceras[v];
					parametros[v]={ campo : $("input[name='c_"+cabeceras[v]+"']").prop("value")};
				}
				console.log(parametros);
/*					,
					"c_name_server": $("input[name='c_name_server']").prop("value"),
					"c_pais": $("input[name='c_pais']").prop("value"),
					"c_ldap_host": $("input[name='c_ldap_host']").prop("value"),
					"c_DOMINIO": $("input[name='c_DOMINIO']").prop("value"),
					"c_base_dn": $("input[name='c_base_dn']").prop("value"),
					"c_ftp_concentrador": $("input[name='c_ftp_concentrador']").prop("value"),
					"c_ftp_user_name": $("input[name='c_ftp_user_name']").prop("value"),
					"c_ftp_user_pass": $("input[name='c_ftp_user_pass']").prop("value"),
					"c_Idioma": $("input[name='c_Idioma']").prop("value"),
				};*/

				$("#resultado").load(local_url+"?guardar_datos=yes", parametros, function () {
					alert("Operacion realizada");
				});

				$( this ).dialog( "close" );
				location.reload();
				},
			"Cancelar": function() {
				$( this ).dialog( "close" );
			}
		}
	});

	function PonDatos(e) {
		$("#Modificar").prop('disabled', false);
		$("#Borrar").prop('disabled', false);
		selection = lista_servers.getSelection();
		for (v=0;v<=tama_cabe;v++) {
			$("input[name='c_"+cabeceras[v]+"']").prop("value",data1.getValue(selection[0].row,v));
		}
	}

	function drawTables() {
		data1 = new google.visualization.DataTable();
		for (v=0;v<=tama_cabe;v++)
			data1.addColumn('string',cabeceras[v]);
		data1.addRows([ <?php echo $l_servers; ?> ]);
		lista_servers = new google.visualization.Table(document.getElementById('div_lista_servers'));
		lista_servers.draw(data1, { title: 'BASES DE DATOS', width:"100%" });
		google.visualization.events.addListener(lista_servers, 'select', PonDatos );
	}
	
	

$(document).ready(function () {
	$("#Modificar").prop('disabled', true);
	$("#Borrar").prop('disabled', true);
	
	$("#Modificar").on("click",function () {
		if (!selection) alert("Debe elegir un elemento a modificar!!");
		else $("#captura_datos").dialog("open");
	});

	$("#Borrar").on("click",function () {
		var servidor=$("input[name='c_ip_server']").prop("value");
		if (confirm("Desea eliminar este registro: "+servidor)) {
			$.get(local_url, { "Borrar":"yes", "c_ip_server":servidor }, function () {
					alert("Operacion realizada");
			});
			location.reload();
		}
	});
	
	$("#Nuevo").on("click",function () {
		$("#captura_datos").dialog("open");
	});


	$("#Exportar").on("click",function () {
		if (confirm("Desea exportar estos datos a todos los servidores?"))
			$("#proceso").show();
			$("#proceso").prop('src', local_url+"?Exportar=yes");
	});

	google.charts.setOnLoadCallback(drawTables);		
	
});

</script>
