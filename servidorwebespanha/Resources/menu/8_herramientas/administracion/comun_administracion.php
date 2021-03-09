<?php

@session_start();
require($_SERVER['DOCUMENT_ROOT']."/config.php");
$NO_CSS=true;

require($DOCUMENT_ROOT.$DIR_RAIZ."/styles_js/head_1.php");

echo '<link rel="stylesheet" type="text/css" href="./administracion.css" />';
echo '<link rel="stylesheet" type="text/css" href="/Resources/css/tabla2.css" />';

// echo '<script>clearInterval(refresco_pagina);</script>';

function Boton_Borrar($Name) {
	return '<input id="borrar_script" type="submit" name="'.$Name.'"
			onclick="javascript:Borra_Registro(this);" value="X"
			title="Borrar Registro">';
}

function Options_Select($Neutro, $Lista, $PorDefecto="") {
	$Tmp='<option value="">--Seleccione '.$Neutro.' --</option>';
	foreach($Lista as $d) {
		if (is_array($d)) $Valor=$d[0]; else $Valor=$d;
		if (!empty($PorDefecto) && $PorDefecto==$Valor) 
			$Tmp.='<option value="'.$Valor.'" selected>'.$Valor.'</option>';
		else 
			$Tmp.='<option value="'.$Valor.'">'.$Valor.'</option>';
	}
	return $Tmp;
}

require_once("./actualiza_datos.php");

$DIR_ADMINISTRACION=$url_herramientas."/Administracion/";
if ($PAIS_SERVER=="ARG") $DIR_ADMINISTRACION="/".$DIR_ADMINISTRACION;

?>