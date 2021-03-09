<?php
if (!empty($_GET["opcion_arti"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();

	foreach($_GET as $k => $d) $$k=$d;

	require_once("library/conexion_mysql_tienda.php");
	
	switch($opcion_arti) {
		case "get_info":
			$res1=MyQUERY_Tienda($mysqli_tienda,"
			SELECT
				CONCAT(
					LPAD(I.ITEM_ID,7,' '), 
					DATE_FORMAT(IFNULL(C.ACTIVATION_DATE,P1.BEGIN_DATE), ' %d/%m/%Y'), 
					DATE_FORMAT(IF(P1.END_DATE,P1.END_DATE,'0000-00-00'),'   %d/%m/%Y')
				),
				CONCAT(
					LPAD(FORMAT(P1.PRICE_AMOUNT,2),11,' '), 
					LPAD(FORMAT(P2.PRICE_AMOUNT,2),10,' '), 
					LPAD(FORMAT(P3.PRICE_AMOUNT,2),10,' ')
				)
			FROM ITEM I 
				INNER JOIN PRICE P1 ON I.ITEM_ID = P1.ITEM_ID AND P1.PRICE_TYPE_ID = 1 AND P1.PRICE_KEY_ID = 1 
				INNER JOIN PRICE P2 ON I.ITEM_ID = P2.ITEM_ID AND P2.PRICE_TYPE_ID = 1 AND P2.PRICE_KEY_ID = 3 AND P1.BEGIN_DATE = P2.BEGIN_DATE 
				INNER JOIN PRICE P3 ON I.ITEM_ID = P3.ITEM_ID AND P3.PRICE_TYPE_ID = 1 AND P3.PRICE_KEY_ID = 2 AND P1.BEGIN_DATE = P3.BEGIN_DATE 
				LEFT JOIN (SELECT PCPI1.ITEM_ID, MAX(PCPI1.BEGIN_DATE) ACTIVATION_DATE FROM PRICE_CHANGE_PROCESS_ITEM PCPI1 WHERE PCPI1.PRICE_CHANGE_PROCESS_ID = (SELECT MAX(PCP.PRICE_CHANGE_PROCESS_ID) PRICE_CHANGE_PROCESS_ID FROM PRICE_CHANGE_PROCESS PCP INNER JOIN PRICE_CHANGE_PROCESS_ITEM PCPI ON PCP.PRICE_CHANGE_PROCESS_ID = PCPI.PRICE_CHANGE_PROCESS_ID AND PCP.PRICE_CHANGE_TYPE_ID IN (3,4) WHERE PCPI.ITEM_ID = PCPI1.ITEM_ID GROUP BY PCPI.ITEM_ID) GROUP BY PCPI1.ITEM_ID) C ON C.ITEM_ID = I.ITEM_ID AND DATE(P1.BEGIN_DATE) = DATE('1970-01-01 00:00:00') WHERE I.ITEM_ID = $item_id AND P1.IS_REAL_FLAG = 1 AND (P1.BEGIN_DATE >= (SELECT MAX(BEGIN_DATE) FROM PRICE WHERE ITEM_ID = I.ITEM_ID AND PRICE_TYPE_ID = 1 AND IS_REAL_FLAG = 1 AND PRICE_KEY_ID = 1 AND BEGIN_DATE <= CURDATE()) ) 
			ORDER BY I.ITEM_ID, P1.BEGIN_DATE
			");

			echo "<pre>";
			echo "LISTADO PRECIOS EXCEPCION\n";
			echo "\n";
			echo " ART. FECHA ENTRADA FECHA SALIDA\n";
			echo "  P.EXCEP. PVP. FIDE PVP.NO FIDE\n";
			echo "--------------------------------\n";

			foreach($res1 as $k => $d) {
				foreach($d as $d1)
				echo $d1."\n";
			}
			echo "</pre>";
			break;
			
		case "get_list_arti":
			require_once("library/json_get_list_prec_exce.php");
			break;
	}
	@mysqli_close($mysqli_tienda);
	exit;
}

?>
<style type="text/css" ="">
	<?php require_once("library/css_comun.css"); ?>
</style>

<?php 
	echo '
		<div id="Info">
			<div style="width:800px; float:left;">
				<div class="ui-jqgrid " id="gbox_jqGrid" dir="ltr"><table id="jqGrid" class="F_SIZE_10"></table><div id="jqGridPager"></div></div>
			</div>
			<div id="info_articulo" style="float:left; margin-left:1em; border:1px solid black; border-radius:3px; padding:1em; background-color:azure">
				<span style="background-color:lightgreen; font-size:12px; font-weight:bold;">&#9664 Haga click en un articulo para ver sus datos.</span>
			</div>
		</div>';
?>

<script>
	var url_local="Listados_PHP/<?php echo basename(__FILE__); ?>";
	var IP_Tienda="<?php echo $con_tda->GetIP(); ?>";
	var Es_VELA=<?php echo $con_tda->VELA; ?>;
	var Titulo="LISTADO DE PRECIOS DE EXCEPCION";
	
	Desbloqueo();
	
	<?php require_once("library/list_arti_prec_exce.js"); ?>
	
</script>