<?php
$Sub_Actual="list_arti_conc";
$Item = "ARTICULO";

if (preg_match("/^06/",$con_tda->version)) {
	echo "<div class='Aviso Aviso_Rojo'>LISTADO NO VALIDO PARA VERSION ACTUAL DE ESTA TIENDA</div>";
} else 

{

$N_Reg=$con_tda->cmdExec("mysql n2a -e \"select count(*) from PARTY_ITEM\" | tail -1");
$Res="<h2>Numero de registros total: $N_Reg</h2>";
$Res.="<p><BIG>NOTA:</big> Este listado solo es valido para tiendas en version <b>N2A.04</b></p>";

if (@$Subaction!=$Sub_Actual) {
	$Res.=Desde_Hasta($Item, $Sub_Actual, $myListados);
} else {
	$Listado = $con_tda->cmdExec("
DESDE=$Desde
HASTA=$Hasta
echo \"  LISTA . ARTICULOS DE CONCESION \";
echo \"\"
printf \"DESDE CODIGO:             %06d\n\" $Desde
printf \"HASTA CODIGO:             %06d\n\" $Hasta
echo \"\"
echo \"--------------------------------\"
echo
mysql n2a -e \"SELECT 
CONCAT(LPAD(IP.ITEM_ID,6,' '),' ', I.DESCRIPTION) '',
CONCAT('IVA: ',T.TAX_CODE,'   TRAT:', IF(SI.WEIGHT_FLAG=1,'PESO','PRECIO')) '',
CONCAT('VENDIBLE:',IF(I.SALE_FLAG=1,'SI ','NO '), 'TIPO.CONC:',LPAD(PT.PARTY_TYPE_CODE,4,' ')) '',
CONCAT('FAM',ISF.CODE_FAMILY,' SECC ',ISC.CODE_SECTION, ' SUB-FA:',LPAD(ISS.CODE_SUBFAMILY,5,' ')) '',  
CONCAT('PVP:',LPAD(FORMAT(PRA.PRICE_AMOUNT,2),23,' ')) '',
CONCAT('PVP FIDELIZADO   :',LPAD(FORMAT(PLCA.PRICE_AMOUNT,2),9,' ')) '',
CONCAT('PVP NO FIDELIZADO:',LPAD(FORMAT(PLA.PRICE_AMOUNT,2),9,' ')) '',
''

FROM PARTY_ITEM IP  
INNER JOIN ITEM I ON IP.ITEM_ID = I.ITEM_ID  
INNER JOIN ITEM_SECTION ISC ON I.ITEM_ID = ISC.ITEM_ID  
INNER JOIN ITEM_FAMILY ISF ON I.ITEM_ID = ISF.ITEM_ID  
INNER JOIN ITEM_SUBFAMILY ISS ON I.ITEM_ID = ISS.ITEM_ID  
INNER JOIN ITEM_SALE ISA ON I.ITEM_ID = ISA.ITEM_ID  
INNER JOIN TAXES T ON ISA.TAX_ID = T.TAX_ID  
INNER JOIN ITEM_STOCK SI ON SI.ITEM_ID = I.ITEM_ID AND SI.ITEM_TYPE_ID = I.ITEM_TYPE_ID  
INNER JOIN PRICE_RATE_ACTUAL PRA ON PRA.ITEM_ID = IP.ITEM_ID  
INNER JOIN PRICE_LOYALTY_ACTUAL PLA ON PLA.ITEM_ID = IP.ITEM_ID  
INNER JOIN PRICE_LOYALTY_CARD_ACTUAL PLCA ON PLCA.ITEM_ID = IP.ITEM_ID  
INNER JOIN PARTY PTY ON PTY.PARTY_ID = IP.PARTY_ID  
INNER JOIN PARTY_TYPE PT ON PT.PARTY_TYPE_ID = PTY.PARTY_TYPE_ID  
WHERE (IP.ITEM_ID >= 0 AND IP.ITEM_ID <= 999999)  
ORDER BY IP.ITEM_ID\G;\" | grep : | cut -f2- -d':'");

	$Res.="<pre>".$Listado."</pre>";
	unset($_POST['Subaction']);
}

$Res.="</div>";
echo FIELDSET_DATOS($myListados." ".$Repetir_Listado,$Res);
}

?>


