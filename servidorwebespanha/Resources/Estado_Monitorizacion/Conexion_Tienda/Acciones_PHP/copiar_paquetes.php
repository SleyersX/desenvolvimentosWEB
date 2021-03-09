<?php

die('<script>Desbloqueo();</script><div class="Aviso Aviso_Rojo"><h2>NO POSIBLE EN ESTOS MOMENTOS...</h2></div>');

if ($con_tda->caja != 1)
	echo '<div class="Aviso Aviso_Rojo">
			<h2>NO POSIBLE EN ESTOS MOMENTOS...</h2>
			<h2>NO POSIBLE, SOLO EN CAJA MASTER</h2>
			<p>Esta funcionalidad solo est&aacute; disponible para cajas Master</p>
		</div>
	';
else {

$DIR_BAS="/usr/local/n2a-continuous-day-manager/var/data/communications/softwareFiles";
$DIR_PKG=$DIR_BAS."/historyIn";
$DIR_PEN=$DIR_BAS."/pending";
$DIR_AUT=$DIR_BAS."/pending/autoinstall/level3";

if (!empty($paquete_a_copiar)) {
	_ECHO("<script>Desbloqueo();</script>
		<div class='div_fondo b_azul'>
		<p>Ejecutando herramienta de distribucion de software en la TPV (send_cmd):</p>");
	$res=$con_tda->cmdExec("send_cmd 0");
	_ECHO("<p>Resultado: $res</p>");
// 	$lista_cajas_a_copiar=explode(" ",$cajas_a_copiar);
// 	foreach($lista_cajas_a_copiar as $k => $d) {
// 		if (!empty($d)) {
// 			_ECHO("<li>Copiando paquete a caja $d... ");
// 			$con_tda->cmdExec("scp -P23 $DIR_PKG/$paquete_a_copiar root@caja_$d:/$DIR_PKG/package.tbz");
// 			$con_tda->cmdExec("scp -P23 $DIR_PKG/$paquete_a_copiar root@caja_$d:/$DIR_AUT/package_ready.tbz");
// 			_ECHO("OK</li>");
// 		}
// 	}
	_ECHO('<div id="Mensaje_Aviso">'.Alert("warning","NOTA: Ejecutar bat 200 o descanso, segun corresponda.").'</div>
		</div>
		<script>INPUT_HIDDEN("paquete_a_copiar","","myForm");</script>');
	
} else {
	$res=$con_tda->cmdExec("cd $DIR_PKG; ls -ltra Pkg* | awk '{print $5,$9}'");
	$Lista_Pkgs=explode("\n",$res);

	echo '<div class="Aviso">';
	echo "<p id='Lista_Paquetes'>Lista de paquetes disponibles en la caja (Historico)</p>";
	echo "<ul>";
	foreach($Lista_Pkgs as $k => $d) {
		@list($Tama, $Name) = explode(" ",$d);
		if (!empty($Name))
			echo "<li><a id='$Name' href='javascript:{}' >$Name (Size: ".form_size($Tama,"KB").")</a></li>";
	}
	echo "</ul>";
	$res=$con_tda->cmdExec("cd $DIR_PEN; [ -f package.tbz ] && ls -ltra package.tbz | awk '{print $5,$9}'");
	@list($Tama, $Name) = explode(" ",$res);
	if (!empty($Name)) {
		echo "<p>Paquete actual (bat 200):</p><ul>";
		echo "<li><a id='$Name' href='javascript:{}' >$Name (Size: ".form_size($Tama,"KB").")</a></li>";
		echo "</ul>";
	}

	$res=$con_tda->cmdExec("cd $DIR_AUT; [ -f package_ready.tbz ] && ls -ltra package_ready.tbz | awk '{print $5,$9}'");
	@list($Tama, $Name) = explode(" ",$res);
	if (!empty($Name)) {
		echo "<p>Paquete autoinstall actual (descanso):</p><ul>";
		echo "<li><a id='$Name' href='javascript:{}' >$Name (Size: ".form_size($Tama,"KB").")</a></li>";
		echo "</ul>";
	}

	echo '</div>';

	$txtCajas="";	
	for($c=2; $c<=$con_tda->NumeTPVs; $c++)
		$txtCajas.='<li><input type="checkbox" name="caja['.$c.']" value="'.$c.'"/> Caja '.$c.'</li>';
	echo '<div id="dialogo_copia_paquetes" style="display:none; overflow:hidden;">
			<h2>COPIAR PAQUETE <span id="paquete_actual"></span> A ESCLAVAS</h2>
			<h3>CAJAS DISPONIBLES:</h3>
			<ul>'.$txtCajas.'</ul>
		</div>
	';
	echo '<script>
		function Get_Cajas() {
			var cajas="";
			$("#dialogo_copia_paquetes input:checkbox:checked").each(function(){
				cajas=cajas+" "+$(this).val();
			});
			return cajas;
		}
		$("#dialogo_copia_paquetes").dialog({
			autoOpen: false, modal: true, width: "auto", height: 400, resizable: false,
			buttons: {
				"Enviar": function() {
					cajas=Get_Cajas();
					if (cajas == "")
						alert("No ha seleccionado cajas para enviar...");
					else {
						INPUT_HIDDEN("paquete_a_copiar",$("#paquete_actual").text(),"myForm");
						INPUT_HIDDEN("cajas_a_copiar",cajas,"myForm");
						$("#myForm").submit();
					}
				},
				"Cerrar": function() { $(this).dialog("close"); }
			}
		});

		$("#Lista_Paquetes a").on("click",function(x){
			$("#paquete_actual").text($(this).attr("id"));
			$("#dialogo_copia_paquetes").dialog("open");
		});
	</script>';
}
}
?>
