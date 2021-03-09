<title>VELA</title>
<?php
require("./cabecera_vistas.php");


$database = 'VELA';
$user = 'vma001es';
$password = 'c$dd3rly';
$hostname = '10.71.196.47';
$port = 50000;

$conn_string = "DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$database;" .
  "HOSTNAME=$hostname;PORT=$port;PROTOCOL=TCPIP;UID=$user;PWD=$password;";
$conn = db2_connect($conn_string, '', '');

if ($conn) {
    echo "Connection succeeded.";
    db2_close($conn);
}
else {
    echo "Connection failed.";
}

?>