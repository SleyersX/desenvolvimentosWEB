<?php
$Textos_Formulario=array(
	"txtTitulo" => array("ESP" => "BUSQUEDA DE TIENDAS", "ENG" => "STORES SEARCH"),
	"txtTienda" => array("ESP" => "Tienda", "ENG" => "Store"),
	"txtVersion" => array("ESP" => "Version", "ENG" => "Version"),
	"txtCentro" => array("ESP" => "Centro", "ENG" => "Center"),
	"txtTipo" => array("ESP" => "Tipo", "ENG" => "Type"),
	"txtSubtipo" => array("ESP" => "Subtipo", "ENG" => "Subtype"),
	"txtPoblacion" => array("ESP" => "Poblacion", "ENG" => "Town"),
	"txtCP" => array("ESP" => "Cod.Postal", "ENG" => "Code"),
	"txtProvincia" => array("ESP" => "Provincia", "ENG" => "Province"),
	"txtTelefono" => array("ESP" => "Tel&eacute;fono", "ENG" => "Phone number"),
	"txttipoEtiquetadora" => array("ESP" => "Tipo Etiquetadora", "ENG" => "Print format"),
	"txtVELA" => array("ESP" => "VELA", "ENG" => "VELA"),
	
	"txtBuscar" => array("ESP" => "Buscar", "ENG" => "Search"),
	"txtResetear" => array("ESP" => "Limpiar", "ENG" => "Clean"),
	"txtAvanzada" => array("ESP" => "Avanzada", "ENG" => "Advanced")
);

$Idioma=$_SESSION['Idioma'];

foreach ($Textos_Formulario as $k => $d) { $$k = @$d[$Idioma]; }

?>