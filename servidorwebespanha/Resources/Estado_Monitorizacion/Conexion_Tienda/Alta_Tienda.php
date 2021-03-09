<?php
$Lista_Versiones=myQUERY("SELECT DISTINCT(Version) FROM $Table WHERE Version<>'' GROUP BY Version ORDER BY 1");
$Lista_de_=array("Centro", "Tipo", "Subtipo", "Poblacion", "Provincia");
foreach($Lista_de_ as $d) {
	$variable="Lista_$d";
	$$variable=myQUERY("SELECT DISTINCT($d) FROM tiendas WHERE Pais IN ('$Pais','XXX') GROUP BY $d ORDER BY 1");
}

function Get_DataList($id,$Lista) {
	$tmp='<datalist id="'.$id.'">';
	foreach($Lista as $d) $tmp.='<option value="'.$d[0].'">';
	$tmp.='</datalist>';
	return $tmp;
}

echo '
<div id="ALTA_TIENDA" style="display:none" title="Alta de nueva tienda en pruebas">
	<div class="Aviso">
		<div id="Mensaje_exec1"></div>
		<div id="div_new_tienda">
			<table style="border: 1px solid #000;">
				<th colspan=2 style="background: #eee;">Datos obligatorios</th>
				<tr><td>Tienda:</td><td><input type="text" id="id_newTienda" name="newTienda" value=""/></td></tr>
				<tr><td>Version:</td><td><input list="lVersiones" type="text" name="newVersion" value=""/>
					'.Get_DataList("lVersiones",$Lista_Versiones).'</td></tr>
				<tr><td>Centro:</td><td><input list="lCentro" type="text" name="newCentro" value="SEDE"/></td></tr>
				<tr><td>IP:</td><td><input type="text" name="newip" /></td></tr>
			</table>

			<table style="border: 1px solid #000;">
				<th colspan=2 style="background: #eee;">Datos recomendados</th>
				<tr><td>Tipo:     </td><td><input list="lTipo" type="text" name="newTipo" value=""/></td></tr>
				<tr><td>Subtipo:  </td><td><input list="lSubt" type="text" name="newSubt" value=""/></td></tr>
				<tr><td>Poblacion:</td><td><input list="lPobl" type="text" name="newPobl" value="SEDE"/></td></tr>
				<tr><td>Provincia:</td><td><input list="lProv" type="text" name="newProv" value="SEDE"/></td></tr>
			</table>
		</div>
';

echo Get_DataList("lTipo",$Lista_Tipo).Get_DataList("lSubt",$Lista_Subtipo).
     Get_DataList("lPobl",$Lista_Poblacion).Get_DataList("lProv",$Lista_Provincia);

echo '
	<p>Aqu&iacute; podremos crear tiendas temporalmente para uso interno de de pruebas.</p>
	<p><b>NOTA:</b>Se puede hacer doble click sobre los cuadros de edicion para ver las opciones disponibles</p>
	</div>
</div>

<script>
	$("#ALTA_TIENDA").dialog({
		autoOpen: false, modal: true, width: "auto", height: 600, resizable: false,
		buttons: {
			"Alta": function() {
				var parametros= {
					"OPCION":"ADD_SHOP",
					"newTienda": $("input:text[name=newTienda]" ).val()
					};
				Ejecuta_AJAX("Mensaje_exec1", parametros );
			},
			"Cancelar": function() { $(this).dialog("close"); }
		}
	});
	$("#bNuevaTda").on("click",function() {
		$("#ALTA_TIENDA").dialog("open");
	});
</script>';
?>