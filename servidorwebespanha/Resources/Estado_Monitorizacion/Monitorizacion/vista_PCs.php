<?php
require("./cabecera_vistas.php");

switch(@$_GET["opcion"]) {
	case "all_pcs":
		$res=myQUERY("select Tienda,PC,IP_PC,IF(EXEC_PC>0,'ON','OFF'),IF(MYSQL_PC>0,'ON','OFF'),IF(CAPTUR_PC>0,'ON','OFF'), Version_PC,Unix_PC,HDD_PC,RAM_PC,BIOS_PC,Modelo_PC,LAN_PC,CARGA_PC,TEMP_PC from PC_Tienda");
		$cabecera=array("Tienda","PC","IP","Hydra","MySQL","Captur.","Vers.PC","Vers.UNIX","Disco","RAM","BIOS","MODELO","Errores LAN","CARGA","Temperatura");
		if (count($res)>0) {
			$tmp="<table id='t_pcs' class='tabla2'>";
			$tmp.="<tr>"; foreach($cabecera as $c) $tmp.="<th>".$c."</th>"; $tmp.="</tr>";
			foreach($res as $d) {
				$tmp.="<tr class='linea_pc'>"; foreach($d as $d1) $tmp.="<td>".$d1."</td>"; $tmp.="</tr>";
			}
			$tmp.="</table>";
			echo $tmp;
		}
		exit;
}

$Alto_Tablas="800px";

if (empty($opcion)) {
	echo '<script type="text/javascript" src="/Resources/js/jquery.js"></script>';
	echo "<style src='/css/tabla2.css'/>";
}

?>
<title>VISTA PCs EN TIENDA</title>
<style>
	.gene_ofertas { overflow:auto;border:1px solid blue;border-radius:3px; padding:2px; height:<?php echo $Alto_Tablas;?>;  }
	.centrado { font-size:16px; margin-left: 2em; text-align: center; vertical-align: middle; line-height:<?php echo $Alto_Tablas;?>;  }
	.oculto { display: none; }
	.visible { display: block; }
</style>

<div style="border:1px solid black; border-radius:2px; background-color:white;">
	<b>Informacion de todas los PCs de la red</b><br>
	<div id="div_pcs"></div>
</div>

<script  type="text/javascript">
	var doHandle = true;
	var oferta;
	function Recarga(donde, url) {
		$("#"+donde).html("<img src='/img/Loading-data.gif'/>").load(url);
	}

	Recarga("div_pcs","Monitorizacion/vista_PCs.php?opcion=all_pcs");

/*	$("#gene_oferta").on("click", '.oferta', function(event) { 
		oferta = $(this).children().first().text();
		$("#id_oferta").html(oferta);
		$("#detalle_tiendas").html("&#9650 Pulse en subtipo para ver el detalle");
		Recarga("info_oferta","Monitorizacion/vista_ofertas_nuevo.php?opcion=info_ofertas&oferta="+oferta);
	});
*/


</script>