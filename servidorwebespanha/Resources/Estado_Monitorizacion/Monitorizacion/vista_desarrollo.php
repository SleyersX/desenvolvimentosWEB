<title>VISTA DESARROLLO</title>
<?php
if (!empty($_GET["get_data"])) {
	$Query=urldecode($_GET["QUERY"]); 
	echo $Query;
	$tmp=shell_exec('sudo mysql soporteremotoweb -e "'.$Query.'";');
	echo "<pre>".$tmp."</pre>";
	exit;
}
require("./cabecera_vistas.php");

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }
if ($_SESSION['grupo_usuario'] > 2 && $_SESSION['grupo_usuario'] != 6) {
	require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/incorrect_profile.php"); die();
}

$CASE_1="SUM(CASE WHEN comentario like 'INTERVENCION: regularizada cantidad pendiente servir de%' then 1 else 0 end)";
$CASE_2="sum(case when comentario like 'INTERVENCION: eliminadas%' then 1 else 0 end)";
$CASE_3="sum(case when comentario like 'INTERVENCION: regularizada cantidad pendiente servir x diferencias%' then 1 else 0 end)";

$Queries["DIFF_HISTORICO"]=array("HISTORICO INTERVENCIONES<br>TIENDAS CON FALTAS",
	array("Fecha","Faltas y Pdtes","Con Diff > 15d","Con sobras > 15d"),
	"select DATE(Fecha),$CASE_1,$CASE_2,$CASE_3 from HistoricoESP where Date(Fecha)>'2016-02-17' group by 1;
	select '<b>TOTAL</b>',  concat('<b>',$CASE_1,'</b>'), concat('<b>',$CASE_2,'</b>'), concat('<b>',$CASE_3,'</b>')
	from HistoricoESP",
	NULL,
	"",
	"");

$Queries["DAT_ADIC5"]=array("ACTUALIZACIONES",
	array("Valor","Cantidad"),
	"select DAT_ADIC5, COUNT(*) from Solo_DATS group by 1;",
	NULL,
	"ESP",
	"");

$Queries["CAPTURA"]=array("FUNC.CAPTU.",
	array("Tipo","Version", "Cantidad"),
	"select IF(a.Capt_Pend,'BLOQUEADO','PERMITIDO'), SUBSTRING(b.Version,1,8), COUNT(a.Tienda) from Funcionalidades_TPV a JOIN ChecksESP b ON a.Tienda=b.Tienda AND a.Caja=b.Caja group by 1,2",
	NULL,
	"",
	"");

$Queries["PS_15d_Eliminados"]=array("ELIMINADOS<br>+15 DIAS",
	array("Tienda","Ulti.Movi","Articulos","PS Eliminados Total"),
	"select Tienda, date(UltiMovi), COUNT(ARTI), SUM(PS) from Pend_Serv_15d_Eliminados group by 1, 2 order by 2,1;",
	NULL,
	"",
	"");

// $Lista_Tiendas_PS="60172,73201,60295,62202,60202,60004,60888,67213,60549,60987,60027,18096,52404,60093,19500,60288,67214,05362,60996,60068,60274,61098,60012,60831,66217,60699,65203,04680,70719,41022,60182,16084,60283,52084,60626,03189,60954,60713,67216,74203,71203,60168,14018,60267,60710,42019,60035,60833,60759,19032,66216,60333,01220,39017,19036,60121,52288,60876,18115,62200,60842,60005,60305,04321,60067,67201,03158,03914,47006,60794,60965,34015,66208,02762,31109,60920,60114,22002,60226,47015,60079,70718,07565,60135,60760,74700,64201,00943,60001,60687,00535,60635,52196,07976,71701,52218,60084,60110,06969,53723,60159,03401,75714,60891,60089,60743,13052,04565,08254,52330,60852,76704,60285,02495,60828,52576,60819,60815,69200,60708,64202,60786,60984,64204,60803,60792,60621,60088,60633,34005,60807,06970,60041,60230,76706,71205,60170,60724,60026,60028,60161,60783,01784,42025,67211,67207,66212,25006,52275,60993,60854,04089,00481,10126,60877,60277,60868,60989,66205,06462,60023,68201,04674,31112,66207,61205,60919,25009,39022,75712,01667,54651,31114,17517,60836,70717,60822,60016,52321,05187,16105,75705,72206,61002,60903,05993,08165,02476,60907,60651,60705,61017,03186,42022,18102,70229,71705,64205,60933,08825,61207,60896,61006,60999,70720,60156,60102,04679,60967,01776,60909,52007,70228,60548,31097,00757,60997,72209,60195,60025,60258,50003,60853,75205,60820,52216,62201,04459,60059,00484,60895,64200,31040,60125,16104,60192,60015,08036,13002,00926,04676,60275,60814,67200,08878,60199,75702,04165,60181,60481,76702,02263,60291,60011,04192,73202,16094,02725,60628,67212,70712,46009,60901,01285,60881,04477,60591,60707,31072,04320,04194,60039,63203,61022,60639,00896,41047,67208,66213,60022,08905,60149,60780,72705,60821,60193,60840,17011,52328,60843,60622,10163,39018,60749,60883,60040,60784,01638,41046,60029,60310,60886,60008,13029,30016,09489,60300,66200,43039,60200,61202,00787,05101,19514,60718,60958,61010,01460,39025,04188,60675,52075,10122,34017,60167,60100,60738,34023,60198,24002,01783,70233,01490,60899,60443,60761,10165,60624,04580,72706,60927,18009,00836,00807,52552,36069,60855,07990,73203,09842,74204,60638,61076,60709,60647,60231,42016,60006,17531,63207,60092,64203,07898,07592,08918,60711,61005,61000,78700,13015,60838,60890,60826,31110,07693,60095,66201,60641,10189,60684,01205,61201,60832,60700,42011,76206,39019,61064,29025,60117,01487,07242,60859,05140,01246,60990,60893,07585,75203,60279,60097,60637,60150,72703,60918,70223,00637,47025,39021,60913,17501,43037,73708,60260,66209,61071,60863,07306,72704,18022,07869";
// $tmp_PS=myQUERY("select tienda, DAT_ADIC4+0 from Solo_DATS where tienda in ($Lista_Tiendas_PS) and DAT_ADIC4>0 order by 2 desc");
// 
// 
// $Queries_s["PEND_SERV_1"]=array("PENDIENTES SERVIR (SSAA)",
// 	array("Tienda","Cantidad","Fichero"),
// 	$tmp_PS,
// 	NULL,
// 	"ESP",
// 	"");

$Queries["DIFF_2"]=array("DIFF > ENTREGA",
	array("Tienda","Centro","Tipo","Cantidad"),
	"select a.Tienda, b.Centro, b.tipo, a.DAT1 from $Table a join tmpTiendas b on b.numerotienda=a.tienda where a.DAT1 > 0 order by centro,tipo",
	NULL,
	"",
	"");

$Queries["Check_Pend"]=array("CHECK PENDIENTES",
	array("Tienda","Valor","Ulti.Inic"),
	"select a.Tienda, (a.DAT_ADIC4)+0, IFNULL(b.Fecha,'') from Solo_DATS a LEFT JOIN Inic_Pend_Serv b ON a.Tienda=b.Tienda left join tmpTiendas c ON c.numerotienda=a.Tienda where DAT_ADIC4<>0 and a.caja=1 and c.conexion=1 order by 2 desc",
	NULL,
	"",
	"");

$Queries["Error_Quique"]=array("ERROR QUIQUE",
	array("Tienda","Version","Valor"),
	"select Tienda, version, DAT2 from $Table where DAT2 not like '0	0%' and DAT2 <> ''  and caja=1",
	NULL,
	"",
	"");

$Queries["CAPTURADOR"]=array("CAPTURADOR",
	array("Tienda","Caja","GUC","CAB"),
	"select Tienda, Caja, Ver_GUC, CAB from capturador",
	NULL,
	"",
	"");

/* ----------------------------------------------------------------------------------------------------------------------------- */

if ( $PAIS_SERVER == "" ) {
$T_Faltas=shell_exec("echo $(find /home/datos/pendientes_servir_x_tienda/ -name \"*.faltas.*\" -not -size 0 | cut -f5 -d'/' | cut -f1 -d'.') | tr ' ' ','");
$T_Antes=shell_exec("echo $(find /home/datos/pendientes_servir_x_tienda/ -name \"*.antes.*\" -not -size 0 | cut -f5 -d'/' | cut -f1 -d'.') | tr ' ' ','");
$T_Diffs=shell_exec("echo $(find /home/datos/pendientes_servir_x_tienda/ -name \"*.diffs.*\" -not -size 0 | cut -f5 -d'/' | cut -f1 -d'.') | tr ' ' ','");
$T_Diffs_Antes=shell_exec("echo $(find /home/datos/pendientes_servir_x_tienda/ -name \"*.diffs_antes.*\" -not -size 0 | cut -f5 -d'/' | cut -f1 -d'.') | tr ' ' ','");
if (!preg_match("/0/", $T_Antes)) $T_Antes="00000";
if (!preg_match("/0/", $T_Faltas)) $T_Faltas="00000";
if (!preg_match("/0/", $T_Diffs)) $T_Diffs="00000";
if (!preg_match("/0/", $T_Diffs_Antes)) $T_Diffs_Antes="00000";

myQUERY("
drop table if exists tmpRegu_Faltas;
create table tmpRegu_Faltas as
	select
		distinct(numerotienda) as tienda,
		centro as centro,
		IF(numerotienda in ($T_Faltas),1,0) as con_falta,
		IF(numerotienda in ($T_Diffs),1,0) as con_diffs,
		IF(numerotienda in ($T_Diffs_Antes),1,0) as con_diffs_antes,
		IF(numerotienda in ($T_Antes),1,0) as antes
	from tmpTiendas");

$tmp=myQUERY("
select centro,
	SUM(CASE WHEN con_falta=1 THEN 1 ELSE 0 END) 'Con Faltas',
	SUM(CASE WHEN con_diffs=1 THEN 1 ELSE 0 END) 'Con Diffs',
	SUM(CASE WHEN con_diffs_antes=1 THEN 1 ELSE 0 END) 'Con Diffs Antes',
	SUM(CASE WHEN antes=1 THEN 1 ELSE 0 END) 'Regularizadas',
	count(*) 'Totales'
from tmpRegu_Faltas group by centro;
select 'TOTAL',
	SUM(CASE WHEN con_falta=1 THEN 1 ELSE 0 END),
	SUM(CASE WHEN con_diffs=1 THEN 1 ELSE 0 END),
	SUM(CASE WHEN con_diffs_antes=1 THEN 1 ELSE 0 END),
	SUM(CASE WHEN antes=1 THEN 1 ELSE 0 END),
	count(*)
from tmpRegu_Faltas");
echo '<style> #regu_faltas tbody td { text-align:right; padding-right:1em; } </style>';
echo '<div class="PANEL">';
echo '<table id="regu_faltas" class="new_list">';
echo '<caption>REGULARIZACION FALTAS EN LA RED DE TIENDAS</caption>';
echo '<thead>';
echo "<tr><th rowspan=2>CENTRO</th><th colspan=2>Tiendas con Faltas</th><th colspan=2>Tiendas con diffs</th><th rowspan=2>Total</th></tr>
	<tr><th>Actuales</th><th>Regular.</th><th>Actuales</th><th>Regular.</th></tr>";
echo '</tr></thead>';
foreach($tmp as $k => $d) {
	@list($d_centro,$d_faltas,$d_diffs,$d_diffs_antes,$d_regul,$d_total)=$d;
	echo "<tr>
			<td>$d_centro</td>
			<td>$d_faltas (".@round($d_faltas/$d_total*100,0)."%)</td>
			<td class='new_right'>$d_regul</td>
			<td class='new_right'>$d_diffs</td>
			<td class='new_right'>$d_diffs_antes</td>
			<td class='new_right'>$d_total</td>
		</tr>";
}
echo '</table>';
echo '</div>';
}

/*
$Ficheros_dat=exec("ls /home/pendserv/*.dat | wc -l");
$Ficheros_cero=exec('find /home/pendserv -maxdepth 1 -name "*.dat" -size 0 | wc -l');
$Ficheros_validos=$Ficheros_dat-$Ficheros_cero;
$Arti_a_regu=exec("[ ! -f /tmp/Arti_a_regu ] && sudo cat /home/pendserv/*.dat > /tmp/Arti_a_regu; cat /tmp/Arti_a_regu | wc -l");
exec("sudo cat /home/pendserv/resultados/*resultado* > /tmp/kk");
myQUERY("drop table if exists tmp_resultado; create table tmp_resultado like Pend_Serv; load data infile '/tmp/kk' into table tmp_resultado");
$Fechas=myQUERY("select distinct(date(Fecha)) from tmp_resultado",true);
// $tmp=shell_exec("ls /home/pendserv/resultados/*resultado* | cut -f4 -d'.' | cut -b1-8 | sort | uniq -c | awk '{print $2,$1\"#\"}'");
// $Resu_x_dia=explode("#",$tmp);
print_r($Fechas);
*/


/*$Resultados=shell_exec("sudo bash /home/pendserv/scripts/saca_estadisticas.sh; sudo cat /home/pendserv/estadisticas");
echo '<div class="PANEL" style="font-size:80%">';
echo $Resultados;
echo '</div>';*/

if (isset($Queries))
	foreach ($Queries as $key => $dato) {
		Show_data2($key, $dato); echo PHP_EOL;
	}
if (isset($Queries_s))
	foreach ($Queries_s as $key => $dato) {
		Show_data_sin_query($key, $dato);
	}
?>
<div id="vista_ejemplo"></div>

<script>
	var i1;
	clearInterval(i1);
//	i1=en_background("#vista_ejemplo", 'Monitorizacion/vista_desarrollo.php?get_data=1&QUERY='+encodeURI("select DAT_ADIC5 'Valor', COUNT(*) 'Cantidad' from Solo_DATS group by 1;"),1000);
//	$("#vista_ejemplo").html('<img src="/img/wait.gif"/>').load('Monitorizacion/vista_desarrollo.php?get_data=1&QUERY='+encodeURI("select DAT_ADIC5 'Valor', COUNT(*) 'Cantidad' from Solo_DATS group by 1;"));
</script>