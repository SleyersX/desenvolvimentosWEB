<title>IMPRESORA TIENDA 11 - PARAGUAY</title>
<?php
require("../Monitorizacion/cabecera_vistas.php");
?>
<style type="text/css">
	#estado_impresora {
			background-color: whitesmoke;
			border:1px solid black;
			padding: 1em;
	}
	#captura_datos {
			margin-bottom: 1em;
	}
	#impresora {
		height:800px; width:98%;
		-webkit-transform: scale(0.85);
		-webkit-transform-origin: 0 0;
	}
</style>
<div id="estado_impresora">
	<div id="captura_datos">
		<label for="ip_impresora">IP de la impresora</label>
		<input id="ip_impresora" type="text" title="IP de la impresora:"></input>
		<button id="b_ip_impresora">Conectar</button>
		<span id="estado_conexion"></span>
	</div>
	
	<div id="contenedor_impresora">
		<iframe id="impresora"></iframe>
	</div>

</div>
<script>
	jQuery(document).ready(function () {
		$("#b_ip_impresora").on("click",function () {
			var ip_impresora=$("#ip_impresora").val();
			$("#estado_conexion").html("Conectando a la IP "+ip_impresora+"...");
			$("#impresora").attr("src","http://"+ip_impresora);
			$("#CUERPO").height(800);
			$("#CUERPO").attr("overflow","hidden");
		})
	});
</script>