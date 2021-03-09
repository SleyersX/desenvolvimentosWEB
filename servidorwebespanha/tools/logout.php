<!DOCTYPE html>
<?php
	@session_start();
// 	exec('sudo mysql soporteremotoweb -e "UPDATE sesiones SET F_FIN=NOW() WHERE id_sesion=\''.session_id().'\'"');
	session_destroy();
	
?>
<script>
	alert("Su sesion ha sido cerrada.");
	window.location.href="/";
</script>