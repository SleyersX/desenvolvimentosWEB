VERS=`. /usr/bin/parse_release_info.sh  /usr/local/n2a/etc/release-info.properties`;
function LF() { echo "$1"; };
function MySQL() { mysql n2a -e "$1" | grep -v " row " | awk '{ if ($0 ~ /^: /) {print substr($0,3);} else {print $0}}'; };

[ -z "$D" ] && D=1;
[ -z "$H" ] && H=98;

IN_VERS=`expr match $VERS "^04."`;
ADIC="";
[ $IN_VERS -gt 0 ] && ADIC=", CONCAT('S.P.A:',LPAD(PREVALENCE_FLAG,2,' '),'  S.P: ',IF(PROMOTION_FLAG,'SI','NO'),' S.E: ',IF(EMPLOYEE_DISCOUNT_FLAG,'SI','NO')) ''";

LF "     LISTADO DE CONCESIONES"; 
LF ""; 
LF "DESDE CODIGO: $D";
LF "HASTA CODIGO: $H";
LF"";
LF "---------------------------------"; 
MySQL "select
	'',
	CONCAT('CODIGO CONCESION :', LPAD(t1.PARTY_CODE,6,' ')) '',
	CONCAT('NOMBRE COMERCIAL:') '',
	IFNULL(NAME,'') '',
	CONCAT('DIRECCION COMERCIAL:') '',
	IFNULL(ADDRESS,'') '',
	CONCAT('LOCALIDAD:') '',
	IFNULL(CITY,'') '',
	CONCAT('PROVINCIA') '',
	IFNULL(COUNTRY,'') '',
	CONCAT('C.I.F.:    ',IFNULL(FISCAL_IDENTIFICATION,'')) '',
	CONCAT('T.C: ',t2.PARTY_TYPE_CODE,' S.V: ',IF(SALE_FLAG,'SI','NO'),' S.C: ', IF(CHARGE_FLAG,'SI','NO')) ''
	$ADIC
	FROM PARTY t1
		JOIN PARTY_TYPE t2 ON t1.PARTY_TYPE_ID=t2.PARTY_TYPE_ID
	WHERE PARTY_CODE BETWEEN $D AND $H ORDER BY PARTY_CODE\G";