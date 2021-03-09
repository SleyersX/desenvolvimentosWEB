<?php
@session_start();
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
$Idioma=$_SESSION['Idioma'];
$Textos1=array(
	"user"    => array("ESP" => "Usuari@:",	"ENG" => "User:"),
);

$Welcome=$Textos1["user"][$Idioma];

$User1=$_SESSION['usuario'].' ('.$_SESSION['nombre_usuario'].')';

echo '<div id="Cabecera_Linea2" style="font-size:80%">
		<span>'.$Welcome.' </span>
		<span>'.$User1.'</span><br>
		<span>IP: '.$_SERVER['REMOTE_ADDR'].'</span> 
	</div>';
?>
