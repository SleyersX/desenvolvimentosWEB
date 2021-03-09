<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }
if ($_SESSION['grupo_usuario'] > 2 && strtoupper($_SESSION['usuario'] != "mss004es") && strtoupper($_SESSION['usuario']) != "DBA001ES") {
	require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/incorrect_profile.php"); die();
}

function Pon_Status($Texto) {
	if ($Texto != NULL) {
		if (preg_match("/CORRECTO/",$Texto))
			return '<text class="ok" style="font-size:200%">'.$Texto.'</text>';
		else
			return '<text class="css3-blink" style="font-size:250%; background-color:red; color:white;">'.$Texto.'</text>';
	} else return '<text style="font-size:200%">N/A</text>';
}

function _ECHO_IN($Donde, $Texto) {
	_ECHO("<script>$('#$Donde').after('$Texto<br>');</script>");
	flush(); @ob_flush();
}

	echo '
		<table id="t_hist_serv_cupo">
		<tr><td colspan="3" class="celda_servidores">
			<form>
			<b>Fecha del servidor:</b> <input type="date" id="id_f_servidor" name="f_servidor"><input type="button" id="Enviar" value="Enviar"/>
			</form>
			</td>
		</tr>';

	if (isset($f_servidor) & !empty($f_servidor)) {
		$res1=MyQUERY("SELECT TIME(ID),Oper from serv_cupo1 where DATE(ID)=DATE('$f_servidor') AND TIME(ID)>'00:00:00'");
		$res2=MyQUERY("SELECT TIME(ID),Oper from serv_cupo2 where DATE(ID)=DATE('$f_servidor') AND TIME(ID)>'00:00:00'");
		if (count($res1) == 0)
			die(Alert("warning","Lo sentimos, pero no existe esa fecha en el historico."));
		else {
			$DIRE_REDU=$DIR_TMP."hist_serv_otros_dias";
			$DIRE_TRAB=$DOCUMENT_ROOT.$DIRE_REDU;
			$cmd="
					mkdir -p $DIRE_TRAB 2>/dev/null;
					cp ".$DOCUMENT_ROOT."/tools/servidor_cupones/crea_all.gp ".$DIRE_TRAB."/ -vf;
					rm ".$DIRE_TRAB."/datos*.dat -f;
					";
				shell_exec($cmd);

				$txt_datos1=""; $txt_datos2="";
				foreach($res1 as $k => $d) { $txt_datos1.=$d[0].'\t'.$d[1].'\n'; }
				foreach($res2 as $k => $d) { $txt_datos2.=$d[0].'\t'.$d[1].'\n'; }
				shell_exec('echo -e "'.$txt_datos1.'" > '.$DIRE_TRAB.'/datos1.dat; echo -e "'.$txt_datos2.'" > '.$DIRE_TRAB.'/datos2.dat');
				shell_exec("cd $DIRE_TRAB; gnuplot crea_all.gp");
				sleep(1);

		// ----------------------------------------------------------------------------------------------------------------
			echo '<tr>
					<td class="celda_servidores" valign="top" width="80%">
						<h2>Historico de operaciones para el dia '.$f_servidor.'</h2>
						<img class="img_serv" src="'.$DIRE_REDU.'/porhora1_b_all.jpg" height="350" width="450"/>
						<img class="img_serv" src="'.$DIRE_REDU.'/porhora2_b_all.jpg" height="350" width="450"/>';
			echo '<div id="div_total_servidores">';
			Show_data_sin_query("Oper_server_total", array (
				"OPERACIONES SERVIDOR TOTAL $f_servidor", array("TOTAL","Servidor 1","Servidor 2"), Get_Oper_Total_Serv_Cupo($f_servidor), NULL, "ESP POR ARG", ""));
			echo '</div>';
			echo '	</td>';
			echo '	<td class="celda_servidores">';
			echo '<div id="historico">';
		// ----------------------------------------------------------------------------------------------------------------
			Show_data2("Oper_server_1",
				array ("OPERACIONES POR TRAMOS HORARIOS<br>($f_servidor)",
					array("TRAMO","SERVIDOR 1" ,"SERVIDOR 2"),
					"select   TIME(sc1.ID),
						IF(sc1.Oper=0, CONCAT('<font color=\"red\">',sc1.Oper,'</font>'),sc1.Oper),
						IF(sc2.Oper=0, CONCAT('<font color=\"red\">',sc2.Oper,'</font>'),sc2.Oper)
						from serv_cupo1 sc1
					inner join serv_cupo2 sc2 on sc1.id = sc2.id
						where DATE(sc1.ID) = DATE('$f_servidor')
					order by 1",
					NULL,
					"ESP POR ARG",
					"Operaciones el servidor efectuadas en franjas de diez minutos.\nFranja de muestra: 08:00 - 22:00")
			);
			echo '</div>';
			echo '</td></tr>';
		// ----------------------------------------------------------------------------------------------------------------
			echo "</table>";

		}
	}

?>
<script>
jQuery(document).ready(function () {
	$("#Enviar").on("click",function() {
		if ($("#id_f_servidor").val()=="")
			alert("Debe seleccionar una fecha");
		else {
			if (Repaginar) clearInterval(Repaginar);
			Recarga_Cuerpo("CUERPO", "<?php echo $_SERVER['PHP_SELF']; ?>?f_servidor="+$("#id_f_servidor").val())
		}
	});
	$("#dialogLoading_Cupones").dialog("close");
});
</script>
