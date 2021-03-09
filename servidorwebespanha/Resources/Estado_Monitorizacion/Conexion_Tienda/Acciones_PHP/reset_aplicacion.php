<div style="background:white; border:1px solid black; border-radius:3px; padding:3px;">
	<h3>Ultimo mensaje del visor:</h3>
	<iframe id="ulti_mens_visu" style="height:100px; width:100%;"></iframe>
</div>
<div style="background:white; border:1px solid black; border-radius:3px; padding:3px;">
	<h3>Ultimos 10 mensajes del log:</h3>
	<iframe id="ulti_mens_log" style="height:200px; width:100%;"></iframe>
</div>
<p>
<input type="button" id="boton_reset" value="Pulse aqui para reset" />
</p>
<div id="div_resultado_ejecucion" style="background:white; border:1px solid black; border-radius:3px; padding:3px; display:none">
	<iframe id="resultado_ejecucion" style="height:150px; width:100%;"></iframe>
	<iframe id="resultado_actualizacion" style="height:150px; width:100%;"></iframe>
</div>

<script>
	var tienda=<?php echo $con_tda->tienda; ?>;
	var caja=<?php echo $con_tda->caja; ?>;
	var IP="<?php $con_tda->GetIP(); ?>";
	var usuario="<?php echo ; ?>"; 
	var url_SIABox = '<?php echo $SERVER_SHELLINABOX."?IP=".$IP."&caja=".$Puerto."&usuario=".@$_GET["usuario"]; ?>';
	
	$("#ulti_mens_visu").attr("src",url_SIABox + "&comandoTerminal=ulti_mens_visu");	
	$("#ulti_mens_log").attr("src",url_SIABox + "&comandoTerminal=tailog");	
	$("#boton_reset").on("click",function () {
		$("#div_resultado_ejecucion").show();
		$("#resultado_ejecucion").attr("src",url_SIABox + "&comandoTerminal=reset_app");
		$("#resultado_actualizacion").attr("src",url_SIABox + "&comandoTerminal=log_actu");
	})
</script>