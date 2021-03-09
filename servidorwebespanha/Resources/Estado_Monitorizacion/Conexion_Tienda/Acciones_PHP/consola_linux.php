<?php
Graba_Historico("SE HA ABIERTO CONSOLA LINUX");
echo '<script>
	new_window_open("http://'.$SERVER_ADDR.':8080/?IP='.$con_tda->GetIP().'&caja='.$con_tda->GetPort().'&comandoTerminal=prompt","CONSOLE: '.$con_tda->tienda.'-'.$con_tda->caja.'","_blank");

	</script>';
// echo '
// <iframe scrolling="auto" id="iframe_consola" src="http://'.$SERVER_ADDR.':8080/?IP='.$con_tda->GetIP().'&caja='.$con_tda->GetPort().'&comandoTerminal=prompt"></iframe>
// ';
	// window.open("http://'.$SERVER_ADDR.':8080/?IP='.$con_tda->GetIP().'&caja='.$con_tda->GetPort().'&comandoTerminal=prompt", "_blank");
	//new_window_open("http://'.$SERVER_ADDR.':8080/?IP='.$con_tda->GetIP().'&caja='.$con_tda->GetPort().'&comandoTerminal=prompt","CONSOLE: '.$con_tda->tienda.'-'.$con_tda->caja.'","_blank");
?>