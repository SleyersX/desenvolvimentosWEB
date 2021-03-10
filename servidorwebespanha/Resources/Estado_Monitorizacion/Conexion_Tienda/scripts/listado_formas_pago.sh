function LF() { echo "$1"; };
function MySQL() { mysql n2a -e "$1" | grep -v " row " | awk '{ if ($0 ~ /^: /) {print substr($0,3);} else {print $0}}'; };

LF "     LISTADO FORMAS DE PAGO"; 
LF ""; 
LF "---------------------------------"; 
MySQL "select
	IF(0,'    SI','    NO') 'STATUS IMPRESION TALONES ',
	IF(ENVELOPE_FLAG,'    SI','    NO') 'STATUS IMPR.SOBRE RETIRA.',
	IF(START_MONEY_STATUS_FLAG,'    SI','    NO') 'STATUS DINERO APERTURA...',
	IF(0,'    SI','    NO') 'ARQUEO DINERO APERTURA...',
	LPAD(FORMAT(START_MONEY,2),6,' ') 'IMPORTE DINERO APERTURA..'
	from SITE_PARAMETERS_FINANCES\G";
MySQL "select LPAD(MINS_RANGE_TIME, 6,' ') 'MINUTOS TRAMO HORARIO....' from SITE_PARAMETERS_RANGES\G";

MySQL "select IF(0,'    SI','    NO') 'STATUS IMPRESION DEL IVA.' from SITE_PARAMETERS_TICKET\G";

MySQL "select IF(PARTY_SALES_PRINT_FLAG,'    SI','    NO') 'IMPRE. ARTI. CONCESIONES.' from SITE_PARAMETERS_END_DAY\G";
MySQL "select LPAD(FORMAT(MIN_TENDER_LIMIT,2),10,' ') 'IMPORTE MIN. PAGO TEF' from TENDER where TENDER_CODE=11\G";
MySQL "select IF(t1.ALLOWED_PRINT_INVOICE,'     SI','     NO') as 'STATUS IMPR. FACTURAS...' from SITE_PARAMETERS_INVOICE t1\G";
MySQL "select
	t2.DESCRIPTION as ''
	from SITE_PARAMETERS_INVOICE t1
		JOIN PRINT_INVOICE_TYPE t2 ON t1.PRINT_INVOICE_TYPE_ID=t2.PRINT_INVOICE_TYPE_ID\G";
MySQL "select CONCAT('N. MAX. DIRECCIONES ENVIO:',LPAD(LPAD(MAX_NUMBER_DELIVERY_ADDRESS_FISCAL_ENTITY,2,'0'),7,' ')) ''
	from SITE_PARAMETERS_INVOICE\G";

LF"";
MySQL "select CONCAT('IMPORTE FONDO CAJON:',LPAD(FORMAT(MAX_TILL_FUND,2),15,' ')) ''
	from SITE_PARAMETERS_FINANCES\G";

LF "";
MySQL "select
	CONCAT('DENOM. FORMA DE PAGO ',TENDER_CODE,':') '',
	CONCAT(LPAD('',22,' '),DESCRIPTION) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('OPER. VENTA  ',RPAD(DESCRIPTION,12,' '),':    ',IF(SALE_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('OPERAT.RETIR.',RPAD(t1.DESCRIPTION,12,' '),':     ',t2.CARRIED_MANAGE_TYPE_CODE) ''
		from TENDER t1
			JOIN CARRIED_MANAGE_TYPE t2 ON t1.CARRIED_MANAGE_TYPE_ID=t2.CARRIED_MANAGE_TYPE_ID
		WHERE t1.OPERATIVE_FLAG order BY CAST(t1.TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('OPER. ARQUEO ',RPAD(t1.DESCRIPTION,12,' '),':     ',t2.CASHCOUNT_MANAGEMENT_TYPE_CODE) ''
		from TENDER t1
			JOIN CASHCOUNT_MANAGEMENT_TYPE t2 ON t1.CASHCOUNT_MANAGE_TYPE_ID=t2.CASHCOUNT_MANAGEMENT_TYPE_ID
		WHERE t1.OPERATIVE_FLAG order BY CAST(t1.TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('OPE.ARQ.RET. ',RPAD(t1.DESCRIPTION,12,' '),':     ',t2.CASHCOUNT_MANAGEMENT_TYPE_CODE) ''
		from TENDER t1
			JOIN CASHCOUNT_MANAGEMENT_TYPE t2 ON t1.CARRIED_CASHCOUNT_MANAGE_TYPE_ID=t2.CASHCOUNT_MANAGEMENT_TYPE_ID
		WHERE t1.OPERATIVE_FLAG order BY CAST(t1.TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('OPE.AVS.RET. ',RPAD(DESCRIPTION,12,' '),':     ',CARRIED_ADVICE_FLAG) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('STAT.GRABAC. ',RPAD(DESCRIPTION,12,' '),':    ',IF(RECORD_STATUS_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('OPERAT. ROBO ',RPAD(DESCRIPTION,12,' '),':    ',IF(STEAL_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('C. ASISTIDA  ',RPAD(DESCRIPTION,12,' '),':    ',IF(HELPED_CAPTURE_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('C. CAPT. EAN ',RPAD(DESCRIPTION,12,' '),':    ',IF(ENVELOPE_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('O.TRANS/INGR ',RPAD(DESCRIPTION,12,' '),':    ',IF(TRANSPORT_FLAG,'SI','NO')) '',
	CONCAT('   GENERAR DISCO      :') '', 
	CONCAT('   GENERAR TRANSACCION:') ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('VERIFICACION ',RPAD(DESCRIPTION,12,' '),':    ','NO') '',
	CONCAT('     CODIGO AUTORIZADORA :',LPAD('0',7,' ')) '' 
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select 
	CONCAT('IMPORTE AVISO RETIRADAS') '',
	CONCAT('         ',RPAD(DESCRIPTION,12,' '),':',LPAD(FORMAT(CARRIED_ADVICE_AMOUNT,2),11,' ')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('STAT.DEVOL.  ',RPAD(DESCRIPTION,12,' '),':     ',IF(RETURN_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('STAT.ANULAC. ',RPAD(DESCRIPTION,12,' '),':     ',IF(VOID_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('ST.ANU.AUTO. ',RPAD(DESCRIPTION,12,' '),':     ',IF(AUTOMATIC_VOID_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('OPER.P./C./R.',RPAD(DESCRIPTION,12,' '),':     ',IF(FINANCE_ITEM_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('IMP. MINIMO ',RPAD(DESCRIPTION,13,' '),': ',LPAD(FORMAT(MIN_TENDER_LIMIT,1),6,' ')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('IMP. MAXIMO ',RPAD(DESCRIPTION,13,' '),': ',LPAD(FORMAT(MAX_TENDER_LIMIT,1),6,' ')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('N. MAXIMO EFECTO ',RPAD(DESCRIPTION,10,' '),': ',LPAD(MAX_EFFECTS_QUANTITY,3,' ')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select 
	CONCAT('IMPORTE SUPERIOR TOTAL TICKET') '',
	LPAD(CONCAT(RPAD(DESCRIPTION,10,' '),':  ',IF(UP_TOTAL_TICKET_AMOUNT_FLAG,'SI','NO')),33,' ') ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('STAT.CAMBIO ',RPAD(DESCRIPTION,11,' '),':       ',IF(CHANGE_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";
LF "";
MySQL "select
	CONCAT('APERTURA DE CAJON ',RPAD(DESCRIPTION,11,' '),': ',IF(OPEN_TILL_FLAG,'SI','NO')) ''
		from TENDER WHERE OPERATIVE_FLAG order BY CAST(TENDER_CODE AS DECIMAL)\G";