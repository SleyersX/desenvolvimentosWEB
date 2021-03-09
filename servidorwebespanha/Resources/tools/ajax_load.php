<?php
$Server=( (!empty($_GET['Server'])?$_GET['Server']:"10.208.162.17" ) );
// $cmd="df -h -t ext3";
// $cmd1='sudo ssh soporte@'.$Server.' "'.$cmd.'" || echo "ERROR EN CONEXION SERVIDOR"';
$cmd = "cat /home/soporteweb/info_servidores/".$Server.".loadavg";
echo shell_exec($cmd);

die();

?>