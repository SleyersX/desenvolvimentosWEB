<?php
if (isset($_GET['dir']) && !empty($_GET['dir']))
	exec("rm -fr ".$_GET['dir']);
?>
<html>
<script language="JavaScript">
	window.close();
</script>
</html>