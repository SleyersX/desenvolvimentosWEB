<?php

function Actualiza_Datos($query, $verbose=false) {
	$Server=$_SERVER['SERVER_ADDR'];
	if ($Server=="10.208.162.17") {
		$Server="10.208.162.6";
		$cmd='mysql soporteremotoweb -e "'.$query.'" -u root -h '.$Server;
		if ($verbose) echo "Comando: $cmd";
		$Resu=shell_exec($cmd);
		if ($Server=="10.208.162.17") sleep(5);
	}
	else
	{
		$Resu=myQUERY($query);
	}
	if ($verbose) echo $Resu;
}

?>