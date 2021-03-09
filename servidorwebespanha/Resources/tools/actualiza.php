<?php
	$TIENDA=sprintf("%05d",$_GET['Tienda']);
	$PAIS=$_GET['Pais'];
	$RESET_INV=(!empty($_GET['reset_inv'])?$_GET['reset_inv']:1);
	if ($PAIS == "XXX")
		$cmd="cd /home/MULTI && timeout 60s sudo bash ./Actualiza_pruebas ".$TIENDA." ".$PAIS." ".$RESET_INV;
	else
		$cmd="cd /home/MULTI && timeout 60s sudo bash ./Actualiza ".$TIENDA." ".$PAIS." ".$RESET_INV;
	echo "$cmd";
	$res=shell_exec($cmd);
	if (preg_match("/ERROR/", $res)) {
		$txt_error=explode(":",$res);
		die($txt_error[1]);
	}
	echo "<pre>$res - OK</pre>";
	die("<pre>$res - OK</pre>");
?>
