<?php
@session_start();

$Opcion=(!empty($_GET['Opcion'])?$_GET['Opcion']:"CHG_SESSION");

switch ($Opcion) {
	case "CHG_SESSION":
		$Var=$_GET['Var']; $Valor=$_GET['Valor'];
		$_SESSION["$Var"] = $Valor;
// 		error_log("Valor cambiado: $Var - ".$_SESSION["$Var"],3,"/tmp/mensajes.log");
		break;
	case "UNSET_SESSION":
		$Var=$_GET['Var'];
		unset($_SESSION["$Var"]);
		break;
}
?>