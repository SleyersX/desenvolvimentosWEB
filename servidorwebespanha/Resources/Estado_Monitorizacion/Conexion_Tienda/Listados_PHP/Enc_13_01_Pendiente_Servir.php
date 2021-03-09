<?php
if (!empty($_GET["opcion_arti"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();
	require_once("/home/soporteweb/tools/mysql.php");
	foreach($_GET as $k => $d) $$k=$d;
	$mysqli_tienda = new mysqli($IP_Tienda, "root", "", "n2a");
	switch($opcion_arti) {
		case "get_info":
			$ARTICULO=$item_id;
			$res1=MyQUERY_Tienda($mysqli_tienda,"
				SELECT
					CONCAT(LPAD(I.ITEM_ID,6,'0'),' ', RPAD(I.DESCRIPTION,13,' '),' ',LPAD(IO.UNDELIVERED_ORDER_QUANTITY,9,' '),
					' ',
					LPAD(FORMAT(IF(IST.BULKS_BY_SERVICE_FORMAT = 0 OR IF(IST.FIXED_UNITS_FLAG,FUI.UNIT_BULKS_QUANTITY, IST.BULKS_QUANTITY) = 0, 0, IF(IST.FIXED_UNITS_FLAG, FUI.UNIT_BY_STANDARD_WEIGHT/FUI.UNIT_BULKS_QUANTITY, IO.UNDELIVERED_ORDER_QUANTITY/IST.BULKS_QUANTITY)/IST.BULKS_BY_SERVICE_FORMAT),2),9,' ')) '',
					CONCAT('                     ', LPAD(IF(IST.FIXED_UNITS_FLAG, FUI.UNIT_BY_STANDARD_WEIGHT, IST.CURRENT_QUANTITY), 9,' '),
					' ',
					LPAD(FORMAT(IF(IST.BULKS_BY_SERVICE_FORMAT = 0 OR IF(IST.FIXED_UNITS_FLAG,FUI.UNIT_BULKS_QUANTITY, IST.BULKS_QUANTITY) = 0, 0, IF(IST.FIXED_UNITS_FLAG, FUI.UNIT_BY_STANDARD_WEIGHT/FUI.UNIT_BULKS_QUANTITY, IST.CURRENT_QUANTITY/IST.BULKS_QUANTITY)/IST.BULKS_BY_SERVICE_FORMAT),2),9,' ')) ''

				FROM ITEM I  
					INNER JOIN ITEM_ORDER IO ON IO.ITEM_ID = I.ITEM_ID  
					INNER JOIN ITEM_STOCK_BULKS IST ON IST.ITEM_ID = I.ITEM_ID AND IST.ITEM_TYPE_ID = I.ITEM_TYPE_ID  
					LEFT JOIN FIXED_UNIT_ITEM FUI ON FUI.ITEM_ID = IST.ITEM_ID AND FUI.ITEM_TYPE_ID = IST.ITEM_TYPE_ID AND FUI.FIXED_UNITS_FLAG = IST.FIXED_UNITS_FLAG  
					LEFT JOIN DISPLAY_UNIT_ITEM DUI ON DUI.ITEM_ID = I.ITEM_ID  
					LEFT JOIN PARTY_ITEM PI ON PI.ITEM_ID = I.ITEM_ID  
					LEFT JOIN ITEM_COUNTS IC ON IC.ITEM_ID = I.ITEM_ID  
				WHERE
					I.ITEM_ID = $item_id
					AND DUI.ITEM_ID IS NULL  
					AND PI.ITEM_ID IS NULL");

			echo "<pre>";
			echo "      LISTADO PENDIENTE DE SERVIR\n";
			echo "\n";
			echo "        CODIGO: $item_id\n";
			echo "\n";
			echo "CODIGO DESCRIPCION    STK.PDTE   PDTE.FS\n";
			echo "                      STK.TEOR   TEOR.FS\n";
			echo "                       STK.SPC    SPC.FS\n";
			echo "----------------------------------------\n";

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
	var Titulo="LISTADO PENDIENTES DE SERVIR DE ARTICULOS";
	
	Desbloqueo();
	
	<?php require_once("library/list_arti.js"); ?>
	
</script>
