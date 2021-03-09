<?php
//dirname($SCRIPT_FILENAME).
$DIR_ACCIONES_PHP="./Acciones_PHP/";
$DIR_LISTADOS_PHP="./Listados_PHP/";
$DIR_DATOS_PHP="./Datos_PHP/";

/////////////////// OBTENEMOS LA LISTA DE SCRIPT GLOBAL PARA EL PAIS.
	$Lista_Scripts=array();
// 	if ($Pais = 'XXX') $Busca_Pais='ESP'; else $Busca_Pais=$Pais;
//	$result=myQUERY("select * from scripts_web where pais like '%$Pais%'");
	$result=myQUERY("select * from scripts_web where pais=1");
	if (count($result) == 0) die(Alert("warning", myGetText("NO_SCRIPTS_PAIS")));
	foreach($result as $d) {
		list($id_script, $script, $tipo, $php, $pais) = $d;
		$Lista_Scripts[$id_script] = array($script, $tipo, $php, $pais);
	}

////////////////// MIRAMOS SI EL GRUPO TIENE LOS PERMISOS PARA EJECUTAR CADA GRUPO DE SCRIPTS...
	$result=myQUERY("select sw_Listados,sw_Acciones,sw_Datos from grupos where grupoID=".$grupo_usuario);
	list($sw_Listados, $sw_Acciones, $sw_Datos) = $result[0];

/////////////////// SACAMOS LOS SCRIPTS PARTICULARES DE CADA USUARIO/GRUPO.
	$s_x_g=myQUERY("select id_script from scripts_x_grupo where id_grupo=".$grupo_usuario." AND Valor=1");
	$s_x_u=myQUERY("select id_script from scripts_x_usuario where id_usuario=".$id_usuario." AND Valor=1");

////////////////// JUNTAMOS TODOS LOS SCRIPTS EN UN SOLO ARRAY
	$scripts_total = array();
	if (count($s_x_g) > 0) foreach($s_x_g as $d) $scripts_total[]=$d[0];
	if (count($s_x_u) > 0) foreach($s_x_u as $d) $scripts_total[]=$d[0];

	foreach($Lista_Scripts as $id => $sc) {
		if (in_array($id, array_values($scripts_total))) {
			if ($sw_Listados == 1 && $sc[1] == "Listados") $Lista_Listados[$sc[0]]=$DIR_LISTADOS_PHP.$sc[2];
			if ($sw_Acciones == 1 && $sc[1] == "Acciones") $Lista_Acciones[$sc[0]]=$DIR_ACCIONES_PHP.$sc[2];
			if ($sw_Datos == 1 && $sc[1] == "Datos") $Lista_Datos[$sc[0]]=$DIR_DATOS_PHP.$sc[2];
		}
	}

/*
	echo "<pre>";
	print_r($Lista_Listados);
	print_r($Lista_Datos);
	print_r($Lista_Datos);
	var_dump($id_usuario,$usuario);
	echo "</pre>";
*/

$MENU_LISTADOS='<a>LISTADOS<span class="flecha">&#9660</span></a><ul>';
$MENU_LISTADOS.='<li><a src="./Listados_PHP/listados_generales.php">Listados de Articulos</a></li>';
$MENU_LISTADOS.='<li><a src="./Listados_PHP/listados_promociones.php">Listados de Cupones y Ofertas</a></li>';
/*foreach($Lista_Listados as $k => $d)
	$MENU_LISTADOS.='<li><a src="'.$d.'">'.$k.'</a></li>';
*/
$MENU_LISTADOS.='</ul>';

$MENU_ACCIONES='<a>ACCIONES<span class="flecha">&#9660</span></a><ul>';
foreach($Lista_Acciones as $k => $d)
	$MENU_ACCIONES.='<li><a src="'.$d.'">'.$k.'</a></li>';
$MENU_ACCIONES.='</ul>';

$MENU_DATOS='<a>DATOS<span class="flecha">&#9660</span></a><ul>';
foreach($Lista_Datos as $k => $d)
	$MENU_DATOS.='<li><a src="'.$d.'">'.$k.'</a></li>';
$MENU_DATOS.='</ul>';

$MENU_HERRAMIENTAS='<a>HERRAMIENTAS<span class="flecha">&#9660</span></a><ul>';
/*
$MENU_HERRAMIENTAS.='
<li><a>Hola1<span class="flecha">&#9660</span>
	<ul>
		<li><a>Hola1.1</a></li>
		<li><a>Hola1.2</a></li>
	</ul>
</li>
</a>';
*/
//foreach($Lista_Herramientas as $k => $d)
//	$MENU_HERRAMIENTAS.='<li><a src="'.$d.'">'.$k.'</a></li>';
$MENU_HERRAMIENTAS.='</ul>';


if (isset($Lista_Listados)) ksort($Lista_Listados);
?>