<title>COMPOSICIONES</title>
<?php
$Sub_Actual="list_caducidades";
$File_Res="despiece2.csv";

$fp = fopen($DOCUMENT_ROOT.$_SESSION["DIR_TMP"]."/$File_Res", 'w');
$comando = "rm -f /tmp/$File_Res ;
mysql n2a -e \"
	SELECT CIH.INTERNAL_CODE_COMPOSITION_ITEM AS CODIGO,CIH.ITEM_ID AS ARTICULO_PADRE,
	IF(CIH.TREATMENT_TYPE=1,'PESO','UNIDADES') AS TRATAMIENTO_PADRE, 
	CIH.DESCRIPTION_COMPOSITION AS DESCRIPCION,CID.ITEM_ID AS ARTICULO_HIJO,
	CID.QUANTITY AS PROPORCION_HIJO, IF(CID.TREATMENT_TYPE=1,'PESO','UNIDADES') AS TRATAMIENTO_HIJO, CID.DECREASE AS PORCENTAJE_MERMA
	FROM COMPOSITION_ITEM_HEADER CIH , COMPOSITION_ITEM_DETAIL CID WHERE CIH.INTERNAL_CODE_COMPOSITION_ITEM = CID.INTERNAL_CODE_COMPOSITION_ITEM
	INTO OUTFILE '/tmp/$File_Res' FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n'\";
cat /tmp/$File_Res";
// echo $comando;
$Listado = $con_tda->cmdExec($comando);

$List_Temp=explode("\n",$Listado);
$Tabl_Cadu='<table class="TABLA">';

$Cabe_Cadu=array("CODIGO","ARTICULO<br>DESTINO","TRATAMIENTO<br>DESTINO","DESCRIPCION","ARTICULO<br>ORIGEN","CANTIDAD<br>ORIGEN","TRATAMIENTO<br>ORIGEN","% MERMA<br>ORIGEN");
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

$Grabar_CSV='<a class="button" href="'.$DIR_TMP.$File_Res.'" title="Permite guardar la tabla de abajo en formato CSV para abrirlo con programas de hoja de calculo">Grabar .csv</a>';

$Res.="</div>";
echo FIELDSET_DATOS($myListados." ".$Repetir_Listado." ".$Grabar_CSV,$Res);
fclose($fp);

?>

