<?php
$Idioma=$_SESSION["Idioma"];

$Textos_Select_Idioma=array(
	"Titulo" => array(
		"ESP" => "Seleccione idioma",
		"ENG" => "Choose your language"),
);
?>


<div id="div_select_idioma" title="<?php echo $Textos_Select_Idioma["Titulo"][$Idioma]; ?>" style="display:none">
	<p><?php echo $Textos_Select_Idioma["Titulo"][$Idioma]; ?><br>
		<ul style="list-style:none;">
			<li><input type="radio" name="input_idioma" value="ESP" checked>Espanol <?php echo @$banderas["ESP"];?></input></li>
			<li><input type="radio" name="input_idioma" value="ENG">English <?php echo @$banderas["ENG"];?></input></li>
		</ul>
	</p>
</div>
<script>
	var buttons = [,];
	buttons['OK'] = 'OK :)';
	buttons['Cancel'] = 'Cancel :(';

	var buttonArray = {};
	buttonArray[buttons['OK']] = function() {
		var valor_idioma = $('input:radio[name="input_idioma"]:checked').val();
		Put_SESSION("CHG_SESSION","Idioma", valor_idioma);
		location.reload(true);
		$(this).dialog('close');
	};
	buttonArray[buttons['Cancel']] = function() {
		$(this).dialog('close');
	};
	$("#div_select_idioma").dialog({
		autoOpen: false,
		resizable: false,
		height: 300, width:300,
		modal: true,
		buttons: buttonArray
	});
	$("#a_select_idioma").on("click",function() {
		$("#div_select_idioma").dialog("open");
	});
</script>