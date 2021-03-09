<?php

if (!empty($_GET["opcion_cupo"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();

	foreach($_GET as $k => $d) $$k=$d;

	require_once("../library/conexion_mysql_tienda.php");
		
	switch($opcion_cupo) {
		case "get_cupo":
		$res0=MyQUERY_Tienda($mysqli_tienda,"select CUPON_ID from CUPON where cupon_code=$CUPON");
		if (count($res0) < 1) {
				echo "No match...";
				exit;
		} else {
				$CUPON_ID=$res0[0][0];
		}
		$sql="select
	CONCAT('CUPON:',LPAD(c.CUPON_CODE,6,' '),' TIPO:',ct.CUPON_TYPE_CODE,' DIA:1111111') '',
	c.DESCRIPTION '',
	DATE_FORMAT(cvd.BEGIN_DATE,'FEC.INI.:%d/%m/%Y HORA:%h:%i') '',
	DATE_FORMAT(cvd.END_DATE  ,'FEC.FIN.:%d/%m/%Y HORA:%h:%i') '',
	'',
	CONCAT('CLIENTE CERO: ', 'TODOS' ) '',
	CONCAT('PAGO TARJETA DIA%: ',IF(c.TENDER_FLAG,'SI','NO')) '',
	CONCAT('MARCA CONDICION: ', IF(bct.BRAND_CONDITION_TYPE_CODE=0,'TODOS',IF(bct.BRAND_CONDITION_TYPE_CODE=1,'MARCA DIA%','NO MARCA DIA%'))) '',
	CONCAT('MARCA REGALO: ', IF(bgt.BRAND_GIFT_TYPE_CODE=0,'TODOS',IF(bgt.BRAND_GIFT_TYPE_CODE=1,'MARCA DIA%','NO MARCA DIA%'))) '',
	'',
	CONCAT('LIMITE USO:',LPAD(c.MAX_REDEMPTION_NUMBER,2,' '),
		' NUME.VECES:',LPAD((SELECT COUNT(*) FROM CUSTOMER_CUPON_REDEMPTION t2 WHERE c.CUPON_ID=t2.CUPON_ID),5,' ')) '',
	CONCAT('PRIORIDAD DE REDENCION:',LPAD(c.PRIORITY, 3,' ')) '',
	CONCAT('TIPO REDENCION: ',
		IF(CUPON_REDEMPTION_LOADER_TYPE_ID=0,'CONFIGURACION TIENDA',
		IF(CUPON_REDEMPTION_LOADER_TYPE_ID=1,'AUTOREDIMIBLE',
		'ENTRADA MANUAL'))) ''
	
	FROM CUPON c
		JOIN CUPON_VALIDATION_DATE cvd ON cvd.CUPON_ID=c.CUPON_ID
		JOIN BRAND_CONDITION_TYPE bct ON bct.BRAND_CONDITION_TYPE_ID=c.BRAND_CONDITION_TYPE_ID
		JOIN BRAND_GIFT_TYPE bgt ON bgt.BRAND_GIFT_TYPE_ID=c.BRAND_GIFT_TYPE_ID
		JOIN CUPON_TYPE ct ON ct.CUPON_TYPE_ID=c.CUPON_TYPE_ID

	WHERE c.CUPON_ID = $CUPON_ID";
	$res1=MyQUERY_Tienda($mysqli_tienda,$sql);

$res2=MyQUERY_Tienda($mysqli_tienda,"select 
	'',
	CONCAT('CONDICION:',LPAD(cc.CONDITION_ORDER,4,' '),' T.REL.: ',crt.DESCRIPTION) '',
	CONCAT('COD: ',LPAD(CONCAT(IFNULL(cci.ITEM_ID,''),IFNULL(cgr.GROUP_CODE,''),IFNULL(cch.MERCHANDISE_HIERARCHY_GROUP_ID,'')),6,' ')) '',
	CONCAT('BASE: ', IF(cbt.PROMOTION_BASE_TYPE_CODE>1,'CODIGO DE ',''),cbt.DESCRIPTION) '',
	CONCAT('FORMATO: ',cft.DESCRIPTION) '',
	'',
	CONCAT('CANTIDAD:',LPAD(FORMAT_VALUE,13,' ')) '',
	CONCAT('MINIMO  :',LPAD(cc.MIN_CONDITION_VALUE,13,' ')) '',
	CONCAT('MAXIMO  :',LPAD(cc.MAX_CONDITION_VALUE,13,' ')) ''

	FROM CUPON_CONDITION cc 
		JOIN CONDITION_REL_TYPE             crt ON cc.CONDITION_REL_TYPE_ID=crt.CONDITION_REL_TYPE_ID
		JOIN PROMOTION_FORMAT_TYPE          cft ON cc.PROMOTION_FORMAT_TYPE_ID=cft.PROMOTION_FORMAT_TYPE_ID
		JOIN PROMOTION_BASE_TYPE            cbt ON cc.PROMOTION_BASE_TYPE_ID=cbt.PROMOTION_BASE_TYPE_ID
		LEFT JOIN CUPON_CONDITION_ITEM      cci ON cc.CUPON_ID=cci.CUPON_ID
		LEFT JOIN CUPON_CONDITION_GROUP     ccg ON cc.CUPON_ID=ccg.CUPON_ID
		LEFT JOIN CUPON_CONDITION_HIERARCHY cch ON cc.CUPON_ID=cch.CUPON_ID
		LEFT JOIN CUPON_GROUPS         		cgr ON cgr.group_id=ccg.group_id
	WHERE cc.CUPON_ID=$CUPON_ID");

$res3=MyQUERY_Tienda($mysqli_tienda,"select 
	'',
	CONCAT('REGALO:',LPAD(GIFT_ORDER,4,' ')) '',
	CONCAT('COD: ',LPAD(CONCAT(IFNULL(cci.ITEM_ID,''),IFNULL(cgr.GROUP_CODE,''),IFNULL(cch.MERCHANDISE_HIERARCHY_GROUP_ID,'')),6,' ')) '',
	CONCAT('BASE: ', IF(pbt.PROMOTION_BASE_TYPE_CODE>1,'CODIGO DE ',''),pbt.DESCRIPTION) '',
	CONCAT('FORMATO: ',pft.DESCRIPTION) '',
	CONCAT('CANTIDAD:',LPAD(FORMAT(FORMAT_VALUE,3),13,' ')) '',
	CONCAT('ESCALA  :',LPAD(FORMAT(SCALE_FACTOR,3),13,' ')) '',
	CONCAT('F.LIM.: ',(select pft1.DESCRIPTION from PROMOTION_FORMAT_TYPE pft1 where cg.PROMOTION_MAX_GIFT_FORMAT_TYPE_ID=pft1.PROMOTION_FORMAT_TYPE_ID)) '',
	CONCAT('MAX.    :',LPAD(FORMAT(MAX_GIFT_VALUE,3),13,' ')) ''

	FROM CUPON_GIFT cg
		LEFT JOIN PROMOTION_FORMAT_TYPE     pft ON cg.PROMOTION_FORMAT_TYPE_ID=pft.PROMOTION_FORMAT_TYPE_ID
		LEFT JOIN PROMOTION_BASE_TYPE       pbt ON cg.PROMOTION_BASE_TYPE_ID=pbt.PROMOTION_BASE_TYPE_ID
		LEFT JOIN CUPON_GIFT_ITEM      cci ON cg.CUPON_ID=cci.CUPON_ID
		LEFT JOIN CUPON_GIFT_GROUP     ccg ON cg.CUPON_ID=ccg.CUPON_ID
		LEFT JOIN CUPON_GIFT_HIERARCHY cch ON cg.CUPON_ID=cch.CUPON_ID
		LEFT JOIN CUPON_GROUPS         cgr ON cgr.group_id=ccg.group_id

	WHERE cg.CUPON_ID=$CUPON_ID");

		
			echo "<pre>";
			if (count($res1)<1) {
				$res1[][]="<div style='border:1px solid blue; background-color: salmon; padding:1em; margin:1em;'>ERROR: ha fallado\nla extracción de datos de\nla BBDD para este articulo.</div>";
			}
			foreach(array_merge($res1,$res2,$res3) as $k => $d) {
				foreach($d as $d1) echo $d1."\n";
			}
			echo "</pre>";
			break;
			
		case "get_list_cupo":
			require_once("../library/json_get_list_cupo.php");
			break;
	}
	@mysqli_close($mysqli_tienda);
	exit;
}

?>