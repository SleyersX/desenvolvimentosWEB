<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!empty($_GET["opcion"])) {
	require_once("/home/soporteweb/tools/mysql.php");
	require_once("./actualiza_datos.php");
	foreach($_GET as $k => $d) $$k=$d;
	switch($_GET["opcion"]) {
		case "update_scripts":
			$lista_registros=myQUERY("SELECT * from scripts_web ORDER BY tipo,script");
			if (count($lista_registros)>1) {
				$listado='<table class="tabla2">
					<thead><tr><th style="display:none">ID</th><th style="display:none">ACTIV.</th><th>OPCION</th><th>TIPO</th><th>FICHERO PHP</th><th>DESCRIPCION</th>';
				$listado.='</tr></thead>';
				$Query="";
				foreach ($lista_registros as $d) {
					list($id_script,$script,$tipo,$php,$pais,$descripcion) = $d;
					$listado.='<tr class="row_scripts '.($pais?'activado':'desactivado').'">';
					$listado.='<td style="display:none">'.$id_script.'</td>';
					$listado.='<td style="display:none">'.($pais?true:false).'</td>';
					$listado.='<td>'.$script.'</td>';
					$listado.='<td>'.$tipo.'</td>';
					$listado.='<td>'.$php.'</td>';
					$listado.='<td title="'.$descripcion.'">'.(sprintf("%-30.30s",$descripcion)).'</td>';
					$listado.='</tr>';
				}
				$listado.='</table>';
				$tmp=myQUERY("select max(id_script) from scripts_web");
				$listado.="<span id='ultimo_id' style='display:none'>".$tmp[0][0]."</span>";
			} else
				$listado=Alert("error", "No se han encontrado registros...");
			echo $listado;
			break;
			
		case "salvar_datos":
			Actualiza_Datos("
				INSERT INTO scripts_web VALUES($id,'$nombre','$tipo','$fichero',$activado, '$descripcion') ON DUPLICATE KEY UPDATE script='$nombre', tipo='$tipo', php='$fichero', pais=$activado, descripcion='$descripcion';
				INSERT INTO scripts_x_grupo (select grupoID, $id,'',0 from grupos) on duplicate key update valor=valor;
				INSERT INTO scripts_x_usuario (select usuarioID, $id,'',0 from usuarios) on duplicate key update valor=valor;
			");
			echo "<b class='Mensaje'>DATOS MODIFICADOS!!</b>";
			break;

		case "borrar_datos":
			Actualiza_Datos("
				DELETE FROM scripts_web WHERE id_script = $id;
				DELETE FROM scripts_x_grupo WHERE id_script=$id;
				DELETE FROM scripts_x_usuario WHERE id_script=$id;
			");
			echo "<b class='Mensaje'>REGISTRO ELIMINADO!!</b>";
			break;

	}
	exit;
}

require_once("./comun_administracion.php");

if ($_SESSION['grupo_usuario'] != 2 ) {
	die(Alert("error","PERFIL INSUFICIENTE PARA GESTIONAR SCRIPTS..."));
}

$tmp=myQUERY("select distinct(Tipo) FROM scripts_web");
$lista_tipos='<datalist id="datalist_tipos">';
foreach($tmp as $d) $lista_tipos.='<option value="'.$d[0].'">';
$lista_tipos.="</datalist>";

?>
<style>
	.activado    { background-color: lightgreen;}
	.desactivado { background-color: #F78181;}
	#datos_script > *{
		font-size: 12px !important;
	}
</style>

<table style="width:100%">
	<tr>
		<td id="td_Menu" valign="top" width="20%">
			<fieldset><legend>Menu de opciones</legend>
			<ul>
				<li><a href="./gestion.php">Menu principal</a></li>
				<br>
				<li><a id="nuevo_elemento" href="javascript:{}">Nueva Opcion</a></li>
			</ul>
			</fieldset>

			<fieldset id="f_opciones" style="display:none">
				<legend>Datos de la opcion</legend>
				<div id="datos_script">
					<table>
						<tr title="SI ESTA O NO ACTIVADO PARA ESTE PAIS. Marque el check para activar."><td>ACTIVADO:</td><td><input type="checkbox" id="i_activado" /></td></tr>
						<tr style="display:none"><td>ID:</td><td><input type="text" id="i_id" value=""></td></tr>
						<tr><td>OPCION:</td><td><input type="text" id="i_nombre" value="" placeholder="Opci&oacute;n en men&uacute;"></td></tr>
						<tr><td>TIPO:</td><td><input list="datalist_tipos" type="text" id="i_tipo" value="" placeholder="Tipo de opcion"></td><?php echo $lista_tipos; ?></tr>
						<tr><td>FICHERO PHP:</td><td><input type="text" id="i_fichero" value="" placeholder="Fichero PHP con la opcion"></td></tr>
						<tr><td>DESCRIPCION:</td><td><input type="text" id="i_descripcion" value="" placeholder="Breve descripcion de la opcion"></td></tr>
						<tr></tr>
						<tr><td>
							<input type="button" id="b_Salvar" value="Salvar datos">
							<input type="button" id="b_Borrar" value="Borrar opcion">
							</td><td><span id="resultado"></span></td></tr>
					</table>
				</div>
			</fieldset>
		</td>
		<td id="td_Gestion">
			<fieldset>
				<legend>Listado de Scripts</legend>
				<div id="div_de_datos"></div>
			</fieldset>
		</td>
	</tr>
</table>

<script type="text/javascript">

jQuery("#csvalid").jqGrid({        
	url:'<?php echo ".".basename(__FILE__);?>?q=1',
	datatype: "json",
	colNames:['ID','Opcion','Tipo', 'Fichero PHP', 'Descripcion'],
	colModel:
	[
		{name:'id_script',index:'id_script', width:55,editable:false,editoptions:{readonly:true,size:10}},
   	{name:'script',index:'script', width:80,editable:true,editoptions:{size:10},editrules:{required:true}},
		{name:'tipo',index:'tipo',width:70, editable: true,edittype:"select",editoptions:{value:"Listados:Acciones:Datos"}},
   	{name:'php',index:'php', width:100, sortable:false,editable: true,edittype:"textarea", editoptions:{rows:"2",cols:"20"}}		
   ],
   	rowNum:10,
   	rowList:[10,20,30],
   	pager: '#pcsvalid',
   	sortname: 'id',
    viewrecords: true,
    sortorder: "desc",
    caption:"Validation Example",
    editurl:"someurl.php",
	height:210
});
jQuery("#csvalid").jqGrid('navGrid','#pcsvalid',
{}, //options
{height:280,reloadAfterSubmit:false}, // edit options
{height:280,reloadAfterSubmit:false}, // add options
{reloadAfterSubmit:false}, // del options
{} // search options
);


	function Pon_Datos(parametros) {
		$("#i_id").val(parametros.id_script);
		$("#i_activado")[0].checked = parametros.activado;
		$("#i_nombre").val(parametros.nombre);
		$("#i_tipo").val(parametros.tipo);
		$("#i_fichero").val(parametros.fichero);
		$("#i_descripcion").val(parametros.descripcion);		
	}

	function Recarga() {
		$("#div_de_datos").load("./gestiona_scripts.php?opcion=update_scripts",function () {
			$("tr .row_scripts").on("click",function () {
				$( "#resultado" ).html("");
				Pon_Datos( { id_script:$(this).children(":nth-child(1)").html(), activado:$(this).children(":nth-child(2)").html(), nombre:$(this).children(":nth-child(3)").html(), tipo:$(this).children(":nth-child(4)").html(), fichero:$(this).children(":nth-child(5)").html(), descripcion:$(this).children(":nth-child(6)").html()  } );
				$("#f_opciones").show();
			});
		});
	}

	$("#b_Salvar").on("click",function () {
		if ($("#i_nombre").val() == "") { alert("Falta el nombre de la opcion"); return; }
		if ($("#i_tipo").val() == "") { alert("Falta el tipo de la opcion"); return; }
		if ($("#i_fichero").val() == "") { alert("Falta el fichero de la opcion"); return; }
		var parametros = {
			opcion:"salvar_datos",
			id:$("#i_id").val(),
			activado:$("#i_activado")[0].checked,
			nombre:$("#i_nombre").val(),
			tipo:$("#i_tipo").val(), 
			fichero:$("#i_fichero").val(),
			descripcion:$("#i_descripcion").val() }
		var res=$.get("./gestiona_scripts.php", parametros, function( data ) {
			$("#resultado").html(data);
			Recarga();
		});		
	});

	$("#nuevo_elemento").on("click",function () {
		Pon_Datos( { id_script:parseInt($("#ultimo_id").html(),10)+1, activado:true, nombre:"", tipo:"", fichero:"", descripcion:"" });
		$("#f_opciones").show();
	})

	$("#b_Borrar").on("click",function () {
		if ($("#i_nombre").val() == "") { alert("Seleccione un script a borrar"); return; }
		var parametros = { opcion:"borrar_datos", id:$("#i_id").val() }
		var res=$.get("./gestiona_scripts.php", parametros, function( data ) {
			$("#resultado").html(data);
			Recarga();
		});		
	});


	Recarga();
</script>
</body>