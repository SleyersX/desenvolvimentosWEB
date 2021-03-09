<?php
$Sub_Actual="list_cucl";
$Item = "CUPON";

$N_Reg=$con_tda->cmdExec("mysql n2a -e \"select count(*) from CUSTOMER_CUPON\" | tail -1");
$Res="<h2>Numero de registros total: $N_Reg</h2>";

if (@$Subaction!=$Sub_Actual) {
	$Res.=Desde_Hasta($Item, $Sub_Actual, $myListados);
} else {
	$Listado = $con_tda->cmdExec("
echo   \"   LISTADO CUPONES / CLIENTES   \"
echo
echo \"DESDE CUPON:              \"".sprintf("% 6d",$Desde)."
echo \"HASTA CUPON:              \"".sprintf("% 6d",$Hasta)."
echo
echo \"select CONCAT(cpd.CUPON_PRINT_DATA_CODE, '  ', cc.CUSTOMER_CODE, '   ', LPAD(ct.CUPON_TYPE_CODE,2,' '), LPAD(cc.PRINT_PRIORITY,3,' '), LPAD(cc.TEMPLATE_PRIORITY,3,' '), LPAD(cc.CUSTOMER_CUPON_STATUS_ASSIGNMENT_TYPE_ID,3,' ')) ' CUPON:  CLIENTE    TC PC PP IM \n--------------------------------' from CUSTOMER_CUPON cc, CUPON_PRINT_DATA cpd, CUPON_TYPE ct where cc.CUPON_PRINT_DATA_ID=cpd.CUPON_PRINT_DATA_ID and cc.CUPON_TYPE_ID=ct.CUPON_TYPE_ID AND cpd.CUPON_PRINT_DATA_CODE BETWEEN $Desde AND $Hasta order by 1\" | mysql n2a
");
	$Res.="<pre>".$Listado."</pre>";
	unset($_POST['Subaction']);
}
$Res.="</div>";
echo FIELDSET_DATOS($myListados." ".$Repetir_Listado,$Res);
?>
