<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/config.php");
	$go_pais=$_GET["go_pais"];
	$ip=gethostbyname($IP_WEBSERVER[$go_pais]["ALIAS"]);
	$url_final="http://".$ip."/".$IP_WEBSERVER[$go_pais]["HOME"];
	
?>
<script>
	window.open("<?php echo $url_final; ?>","_self");
</script>