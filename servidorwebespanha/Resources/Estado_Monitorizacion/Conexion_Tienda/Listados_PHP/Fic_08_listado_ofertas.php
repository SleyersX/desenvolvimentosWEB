<?php
if (!empty($_GET["opcion_ofer"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();

	foreach($_GET as $k => $d) $$k=$d;

	require_once("library/conexion_mysql_tienda.php");
	
	switch($opcion_ofer) {
		case "get_info":
			$res1=MyQUERY_Tienda($mysqli_tienda,"
				select
					CONCAT('OFERTA:',LPAD(p.PROMOTION_CODE,6,' '),' TIPO: ',0,' DIA:',
					CONCAT(
						IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 1),0,1),
						IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 2),0,1),
						IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 3),0,1),
						IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 4),0,1),
						IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 5),0,1),
						IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 6),0,1),
						IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 7),0,1))
					),
					p.DESCRIPTION,
					DATE_FORMAT(pvd.BEGIN_DATE,'FEC.INI.:%d/%m/%Y HORA:%h:%i'),
					DATE_FORMAT(pvd.END_DATE  ,'FEC.FIN.:%d/%m/%Y HORA:%h:%i'),
					'',
					CONCAT('Solo ClubDia%: ',IF(p.DIA_CLUB_FLAG,'SI','NO')),
					CONCAT('MARCA CONDICION: ', IF(bct.BRAND_CONDITION_TYPE_CODE=0,'TODOS',IF(bct.BRAND_CONDITION_TYPE_CODE=1,'MARCA DIA%','NO MARCA DIA%'))),
					CONCAT('MARCA REGALO: ', IF(bgt.BRAND_GIFT_TYPE_CODE=0,'TODOS',IF(bgt.BRAND_GIFT_TYPE_CODE=1,'MARCA DIA%','NO MARCA DIA%'))),
					CONCAT('PRIORIDAD DE REDENCION:',LPAD(p.PRIORITY, 3,' ')),
					CONCAT('ETIQUETA ESPECIAL: ', IF(p.SPECIAL_LABEL_FLAG,'SI','NO')),
					CONCAT('OFERTA ACUMULABLE: ', IF(p.ACCUMULATIVE_FLAG,'SI','NO'))

				FROM PROMOTION p
					JOIN PROMOTION_VALIDATION_DATE pvd ON pvd.PROMOTION_ID=p.PROMOTION_ID
					JOIN BRAND_CONDITION_TYPE bct ON bct.BRAND_CONDITION_TYPE_ID=p.BRAND_CONDITION_TYPE_ID
					JOIN BRAND_GIFT_TYPE bgt ON bgt.BRAND_GIFT_TYPE_ID=p.BRAND_GIFT_TYPE_ID

				WHERE PROMOTION_CODE = $Oferta ORDER BY PROMOTION_CODE");

			$res2=MyQUERY_Tienda($mysqli_tienda,"
				SELECT 
					'',
					CONCAT('CONDICION:',LPAD(pc.CONDITION_ORDER,4,' '),' T.REL.: ',crt.DESCRIPTION) '',
					CONCAT('COD: ',LPAD(CONCAT(
						IFNULL(pci.ITEM_ID,''),
						IFNULL((select group_code from PROMOTION_GROUP where group_id=pcg.GROUP_ID),''),
						IFNULL((select MERCHANDISE_HIERARCHY_GROUP_CODE from MERCHANDISE_HIERARCHY_GROUP where MERCHANDISE_HIERARCHY_GROUP_ID=pch.MERCHANDISE_HIERARCHY_GROUP_ID),'')),6,' ')) '',
					CONCAT('BASE: ', IF(pbt.PROMOTION_BASE_TYPE_CODE>1,'CODIGO DE ',''),pbt.DESCRIPTION) '',
					CONCAT('ARTICULOS: ',IF(DIFFERENT_ITEMS_FLAG,'DISTINTOS','TODOS')) '',
					CONCAT('CODIGO EXCLUSION: (N/D)') '',
					CONCAT('BASE EXCLUSION: (N/D)') '',
					CONCAT('FORMATO: ',pft.DESCRIPTION) '',
					CONCAT('CANTIDAD:',LPAD(pc.FORMAT_VALUE,13,' ')) '',
					CONCAT('MINIMO  :',LPAD(pc.MIN_CONDITION_VALUE,13,' ')) '',
					CONCAT('MAXIMO  :',LPAD(pc.MAX_CONDITION_VALUE,13,' ')) ''

				FROM PROMOTION_CONDITION pc
					JOIN CONDITION_REL_TYPE crt ON pc.CONDITION_REL_TYPE_ID=crt.CONDITION_REL_TYPE_ID
					JOIN PROMOTION_FORMAT_TYPE pft ON pft.PROMOTION_FORMAT_TYPE_ID=pc.PROMOTION_FORMAT_TYPE_ID
					JOIN PROMOTION_BASE_TYPE pbt ON pbt.PROMOTION_BASE_TYPE_ID=pc.PROMOTION_BASE_TYPE_ID
					LEFT JOIN PROMOTION_CONDITION_ITEM pci ON pc.PROMOTION_ID=pci.PROMOTION_ID
					LEFT JOIN PROMOTION_CONDITION_GROUP pcg ON pc.PROMOTION_ID=pcg.PROMOTION_ID
					LEFT JOIN PROMOTION_CONDITION_HIERARCHY pch ON pc.PROMOTION_ID=pch.PROMOTION_ID
				WHERE pc.CONDITION_CODE=$Oferta");

			$res3=MyQUERY_Tienda($mysqli_tienda,"
				SELECT 
					'',
					CONCAT('REGALO:',LPAD(pg.GIFT_ORDER,4,' ')) '',
					CONCAT('COD: ',LPAD(CONCAT(
						IFNULL(pgi.ITEM_ID,''),
						IFNULL((select group_code from PROMOTION_GROUP where group_id=pgg.GROUP_ID),''),
						IFNULL((select MERCHANDISE_HIERARCHY_GROUP_CODE from MERCHANDISE_HIERARCHY_GROUP where MERCHANDISE_HIERARCHY_GROUP_ID=pgh.MERCHANDISE_HIERARCHY_GROUP_ID),'')),6,' ')) '',
					CONCAT('BASE: ', IF(pbt.PROMOTION_BASE_TYPE_CODE>1,'CODIGO DE ',''),pbt.DESCRIPTION) '',
					CONCAT('FORMATO: ',pft.DESCRIPTION) '',
					CONCAT('CANTIDAD:',LPAD(pg.FORMAT_VALUE,13,' ')) '',
					CONCAT('ESCALA  :',LPAD(pg.SCALE_FACTOR,13,' ')) '',
					CONCAT('F.LIM.: ','(N/D)') '',
					CONCAT('MAX.    :',LPAD(pg.MAX_GIFT_VALUE,13,' ')) ''

				FROM PROMOTION_GIFT pg
					JOIN PROMOTION_FORMAT_TYPE         pft ON pg.PROMOTION_FORMAT_TYPE_ID=pft.PROMOTION_FORMAT_TYPE_ID
					JOIN PROMOTION_BASE_TYPE           pbt ON pg.PROMOTION_BASE_TYPE_ID=pbt.PROMOTION_BASE_TYPE_ID
					LEFT JOIN PROMOTION_GIFT_ITEM      pgi ON pg.PROMOTION_ID=pgi.PROMOTION_ID
					LEFT JOIN PROMOTION_GIFT_GROUP     pgg ON pg.PROMOTION_ID=pgg.PROMOTION_ID
					LEFT JOIN PROMOTION_GIFT_HIERARCHY pgh ON pg.PROMOTION_ID=pgh.PROMOTION_ID
				WHERE pg.GIFT_CODE=$Oferta");

			echo "<pre>";
			if (empty($res3)) {
				$res3[][]="<p><b> OFERTA CON ERROR EN REGALO </b></p>";
			}
			foreach(array_merge($res1,$res2,$res3) as $k => $d) {
				foreach($d as $d1)
				echo $d1."\n";
			}
			echo "</pre>";
			break;
			
		case "get_list_ofer":
			require_once("library/json_get_list_ofer.php");
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
			<div style="width:600px; float:left;">
				<div class="ui-jqgrid " id="gbox_jqGrid" dir="ltr"><table id="jqGrid" class="F_SIZE_10"></table><div id="jqGridPager"></div></div>
			</div>
			<div id="info_adicional" style="float:left; margin-left:1em; border:1px solid black; border-radius:3px; padding:1em; background-color:azure">
				<span style="background-color:lightgreen; font-size:12px; font-weight:bold;">&#9664 Haga click en un articulo para ver sus datos.</span>
			</div>
		</div>';
?>

<script>
	var url_local="Listados_PHP/<?php echo basename(__FILE__); ?>";
	var IP_Tienda="<?php echo $con_tda->GetIP(); ?>";
	var Es_VELA=<?php echo $con_tda->VELA; ?>;
	var Titulo="LISTADO DE OFERTAS";
	
	Desbloqueo();
	
	<?php require_once("library/list_ofer.js"); ?>
	
</script>
