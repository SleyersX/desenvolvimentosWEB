<title>DESPIECE</title>
<?php
$Sub_Actual="list_caducidades";
$File_Res="despiece1.csv";

$fp = fopen($DOCUMENT_ROOT.$_SESSION["DIR_TMP"]."/$File_Res", 'w');
$comando = "rm -f /tmp/$File_Res ;
mysql n2a -e \"
	SELECT EIH.INTERNAL_CODE_EXPLODED_ITEM AS CODIGO,EIH.ITEM_ID AS ARTICULO_PADRE,
	IF(EIH.TREATMENT_TYPE=1,'PESO','UNIDADES') AS TRATAMIENTO_PADRE, 
	IF(EIH.AUTOMATIC_TRANSFORMATION_FLAG=0,'FALSO','TRUE') AS AUTOMATICA,
	EIH.DESCRIPTION_EXPLODED AS DESCRIPCION,EIH.DECREASE AS PORCENTAJE,
	EID.ITEM_ID AS ARTICULO_HIJO,EID.QUANTITY AS PROPORCION,
	IF(EID.TREATMENT_TYPE=1,'PESO','UNIDADES') AS TRATAMIENTO_HIJO
	FROM EXPLODED_ITEM_HEADER EIH , EXPLODED_ITEM_DETAIL EID WHERE EIH.INTERNAL_CODE_EXPLODED_ITEM = EID.INTERNAL_CODE_EXPLODED_ITEM
	INTO OUTFILE '/tmp/$File_Res' FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n'\";
cat /tmp/$File_Res";
// echo $comando;
$Listado = $con_tda->cmdExec($comando);

$List_Temp=explode("\n",$Listado);
$Tabl_Cadu='<table class="TABLA">';

$Cabe_Cadu=array("CODIGO","ARTICULO<br>ORIGEN","TRATAMIENTO<br>ORIGEN","AUTOMATICA","DESCRIPCION","% MERMA<br>TRANSFORMACION","ARTICULO<br>DESTINO","CANTIDAD<br>DESTINO","TRATAMIENTO<br>DESTINO");
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

