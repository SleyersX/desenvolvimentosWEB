<title>VELA - DASHBOARD</title>
<?php
require("../Monitorizacion/cabecera_vistas.php");
?>
<style>
	#v_general { background-color: white; width: 1200px; height: 800px;}

	.pestania {
		border:1px solid gray;
		margin-top: 51px;
		margin:4px;
		width: 100%; height: 100%;
		vertical-align: top;
		background-color: white; 
	}
	
	.titulo_pestania {
		margin-top: 6px;
		border:1px solid gray;
		border-radius: 3px 3px 0 0;
		font: 12px Arial;
		height: 25px;
		padding: 2px 7 5px 7;
		background-color: whitesmoke;	
	}
	.activa_pestania {
		background-color: white;
		border-bottom: 1px solid white;	
	}
	.titulo_pestania:hover { background-color: white; cursor: pointer;}

</style>

<div id="v_general">
	<span asociado="vista_global"    class="titulo_pestania activa_pestania" style="margin-left:5px;">Vista Global</span>
	<span asociado="vista_articulos" class="titulo_pestania">Vista Articulos</span>

	<div class="pestania">
		<div id="vista_global" class="v_pestania">	
			<table>
				<tr><td> <div id='tabla_pedidos_total' style='border: 1px solid #ccc; height:400'></div> </td></tr>
				<tr>
					<td>
						<table><tr>
							<td><div id='graph_total_pedidos' style='border: 1px solid #ccc; width:550; height:250'></div></td>
							<td></td>
							<td><div id='graph_total_horas' style='border: 1px solid #ccc; width:550; height:250'></div></td>
						</tr></table>
					</td>
				</tr>
			</table>
		</div>
		
		<div id="vista_articulos"  class="v_pestania" style="display:none">					
			KKSDKJSKJSKDJ
		</div>
	</div>
	
</div>

<script>
	jQuery(document).ready(function () {
//		$("#kibana_dashboard").attr("src","https://soporte:s4p4rt2@kibana.lares.dsd/app/kibana#/dashboard/VELA-COMMUNICATIONS?embed=true&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now%2Fd-4h,mode:quick,to:now%2Fd-1d%2B8h))&_a=(filters:!(),options:(darkTheme:!f),panels:!((col:5,id:ESB-END_DAY-OK-Count,panelIndex:2,row:6,size_x:4,size_y:2,type:visualization),(col:1,id:ESB-END_DAY-Files-OK-Count,panelIndex:5,row:6,size_x:4,size_y:2,type:visualization),(col:1,id:ESB-END_DAY-Files-KO-Count,panelIndex:7,row:8,size_x:4,size_y:2,type:visualization),(col:9,id:ESB-SED-OK-Metric,panelIndex:11,row:6,size_x:4,size_y:2,type:visualization),(col:5,id:ESB-ETL-End-Traffic,panelIndex:13,row:2,size_x:4,size_y:4,type:visualization),(col:1,id:VELA-COMMUNICATIONS-Fines-de-D%C3%ADa-BO-Chart,panelIndex:14,row:2,size_x:4,size_y:4,type:visualization),(col:9,id:ESB-START_DAY-Inicios-D%C3%ADa-Chart,panelIndex:15,row:2,size_x:4,size_y:4,type:visualization),(col:5,id:ESB-END_DAY-Errores-Count,panelIndex:16,row:8,size_x:4,size_y:2,type:visualization),(col:9,id:ESB-SED-KO-Metric,panelIndex:17,row:8,size_x:4,size_y:2,type:visualization),(col:1,id:VELA-COMMUNICATIONS-Markdown,panelIndex:18,row:1,size_x:12,size_y:1,type:visualization)),query:(query_string:(analyze_wildcard:!t,query:'*')),title:'VELA+-+COMMUNICATIONS',uiState:(P-14:(vis:(legendOpen:!f)),P-15:(vis:(legendOpen:!f)),P-5:(spy:(mode:(fill:!f,name:!n)))))");
		$("#kibana_dashboard").attr("src","https://kibana.lares.dsd/app/kibana#/dashboard/ES-VELA-COMMUNICATIONS?embed=true");
		$("#CUERPO").height(800);
		$("#CUERPO").attr("overflow","hidden");
	});
</script>
