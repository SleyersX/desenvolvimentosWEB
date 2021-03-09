<?php
// borrar_imprfindia.php
if (!empty($Borrar_Fichero)) {
	_ECHO('<div class="Aviso Aviso_Verde">');
	_ECHO('<p>Borrando fichero... ');
		$con_tda->cmdExec("mv /confdia/ficcaje/imprfindia.txt /root -f");
	_ECHO('<b>OK</b></p>');
	_ECHO('<p>Inicializando aplicacion... ');
		$con_tda->cmdExec("cd /confdia/bin; touch auto01; killall -9 ventas.exe");
	_ECHO('<b>OK</b></p>');
} else {
	echo '<div class="Aviso">
	<p>Esta opcion borra el fichero resultado del fin de dia y comunicaciones, para solucionar posibles bloqueos en el arranque de la TPV por la impresion de transacciones del dia anterior (por dejar puesta la llave 4)</p>
	<p><input type=button name=Borrar_Fichero id="i_borrar_fichero" value="Pulse aqui para borrar fichero"/></p>
	</div>
	<script>
	$("#i_borrar_fichero").on("click",function() {
		INPUT_HIDDEN("Borrar_Fichero","Borrar_Fichero","myForm");
		INPUT_HIDDEN("myAcciones","$myAcciones","myForm");
		SUBMIT("myForm");
	});
	</script>';
}

?>