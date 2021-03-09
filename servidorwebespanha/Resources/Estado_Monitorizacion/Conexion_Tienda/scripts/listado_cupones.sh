function LF() { echo "$1"; };
function MySQL() { mysql n2a -e "$1" | grep -v " row " | awk '{ if ($0 ~ /^: /) {print substr($0,3);} else {print $0}}'; };

[ -z "$D" ] && D=0;
[ -z "$H" ] && H=999999;

LF "     LISTADO REDENCION CUPONES"; 
LF ""; 
LF "DESDE CUPON: $D";
LF "HASTA CUPON: $H";
LF"";

L_CUPONES=`echo $(mysql n2a -e "select CUPON_ID from CUPON where CUPON_CODE BETWEEN $D AND $H" | grep -v CUPON)`;
[ -z "$L_CUPONES" ] && echo "NO HAY CUPONES" && exit;

for CUPON in $L_CUPONES; do
LF "---------------------------------"; 
MySQL "select
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

	WHERE c.CUPON_ID = $CUPON\G";

MySQL "SELECT 
	'',
	CONCAT('CONDICION:',LPAD(cc.CONDITION_ORDER,4,' '),' T.REL.: ',crt.DESCRIPTION) '',
	CONCAT('COD: ',LPAD(CONCAT(IFNULL(cci.ITEM_ID,''),IFNULL(ccg.GROUP_ID,''),IFNULL(cch.MERCHANDISE_HIERARCHY_GROUP_ID,'')),6,' ')) '',
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
	WHERE cc.CUPON_ID=$CUPON\G";

MySQL "SELECT 
	'',
	CONCAT('REGALO:',LPAD(GIFT_ORDER,4,' ')) '',
	CONCAT('COD: ',LPAD(CONCAT(IFNULL(cci.ITEM_ID,''),IFNULL(cgr.GROUP_CODE,''),IFNULL(cch.MERCHANDISE_HIERARCHY_GROUP_ID,'')),6,' ')) '',
	CONCAT('BASE: ', IF(pbt.PROMOTION_BASE_TYPE_CODE>1,'CODIGO DE ',''),pbt.DESCRIPTION) '',
	CONCAT('FORMATO: ',pft.DESCRIPTION) '',
	CONCAT('CANTIDAD:',LPAD(FORMAT(FORMAT_VALUE,3),13,' ')) '',
	CONCAT('ESCALA  :',LPAD(FORMAT(SCALE_FACTOR,3),13,' ')) '',
	CONCAT('F.LIM.: ','') '',
	CONCAT('MAX.    :',LPAD(FORMAT(MAX_GIFT_VALUE,3),13,' ')) ''

	FROM CUPON_GIFT cg
		LEFT JOIN PROMOTION_FORMAT_TYPE     pft ON cg.PROMOTION_FORMAT_TYPE_ID=pft.PROMOTION_FORMAT_TYPE_ID
		LEFT JOIN PROMOTION_BASE_TYPE       pbt ON cg.PROMOTION_BASE_TYPE_ID=pbt.PROMOTION_BASE_TYPE_ID
		LEFT JOIN CUPON_GIFT_ITEM      cci ON cg.CUPON_ID=cci.CUPON_ID
		LEFT JOIN CUPON_GIFT_GROUP     ccg ON cg.CUPON_ID=ccg.CUPON_ID
		LEFT JOIN CUPON_GIFT_HIERARCHY cch ON cg.CUPON_ID=cch.CUPON_ID
		LEFT JOIN CUPON_GROUPS         cgr ON cgr.group_id=ccg.group_id

	WHERE cg.CUPON_ID=$CUPON\G";

echo;
echo "---------------------------------"
echo "ARTICULOS DEL REGALO";
echo "---------------------------------"
echo;
MySQL "select ITEM_ID 'Articulo',DESCRIPTION 'Descripcion' from ITEM where ITEM_ID in (select item_id from CUPON_GROUP_ITEM where group_id=(select GROUP_ID from CUPON_GIFT_GROUP where CUPON_ID=$CUPON))";

done