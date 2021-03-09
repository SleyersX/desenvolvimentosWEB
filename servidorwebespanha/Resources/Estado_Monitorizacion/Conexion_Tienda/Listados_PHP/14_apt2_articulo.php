<?php
if (!empty($_GET["opcion_arti"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();

	foreach($_GET as $k => $d) $$k=$d;

	require_once("library/conexion_mysql_tienda.php");

	switch($opcion_arti) {
		case "get_info":
			$ARTICULO=$item_id;
			$res1=MyQUERY_Tienda($mysqli_tienda,"SELECT
	CONCAT('<b>',I.ITEM_ID,'  ', I.DESCRIPTION,'</b>') as '',
	'',
	CONCAT('ALMACEN:',LPAD(W.WAREHOUSE_CODE,24,' ')) as '',
	CONCAT('ALTA PEDIDO:', LPAD(IF(IST.ORDERABLE_FLAG=0,'No','Si'),20,' ')) as '',
	CONCAT('SURTIDO:',LPAD(IF(AI.ITEM_ID <> 0,'Si','No'),24,' ')) AS ' ',
	CONCAT('LINEAL: ',LPAD(IL.SHELF,3,' '),'      ALTURA: ', LPAD(IL.HIGH,2,' ')) as '',
	CONCAT('PREVISION:',LPAD(FORMAT(IO.SALES_FORECAST,3),22,' ')) '',
	CONCAT('DST:',LPAD(FORMAT(IO.STOCK_DAYS_CALC,3),28,' ')) '',  
	CONCAT('FECHA DST:',LPAD(DATE_FORMAT(IO.STOCK_DAYS_CALC_DATE,'%d/%m/%Y'),22,' ')) '',
	CONCAT('HORA DST: ',LPAD(DATE_FORMAT(IO.STOCK_DAYS_CALC_DATE,'%H:%i'),22,' ')) '',
	CONCAT('FOR.SERVI:',LPAD(SF.SERVICE_FORMAT_CODE,22,' ')) '',
	CONCAT('CAP.BUL:  ',LPAD(FORMAT(SI.BULKS_CAPACITY,3),22,' ')) '',
	CONCAT('U/K:      ',LPAD(IST.WEIGHT_FLAG,22,' ')) '',
	CONCAT('UNI/BUL:  ',LPAD(IF(SI.FIXED_UNITS_FLAG = 1, FUI.UNIT_BULKS_QUANTITY, IF(SI.WEIGHT_FLAG = 0, SI.BULKS_QUANTITY, 0)),22,' ')) '',
	CONCAT('BUL/FORMATO:',LPAD(FORMAT(IFNULL(SI.BULKS_BY_SERVICE_FORMAT, 0),3),20,' ')) '',  
	CONCAT('PESO M/B: ',LPAD(FORMAT(IF(SI.WEIGHT_FLAG = 1, SI.BULKS_QUANTITY, 0),3),22,' ')) '',
	CONCAT('SUBTIPO PESO:',LPAD(SI.FIXED_UNITS_FLAG, 19,' ')) '',
	CONCAT('TIPO APT2:',LPAD(SI.ORDER_ITEM_TYPE_ID, 22, ' ')) '',
	CONCAT('TIPO PEDIDO:',LPAD(IET.ITEM_EXPIRATED_TYPE_CODE,20,' ')) '',
	CONCAT('PED.MIN:',LPAD(SI.MIN_ORDER,6,' '),'   PED.MAX:', LPAD(SI.MAX_ORDER,7,' ')) '',  
	CONCAT('CONTEO MAXIMO :',LPAD(FORMAT(IC.MAX_COUNT,3),17,' ')) '',
	CONCAT('PROMOCION:',LPAD(IF(PRA.PROMOTION_STATUS_FLAG,'Si','No'),22,' ')) '',
	CONCAT('F. LIMITE DEVOLUCION: ',DATE_FORMAT(IFNULL(SI.RETURN_DATE,'0000-00-00'),'%d/%m/%Y')) '',
	'',
	CONCAT('FR:',LPAD(IF(IFNULL(FR.FR,0) > 0,'Si','No'),29,' ')) '',
	CONCAT('FC:',LPAD(IF(IFNULL(FC.FC,0) > 0,'Si','No'),29,' ')) '',
	CONCAT('FID:',LPAD(IF(IFNULL(FID.FID,0) > 0,'Si','No'),28,' ')) '',
	''

FROM ITEM I  
INNER JOIN ITEM_ORDER IO ON IO.ITEM_ID = I.ITEM_ID  
INNER JOIN ITEM_STOCK IST ON IST.ITEM_ID = I.ITEM_ID AND IST.ITEM_TYPE_ID = I.ITEM_TYPE_ID  
INNER JOIN WAREHOUSE W ON W.WAREHOUSE_ID = IST.WAREHOUSE_ID  
LEFT JOIN ITEM_LOCATIONS IL ON IL.ITEM_ID = I.ITEM_ID  
INNER JOIN ITEM_COUNTS IC ON IC.ITEM_ID = I.ITEM_ID  
INNER JOIN ITEM_EXPIRATED_TYPE IET ON IET.ITEM_EXPIRATED_TYPE_ID = IO.ITEM_EXPIRATED_TYPE_ID  
INNER JOIN ITEM_STOCK_BULKS_MAX_ORDER SI ON SI.ITEM_ID = I.ITEM_ID AND SI.ITEM_TYPE_ID = I.ITEM_TYPE_ID  
INNER JOIN SERVICE_FORMAT SF ON SI.SERVICE_FORMAT_ID = SF.SERVICE_FORMAT_ID  
LEFT JOIN FIXED_UNIT_ITEM FUI ON FUI.ITEM_ID = SI.ITEM_ID AND FUI.FIXED_UNITS_FLAG = SI.FIXED_UNITS_FLAG  
          AND FUI.ITEM_TYPE_ID = I.ITEM_TYPE_ID  
LEFT JOIN RELATED_ITEMS RI ON RI.ITEM_ID_FROM = I.ITEM_ID AND RI.RELATIONSHIP_TYPE_ID = 2 AND RI.DELETE_FLAG = 0  
LEFT JOIN RELATED_ITEMS RI2 ON RI2.ITEM_ID_TO = I.ITEM_ID AND RI2.RELATIONSHIP_TYPE_ID = 2 AND RI2.DELETE_FLAG = 0  
LEFT JOIN (SELECT ITEM_ID, COUNT(*) FR FROM ITEM_CORRECTION_FACTOR ICF WHERE ICF.FACTOR_TYPE_ID = 5 GROUP BY ITEM_ID) FR ON FR.ITEM_ID = I.ITEM_ID  
LEFT JOIN (SELECT ITEM_ID, COUNT(*) FID FROM ITEM_CORRECTION_FACTOR ICF WHERE ICF.FACTOR_TYPE_ID = 6 GROUP BY ITEM_ID) FID ON FID.ITEM_ID = I.ITEM_ID  
LEFT JOIN (SELECT ITEM_ID, COUNT(*) FC FROM ITEM_CORRECTION_FACTOR ICF WHERE ICF.FACTOR_TYPE_ID <> 5 AND ICF.FACTOR_TYPE_ID <> 6 GROUP BY ITEM_ID) FC ON FC.ITEM_ID = I.ITEM_ID  
LEFT JOIN (SELECT DISTINCT ITEM_ID FROM ASSORTMENT_ITEMS) AI ON AI.ITEM_ID = I.ITEM_ID  
INNER JOIN PRICE_RATE_ACTUAL PRA ON PRA.ITEM_ID = I.ITEM_ID  
WHERE I.ITEM_ID = $ARTICULO AND I.ITEM_ID NOT IN (SELECT ITEM_ID FROM DISPLAY_UNIT_ITEM) AND I.ITEM_ID NOT IN (SELECT ITEM_ID FROM PARTY_ITEM)
ORDER BY I.ITEM_ID");

			echo "<pre>";
			foreach($res1 as $k => $d) {
				foreach($d as $d1)
				echo $d1."\n";
			}
			echo "</pre>";
			break;
			
		case "get_list_arti":
			require_once("library/json_get_list_arti.php");
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
if ($con_tda->VELA) {
	echo '
		<div id="Aviso_VELA" class="Aviso Aviso_New" style="font-size:15px; margin-top:50px; ">
			<h3>OPCION NO DISPONIBLE - TIENDA CONFIGURADA CON BACKOFFICE VELA</h3>
			<p>
				Esta opci&oacute;n est&aacute; deshabilitada, porque en TPV no se mantienen estos datos.<br>
				<b>Revisar en VELA.</b>
			</p>
		</div>';
} else {
	echo '
		<div id="Info">
			<div style="width:400px; float:left;">
				<div class="ui-jqgrid " id="gbox_jqGrid" dir="ltr"><table id="jqGrid" class="F_SIZE_10"></table><div id="jqGridPager"></div></div>
			</div>
			<div id="info_articulo" style="float:left; margin-left:1em; border:1px solid black; border-radius:3px; padding:1em; background-color:azure">
				<span style="background-color:lightgreen; font-size:12px; font-weight:bold;">&#9664 Haga click en un articulo para ver sus datos.</span>
			</div>
		</div>';
}

?>
<script>
	var url_local="Listados_PHP/<?php echo basename(__FILE__); ?>";
	var IP_Tienda="<?php echo $con_tda->GetIP(); ?>";
	var Es_VELA=<?php echo $con_tda->VELA; ?>;
	var Titulo="LISTADO DE DATOS DE APT2 DE ARTICULOS";
	
	Desbloqueo();
	
	<?php require_once("library/list_arti.js"); ?>
	
</script>
