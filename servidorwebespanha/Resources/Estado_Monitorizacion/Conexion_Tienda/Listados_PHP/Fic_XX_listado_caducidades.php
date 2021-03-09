<title>CADUCIDADES</title>
<?php
$Sub_Actual="list_caducidades";

$fp = fopen($DOCUMENT_ROOT.$_SESSION["DIR_TMP"].'/caducidades.csv', 'w');
$Listado = $con_tda->cmdExec("
rm -f /tmp/caducidades.csv
mysql n2a -e \"
select
	a.ITEM_ID 'Articulo',
	IF(a.EXPIRATION_DATE is NULL,'Pdte.Gestion',DATE_FORMAT(a.EXPIRATION_DATE,'%d/%m/%Y')) 'Fecha Caducidad',
	b.DESCRIPTION 'Accion de Retirada',
	a.NUM_DAYS_USEFUL_LIFE 'Vida Util',
	IF(a.MANAGEMENT_FLAG,'SI','NO') 'Gest.Manual',
	IF(a.MANAGEMENT_DATE is NULL,'Pdte.Gestion',DATE_FORMAT(a.MANAGEMENT_DATE,'%d/%m/%Y')) 'Fecha Gestion',
	c.RETURN_DAYS 'Dias de Retirada'
from ITEM_EXPIRATION_DATE a
	join ACTION_EXPIRATION_DATE b ON a.ACTION_EXPIRATION_DATE_CODE=b.ACTION_EXPIRATION_DATE_CODE
	join ITEM_LABEL c ON a.ITEM_ID=c.ITEM_ID
where a.ITEM_ID in (select ITEM_ID from ITEM where EXPIRATION_MANAGEMENT_FLAG=1)
order by 1
INTO OUTFILE '/tmp/caducidades.csv' FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n'\";
cat /tmp/caducidades.csv
");
$List_Temp=explode("\n",$Listado);
$Tabl_Cadu='<table class="TABLA">';

$Cabe_Cadu=array("Articulo","Fecha Caducidad","Accion de Retirada", "Vida Util","Gestion Manual","Fecha Gestion","Dias Retirada");
fputcsv($fp, $Cabe_Cadu);
$Style_TD="text-align:center; padding: 0em 1em 0em 1em;";

$Tabl_Cadu.="<tr>"; foreach ($Cabe_Cadu as $k1 => $d1) { $Tabl_Cadu.='<th style="'.$Style_TD.'">'.$d1.'</th>';  } ; $Tabl_Cadu.="</tr>";
foreach($List_Temp as $k => $d) {
	if ($d) {
		$Linea=explode("|",$d);
		$Tabl_Cadu.="<tr>";
		foreach ($Linea as $k1 => $d1) { $Tabl_Cadu.='<td style="'.$Style_TD.'">'.$d1.'</td>';  }
		$Tabl_Cadu.="</tr>"; 
		fputcsv($fp, $Linea);
	}
}
$Tabl_Cadu.="</table>"; 
	
$Res="<pre>".$Tabl_Cadu."</pre>";
unset($_POST['Subaction']);

$Grabar_CSV='<a class="button" href="'.$DIR_TMP.'caducidades.csv" title="Permite guardar la tabla de abajo en formato CSV para abrirlo con programas de hoja de calculo">Grabar .csv</a>';

$Res.="</div>";
echo FIELDSET_DATOS($myListados." ".$Repetir_Listado." ".$Grabar_CSV,$Res);
fclose($fp);

?>