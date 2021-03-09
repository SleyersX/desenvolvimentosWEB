<?php

$Texto_Informativo="
<div class='Aviso Aviso_New' style='font-size:14px'>
	<p><b>NOTA IMPORTANTE:</b> el hecho de que existan mensajes en las cajas, no implica problemas en la tienda.<p/>
	<p>Hay que revisar aparte la repercusi&oacute;n en la tienda de la existencia de dichos mensajes (cajas en modo local, lentitud de procesos, etc), para poder justificar la existencia de
		una incidencia.</p>
</div>
";
echo $Texto_Informativo;
echo FIELDSET_DATOS("Colas de mensajeria y sesiones activas",
$con_tda->cmdExec('
export CAJAS=$(echo $(mysql n2a -e "select WORKSTATION_ID from WORKSTATION where ACTIVE_STATUS_FLAG=1") | cut -f2- -d\' \');
echo "Mensajeria de las esclavas:";
for caja in $CAJAS; do
	printf "Caja %d: %5d mensajes pendientes - " $caja $(mysql n2a -e "select count(*) \'\' from MESSAGE_OUT" -h caja_$caja);
	echo $(mysql n2a -e "select MESSAGE_OUT_ID \'\',ACTION \'\',FROM_UNIXTIME(CREATION_DATE/1000) \'\' from MESSAGE_OUT limit 1" -h caja_$caja);
done;
echo;
mysql n2a -e "select BROKER \'\',QUEUE \'\',WORKSTATION_ID \'\',ACKED_ID \'\' from MESSAGE_SERVER where BROKER=\'ServiceBroker\'";
echo -e "\n\nSESIONES ACTIVAS:";

for caja in $CAJAS; do
	echo -e "------------------------------------------------------------------------";
	mysql n2a -e "select WORKSTATION_ID \'<b>CAJA</b>\', RPAD(DESCRIPTION, 19,\' \') \'<b>TYPE               </b>\', OPERATOR_ID \'<b>CAJER@</b>\', BEGIN_DATE \'<b>F. INICIO          </b>\', IF(END_DATE IS NULL,\'(Pendiente)\',END_DATE) \'<b>F. FIN</b>\' from TRANSACTION T1, TRANSACTION_TYPE T2 where T1.TRANSACTION_TYPE_ID in (10,11,21,24) and (BEGIN_DATE >= DATE(NOW()) OR END_DATE IS NULL) AND T1.TRANSACTION_TYPE_ID=T2.TRANSACTION_TYPE_ID and WORKSTATION_ID=$caja order by 1,4";
done
'));
?>