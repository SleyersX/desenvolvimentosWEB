<?php
	if (strtoupper(@$_GET["usuario"]) == "VMA001ES")
		require_once($DOCUMENT_ROOT.$DIR_LIBRERIAS."ssh2/my_ssh2.php");
	else
		require_once($DOCUMENT_ROOT.$DIR_LIBRERIAS."ssh2/ssh2.php");
?>
