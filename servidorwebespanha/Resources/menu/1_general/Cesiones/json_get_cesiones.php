<?php
	$json=array();
	$tmp=file_get_contents("/home/MULTI/tools/lista_tiendas_amazon.dat");
	$TIENDAS_AMAZON=explode("\n",$tmp);

	switch($_GET["opcion"]) {
		case "detalles_tienda":
			$tienda=sprintf("%05d",$_GET["tienda"]);
			$listado_tiendas=shell_exec("cat /home/MULTI/tmp/cesiones/$tienda/*.dat | awk '{print $1,$3}' | uniq -c | awk '{print $1,$2,$3}'");
			$tmp=explode("\n",$listado_tiendas);
			$json["cols"]=array(
				array('label' => 'Tienda', 'type' => 'number'),
				array('label' => 'Fecha', 'type' => 'date'),
				array('label' => 'Cesiones', 'type' => 'number'));
			$rows = array();
			$tabla=array();
			foreach($tmp as $k => $d) {
				if ($d) {
					list($numero,$tienda,$fecha) = explode(" ",$d);
					$t=array();
					$year=substr($fecha, 0, 4); $mes=substr($fecha, 4, 2); $dia=substr($fecha, 6, 2);
					$t[] = array('v' => $tienda);
					$t[] = array('v' => "Date($year,".($mes-1).",$dia)");
					$t[] = array('v' => $numero);
					$rows[] = array('c' => $t);

				}
			}
			break;

		case "listado_tiendas":
			$listado_tiendas=shell_exec("cat $(find /home/MULTI/tmp/cesiones/ -type f) | awk '{print $1}' | uniq -c | awk '{print $1,$2}'");
			$tmp=explode("\n",$listado_tiendas);
			$json["cols"]=array(
				array('label' => 'Tienda', 'type' => 'number'),
				array('label' => 'Total Cesiones', 'type' => 'number'));
			$rows = array();
			foreach($tmp as $k => $d) {
				if ($d) {
					list($numero,$tienda) = explode(" ",$d);
					$t=array();
					$t[] = array('v' => $tienda);
					$t[] = array('v' => $numero);
					$rows[] = array('c' => $t);
				}
			}
			break;
			
		case "agrupado":
			$listado_tiendas=shell_exec("cat $(find /home/MULTI/tmp/cesiones/ -type f) | awk '{ t[$1]++; } END { for (c in t) { print c\",\"t[c] }}' | sort -n");
			$tmp=explode("\n",str_replace("\r","",$listado_tiendas));
			$json["cols"]=array(
				array('label' => 'Tienda', 'type' => 'number'),
				array('label' => 'Cesiones', 'type' => 'number'),
				array('label' => 'Tipo', 'type' => 'string')
			);
			
			$rows = array();
			foreach($tmp as $k => $d) {
				if ($d) {
					list($tienda,$numero) = explode(",",$d);
					if (in_array($tienda,$TIENDAS_AMAZON)) $tipo="AMAZON"; else $tipo="HYBRIS";
					if ($tienda == 955) $tipo="MIXTA";
					$t=array();
					$t[] = array('v' => $tienda);
					$t[] = array('v' => $numero);
					$t[] = array('v' => $tipo);
					$rows[] = array('c' => $t);
				}
			}			
			break;
			
		case "listado":
			$json=array();
			$json["cols"]=array(
				array('label' => 'Tienda', 'type' => 'number'),
				array('label' => 'Fecha', 'type' => 'string'),
				array('label' => 'Cesion', 'type' => 'string'));
			$rows = array();
			foreach($tmp as $k => $d) {
				list($tienda,$numero,$fecha) = explode(" ",$d);
				$t=array();
				$t[] = array('v' => $tienda);
				$t[] = array('v' => $fecha);
				$t[] = array('v' => $numero);
				$rows[] = array('c' => $t);
			}
			break;
	}

	$json['rows'] = $rows;
	header('Content-type: application/json');
	echo json_encode($json, JSON_NUMERIC_CHECK);
?>