<?php
require($_SERVER['DOCUMENT_ROOT'].'/config.php');
require($_SERVER['DOCUMENT_ROOT'].$DIR_TOOLS.'head_1.php');

if (count($_GET)>0)
	foreach($_GET as $k => $d) $$k=$d;

if (!empty($Salva_Ventas)) {
	echo "
		Realizando proceso de salvado de ventas:
		- Comprobacion de fin de dia realizado... OK
		- Comprobacion de TPV en SELECCIONE PROGRAMA... OK
	";
	die();
}
?>
	
<div class="Aviso" style="width: 80%; ">
	<p>
		Esta opci&oacute;n permite salvar las ventas no trasnsmitidas en el &uacute;ltimo gestor (o gestores) noche de una tienda.<br>
		Si la tienda lleva m&aacute;s de 4 d&iacute;as sin transmitir, se debe dar aviso para que se revisen las comunicaciones.<br>
		SOLO SE PUEDE EJECUTAR UN SALVADO DE VENTAS UNA VEZ AL DIA.<br>
	</p>
	<p>
		Recordamos que se debe realizar en las siguientes circunstancias:
		<ul>
			<li>Caso de extrema necesidad.</li>
			<li>Cuando la tienda lleva sin comunicar mas de 5 d&iacute;as.</li>
			<li>Si por alg&uacute;n motivo, no se ha podido solventar el problema de la no comunicaci&oacute;n de la tienda con concentrador, que se puede dar en estos casos, entre otros:
			<ul>
				<li>Errores en la red local de la tienda.</li>
				<li>Errores de red entre la tienda y sistemas centrales.</li>
				<li>Errores software (EBD) que impidan un fin de d&iacute;a correcto.</li>
			</ul>
		</ul>
	</p>
	<p>Instrucciones para el uso de esta opcion:
	<ul>
		<li>Haber realizado un fin de dia previo.</li>
		<li>Caja en SELECCIONE PROGRAMA (llave 4 en arranque TPV).</li>
	</ul>
	<hr>

	<p>
		<b>IMPORTANTE:</b>El proceso precisa que la caja est&eacute; en SELECCIONE PROGRAMA, por lo que antes de ejecutar esta opci&oacute;n, la TPV no est&aacute; realizando alg&uacute;n proceso cr&iacute;tico, ya que ser&aacute; necesario llevar la TPV a SELECCIONE PROGRAMA.
	</p>

	</ul> <input id="Generar_Fichero" type=button value="Generar fichero de ventas?"/>

	<div id="div_salvado"></div>
</div>

<script>
	$("input[name=Opcion_Salvado]:radio").change(function (e) { $("#Aviso_Fecha").hide(); });
	$("#Generar_Fichero").on("click",function() {
		$("#div_salvado").html('<img src="/img/ventana_espera.gif"/>');
		$("#div_salvado").load("./Salvado_Ventas.php?Salva_Ventas=true");
	});
</script>
