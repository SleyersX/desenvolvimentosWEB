<title>SERVIDOR CUPONES</title>
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

if (empty($_SESSION['b_cambio_graph'])) $_SESSION['b_cambio_graph']=8;

if ($Pais == "CHI") {
	echo "<div class='Aviso Aviso_Rojo'>
	<p>No esta implementada esta funcionalidad aun en el pais.</p>
	<p><i>This functionality is not implemented yet in the country.</i></p>
	</div>";
	die();
}

$DIR_SERV_CUPO="/tools/servidor_cupones";
$DIR_SCRIPTS="/$DOCUMENT_ROOT/$DIR_SERV_CUPO";

// $tmp=shell_exec('sudo ssh soporte@10.208.162.6 "sudo bash '.$DIR_SCRIPTS.'/get_oper.sh GET_STAT;"; sync');
$tmp=shell_exec('sync; sudo cat '.$DIR_SCRIPTS.'/status_servidores');
$lista = explode("\n",$tmp);
exec("cd $DIR_SCRIPTS; sudo gnuplot ./crea.gp; sudo gnuplot ./crea_web.gp; sync; sleep 1");

$IMG_TDA_1="<img id='img_tda_1' class='graph_cupon' src='$DIR_SERV_CUPO/porhora1_b.jpg?nocache=".md5(time())."'/>";
$IMG_TDA_2="<img id='img_tda_2' class='graph_cupon' src='$DIR_SERV_CUPO/porhora2_b.jpg?nocache=".md5(time())."'/>";
$IMG_WEB_1="<img id='img_web_1' class='graph_cupon' src='$DIR_SERV_CUPO/porhora_web1.jpg?nocache=".md5(time())."'/>";
$IMG_WEB_2="<img id='img_web_2' class='graph_cupon' src='$DIR_SERV_CUPO/porhora_web2.jpg?nocache=".md5(time())."'/>";

$IMG_TDA_1_ALL="<img id='img_tda_1_all' class='graph_cupon' src='$DIR_SERV_CUPO/porhora1_b_all.jpg?nocache=".md5(time())."'/>";
$IMG_TDA_2_ALL="<img id='img_tda_2_all' class='graph_cupon' src='$DIR_SERV_CUPO/porhora2_b_all.jpg?nocache=".md5(time())."'/>";
$IMG_WEB_1_ALL="<img id='img_web_1_all' class='graph_cupon' src='$DIR_SERV_CUPO/porhora_web1_all.jpg?nocache=".md5(time())."'/>";
$IMG_WEB_2_ALL="<img id='img_web_2_all' class='graph_cupon' src='$DIR_SERV_CUPO/porhora_web2_all.jpg?nocache=".md5(time())."'/>";


function Pon_Status($Texto) {
	if ($Texto != NULL) {
		if (preg_match("/CORRECTO/",$Texto))
			return '<span class="ok" style="font-size:200%;">'.$Texto.'</span>';
		else
			return '<span class="css3-blink" style="font-size:250%; background-color:red; color:white;"> '.$Texto.' </span>';
	} else return '<span style="font-size:200%">N/A</span>';
}

if (isset($_GET['SOLO_IMG'])) {
	echo "$IMG1 $IMG2 $IMG3 $IMG4";
	die();
}

// shell_exec("cd $DIR_SCRIPTS; sudo bash get_oper.sh GET_OPER");

// if ($_SERVER['REMOTE_ADDR'] == "10.208.185.5")
$img_Alerta_SC1="<img id='img_Alerta_SC1' class='blink' src='/img/error.png' title='ERROR: HAY DOS TRAMAS CONSECUTIVAS CON MOVIMIENTOS A CERO'/>";
$img_Alerta_SC2="<img id='img_Alerta_SC2' src='/img/error.png' title='ERROR: HAY DOS TRAMAS CONSECUTIVAS CON MOVIMIENTOS A CERO'/>";

// $lista[0]="BALANCEADOR: ERROR 01010101";
$Balanceador = Pon_Status(array_find("BALANCEADOR:", $lista));
$SC1 = Pon_Status(array_find("SC1:", $lista));
$SC2 = $img_Alerta_SC2.Pon_Status(array_find("SC2:", $lista));

// $SC1 = Pon_Status("SC1: ERROR");
// $SC2 = Pon_Status("SC2: ERROR");


$tmp=myQUERY("
select
	TIME(sc1.ID)
	,sc1.Oper,sc1.Web
	,sc2.Oper,sc2.Web
	,sc1.Oper+sc2.Oper,sc1.Web+sc2.Web
from serv_cupo1 sc1 inner join serv_cupo2 sc2 on sc1.id = sc2.id where DATE(sc1.ID) = DATE(NOW()) and time(sc1.ID) >= '00:10:00' and time(sc1.ID) <= '23:50:00' order by 1 desc");
$Lista_Operaciones='
<table class="list_oper_cupones">
<thead>
	<tr><th rowspan="2">TRAMO</th><th colspan="2">OPER.TIENDAS</th></tr>
	<tr><th>SERV1</th><th>SERV2</th></tr>
</thead>';
$Lista_Operaciones_Web='
<table class="list_oper_cupones">
<thead>
	<tr><th rowspan="2">TRAMO</th><th colspan="2">OPER.WEB</th></tr>
	<tr><th>SERV1</th><th>SERV2</th></tr>
</thead>';
$AnteOper1=$AnteOper2=$OperServ1=$OperServ2=$WebServ1=$WebServ2=0;
$Aviso1=$Aviso2="none";
foreach($tmp as $k => $d) {
	@list($Tramo, $Oper1, $Web1, $Oper2, $Web2, $TOper, $TWeb)=$d;
/*	if ($AnteOper1 == 0 && $Oper1 == 0) {
		$Aviso1 = "show";
	}
	if ($AnteOper2 == 0 && $Oper2 == 0) {
		$Aviso1 = "show";
	}*/
	$AnteOper1=$Oper1; $AnteOper2=$Oper2;
	$OperServ1+=$Oper1; $OperServ2+=$Oper2; $WebServ1+=$Web1; $WebServ2+=$Web2;
	$Lista_Operaciones.='
	<tr><td class="new_center">'.$Tramo.'</td>
		<td class="new_center">'.$Oper1.'</td>
		<td class="new_center">'.$Oper2.'</td></tr>';
	$Lista_Operaciones_Web.='
	<tr><td class="new_center">'.$Tramo.'</td>
		<td class="new_center">'.$Web1.'</td>
		<td class="new_center">'.$Web2.'</td>
	</tr>';
}
$Lista_Operaciones.='</table>'; $Lista_Operaciones_Web.='</table>';  
$TOTALOper=$OperServ1+$OperServ2;
$TOTALWeb=$WebServ1+$WebServ2;

echo '
<style>
	table.cupones { witdh=100%; background-color:white; }
	table.cupones td.general { padding:0 0.5em 0 0.5em; text-align:center; vertical-align:top; }
	table.cupones th { padding:0 0; vertical-align:top; text-align:center; }
	table.cupones thead tr { height:40px; }
	.b_24 { font-size:24px; cursor:pointer; }
	table.cupones tbody td img { top:0; left:0; }

	#img_Alerta_SC1, #img_Alerta_SC2 { height:40px; width:40px; margin:auto; vertical-align: middle;}
</style>

<table class="cupones">
<thead >
	<input type="button" id="b_cambio_graph" style="float:left; position:absolute;"></input>
	<tr id="mostrar_operaciones"  title="Pulse aqui para ver detalles">
		<th colspan="3">
		'.$Balanceador.'<br>
		<b class="b_24">( Total operaciones: '.$TOTALOper.' - WEB : '.$TOTALWeb.' )</b></th>
	</tr>
	<tr><th>'.$SC1.'</th><th>'.$SC2.'</th></tr>
</thead>
<tbody>
	<tr>
		<td class="general"><b class="b_24">Tiendas:'.$OperServ1.'</b>'.$IMG_TDA_1.$IMG_TDA_1_ALL.'</td>
		<td class="general"><b class="b_24">Tiendas:'.$OperServ2.'</b>'.$IMG_TDA_2.$IMG_TDA_2_ALL.'</td>
		<td id="lista_operaciones" style="display:none" rowspan="2">
			<table>
				<caption id="a_lista_operaciones_hide" style="cursor:pointer;" href="#">Pulse aqui para ocultar detalles</caption>
				<tr><td>'.$Lista_Operaciones.'</td><td>'.$Lista_Operaciones_Web.'</td></tr></table></td>
	</tr>
	<tr>
		<td class="general"><b class="b_24">WEB:'.$WebServ1.'</b><br>'.$IMG_WEB_1.$IMG_WEB_1_ALL.'</td>
		<td class="general"><b class="b_24">WEB:'.$WebServ2.'</b><br>'.$IMG_WEB_2.$IMG_WEB_2_ALL.'</td>
	</tr>
</tbody>
</table>
';
?>
<script>
	if (!mostrar_operaciones) { mostrar_operaciones=2; }
	if (mostrar_operaciones==1) { $("#lista_operaciones").show(); } else { $("#lista_operaciones").hide(); }
	$("#mostrar_operaciones").on("click",function(x) {
		$("#lista_operaciones").toggle();
		if (mostrar_operaciones==1) { mostrar_operaciones=2; } else { mostrar_operaciones=1; }
	});
	$("#a_lista_operaciones_hide").on("click",function(x) {
		$("#lista_operaciones").hide();
		mostrar_operaciones=2;
	});
//	$("#img_Alerta_SC1").css("display","'.$Aviso1.'");
//	$("#img_Alerta_SC2").css("display","'.$Aviso2.'");
	if (blink) { clearInterval(blink); }
	var blink=setInterval(function() {
		$( ".blink" ).toggle( "pulsate" );
	}, 500);

	if (!b_cambio_graph) { var b_cambio_graph=1; }
	function Pon_Graficas(tipo) {
		if (tipo == 1) {
			$("#b_cambio_graph").attr("value","08:00 - 22:00");
			$("#img_tda_1_all").hide(); $("#img_tda_1").show();
			$("#img_tda_2_all").hide(); $("#img_tda_2").show();
			$("#img_web_1_all").hide(); $("#img_web_1").show();
			$("#img_web_2_all").hide(); $("#img_web_2").show();
		} else {
			$("#b_cambio_graph").attr("value","00:00 - 23:00");
			$("#img_tda_1_all").show(); $("#img_tda_1").hide();
			$("#img_tda_2_all").show(); $("#img_tda_2").hide();
			$("#img_web_1_all").show(); $("#img_web_1").hide();
			$("#img_web_2_all").show(); $("#img_web_2").hide();
		}
	}
	$("#b_cambio_graph").on("click",function(){
		if (b_cambio_graph == 1) { Put_SESSION("CHG_SESSION", "b_cambio_graph", "2"); b_cambio_graph=2; }
		else { Put_SESSION("CHG_SESSION", "b_cambio_graph", "1"); b_cambio_graph=1; }
		Pon_Graficas(b_cambio_graph);
	});
	Pon_Graficas(b_cambio_graph);

</script>
