<?php
if (!isset($_GET['tabla'])) {
	echo '<pre>'.shell_exec('mysql soporteremotoweb -u root -e "show slave status\G" | grep -v row').'</pre>';
}
else {
	$tmp=shell_exec('mysql soporteremotoweb -u root -e "show slave status\G" | grep -v row');
	$tmp_table=explode("\n",$tmp);
	echo "<table id='table_show_slave_status'>";
	foreach($tmp_table as $d) {
		$linea=explode(":",$d); list($c,$valor)=$linea;
		if (!empty($valor)) echo "<tr><td>".$c."</td><td>".$valor."</td></tr>";
	}
	echo "</table>";
}
?>