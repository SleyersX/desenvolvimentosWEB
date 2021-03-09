<?php
// comando="\'n2aStartupFlags -a "+$("#flag_inicio").text()+"\'"
echo '
<div class="div_fondo b_verde" id="Opciones_Marcar_Inicio">
<h2>Opciones disponibles</h2>
<table class="table2">
	<tr><td>AUTO_26</td><td>Permite hacer que el siguiente reinicio de TPV ejecute el bat 26 automaticamente.</td></tr>
	<tr><td>AUTO_04</td><td>Permite hacer que el siguiente reinicio de TPV realice replicacion automatica.</td></tr>
</table>
<ul>
<li><a href="javascript:{}" id="AUTO_26">AUTO_26:</a> Permite hacer que el siguiente reinicio de TPV ejecute el bat 26 automaticamente.</li>
<li><a href="javascript:{}" id="POS_CONFIGURED">POS_CONFIGURED:</a> (Por defecto) Caja operativa normal.</li>
<li><a href="javascript:{}" id="AUTO_04">AUTO_04:</a> Permite hacer que el siguiente reinicio de TPV realice replicacion automatica.</li>
<li><a class="disabled" href="javascript:{}" id="AUTO_09">AUTO_09:</a> Sin uso actualmente.</li>
<li><a class="disabled" href="javascript:{}" id="AUTO_25">AUTO_25:</a> Sin uso actualmente.</li>
</ul>
<div class="Aviso Aviso_Verde">
	<b>NOTA:</b> Es necesario que la TPV haga descanso para que la marca sea efectiva.
</div>

</div>

<div id="resultado_marcar_inicio" title="MARCADO DE FLAG DE INICIO" style="display:none; overflow:hidden;">
	<p>Est&aacute; seguro de enviar la marca <span id="flag_inicio"></span> a esta caja?</p>
	<div id="parte_1" style="display:none"> 
		<p>Se va a proceder a marcar el flag <b></b> para que el siguiente arranque de la TPV</p>
		<p>ESPERE POR FAVOR...</p>
		<div id="resultado"></div>
	</div> 
</div>

<script>
	$("#resultado_marcar_inicio").dialog({
		autoOpen: false, modal: true, width: "auto", height: 400, resizable: false,
		buttons: {
			"Enviar": function(e) {
				$("#parte_1").show();
				Exec_Remoto("'.$new_ip.'", '.$new_port.', "n2aStartupFlags -a "+$("#flag_inicio").text(), $("#resultado"));
			},
			"Cerrar": function() {
				$(this).dialog("close");
			}
		},
		close: function() {
			$("#parte_1").hide(); $("#resultado").html("");
		}
	});
	$("#Opciones_Marcar_Inicio a").on("click",function(x) {
		$("#flag_inicio").text($(this).attr("id"));
		$("#resultado_marcar_inicio").dialog("open");
	});
</script>';

?>