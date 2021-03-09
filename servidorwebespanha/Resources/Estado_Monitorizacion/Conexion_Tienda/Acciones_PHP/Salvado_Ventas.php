<?php
$DIR_FILES_OUT="/usr/local/n2a/var/data/communications/out";

function DIE_ERROR($Texto) {
	die(_ECHO("<div class='Aviso Aviso_Rojo'><b style='color:red'>$Texto</b></div>"));
}

// switch ($Pais) {
// 	case "ARG":
// 		$ftp_server="ARCONCEN1";
// 		$ftp_user_name="lares/usertpvsop"; $ftp_user_pass="al59e1q6";
// 		$F_Salvar="Salvado_Ventas_SA.php";
// 		break;
// 	case "BRA": $ftp_server="BRCONCEN1"; $F_Salvar="Salvado_Ventas_SA.php"; break;
// 	case "POR":
// 		$ftp_server="PTCONCEN1";
// 		$ftp_user_name="lares/usertpvsop"; $ftp_user_pass="al59e1q6";
// 		$F_Salvar="Salvado_Ventas_SA.php";
// 		break;
// 	case "CHI": $ftp_server="CZCONCEN1"; $F_Salvar="Salvado_Ventas_SA.php"; break;
// 	case "ESP": $ftp_server="ESCONCEN1"; $F_Salvar="Salvado_Ventas_N2A.php"; break;
// }

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
}
else
{
echo '
<div class="Aviso" style="width: 80%; ">
<p>
	Esta opci&oacute;n permite salvar las ventas no trasnsmitidas en el &uacute;ltimo gestor (o gestores) noche de una tienda.<br>
	Si la tienda lleva m&aacute;s de 4 d&iacute;as sin transmitir, se debe dar aviso para que se revisen las comunicaciones.<br>
	SOLO SE PUEDE EJECUTAR UN SALVADO DE VENTAS UNA VEZ AL DIA.<br>
</p>
<p>
	Recordamos que se debe realizar en las siguientes circunstancias:
	<ul>
		<li>Caso de extrema necesidad.</li>
		<li>Cuando la tienda lleva sin comunicar mas de 5 d&iacute;as.</li>
		<li>Si por alg&uacute;n motivo, no se ha podido solventar el problema de la no comunicaci&oacute;n de la tienda con concentrador, que se puede dar en estos casos, entre otros:
		<ul>
			<li>Errores en la red local de la tienda.</li>
			<li>Errores de red entre la tienda y sistemas centrales.</li>
			<li>Errores software (EBD) que impidan un fin de d&iacute;a correcto.</li>
		</ul>
	</ul>
</p>
<p><b>NOTA:</b> <i>Esta opci&oacute;n no salva Diario Electr&oacute;nico pendiente de transmitir.</i></p>
<hr>';

echo '<p ><b>IMPORTANTE:</b>El proceso precisa que la caja est&eacute; en SELECCIONE PROGRAMA, por lo que antes de ejecutar esta opci&oacute;n, la TPV no est&aacute; realizando alg&uacute;n proceso cr&iacute;tico, ya que ser&aacute; necesario llevar la TPV a SELECCIONE PROGRAMA.</p>';

echo '</ul> <input id="Generar_Fichero" type=button value="Generar fichero de ventas?"/>
	<div id="div_salvado"></div>
</div>';
}

echo '
<script>
	$("input[name=Opcion_Salvado]:radio").change(function (e) { $("#Aviso_Fecha").hide(); });
	$("#Generar_Fichero").on("click",function() {
		$("#div_salvado").html(\'<img src="/img/ventana_espera.gif"/>\');
		$("#div_salvado").load("Acciones_PHP/Salvado_Ventas/'.$F_Salvar.'?Tienda='.$con_tda->tienda.'");
	});
</script>';

?>
