<?php

if (empty($_GET["opcion"]))
	die();

require_once("/home/soporteweb/tools/mysql.php");

$array = array();
$subArray=array();

switch($_GET["opcion"]) {
	case "warnings":
		$tmp=myQUERY("select w.Tienda, w.Caja, t.centro, w.tipo in (1,2,3), w.Warning, LastM from WARNING_ESP w JOIN tiendas t ON t.numerotienda=w.tienda and t.pais='ESP' and date(LastM)=date(now()) WHERE w.tienda<>0 order by w.tipo, t.centro, w.tienda, w.caja");
		foreach($tmp as $k => $d) {
			list($subArray[tienda], $subArray[caja], $subArray[centro], $subArray[tipo], $subArray[warning], $subArray[lastm])=$d;
			$array[] =  $subArray ;
		}
		echo'{"WARNINGS":'.json_encode($array).'}';
		break;
	case "warnings_g":

		$array_tipos=array(
			"2"=>"Errores fisicos en disco duro",
			"3"=>"Disco duro a punto de llenarse",
			"20"=>"Aplicacion parada",
			"21"=>"CDManager parado",
			"30"=>"Hora incorrecta",
			"60"=>"No hay acceso a PC"
		);	
	
		$tmp=myQUERY("select Tipo,count(*) from WARNING_ESP group by tipo");
		foreach($tmp as $k => $d) {
			list($tipo, $cantidad)=$d;
			$subArray["Tipo"]=$array_tipos[$tipo];
			$subArray["Cantidad"]=$cantidad; 
			$array[] =  $subArray ;
		}
		echo'{"WARNINGS_G":'.json_encode($array).'}';
}

?>