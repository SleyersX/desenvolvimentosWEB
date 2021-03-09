<?php
echo '<script>
	new_window_open("'.$SERVER_SHELLINABOX.'/?IP='.$con_tda->GetIP().'&caja='.$con_tda->GetPort().'&comandoTerminal=mysql","MYSQL: '.$con_tda->tienda.'-'.$con_tda->caja.'");
	</script>';
?>
