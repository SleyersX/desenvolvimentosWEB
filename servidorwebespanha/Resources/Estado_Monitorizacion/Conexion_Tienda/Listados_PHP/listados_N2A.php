<script language="javascript">
	function ejecuta_listado(Valor) {
		Alert("Ejecutamos "+Valor);
	}
</script>

<?php

function Desde_Hasta ($Item, $Value, $Opcion) {
	$tmp="<p>DESDE $Item: <input type='text' name='Desde' id='Desde' /><br>";
	$tmp.="HASTA $Item: <input type='text' name='Hasta' id='Hasta' /><br></p>";
	$tmp.="<input type='submit'
			onkeyup=\"if (event.keyCode == 13) ejecuta_listado('$SubActual')\"
			onclick=\"ejecuta_listado('$SubActual');\" 
			name='myListados' value='$Opcion' autofocus/>";
	$tmp.="<input type='HIDDEN' name='Subaction' value='$Value'/>";
	return $tmp;
}

switch ($_GET['listado']) {
	case "04_listado_de_articulos.php":
		$file="listado_articulos.sh";
		$cmd=file_get_contents("../scripts/$file");
		echo Desde_Hasta("ARTICULO", "list_arti", $myListados);
		break;
	default:
		die("No hay definida peticion");
}
// echo FIELDSET_DATOS($myListados,$Res);
// unset($_POST['Subaction']);
?>