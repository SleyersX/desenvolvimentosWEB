<title>VELA - DASHBOARD</title>
<?php
require("../Monitorizacion/cabecera_vistas.php");
?>
<style type="text/css">
	#kibana_dashboard {
		height:1000px; width:98%;
		-webkit-transform: scale(0.85);
		-webkit-transform-origin: 0 0;
	}
</style>
<div id="contenedor_kibana">
	<iframe id="kibana_dashboard"></iframe>
</div>

<script>
	jQuery(document).ready(function () {
//		$("#kibana_dashboard").attr("src","https://soporte:s4p4rt2@eselkap.lares.dsd:5601/app/kibana#/dashboard/VELA-COMMUNICATIONS?embed=true&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now%2Fd-4h,mode:quick,to:now%2Fd-1d%2B8h))&_a=(filters:!(),options:(darkTheme:!f),panels:!((col:5,id:ESB-END_DAY-OK-Count,panelIndex:2,row:6,size_x:4,size_y:2,type:visualization),(col:1,id:ESB-END_DAY-Files-OK-Count,panelIndex:5,row:6,size_x:4,size_y:2,type:visualization),(col:1,id:ESB-END_DAY-Files-KO-Count,panelIndex:7,row:8,size_x:4,size_y:2,type:visualization),(col:9,id:ESB-SED-OK-Metric,panelIndex:11,row:6,size_x:4,size_y:2,type:visualization),(col:5,id:ESB-ETL-End-Traffic,panelIndex:13,row:2,size_x:4,size_y:4,type:visualization),(col:1,id:VELA-COMMUNICATIONS-Fines-de-D%C3%ADa-BO-Chart,panelIndex:14,row:2,size_x:4,size_y:4,type:visualization),(col:9,id:ESB-START_DAY-Inicios-D%C3%ADa-Chart,panelIndex:15,row:2,size_x:4,size_y:4,type:visualization),(col:5,id:ESB-END_DAY-Errores-Count,panelIndex:16,row:8,size_x:4,size_y:2,type:visualization),(col:9,id:ESB-SED-KO-Metric,panelIndex:17,row:8,size_x:4,size_y:2,type:visualization),(col:1,id:VELA-COMMUNICATIONS-Markdown,panelIndex:18,row:1,size_x:12,size_y:1,type:visualization)),query:(query_string:(analyze_wildcard:!t,query:'*')),title:'VELA+-+COMMUNICATIONS',uiState:(P-14:(vis:(legendOpen:!f)),P-15:(vis:(legendOpen:!f)),P-5:(spy:(mode:(fill:!f,name:!n)))))");
		$("#kibana_dashboard").attr("src","https://eselkap.lares.dsd:5601/app/kibana#/dashboard/VELA-COMMUNICATIONS?embed=true&_g=(refreshInterval:(display:Off,pause:!f,value:0),time:(from:now%2Fd-4h,mode:quick,to:now%2Fd-1d%2B8h))&_a=(filters:!(),options:(darkTheme:!f),panels:!((col:5,id:ESB-END_DAY-OK-Count,panelIndex:2,row:6,size_x:4,size_y:2,type:visualization),(col:1,id:ESB-END_DAY-Files-OK-Count,panelIndex:5,row:6,size_x:4,size_y:2,type:visualization),(col:1,id:ESB-END_DAY-Files-KO-Count,panelIndex:7,row:8,size_x:4,size_y:2,type:visualization),(col:9,id:ESB-SED-OK-Metric,panelIndex:11,row:6,size_x:4,size_y:2,type:visualization),(col:5,id:ESB-ETL-End-Traffic,panelIndex:13,row:2,size_x:4,size_y:4,type:visualization),(col:1,id:VELA-COMMUNICATIONS-Fines-de-D%C3%ADa-BO-Chart,panelIndex:14,row:2,size_x:4,size_y:4,type:visualization),(col:9,id:ESB-START_DAY-Inicios-D%C3%ADa-Chart,panelIndex:15,row:2,size_x:4,size_y:4,type:visualization),(col:5,id:ESB-END_DAY-Errores-Count,panelIndex:16,row:8,size_x:4,size_y:2,type:visualization),(col:9,id:ESB-SED-KO-Metric,panelIndex:17,row:8,size_x:4,size_y:2,type:visualization),(col:1,id:VELA-COMMUNICATIONS-Markdown,panelIndex:18,row:1,size_x:12,size_y:1,type:visualization)),query:(query_string:(analyze_wildcard:!t,query:'*')),title:'VELA+-+COMMUNICATIONS',uiState:(P-14:(vis:(legendOpen:!f)),P-15:(vis:(legendOpen:!f)),P-5:(spy:(mode:(fill:!f,name:!n)))))");
		$("#CUERPO").height(800);
		$("#CUERPO").attr("overflow","hidden");
	});
</script>