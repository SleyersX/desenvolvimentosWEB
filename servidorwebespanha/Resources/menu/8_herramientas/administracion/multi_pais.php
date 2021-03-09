<title>MULTIPAIS</title>
<?php
require("../Monitorizacion/cabecera_vistas.php");
?>
<style type="text/css">
	.iframe_paneles {
		height:400%; width:150%;
		-webkit-transform: scale(0.65);-webkit-transform-origin: 100 0;
	}
	#paneles {
			width:100%; 
	}
	#pais1 { top:-85; left:-150; position:absolute; }
</style>

<div id="estado_impresora">
	<div id="pais1"><iframe class="iframe_paneles" id="argentina"></iframe></div>
	
	<iframe class="iframe_paneles" id="brasil"></iframe>
	<iframe class="iframe_paneles" id="paraguay"></iframe>
	<iframe class="iframe_paneles" id="portugal"></iframe>
</div>

<script>
function autofitIframe(id){
	id.style.height=500+'px';
	if (!window.opera && document.all && document.getElementById){
		id.style.height=id.contentWindow.document.body.scrollHeight;
	} else if(document.getElementById) {
		id.style.height=id.contentDocument.body.scrollHeight+"px";
	}
}
	jQuery(document).ready(function () {
			$("#argentina").attr("src","http://10.94.202.121/Resources/Estado_Monitorizacion/monitorizacion.php");
			$("#brasil").attr("src","http://10.105.186.135/Resources/Estado_Monitorizacion/monitorizacion.php");
			$("#paraguay").attr("src","http://10.95.81.137/Resources/Estado_Monitorizacion/monitorizacion.php");
			$("#portugal").attr("src","http://10.246.64.73/Resources/Estado_Monitorizacion/monitorizacion.php");
			$("#CUERPO").height(800);
			$("#CUERPO").attr("overflow","hidden");
	});
</script>