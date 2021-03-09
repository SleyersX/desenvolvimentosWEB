<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once($DOCUMENT_ROOT.$DIR_TOOLS.'tools.php');
require_once($DOCUMENT_ROOT.$DIR_TOOLS.'comun.php');
require_once($DOCUMENT_ROOT.$DIR_TOOLS."head_1.php");

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }

echo '<div id="Aviso1" class="Aviso"></div>';
echo '<br>';
echo '<form id="myForm" action="'.$Pag_Actual.'" method="post">';
echo '<fieldset>';

if (isset($busca_ip)) {
	echo "COMPROBANDO CAJAS TRAS LA DIRECCION IP $busca_ip...<br><br>";
	for ($caja=1; $caja<=11; $caja++) {
		_ECHO("Checking 10.10.10.$caja : ");
		$Comando='sudo timeout 5s ssh -p'.(10000+$caja).' -lroot -i /root/id_rsa -o StrictHostKeyChecking=no -o ConnectTimeout=3 '.$busca_ip.'  "echo 1" 2>/dev/null  || echo 0';
		$Datos=shell_exec($Comando);
		if ($Datos == 1) {
			_ECHO("FOUND! "); 
			$URL=$PHP_CONECTAR."?host=$busca_ip&port=".(10000+$caja)."&Pais=$Pais";
			_ECHO('<button class="b_caja" onclick="javascript:window.open(\''.$URL.'\',\'_blank\');">Conectar a CAJA '.$caja.'</button><br>');
		} else { _ECHO("NOT FOUND!<br>"); }
	}
	exit;
	
} else {
	if (empty($Tienda)) {
		echo '
			<script>
				$("#Aviso1").load("/Resources/textos/tienda_no_existe.php");
			</script>';
		die();
	}

	if (@$Centro=="SEDE") { $Pais="XXX"; $Table="Checks".$Pais; }

	$Input_Cambiar_IP="CONCAT('<input type=button value=\"',t.IP,'\" title=\"Pulse aqu&iacute; para cambiar temporalmente la IP de esta tienda\" id=\"Cambiar_IP\" />')";

	$Result=myQUERY("
		select
			CAST(t.Numerotienda AS UNSIGNED)
			, t.Centro
			, t.Tipo
			, t.Subtipo
			, t.Direccion
			, t.Poblacion
			, t.Provincia
			, t.Telefono
			, t.IP
			, t.Frescos
			, IFNULL(c.NTPVS,'N/A')
		from tiendas t
			left join $Table c ON t.numerotienda=c.tienda and c.caja=1
		where t.numerotienda=$Tienda and t.centro like '%".@$Centro."%' and pais in ('$Pais')");

	$Boton_Actualizar = '<input class="button" type=button value="Actualizar" id="id_b_Actualizar"/>';
	$Boton_Cerrar= '<button class="button" type="button" onclick="javascript:window.close();">Cerrar</button>';
	$Estado=myQUERY("select CONCAT(last,': ',Comentario) from Accesos_Tiendas where tienda=$Tienda and pais='$Pais'");
	$Estado=$Estado[0][0];
	if (preg_match("/ERROR/",$Estado)) $Estado="<b style='font-size:12px; color:red'>$Estado</b>";
	else $Estado="<b style='font-size:12px; color:blue'>[$Estado]</b>";
	$Queries=array ("Tienda $Tienda </a> $Boton_Cerrar $Boton_Actualizar $Estado",
		array("TIENDA","CENTRO","TIPO","SUBTIPO","DIRECCION","LOCALIDAD","PROVINCIA","TELEFONO","IP","FRESCOS","N.TPVS"),
		$Result, NULL, "ALL", "");
	$datos_tienda="[ {label: 'Tienda'}, {label: 'Centro'}, {label: 'Tipo'},{label: 'Subtipo'},{label: 'Direccion'},
		{label: 'Localidad'}, {label: 'Provincia'}, {label: 'Telefono'}, {label: 'IP', id: 'i_ip'}, {label: 'Frescos'},
		{label: 'N.TPVs'} ],";
	$datos_tienda.="[ ['".$Result[0][0]."','".$Result[0][1]."','".$Result[0][2]."','".$Result[0][3]."',
	'".$Result[0][4]."','".$Result[0][5]."','".$Result[0][6]."','".$Result[0][7]."',
	'".$Result[0][8]."','".$Result[0][9]."','".$Result[0][10]."',]";
//	("TIENDA","CENTRO","TIPO","SUBTIPO","DIRECCION","LOCALIDAD","PROVINCIA","TELEFONO","IP","FRESCOS","N.TPVS"),
//		$Result

	echo '<center>';
	echo '<table>';
	echo '<tr><td>';
	Show_data2("Busqueda", $Queries, false);
	echo '</td>';


	$Result=myQUERY("set @Tienda=$Tienda;
		(select
			(select 'BALANZA') 'Elemento',
			(select count(*) from Elementos where  Elemento like 'balanza%' and conexion=1 and tienda=@Tienda) 'Conectados',
			(select count(*) from Elementos where  Elemento like 'balanza%' and tienda=@Tienda) 'Total')
		UNION
		(select
			'PC' '',
			(select count(*) '' from Elementos where Elemento like 'pc%' and conexion=1 and tienda=@Tienda),
			(select count(*) '' from Elementos where Elemento like 'pc%' and tienda=@Tienda))
		UNION
		(select
			'IMPRESORA' '',
			(select count(*) '' from Elementos where Elemento like 'impres%' and conexion=1 and tienda=@Tienda),
			(select count(*) '' from Elementos where Elemento like 'impres%' and tienda=@Tienda))
		");
// 		UNION
// 		(select
// 			'PRINT_SERVER' '',
// 			(select count(*) '' from Elementos where Elemento like 'print%' and conexion=1 and tienda=@Tienda),
// 			(select count(*) '' from Elementos where Elemento like 'print%' and tienda=@Tienda))

	$Queries=array ("Elementos de la tienda", array("Elemento","Conectados","Total"), $Result, NULL, "ALL", "");
	echo '<td>'; Show_data2("Elementos", $Queries, false); echo '</td>';
	echo '</tr>';

	echo '<tr><td colspan="2"><hr></td></tr>';
	echo '<tr><td colspan="2">';
	echo '<table><tr>';
	echo '<td><div id="datos_tienda" style="border:1px solid black;border-radius:5px; padding:1px;"></div></td>';
	echo '<td><div id="datos_elementos" style="border:1px solid black;border-radius:5px; padding:1px;"></div></td>';
	echo '</tr><tr>';
	echo '<td  colspan="2"><div id="lista_cajas" style="border:1px solid black;border-radius:5px; padding:1px;"></div></td>';
	echo '</tr></table>';

// 	global $Table, $DIR_IMAGE;
//	$total_cajas = myQUERY("select a.*, b.bios from $Table a join tmpHardware b on a.tienda=b.tienda and a.caja=b.caja where a.Tienda=$Tienda order by a.caja");
	$total_cajas = myQUERY("select a.*, IFNULL((select bios from tmpHardware b where a.tienda=b.tienda and a.caja=b.caja),'N/D') 'BIOS' from ChecksESP a where a.Tienda=$Tienda order by a.caja");
	if (count($total_cajas)<1)
		echo Alert("warning", myGetText("NO_INFO_TPVS"));
	else {
		$lista_cajas="";
		for ($caja=1; $caja <= count($total_cajas);  $caja++) {
			echo Datos_Caja_Conexion(NULL, $total_cajas[$caja-1]);
			@list($Tienda,$Caja,$Conexion,$Version,$Modelo,$Exec,$MSG,$RAM,$HDD,$dummy,$LAN,$NTPVS,$N_APAG,$DAT1,$DAT2,$DAT3,$DAT4,$DAT5,$LastM,$IP,$Temper,$HUB,$PINPAD,$ReleaseDate,$INV_HW_SW,$MySQL,$WSD,$SWD, $RAID, $CDMANAGER, $BIOS) = $total_cajas[$caja-1];
			$lista_cajas.="[".$Caja.",";
			$lista_cajas.="'".($Conexion?'SI':'NO')."',";
			$lista_cajas.="'$Version',";
			$lista_cajas.="'".($Exec?'SI':'NO')."',";
			$lista_cajas.="'".($MySQL?'SI':'NO')."',";
			$lista_cajas.="'".($WSD?'SI':'NO')."',";
			$lista_cajas.="'$Modelo',";
			$lista_cajas.="'$BIOS',";
			$lista_cajas.="'$RAM',";
			$lista_cajas.="".explode("%",$HDD)[0].",";
			$lista_cajas.="$Temper,";
			$lista_cajas.="'$LAN',";
			$lista_cajas.="'$N_APAG',";
			$lista_cajas.="],";
		}
	}

	echo '<tr><td colspan="2"><hr></td></tr>';
	$res=myQUERY("select * from HistoricoESP where tienda=$Tienda");
	echo '<tr><td>';
	Show_data2("Historico",
		array("COSAS QUE LE HAN OCURRIDO A ESTA TIENDA... (A&#209;O EN CURSO)", array("Tienda","Caja","Fecha","Comentarios"),
		"select Tienda, Caja, Fecha, Comentario from HistoricoESP where tienda=$Tienda AND DATE(Fecha) = DATE(NOW()) order by 3,2", NULL, "ESP", ""));
	echo '</td></tr>';

	echo '</table>';
	echo '</center>';
}

echo '<div id="dialogo_cambiar_ip" title="Cambiar IP de la tienda." style="display:none">
	<p>
		<label for="id_new_ip">Introduzca una direccion IP valida:</label>
		<input id="id_new_ip" name="tmp_new_ip" type=text value="" placeholder="Introduzca IP valida" title="Se necesita una IP" required pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$"/></p>
	<p><span id="Mensaje_exec1"></span></p>
	'.Alert("info","Recuerde que el cambio es temporal.<br>Desaparece en el siguiente gestor noche.").'
	</div>';

echo '<div id="dialog_actualizar" title="Actualizando datos" style="display:none" >
		<p><img src="/img/wait.gif"/></p>
		<p><span id="Mensaje_exec2"></span></p>
	</div>';

// echo '</div>';
echo '</fieldset>';
echo '</form>';
// 					Ejecuta_AJAX("<?php echo $_SERVER['REQUEST_URI']; &Cambiar_IP="+$("#id_new_ip").val(),"Mensaje_exec1","{}");
?>
<style>
	.sin_conexion {
		background-color: orangered;
	}
</style>

<script>

var Idioma="<?php echo $Idioma; ?>";
$("#dialog_actualizar").dialog({
	autoOpen: false, modal: true, width: 'auto', height: 'auto', resizable: false,
	open: function(event, ui) {
		var parametros = { "OPCION":"UPDATE_DATA", "Tienda": "<?php echo $Tienda; ?>", "Pais":"<?php echo $Pais; ?>" };
<?php
	if (!SoyYo())
		echo 'Ejecuta_AJAX("Mensaje_exec2", parametros );'.PHP_EOL;
	else
	{
/*			var ajaxWhatcha = new XMLHttpRequest();
			ajaxWhatcha.onreadystatechange = function() {
					document.getElementById("Mensaje_exec2").innerHTML = this.responseText;
			}
			ajaxWhatcha.open("GET", "/Resources/tools/actualiza.php?Tienda='.$Tienda.'&Pais='.$Pais.'", true);
			ajaxWhatcha.send(null);
		
		ajax.onreadystatechange = function() {
			$("#Mensaje_exec2").html = this.responseText;
		};
		ajax.open("POST", "/tmp/logs/ESP-'.$Tienda.'.web_log", true);
		ajax.send();

*/
		echo 'setInterval(function() { $("#Mensaje_exec2").load("/tmp/logs/ESP-'.$Tienda.'.web_log"); }, 1000); '.PHP_EOL;
		echo '$.ajax({ url: "/Resources/tools/actualiza.php?Tienda='.$Tienda.'&Pais='.$Pais.'"});'.PHP_EOL;
// 		echo 'var ajax = new XMLHttpRequest();'.PHP_EOL;
// 		echo 'ajax.open("POST", "/Resources/tools/actualiza.php?Tienda='.$Tienda.'&Pais='.$Pais.'",true);'.PHP_EOL;
// 		echo 'ajax.send();'.PHP_EOL;
		sleep(2);
	}
?>
	$("form").submit();
	}

// 	,buttons: {
// 		"Cerrar": function() {
// 			$("form").submit();
// 		}
// 	}
});

$("#dialogo_cambiar_ip").dialog({
	autoOpen: false, modal: true, width: 'auto', height: 'auto', resizable: false,
	buttons: {
		"Aceptar": function() {
			$('#myForm')[0].checkValidity();
			if ($("input#id_new_ip")[0].checkValidity()) {
				if ($("#id_new_ip").val() != $("#Cambiar_IP").val()) {
					var parametros= {
						"OPCION":"CHANGE_IP",
						"Nueva_IP": $("#id_new_ip").val(),
						"Tienda": "<?php echo @$Tienda; ?>",
						"Centro":"<?php echo @$Centro; ?>"
					}
					Ejecuta_AJAX("Mensaje_exec1", parametros );
					$.get('http://estpvds/actualizaTienda.php?numeroTienda='+'<?php echo @$Tienda; ?>');
					$(this).dialog("close");
					$("#Mensaje_exec2").html("<font color=blue>(Intentando nueva direccion IP: "+$("#id_new_ip").val()+")</font>");
					$("#dialog_actualizar").dialog("open");
				}
				else
					$("#Mensaje_exec1").html("<b>NO HA HABIDO CAMBIO DE IP</b>");
			}
		},
		"Cancelar": function () {
			$(this).dialog("close");
		}
	}
});

$("#Cambiar_IP").click(function() {
	$("#id_new_ip").val($(this).val());
	var Grupo=<?php echo getGrupoUser(); ?>;
	if (Grupo == 0)
		alert("Debe loguearse para poder modificar este campo.");
	else if (Grupo==1 || Grupo==2 || Grupo==6 || Grupo==7 || Grupo==8)
		$("#dialogo_cambiar_ip").dialog("open");
	else
		alert("No tiene permisos para modificar este campo.");
});

$("#id_b_Actualizar").click(function() {
	$("#dialog_actualizar").dialog("open");
});

$("#Aviso1").load("/Resources/textos/ayuda_pre_conectar.php #"+Idioma);

//Resize_Cuerpo(); $(window).resize();

function drawCharts() {
	var d_cajas = new google.visualization.DataTable();
		d_cajas.addColumn('number', 'Caja');
		d_cajas.addColumn('string', 'Conexion');
		d_cajas.addColumn('string', 'Version');
		d_cajas.addColumn('string', 'APP');
		d_cajas.addColumn('string', 'MySQL');
		d_cajas.addColumn('string', 'WSD');
		d_cajas.addColumn('string', 'Modelo');
		d_cajas.addColumn('string', 'BIOS');
		d_cajas.addColumn('string', 'RAM Total');
		d_cajas.addColumn('number', 'HDD');
		d_cajas.addColumn('number', 'Temperatura');
		d_cajas.addColumn('string', 'Errores LAN');
		d_cajas.addColumn('string', 'N. Apagados');
		d_cajas.addRows([<?php echo $lista_cajas; ?>]);

	var datos_tienda = new google.visualization.arrayToDataTable([ <?php echo $datos_tienda; ?> ]);

	var disco_duro = new google.visualization.BarFormat({width: 50, max:100, min:0, showValue:true});
	var temperatura = new google.visualization.ColorFormat();
		temperatura.addRange(40, 50, 'white', 'orange');
		temperatura.addRange(51, null, 'red', '#33ff33');

	disco_duro.format(d_cajas, 9);
	temperatura.format(d_cajas, 10);

	var t_cajas = new google.visualization.Table(document.getElementById('lista_cajas'));
	var t_datos_tienda = new google.visualization.Table(document.getElementById('datos_tienda'));

	google.visualization.events.addListener(t_cajas, 'select', function() {
		var row = t_cajas.getSelection()[0].row;
		alert('You selected ' + d_cajas.getValue(row, 0));
	});

	for (var i = 0; i < d_cajas.getNumberOfRows(); i++) {
		console.log(i+'/'+d_cajas.getNumberOfRows()+': '+d_cajas.getValue(i, 1));
		if (d_cajas.getValue(i, 1) === 'NO') {
			for (var c=0; c < d_cajas.getNumberOfColumns(); c++) {
				d_cajas.setProperty(i, c, 'style', 'background-color: gray;');
			}
		}
	}
	
	t_cajas.draw(d_cajas, {width: '100%', height: '100%', allowHtml: true});
	t_datos_tienda.draw(datos_tienda, {width: '100%', height: '100%', allowHtml: true});
}
  
	google.charts.setOnLoadCallback(drawCharts);
</script>


</body>
</html>
