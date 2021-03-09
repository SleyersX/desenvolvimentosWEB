<style>
	.rdiv {
		background-color: white;
		border:1px solid black;
		border-radius: 2px;
	}
</style>
<div class="rdiv">
	<p>Este proceso inserta en la base de datos de la tienda RUC base para poder trabajar.</p>
	<input type="button" id="i_carga_RUC" value="Carga RUCs">
	<hr>
	<iframe id="resultado_RUC" class="rdiv" style="display:none;width:100%"></iframe>
</div>
<script type="text/javascript">
	$("#i_carga_RUC").on("click",function () {
		var url="<?php echo 'http://'.$SERVER_ADDR.':8080/?IP='.$con_tda->GetIP().'&caja='.$con_tda->GetPort().'&comandoTerminal=cargar_RUC&usuario='.getUser(); ?>";
		$("#resultado_RUC").attr("src", url);
		$("#resultado_RUC").show();
	})
</script>