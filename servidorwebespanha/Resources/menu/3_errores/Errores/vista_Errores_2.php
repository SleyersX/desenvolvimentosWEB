<style>
	.dash1 {
		border:1px solid black;
		border-radius: 3px;
		background-color: whitesmoke;
		font-family: Arial, Verdana, sans-serif;
		font-size: 13px;
	}
	.dash1 table {
		width: 100%;
	}
	#warnings {
		width: 250px;
	}
	.titulo_dash {
		width: 100%;
		text-align: center;
		background-color: lightcoral;
		font-weight: bold;
	}
	.tabla2 {
		border-color: none;
	}
</style>
<body>
	<div class="dash1" id="warnings">
		<span class="titulo_dash">WARNINGS</span>
		<div class="tabla_dash"></div>
	</div>
	<div class="dash1" id="id_ebds"></div>
</body>
<script>
	ajax_info_errores="/Resources/menu/3_errores/Errores/ajax_info_errores.php";
	console.log("Antes: "+timer);
	var timer;
	window.clearTimeout(timer);
	console.log("Despues: "+timer);
	
	function get_information() {
		//$(".tabla_dash").html("Actualizando...");
		$.getJSON(ajax_info_errores+"?opcion=warnings_g", function(data) {
			var table='<table class="tabla2"><thead><tr><th>Tipo error</th><th>Cajas</th></tr></thead>';
			$.each(data.WARNINGS_G, function(index, item){
				table+='<tr class="row_warning"><td><i>'+item.Tipo+'</i></td><td>'+item.Cantidad+'</td></tr>';       
      	});
      	table+='</table>';
      	$("#warnings .tabla_dash").html(table);
     		$(".row_warning").on("click",function () {
				console.log("Click");
			});
    	});
    	console.log(timer);
    	timer=window.setTimeout(get_information,10000); 
	}
	
	$(document).ready(function () {
		get_information();
	});
	
	
	//timer = setInterval(get_information, 5 * 1000);
</script>