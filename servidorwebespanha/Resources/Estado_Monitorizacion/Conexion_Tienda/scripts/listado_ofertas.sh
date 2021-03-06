function LF() { echo "$1"; };
function MySQL() { mysql n2a -e "$1" | grep -v " row " | awk '{ if ($0 ~ /^: /) {print substr($0,3);} else {print $0}}'; };

[ -z "$D" ] && D=0;
[ -z "$H" ] && H=999999;

LF "     LISTADO OFERTAS"; 
LF ""; 
LF "DESDE CODIGO: $D";
LF "HASTA CODIGO: $H";
LF"";

L_OFERTAS=`echo $(mysql n2a -e "select PROMOTION_CODE from PROMOTION where PROMOTION_CODE BETWEEN $D AND $H" | grep -v PROMO)`
[ -z "$L_OFERTAS" ] && echo "NO HAY OFERTAS" && exit

for OFERTA in $L_OFERTAS; do
LF "---------------------------------"; 
MySQL "select
	CONCAT('OFERTA:',LPAD(p.PROMOTION_CODE,6,' '),' TIPO: ',0,' DIA:',
	CONCAT(
		IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 1),0,1),
		IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 2),0,1),
		IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 3),0,1),
		IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 4),0,1),
		IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 5),0,1),
		IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 6),0,1),
		IF((SELECT CASE WHEN (COUNT(tb6.STATUS_FLAG)=1 OR tb6.STATUS_FLAG IS NOT NULL) THEN tb6.STATUS_FLAG ELSE '0' END FROM PROMOTION_VALIDATION_DAY tb6 WHERE tb6.PROMOTION_ID = p.PROMOTION_ID AND tb6.VALIDATION_DAY = 7),0,1))
) '',
	p.DESCRIPTION '',
	DATE_FORMAT(pvd.BEGIN_DATE,'FEC.INI.:%d/%m/%Y HORA:%h:%i') '',
	DATE_FORMAT(pvd.END_DATE  ,'FEC.FIN.:%d/%m/%Y HORA:%h:%i') '',
	'',
	CONCAT('Solo ClubDia%: ',IF(p.DIA_CLUB_FLAG,'SI','NO')) '',
	CONCAT('MARCA CONDICION: ', IF(bct.BRAND_CONDITION_TYPE_CODE=0,'TODOS',IF(bct.BRAND_CONDITION_TYPE_CODE=1,'MARCA DIA%','NO MARCA DIA%'))) '',
	CONCAT('MARCA REGALO: ', IF(bgt.BRAND_GIFT_TYPE_CODE=0,'TODOS',IF(bgt.BRAND_GIFT_TYPE_CODE=1,'MARCA DIA%','NO MARCA DIA%'))) '',
	CONCAT('PRIORIDAD DE REDENCION:',LPAD(p.PRIORITY, 3,' ')) '',
	CONCAT('ETIQUETA ESPECIAL: ', IF(p.SPECIAL_LABEL_FLAG,'SI','NO')) '',
	CONCAT('OFERTA ACUMULABLE: ', IF(p.ACCUMULATIVE_FLAG,'SI','NO')) ''
	
	FROM PROMOTION p
		JOIN PROMOTION_VALIDATION_DATE pvd ON pvd.PROMOTION_ID=p.PROMOTION_ID
		JOIN BRAND_CONDITION_TYPE bct ON bct.BRAND_CONDITION_TYPE_ID=p.BRAND_CONDITION_TYPE_ID
		JOIN BRAND_GIFT_TYPE bgt ON bgt.BRAND_GIFT_TYPE_ID=p.BRAND_GIFT_TYPE_ID

	WHERE PROMOTION_CODE = $OFERTA ORDER BY PROMOTION_CODE\G";

MySQL "SELECT 
	'',
	CONCAT('CONDICION:',LPAD(pc.CONDITION_ORDER,4,' '),' T.REL.: ',crt.DESCRIPTION) '',
	CONCAT('COD: ',LPAD(CONCAT(IFNULL(pci.ITEM_ID,''),IFNULL(pcg.GROUP_ID,''),IFNULL(MERCHANDISE_HIERARCHY_GROUP_ID,'')),6,' ')) '',
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
	WHERE pc.CONDITION_CODE=$OFERTA\G";

MySQL "SELECT 
	'',
	CONCAT('REGALO:',LPAD(pg.GIFT_ORDER,4,' ')) '',
	CONCAT('COD: ',LPAD(CONCAT(IFNULL(pgi.ITEM_ID,''),IFNULL(pgg.GROUP_ID,''),IFNULL(pgh.MERCHANDISE_HIERARCHY_GROUP_ID,'')),6,' ')) '',
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
	WHERE pg.GIFT_CODE=$OFERTA\G";


done