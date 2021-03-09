<?php
if (!empty($_GET["tail_kibana"])) {
	set_time_limit(0); 
	ob_implicit_flush(true);
	ob_end_flush();
	echo "<pre>".shell_exec("tail -50 /home/colas/tickets.log | tac")."</pre>";
	exit;
}

?>

<div id="botonera_kibana" style="padding:1em; background-color:white;border:1px solid black;font-size:12px">
	Fichero: <a id="oculto_descarga" href="/tmp/tickets.log">tickets.log</a>
	<input id="parar_actu" type="button" value="Detener"/>
	<input id="set_actu_5" type="button" value="5s"/>
	<input id="set_actu_10" type="button" value="10s"/>
	<input id="set_actu_30" type="button" value="30s"/>
	<input id="set_actu_60" type="button" value="60s"/>
	<span>Tiempo actualizacion: <span id="texto_actu"></span></span>
</div>
<div id="i_log_kibana" style="background-color:white; border:1px solid black; font-size:12px"></div>


<script type="text/javascript">
	var timelog=5;
	var log_kibana;
	function set_time_log(tmp) {
			if (typeof log_kibana !== 'undefined')    clearInterval(log_kibana);
			log_kibana=en_background("#i_log_kibana", "kibana/log_kibana.php?tail_kibana=1",tmp*1000);
			$("#texto_actu").html(tmp+" seg.");
	}
	set_time_log(timelog);
	$("#set_actu_5").on("click",function () { timelog=5; set_time_log(timelog); });
	$("#set_actu_10").on("click",function () { timelog=10; set_time_log(timelog); });
	$("#set_actu_30").on("click",function () { timelog=30; set_time_log(timelog); });
	$("#set_actu_60").on("click",function () { timelog=60; set_time_log(timelog); });
	$("#parar_actu").on("click",function () {
		if ($(this).prop("value") == "Detener") {
			$(this).prop("value","Actualizar");
			timelog=0;
			clearInterval(log_kibana);
			$("#texto_actu").html("Parado");
		}
		else {
			$(this).prop("value","Detener");
			timelog=5;
			set_time_log(timelog);
		}
	});

</script>