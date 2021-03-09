<?php
$DIR_FILES_OUT="/usr/local/n2a/var/data/communications/out";

if (isset($Opcion_Salvado_Ventas)) {
	$FILE_BASE="M".sprintf("%05d",$con_tda->tienda)."00";
	$FECHHORA_ACTUAL=date("Ymd-Hi");
	$DIR_VENTAS="/root/ventas_GN";
	$DIR_TMP_VENTAS="$DIR_VENTAS/$FECHHORA_ACTUAL";

	$FILE_VTA=$DIR_VENTAS.'/'.$FILE_BASE.'.VTA';
	$FILE_SUN=$DIR_VENTAS.'/'.$FILE_BASE.'.SUN';

	switch ($Pais) {
		case "ARG": $ftp_server="ARCONCEN1"; break;
		case "ESP": $ftp_server="ESCONCEN1"; break;
		case "BRA": $ftp_server="BRCONCEN1"; break;
		case "POR": $ftp_server="PTCONCEN1"; break;
		case "CHI": $ftp_server="CZCONCEN1"; break;
	}

	$COMANDO_PREPARA='
		mkdir -p '.$DIR_VENTAS.' 2>/dev/null; mkdir -p '.$DIR_TMP_VENTAS.' 2>/dev/null;
		cd '.$DIR_FILES_OUT.';
		N_FILES=$(dir * | wc -w);
		if [ $N_FILES -gt 0 ] ; then
			cat *.vta > '.$FILE_VTA.'; rm *.vta -f
			cat *.sun > '.$FILE_SUN.'; rm *.sun -f
		fi;';
	$COMANDO_TRANSMITE='scp '.$FILE_VTA.' '.$FILE_SUN.' soporte@'.$_SERVER['SERVER_ADDR'].':/home/soporteweb/'.$DIR_TMP;
	$COMANDO_BACKUP=' mv '.$FILE_VTA.' '.$FILE_VTA.'-'.$FECHHORA_ACTUAL.' -fb; mv '.$FILE_SUN.' '.$FILE_SUN.'-'.$FECHHORA_ACTUAL.' -fb';

	$File1="$DOWNLOAD_SERVER?file=$DIR_TMP$FILE_BASE.VTA";
	$File2="$DOWNLOAD_SERVER?file=$DIR_TMP$FILE_BASE.SUN";

	$FTP_Files=$DOCUMENT_ROOT.$DIR_TMP.$FILE_BASE.".VTA"."#".$DOCUMENT_ROOT.$DIR_TMP.$FILE_BASE.".SUN";

	echo '
	<div class="Aviso" style="width: 80%; ">
	<p>Ejecutando proceso de extraccion de ventas del dia "<b>'.$Opcion_Salvado_Ventas.'</b>"
	<ul>';
	echo '<li>Extrayendo ventas... ';
		$con_tda->cmdExec("$COMANDO_PREPARA");
	echo '</li>';
	echo '<li>Transmitiendo ventas al servidor de soporte...';
		$con_tda->cmdExec("$COMANDO_TRANSMITE");
	echo '</li>';
	echo '<li>Guardando backup...';
		$con_tda->cmdExec("$COMANDO_BACKUP");
	echo '</li>';
	echo '
	</ul>
	<p>Proceso finalizado con exito.</p>
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
	Graba_Historico("SALVADO DE VENTAS POR HSR");
} else {
	$Result=$con_tda->cmdExec("echo $(dir $DIR_FILES_OUT/* | wc -w)");

	echo '
	<div class="Aviso" style="width: 80%; ">
		<p>
			Esta opci&oacute;n permite salvar las ventas no trasnsmitidas en el &uacute;ltimo gestor (o gestores) noche de una tienda.<br>
			Si la tienda lleva m&aacute;s de 4 d&iacute;as sin transmitir, se debe dar aviso para que se revisen las comunicaciones.
		</p>
		<p><i>NOTA: esta opci&oacute;n no salva Diario Electr&oacute;nico pendiente de transmitir.</i></p>
		<hr>';
	if ($con_tda->SA==1) {
		echo '</p>El proceso precisa que la caja est&eacute; en SELECCIONE PROGRAMA, por lo que antes de ejecutar esta opci&oacute;n, la TPV no est&aacute; realizando alg&uacute;n proceso cr&iacute;tico, ya que ser&aacute; necesario llevar la TPV a SELECCIONE PROGRAMA.</p>';
	} else {
		echo '<h3>Opciones disponibles</h3>
		<ul style="list-style-type: none;">
		<li>
			<input id="rad_1" type=radio name=Opcion_Salvado value="Opcion 1"/>
			Seleccion una fecha a salvar ventas: <input id="Fecha_Ventas" type=date name=Fecha_Salvar_Ventas/>
			<span style="display:none; size:50%; color:red;" id="Aviso_Fecha">Debe elegir una fecha</span>
		</li>
		<li>
			<input id="rad_2" type=radio name=Opcion_Salvado value="Opcion 2"/>
			Ficheros detectados sin trasnsmitir: '.$Result.'
		</li>';
	}
	echo '</ul> <input id="Generar_Fichero" type=button value="Generar fichero de ventas?"/>';
	echo '</div>';
}

echo '
<script>
	$("input[name=Opcion_Salvado]:radio").change(function (e) { $("#Aviso_Fecha").hide(); });
	$("#Generar_Fichero").on("click",function() {
		switch ($("input[name=Opcion_Salvado]:checked").val()) {
			case "Opcion 1":
				var Opcion=$("#Fecha_Ventas").val();
				if (!Opcion) { $("#Aviso_Fecha").show(); return false; };
				break;
			case "Opcion 2": 
				var Opcion="Actual";
				break;
			default:
				alert("Debe elegir una opcion!"); return false;
		}
		if (Opcion) {
			INPUT_HIDDEN("Opcion_Salvado_Ventas",Opcion,"myForm");
			INPUT_HIDDEN("myAcciones","$myAcciones","myForm");
			SUBMIT("myForm");
		}
	});
</script>';

/*if (!empty($File1))
	echo " <script>
	function ACTIVA_OPCION(Valor) {
		if (Valor == 'Descargar_PC') {
			window.open('$File1','_new'); window.open('$File2','_new');
		}
		else {
			INPUT_HIDDEN(Valor,Valor,'myForm'); INPUT_HIDDEN('myAcciones','$myAcciones','myForm'); SUBMIT('myForm');
		}
	}
</script>";*/
?>
