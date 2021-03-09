<?php

foreach($_GET as $k => $d) $$k=$d;
$url_local="Listados_PHP/".basename(__FILE__);
if (!empty($con_tda)) {
	$IP_Tienda=$con_tda->GetIP();
	$Es_VELA=$con_tda->VELA;
} else {		
	$IP_Tienda=$_GET['IP_Tienda'];
	$Es_VELA=false;
}

require_once("library/conexion_mysql_tienda.php");
//	var_dump($mysqli_tienda);
$text_conexion=$mysqli_tienda->connect_error;

if (!empty($_GET["opcion_arti"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();

	switch($opcion_arti) {
		case "get_info":
			$CAPT_PESO_SUBTIPO_PESO=",CONCAT('CAPTURA PESO:',IF(ISL.WEIGHT_SCALE_FLAG,'SI','NO'))";
			$res0=MyQUERY_Tienda($mysqli_tienda,"SELECT CONCAT(RPAD(I.ITEM_ID,7,' '), I.DESCRIPTION) FROM ITEM I WHERE I.ITEM_ID=$item_id");
			
			$res1=MyQUERY_Tienda($mysqli_tienda,"SELECT 	
	'' ''
	,IF(I.ITEM_TYPE_ID=2,' **** ARTICULO VIRTUAL ****','') ''
	,CONCAT('TIPO IVA:',T.TAX_CODE,' ',RPAD(IF(IST.WEIGHT_FLAG,'KILOS','UNIDADES'),11,' '), 'VENTA:',IF(I.SALE_FLAG, 'SI','NO')) ''
	,CONCAT('TIPO:',IFNULL(CONCAT(OIT.ORDER_ITEM_TYPE_ID,' ',RPAD(OIT.DESCRIPTION,11,' ')),'NO DISPONIBL '), 'VEN.INTER:', IF(ISL.INTERNAL_ID_SALE_FLAG,'SI','NO')) ''
	,CONCAT('DEPARTAMENTO: ',RPAD(PD.POS_DEPARTMENT_CODE,5,' '),'SECCION: ',HIERARCHY.CODE_SECTION) ''
	,CONCAT('FAMILIA:',RPAD(HIERARCHY.CODE_FAMILY,7,' '),'SUBFAMILIA: ', HIERARCHY.CODE_SUBFAMILY) ''
	,CONCAT('MARCA:',B.BRAND_CODE,' ',B.DESCRIPTION) ''
	,CONCAT('PERTENECE AL SURTIDO: ',IF(IFNULL(AI.ITEM_ID,0) > 0, 'SI','NO')) ''
	,CONCAT('CATEGORIA: ',LPAD(C.CATEGORY_CODE,2,' '),' ',RPAD(C.DESCRIPTION,7,' '),'PEDIDO:',IF(IST.ORDERABLE_FLAG,'SI','NO')) ''
	,CONCAT('PED.MIN:',LPAD(IFNULL(IST.MIN_ORDER,'N/D'),6,' '),' PED.MAX:',LPAD(IFNULL(IST.MAX_ORDER,'N/D'),7,' ')) ''
	,CONCAT('ALM:',LPAD(IFNULL(W.WAREHOUSE_CODE,'N/D'),6,' '), '   D.T.STOCK:',LPAD(IFNULL(IO.STOCK_DAYS_CALC,'N/D'),7,' ')) ''
	,CONCAT('FECHA DST:',IFNULL(DATE_FORMAT(IO.STOCK_DAYS_CALC_DATE,'%d/%m/%Y'),'N/D')) ''
	,CONCAT('CAP.BUL:',LPAD(IFNULL(FORMAT(IST.BULKS_CAPACITY,2),'N/D'),7,' '),'  UNI/BUL:', LPAD(IFNULL(IF(SI.FIXED_UNITS_FLAG = 1, FUI.UNIT_BULKS_QUANTITY, IF(SI.WEIGHT_FLAG = 0, SI.BULKS_QUANTITY, 0)),'N/D'),5,' ')) ''
	
	,CONCAT('FOR.SERVI:',IFNULL(CONCAT(SF.SERVICE_FORMAT_CODE,' ',SF.DESCRIPTION),' N/D')) ''
	,CONCAT('PESO MEDIO/UNIDAD:',LPAD(FORMAT(IF(IST.FIXED_UNITS_FLAG = 1,FUI.STANDARD_WEIGHT,0),3),10,' ')) ''
	,CONCAT('TRANSFORMADO:',IF(0,'SI','NO')) ''
	,CONCAT('ES ENVASE:',IF(0,'SI','NO'),' ES ESQUELETO:',IF(0,'SI','NO')) ''
	,CONCAT('COMODATO:',IF(0,'SI','NO')) ''
	,CONCAT('ENVASE:',LPAD(0,7,' '),' ESQUELETO:',LPAD(0,7,' ')) ''
	
	,CONCAT('PVP.TARI:',LPAD(PRICE.PRICE_AMOUNT_RATE_ACTUAL,9,' '), LPAD(IF(PRICE_NEXT.PRICE_AMOUNT_RATE_NEXT IS NULL, '0.00',PRICE_NEXT.PRICE_AMOUNT_RATE_NEXT),12,' ')) ''
	,CONCAT('PVP.FIDE:',LPAD(PRICE.PRICE_AMOUNT_LOYALTY_ACTUAL,9,' '), 
		LPAD(IF(PRICE_NEXT.PRICE_AMOUNT_LOYALTY_NEXT IS NULL,'0.00',PRICE_NEXT.PRICE_AMOUNT_LOYALTY_NEXT),12,' ')) ''
	,CONCAT('PVP.NO.F:',LPAD(PRICE.PRICE_AMOUNT_NO_LOYALTY_ACTUAL,9,' '), 
		LPAD(IF(PRICE_NEXT.PRICE_AMOUNT_NO_LOYALTY_NEXT IS NULL, '0.00',PRICE_NEXT.PRICE_AMOUNT_LOYALTY_NEXT),12,' ')) ''
	,CONCAT('   P.PVP1:',IF(PRICE.PROMOTION_ACTUAL,'SI','NO'),'      P.PVP2:',IF(PRICE_NEXT.PROMOTION_NEXT, 'SI','NO')) ''
	,CONCAT('PVP.LIBRE:',IF(IP.FREE_PRICE_FLAG,'SI','NO'),'   PVP ESPEC:', IF(IP.SPECIAL_PRICE_FLAG,'SI','NO')) ''
	$CAPT_PESO_SUBTIPO_PESO ''
	,CONCAT('PESO MEDIO/BULTO:',LPAD( FORMAT(IF(IST.WEIGHT_FLAG = 1, IST.BULKS_QUANTITY, 0),3),12,' ')) ''
	,CONCAT('ETIQUETA FyV:', IF(IFL.ITEM_ID,'SI','NO')) ''
	,CONCAT('TIPO ARTICULO PEDIDO:', IFNULL(IO.ITEM_EXPIRATED_TYPE_ID,' N/D')) ''
	,CONCAT('CONTEO MAXIMO:',LPAD(IFNULL(FORMAT(IC.MAX_COUNT,3),'N/D'),9,' ')) ''

FROM
    ITEM I 
LEFT JOIN
    (
        SELECT
            ISC.ITEM_ID AS ITEM_ID,
            ISC.CODE_SECTION AS CODE_SECTION,
            IFA.CODE_FAMILY AS CODE_FAMILY,
            ISB.CODE_SUBFAMILY AS CODE_SUBFAMILY 
        FROM
            ITEM_SECTION ISC 
        INNER JOIN
            ITEM_FAMILY IFA 
                ON IFA.ITEM_ID = ISC.ITEM_ID 
        INNER JOIN
            ITEM_SUBFAMILY ISB 
                ON ISB.ITEM_ID = IFA.ITEM_ID 
        WHERE
            ISC.ITEM_ID = $item_id
    ) HIERARCHY 
        ON I.ITEM_ID = HIERARCHY.ITEM_ID 
LEFT JOIN
    (
        SELECT
            VPRA.ITEM_ID AS ITEM_ID,
            VPRA.PRICE_AMOUNT  AS PRICE_AMOUNT_RATE_ACTUAL,
            VPRA.PROMOTION_STATUS_FLAG AS PROMOTION_ACTUAL,
            VPLCA.PRICE_AMOUNT AS PRICE_AMOUNT_LOYALTY_ACTUAL,
            VPLA.PRICE_AMOUNT  AS PRICE_AMOUNT_NO_LOYALTY_ACTUAL 
        FROM
            PRICE_RATE_ACTUAL VPRA 
        INNER JOIN
            PRICE_LOYALTY_ACTUAL VPLA 
                ON VPLA.ITEM_ID = VPRA.ITEM_ID 
        INNER JOIN
            PRICE_LOYALTY_CARD_ACTUAL VPLCA 
                ON VPLCA.ITEM_ID = VPLA.ITEM_ID 
        WHERE
            VPRA.ITEM_ID = $item_id
    ) PRICE 
        ON I.ITEM_ID = PRICE.ITEM_ID 
LEFT JOIN ITEM_SALE ISL ON ISL.ITEM_ID = I.ITEM_ID 
LEFT JOIN ITEM_PRICE IP ON IP.ITEM_ID = I.ITEM_ID 
LEFT JOIN BRAND B ON B.BRAND_ID = I.BRAND_ID 
LEFT JOIN CATEGORY C ON C.CATEGORY_ID = I.CATEGORY_ID 
LEFT JOIN TAXES T ON T.TAX_ID = ISL.TAX_ID 
LEFT JOIN  POS_DEPARTMENT PD ON PD.POS_DEPARTMENT_ID = I.POS_DEPARTMENT_ID 
LEFT JOIN  ITEM_COUNTS IC ON IC.ITEM_ID = I.ITEM_ID 
LEFT JOIN  ITEM_ORDER IO ON IO.ITEM_ID = I.ITEM_ID 
LEFT JOIN  ITEM_STOCK IST ON IST.ITEM_ID = I.ITEM_ID AND I.ITEM_TYPE_ID = IST.ITEM_TYPE_ID 
LEFT JOIN ITEM_STOCK_BULKS_MAX_ORDER SI ON SI.ITEM_ID = I.ITEM_ID AND SI.ITEM_TYPE_ID = I.ITEM_TYPE_ID
LEFT JOIN  (SELECT DISTINCT ITEM_ID FROM ASSORTMENT_ITEMS) AI ON AI.ITEM_ID = I.ITEM_ID 
LEFT JOIN  SERVICE_FORMAT SF ON SF.SERVICE_FORMAT_ID = IST.SERVICE_FORMAT_ID 
LEFT JOIN  FIXED_UNIT_ITEM FUI ON FUI.ITEM_ID = IST.ITEM_ID AND FUI.FIXED_UNITS_FLAG = IST.FIXED_UNITS_FLAG 
LEFT JOIN (SELECT VPRN.ITEM_ID AS ITEM_ID, VPRN.PRICE_AMOUNT AS PRICE_AMOUNT_RATE_NEXT, VPRN.PROMOTION_STATUS_FLAG AS PROMOTION_NEXT, VPLCN.PRICE_AMOUNT AS PRICE_AMOUNT_LOYALTY_NEXT, VPLN.PRICE_AMOUNT AS PRICE_AMOUNT_NO_LOYALTY_NEXT  FROM PRICE_RATE_NEXT VPRN
LEFT JOIN  PRICE_LOYALTY_NEXT VPLN ON VPLN.ITEM_ID = VPRN.ITEM_ID 
LEFT JOIN  PRICE_LOYALTY_CARD_NEXT VPLCN ON VPLCN.ITEM_ID = VPLN.ITEM_ID WHERE VPRN.ITEM_ID = $item_id ) PRICE_NEXT ON I.ITEM_ID = PRICE_NEXT.ITEM_ID 
LEFT JOIN  ITEM_FRESH_LABEL IFL ON I.ITEM_ID = IFL.ITEM_ID
LEFT JOIN  WAREHOUSE W ON W.WAREHOUSE_ID = IST.WAREHOUSE_ID 
LEFT JOIN  ORDER_ITEM_TYPE OIT ON OIT.ORDER_ITEM_TYPE_ID = IST.ORDER_ITEM_TYPE_ID 

WHERE I.ITEM_ID = $item_id");

			$res2=MyQUERY_Tienda($mysqli_tienda,"SELECT
				CONCAT(RPAD(IFNULL(SF.DESCRIPTION,''),10,' '),':',LPAD(FORMAT(IFNULL(IBSF.BULKS_BY_SERVICE_FORMAT,0),3),6,' ')) ''
				FROM ITEM I 
					LEFT JOIN ITEM_BULKS_SERVICE_FORMAT IBSF ON IBSF.ITEM_ID = I.ITEM_ID AND IBSF.ITEM_TYPE_ID = I.ITEM_TYPE_ID 
					LEFT JOIN SERVICE_FORMAT SF ON SF.SERVICE_FORMAT_ID = IBSF.SERVICE_FORMAT_ID 
				WHERE
					I.ITEM_ID = $item_id");
			$res3=MyQUERY_Tienda($mysqli_tienda,"SELECT
				CONCAT('COD.ARANC.:') '',
				CONCAT('GRADOS ALCOHOL:',LPAD(FORMAT(IFNULL(ILA.ALCOHOLIC_PROOF,0),2),7,' '),'   CENT:',IF(IFNULL(ILA.CENTRALIZE_STATUS_TYPE_ID,0),'SI','NO')) '',
				CONCAT('BULTO MIXTO: ',IFNULL(IF(I.MIX_BULK_FLAG,'SI','NO'),'N/D')) '',
				IFNULL(IF(I_S.SCALE_ITEM_TYPE_ID IS NULL,'NO BALANZA DE SECCION',CONCAT('BALANZA SECCION: ', IF(I_S.SCALE_ITEM_TYPE_ID=1,'VENTA ASISTIDA','AUTOSERVICIO'))),'N/D') ''
				FROM ITEM I
					LEFT JOIN ITEM_LABEL_A4 ILA ON ILA.ITEM_ID=I.ITEM_ID
					LEFT JOIN ITEM_SALE I_S ON I_S.ITEM_ID=I.ITEM_ID
				WHERE
					I.ITEM_ID=$item_id");
			$res4=MyQUERY_Tienda($mysqli_tienda,"SELECT CONCAT('GESTION CADUCIDAD: ', IFNULL(IF(EXPIRATION_MANAGEMENT_FLAG,'SI','NO'),'N/D')) '' FROM ITEM WHERE ITEM_ID=$item_id");

			echo "<pre>";
			if (count($res1)<1) {
				$res1[][]="<div style='border:1px solid blue; background-color: salmon; padding:1em; margin:1em;'>ERROR: ha fallado\nla extracción de datos de\nla BBDD para este articulo.</div>";
			}
			foreach(array_merge($res0, $res1,$res2,$res3,$res4) as $k => $d) {
				foreach($d as $d1) echo $d1."\n";
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

<body>
	<div id="Info" style=" display:none">
		<div style="width:400px; float:left;">
			<div class="ui-jqgrid " id="gbox_jqGrid" dir="ltr"><table id="jqGrid" class="F_SIZE_10"></table><div id="jqGridPager"></div></div>
		</div>
		<div id="info_articulo" style="float:left; margin-left:1em; border:1px solid black; border-radius:3px; padding:1em; background-color:azure;">
			<span style="background-color:lightgreen; font-size:12px; font-weight:bold;">&#9664 Haga click en un articulo para ver sus datos.</span>
		</div>
	</div>
</body>

<script>
	var url_local="<?php echo $url_local; ?>";
	var IP_Tienda="<?php echo $IP_Tienda; ?>";
	var Es_VELA="<?php echo $Es_VELA; ?>";
	var Titulo="LISTADO GENERAL DE ARTICULOS";
	var error_conexion="<?php echo $text_conexion; ?>" 

	Desbloqueo();
	if (error_conexion) {
		ERROR_CONEXION(error_conexion);
	} else {	
		$("#Info").show();
		<?php require_once("library/list_arti.js"); ?>
	}
	
</script>