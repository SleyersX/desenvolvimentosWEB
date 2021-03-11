<?php

foreach($_POST as $k => $d) { $$k=$d;}

if(!($con = ssh2_connect($ip, $port))){
	die('No se puede conectar con la máquina '.$ip);
} else {
	//Autentificación
	if(!ssh2_auth_password($con, "root", "root")) {
		die('Fallo de autentificación en la máquina '.$ip);
	} else {
		//Ejecución del comando
		if(!($stream = ssh2_exec($con, $comando )) ){
			die('Fallo de ejecución de comando '.$comando.' en la máquina '.$ip);
		} else {
			echo "Ejecutado comando $comando"; flush();
			stream_set_blocking( $stream, true );
			$data = "";
			while( $buf = fread($stream,4096) ){
			$data .= $buf;
				echo utf8_encode(json_encode($buf));
				flush();
				@ob_flush();
			}
			fclose($stream);
		}
	}
}

?>