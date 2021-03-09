<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once("./textos_formulario.php");

if (empty($_SESSION['usuario'])) { require_once($DOCUMENT_ROOT.$DIR_RAIZ."/Msg_Error/must_login.php"); die(); }

// $Lista_Versiones=myQUERY("SELECT DISTINCT(Version) FROM tmpTiendas WHERE Version<>'NO DISPON.' GROUP BY Version ORDER BY 1");
$Lista_de_=array("Version", "Centro", "Tipo", "Subtipo", "Poblacion", "CP", "Provincia","Telefono","tipoEtiquetadora","VELA");
$tmp_query='tmp_query+="Pais='.$Pais.'&Busca_Tienda="+$("input[name=Busca_Tienda]").val();';
foreach($Lista_de_ as $d) {
	$variable="Lista_$d";
	switch($d) {
		case "CP": $$variable=myQUERY("SELECT DISTINCT($d) FROM CP order by 1"); break;
		default:   $$variable=myQUERY("SELECT DISTINCT($d) FROM tmpTiendas ORDER BY 1");
	}
	$tmp_query.='tmp_query+="&Busca_'.$d.'="+encodeURIComponent($("select[name=Busca_'.$d.'] option:selected").val());';
}

function Get_DataList($id,$Lista) {
	$tmp='<datalist id="'.$id.'">';
	foreach($Lista as $d) $tmp.='<option value="'.$d[0].'">';
	$tmp.='</datalist>';
	return $tmp;
}

$Opciones_Busqueda=array();
foreach($Lista_de_ as $d) {
	$variable="Lista_$d"; $texto="txt$d";
	$Opciones_Busqueda[$d]='<tr><td>'.$$texto.':</td><td>'.Rellena_Select_From_Table("Busca_".$d, $$variable).'</td></tr>';
}

//myQUERY("SELECT DISTINCT($d) FROM tmpTiendas ORDER BY 1");

// $tmp_query=urlencode($tmp_query);

// 			<tr>
// 				<td>'.$txtVersion.':</td><td>'.Rellena_Select_From_Table("Busca_Version", $Lista_Versiones).'</td>
// 			</tr>'.

echo '
<table width="100%">
<tr><td style="text-align:center">
<fieldset id="field_caja1" class="field_caja"><legend>'.$txtTitulo.'</legend>
<table>
	<tr>
		<td>
			<table style="font-size:1em" width=100%>
			<tr>
				<td>'.$txtTienda.':</td><td><input type="text" name="Busca_Tienda" id="Tienda" value="'.@$Tienda.'"/></td>
			</tr>'.
			$Opciones_Busqueda["Version"].
			$Opciones_Busqueda["Centro"].
			$Opciones_Busqueda["Tipo"].
			$Opciones_Busqueda["Subtipo"].
			$Opciones_Busqueda["Poblacion"].
			$Opciones_Busqueda["CP"].
			$Opciones_Busqueda["Provincia"].
			$Opciones_Busqueda["VELA"].
			'
			</table>
		</td>
		<td id="AVANZADA" style="display:none">
			<table style="font-size:1em" width=100%>
			<tr><td>IP :</td><td><input type="text" id="id_busca_ip" name="busca_ip" /></td></tr>'.
			$Opciones_Busqueda["Telefono"].
			$Opciones_Busqueda["tipoEtiquetadora"].
			'</table>
		</td>
		</tr>
		<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td colspan="2" style="text-align:center">
				<input class="button" name="Accion" id="buscar"    value="'.$txtBuscar.'"   type="button" autofocus/>
				<input class="button" name="Accion" id="resetear"  value="'.$txtResetear.'"  type="reset"  />
				<input class="button" name="Accion" id="bAvanzada" value="'.$txtAvanzada.'..." type="button" />
			</td>
		</tr>
		</table>
		</fieldset>';
echo '</td></tr>';
echo '<tr><td>'; require_once("./texto_ayuda_traslate.php");
echo '</td></tr></table>';

// 	$("#buscar").qtip({content:"Click here to search stores with selected criteria...", style: {name: "dark", tip: "topLeft"}});
// 	$("#resetear").qtip({content:"Click here to reset all values...", style: {name: "dark", tip: "topLeft"}});
// 	$("#bNuevaTda").qtip({content:"Click here to add new store...", style: {name: "dark", tip: "topLeft"}});
// 	$("#bAvanzada").qtip({content:"Click here to get advanced search option...", style: {name: "dark", tip: "topLeft"}});

//		$(".qtip").qtip("hide");
echo '
<script>
	$("#buscar").on("click",function() {
		var tmp_busca_ip=$("#id_busca_ip").val();
		if (tmp_busca_ip!="") { window.open("'.$PHP_PRECONECTAR.'?busca_ip="+tmp_busca_ip,"_blank"); }
		tmp_query="Conectar_Tienda/resultado_busqueda.php?";
		'.$tmp_query.'
		$("#CUERPO").load(tmp_query);
	});
	$("#field_caja1").keydown(function (event) {
		if (event.keyCode == 13) {
			$("#buscar").trigger("click");
			return false;
		}
	});
	$("#resetear").on("click",function() { Activa_Opc_Menu("'.$Que_Pagina.'","'.$PAGINA.'"); 	})
	$("#bNuevaTda").on("click",function() {$("#ALTA_TIENDA").dialog("open");})
	$("#bAvanzada").on("click",function() { $(".qtip").qtip("hide"); $("#AVANZADA").toggle(); })

</script>';

?>