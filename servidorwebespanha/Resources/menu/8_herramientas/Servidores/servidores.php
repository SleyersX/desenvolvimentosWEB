<?php
$Modo_Lite=true;
require($_SERVER['DOCUMENT_ROOT']."/config.php");

if (isset($auto_entra)) {
	if ($auto_entra == "show_slave_status") {
		$tmp=shell_exec('mysql soporteremotoweb -u root -e "show slave status\G" | grep -v row');
		$tmp_table=explode("\n",$tmp);
		echo "<table id='table_show_slave_status'>";
		foreach($tmp_table as $d) {
			if (!empty($d)) {
				$linea=explode(":",$d); list($c,$valor)=$linea;
				if (!empty($valor)) echo "<tr><td>".$c."</td><td>".$valor."</td></tr>";
			}
		}
		echo "</table>";
	}
	else if ($auto_entra == "show_hdd_status") {
		$list_servers=array("10.208.162.6","10.208.162.17","10.94.202.121");
		echo "<pre>";
		foreach($list_servers as $d) {
			if ($SERVER_ADDR==$d) $cmd='echo '.$d.'; df -h; echo';
			else	$cmd='echo '.$d.'; sudo ssh soporte@'.$d.' "df -h"; echo';
			echo shell_exec($cmd);
		}
		echo "</pre>";
	}
	else if ($auto_entra == "show_load_status") {
		$list_servers=array("10.208.162.6","10.208.162.17","10.94.202.121","10.105.186.135");
		echo "<pre>";
		foreach($list_servers as $d) {
			$cmd='echo -n CARGA\ '.$d.':\ ;cat /proc/loadavg';
			if ($SERVER_ADDR!=$d) $cmd='sudo ssh soporte@'.$d.' "'.$cmd.'"';
			echo shell_exec($cmd);
		}
		echo "</pre>";
	}
	die();
}

?>
<style>
	#DIV_PADRE {
		background: lightyellow;
		border: 1px solid black;
		border-radius: 4px;
		height:auto;
		word-wrap: break-word;
		font-size:10px;
	}
	#SHOW_SLAVE_STATUS { width:300px; }
/* 	#SHOW_SLAVE_STATUS_PADRE * {  } */
	table #table_show_slave_status { font-size:10px; }
	      #table_show_slave_status td { width:50%; }
	.Titulo_1 {
		margin:0em; text-align:center; background:lightgray;
		border-radius: 4px 4px 0 0;
	}
	.info_1 { margin:.3em;  }
</style>
<body>
<table>
	<tr>
		<td><div id="DIV_PADRE"><div id="SHOW_SLAVE_STATUS"><img src="/img/wait.gif"/></div></div></td>
		<td>
			<div id="DIV_PADRE">
			<h3 class="Titulo_1">Servidor ESP1</h3>
				<pre class="info_1" id="SHOW_HDD_STATUS_ESP1" data="10.208.162.6"></pre>
				<pre class="info_1" id="SHOW_LOAD_STATUS_ESP1"></pre>
			<h3 class="Titulo_1">Servidor ESP2</h3>
				<pre class="info_1" id="SHOW_HDD_STATUS_ESP2" data="10.208.162.17"></pre>
			<h3 class="Titulo_1">Servidor POR1</h3>
				<pre class="info_1" id="SHOW_HDD_STATUS_POR1" data="10.246.64.73"></pre>
			<h3 class="Titulo_1">Servidor ARG1</h3>
				<pre class="info_1" id="SHOW_HDD_STATUS_ARG1" data="10.94.202.121"></pre>
			<h3 class="Titulo_1">Servidor BRA1</h3>
				<pre class="info_1" id="SHOW_HDD_STATUS_BRA1" data=""></pre>
			</div>	
		</td>
		<td><div id="DIV_PADRE"><div id="SHOW_HILOS_STATUS"><img src="/img/wait.gif"/></div></div></td>
	</tr>
</table>
</body>
<script async="async">
clearInterval(interval_show_slave_status);
clearInterval(hdd1); clearInterval(hdd2); clearInterval(hdd3); clearInterval(hdd4); clearInterval(hdd5);
clearInterval(hilos1);
clearInterval(load1);
var interval_show_slave_status;
var hdd1,hdd2,hdd3,hdd4,hdd5;
var hilos1;
var load1;

jQuery(document).ready(function () {
	interval_show_slave_status=en_background("#SHOW_SLAVE_STATUS", '<?php echo get_url_from_local(__FILE__); ?>?auto_entra=show_slave_status',10000);
	hilos1=en_background("#SHOW_HILOS_STATUS", DIR_RAIZ+'/tools/ajax_hilos.php',5000);
	hdd1=en_background("#SHOW_HDD_STATUS_ESP1", DIR_RAIZ+'/tools/ajax_hdd_status.php?Server=10.208.162.6',30000);
	hdd2=en_background("#SHOW_HDD_STATUS_ESP2", DIR_RAIZ+'/tools/ajax_hdd_status.php?Server=10.208.162.17',30000);
	hdd3=en_background("#SHOW_HDD_STATUS_POR1", DIR_RAIZ+'/tools/ajax_hdd_status.php?Server=10.246.64.73',30000);
	hdd4=en_background("#SHOW_HDD_STATUS_ARG1", DIR_RAIZ+'/tools/ajax_hdd_status.php?Server=10.94.202.121',30000);
	hdd5=en_background("#SHOW_HDD_STATUS_BRA1", DIR_RAIZ+'/tools/ajax_hdd_status.php?Server=10.105.186.135',30000);

	load1=en_background("#SHOW_LOAD_STATUS_ESP1", DIR_RAIZ+'/tools/ajax_load.php?Server=10.208.162.6',5000);

// 	var interval_show_hilos_status=setInterval(function() { loadRefresh(id); },5000);
// 	$.ajax({
// 		url: '/ruta/hacia/mi/archivo/archivo.json',
// 		dataType: 'json',
// 		data: data success: function(data) {
// 	}

// 	$(tmp_target).html('<div id="Cargando"><img src="/img/wait.gif"/></div>');
});
</script>
