<?php
if (!empty($_GET["opcion_arti"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();
	foreach($_GET as $k => $d) $$k=$d;

	require_once("library/conexion_mysql_tienda.php");

	switch($opcion_arti) {
		case "get_info":
			$res1=MyQUERY_Tienda($mysqli_tienda,"select
				CONCAT(LPAD(ITEM_ID,7,' '),
				LPAD(POS_ITEM_ID,16,' '),
				SUBSTRING((10 - ((((SUBSTRING(POS_ITEM_ID FROM 2 FOR 1) + SUBSTRING(POS_ITEM_ID FROM 4 FOR 1) + SUBSTRING(POS_ITEM_ID FROM 6 FOR 1) + SUBSTRING(POS_ITEM_ID FROM 8 FOR 1) + SUBSTRING(POS_ITEM_ID FROM 10 FOR 1) + SUBSTRING(POS_ITEM_ID FROM 12 FOR 1))*3) + (SUBSTRING(POS_ITEM_ID FROM 1 FOR 1) + SUBSTRING(POS_ITEM_ID FROM 3 FOR 1) + SUBSTRING(POS_ITEM_ID FROM 5 FOR 1) + SUBSTRING(POS_ITEM_ID FROM 7 FOR 1) + SUBSTRING(POS_ITEM_ID FROM 9 FOR 1) + SUBSTRING(POS_ITEM_ID FROM 11 FOR 1) )) MOD 10)) FROM -1 FOR 1),LPAD(LPAD(QUANTITY,2,'0'),5,' ')) ''
				from POS_IDENTITY
				WHERE item_id = $item_id order by ITEM_ID");

			echo "<pre>";
			echo "COD. INTERNO CODIGO EAN UNIDADES\n";
			echo "<hr>";
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
	echo '
		<div id="Info">
			<div style="width:400px; float:left;">
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
	var Titulo="LISTADO DE EANES DE ARTICULOS";
	
	Desbloqueo();
	
	<?php require_once("library/list_arti.js"); ?>
	
</script>