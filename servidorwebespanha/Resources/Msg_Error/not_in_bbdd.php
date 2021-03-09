<?php
	$Texto="<div class='Aviso Aviso_Rojo'>";
	$Idioma=$_SESSION['Idioma'];
	switch ($Idioma) {
		case "ENG":
			$Texto.='<p>Sorry, but no matches we found in the database.</p>';
			break;
		default:
			$Texto.='<p>Lo sentimos, pero no se han encontrado coincidencias en la base de datos.</p>';
			break;
	}
	if (function_exists("Imprime_Filtro")) $Texto.='<p>'.Imprime_Filtro().'</p>';
	$Texto.="</div>";

	echo $Texto;
?>