<?php
if (!empty($_POST['Modo_Ajax'])) {
	$lite=true;
	$con_tda=new SFTPConnection($ip, $port);
}

if ($con_tda->SA==1) {
	$Patron='-name "error.log" -o -name "error.log.*" -o -name "error.*.log"';
	_ECHO("<ul>");
		_ECHO("<li>Searching files error.log... ");
		$con_tda->cmdExec('cd /confdia; cat $(find . '.$Patron.') > /tmp/error_all.log;');
		_ECHO("Done!</li>");

		_ECHO("<li>Making an unique file...");
		$con_tda->cmdExec('cd /tmp; grep "^[0-9]" error_all.log > error_all_grep.log;');
		_ECHO("Done!</li>");

		_ECHO("<li>Sorting result file...");
		$con_tda->cmdExec('cd /tmp; sort error_all_grep.log > error.log;');
		_ECHO("Done!</li>");

		_ECHO("<li>");
		$local_file=sprintf("%s/%s-%02d-error.log", $_SESSION['DIR_TMP'],$con_tda->tienda, $con_tda->caja);
		$con_tda->get_file_from_tpv("/tmp/error.log",$DOCUMENT_ROOT.$local_file);
		_ECHO("Done!</li>");
	_ECHO("</ul>");
	_ECHO('<p><a href="'.$local_file.'" target="_blank">Pulse aqui para descargar/visualizar fichero</a></p>');
}

?>