<head>
	<link rel="stylesheet" type="text/css" href="/Resources/css/tabla2.css">
	<style>
		.finished { background-color: cyan;}
		.ongoing { background-color: lightgreen;}
	</style>
</head>

<?php		
/*
if (empty($_GET["MODO"]))
	echo "<pre>",shell_exec("sudo bash cmd_hilos.sh"),"</pre>";
else
	echo shell_exec("sudo awk -F',' '{c+=$2; t+=$3} END { printf \"%d/%d (%3.2f%%)\",c,t,c/t*100}' /home/MULTI/tmp/Hilo_*");
*/
//$time_file=stat("/home/MULTI/tmp/inicio");
//echo "Inicio de proceso: ",print_r($time_file);
echo "Inicio de proceso: ",date ("d/F/Y H:i:s.",filemtime("/home/MULTI/tmp/inicio"));
//echo "Tiempo en ejecucion: " $(mysql -N -e "select timediff(now(),'$time_file')")
#     "ETA: " $(mysql -N -e "select timediff('$time_file',ADDTIME(now(),INTERVAL 1 HOUR))")
//echo

echo "<table class='tabla2'>";
$files=glob("/home/MULTI/tmp/Hilo_*");
$T_Actual=$T_Total=0;
foreach($files as $d) {
	$content=file_get_contents($d);
	@list($Hilo, $Actual, $Total, $Hora, $Comentario) = explode(",",$content);
	if ($Actual==$Total)
		$Clase_Fila="finished";
	else
		$Clase_Fila="ongoing";
	echo "<tr class='".$Clase_Fila."'>";
	echo "<td>".$Hilo."</td>";
	echo "<td>".sprintf("% 3d%% (% 3d/% 3d)", round($Actual/$Total*100,0),$Actual,$Total)."</td>";
	echo "<td>".$Hora."</td>";
	echo "<td>".$Comentario."</td>";
	echo "</tr>";
	$T_Actual+=$Actual;
	$T_Total+=$Total;
}
echo "</table>";
echo "<span>Progreso Actual: ".round($T_Actual/$T_Total*100, 2)."%</span>";

?>