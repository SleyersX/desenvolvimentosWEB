<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!empty($_GET["opcion"])) {
	require_once("/home/soporteweb/tools/mysql.php");
	require_once("./actualiza_datos.php");
	foreach($_GET as $k => $d) $$k=$d;
	switch($_GET["opcion"]) {
		case "update_grupos":
			$tmp=myQUERY("SELECT *,(SELECT COUNT(*) FROM usuarios WHERE grupo=grupoid) from grupos");
			if (count($tmp)>0) {
				$listado='<table class="tabla2">
					<thead><tr><th style="display:none">ID</th><th>GRUPO</th><th>LISTADOS</th><th>ACCIONES</th><th>DATOS</th><th>USUARIOS</th></tr></thead>';
				$Query="";
				foreach ($tmp as $d) {
					list($id_grupo,$grupo,$sw_listados,$sw_acciones,$sw_datos,$usuarios) = $d;
					$listado.='<tr class="row_scripts">';
					$listado.='<td style="display:none">'.$id_grupo.'</td>';
					$listado.='<td>'.$grupo.'</td>';
					$listado.='<td>'.($sw_listados?"ON":"OFF").'</td>';
					$listado.='<td>'.($sw_acciones?"ON":"OFF").'</td>';
					$listado.='<td>'.($sw_datos?"ON":"OFF").'</td>';
					$listado.='<td>'.$usuarios.'</td>';
					$listado.='</tr>';
				}
				$listado.='</table>';
				$tmp=myQUERY("select max(grupoID) from grupos");
				$listado.="<span id='ultimo_id' style='display:none'>".$tmp[0][0]."</span>";
			} else
				$listado="No se han encontrado registros...";
			echo $listado;
			break;

		case "update_usuarios":
			$tmp=myQUERY("SELECT grupoNombre FROM grupos WHERE grupoid=$grupoID");
			$nombre_grupo=$tmp[0][0];
			$tmp=myQUERY("SELECT * FROM usuarios WHERE grupo=$grupoID");
			if (count($tmp)>0) {
				$listado='<table class="tabla2">
					<caption>Datos de usuarios asignados al grupo: '.$nombre_grupo.'</caption>
					<thead><tr><th style="display:none">ID</th><th>USUARIO</th><th>NOMBRE</th><th>LOGGED</th><th>LAST.LOGGED</th></tr></thead>';
				foreach ($tmp as $d) {
					list($usuarioID, $usuario, $nombre, $grupo, $logged, $LastLogged) = $d;
					$listado.='<tr class="row_scripts">';
					$listado.='<td style="display:none">'.$usuarioID.'</td>';
					$listado.='<td>'.$usuario.'</td>';
					$listado.='<td>'.$nombre.'</td>';
					$listado.='<td>'.($logged?"LOGGED":"AFK").'</td>';
					$listado.='<td>'.$LastLogged.'</td>';
					$listado.='</tr>';
				}
				$listado.='</table>';
			} else
				$listado="No se han encontrado registros...";
			echo $listado;
			break;
			
		case "salvar_datos":
			Actualiza_Datos("
				INSERT INTO grupos VALUES($grupoID,'$grupo',$sw_listados,$sw_acciones,$sw_datos) ON DUPLICATE KEY UPDATE gruponombre='$grupo', sw_listados=$sw_listados, sw_acciones=$sw_acciones, sw_datos=$sw_datos;
				INSERT INTO scripts_x_grupo (select $grupoID, id_script,'',0 from scripts_web) on duplicate key update valor=valor;;
			");
			echo "<b class='Mensaje'>DATOS MODIFICADOS!!</b>";
			break;

		case "borrar_datos":
			Actualiza_Datos("
				DELETE FROM grupos WHERE grupoid=$grupoID;
				DELETE FROM scripts_x_grupo WHERE id_grupo=$grupoID;
			");
			echo "<b class='Mensaje'>REGISTRO ELIMINADO!!</b>";
			break;
	}
	exit;
}

require("./comun_administracion.php");

$Alto_Pantalla=1000;
$Alto_Trabajo=$Alto_Pantalla*85/100;

?>
<style>
	#div_de_datos { overflow: auto; height: <?php echo $Alto_Trabajo*20/100; ?>; width: 100%; }
	#div_de_usuarios { overflow: auto; height: <?php echo $Alto_Trabajo*80/100; ?>; width: 100%}
</style>

<table style="width:100%">
	<tr>
		<td id="td_Menu" valign="top" width="20%">
			<fieldset><legend>Menu de opciones</legend>
			<ul>
				<li><a href="./gestion.php">Menu principal</a></li>
				<br>
				<li><a id="nuevo_elemento" href="javascript:{}">Nuevo Grupo</a></li>
			</ul>
			</fieldset>

			<fieldset id="f_opciones" style="display:none">
				<legend>Datos del grupo</legend>
				<div id="datos_grupo">
					<table>
						<tr style="display:none"><td>ID:</td><td><input type="text" id="i_id" value=""></td></tr>
						<tr><td>GRUPO:</td><td><input type="text" id="i_grupo" value="" placeholder="Opci&oacute;n en men&uacute;"></td></tr>
						<tr><td>LISTADOS</td><td><input id="i_sw_listados" type="checkbox"></td></tr>
						<tr><td>ACCIONES</td><td><input id="i_sw_acciones" type="checkbox"></td></tr>
						<tr><td>DATOS</td><td><input id="i_sw_datos" type="checkbox"></td></tr>
						<tr></tr>
						<tr><td>
							<input type="button" id="b_Salvar" value="Salvar datos">
							<input type="button" id="b_Borrar" value="Eliminar">
							</td><td><span id="resultado"></span></td></tr>
					</table>
				</div>
			</fieldset>
		</td>
		<td id="td_Gestion">
			<fieldset>
				<legend>Listado de Grupos</legend>
				<div id="div_de_datos" ></div>
				<div id="div_de_usuarios"></div>
			</fieldset>
		</td>
	</tr>
</table>

<script type="text/javascript">
	var url_local="<?php echo basename(__FILE__); ?>";
	function Pon_Datos(parametros) {
		$("#i_id").val(parametros.grupoID);
		$("#i_grupo").val(parametros.grupo);
		$("#i_sw_listados")[0].checked = (parametros.sw_listados=="ON");
		$("#i_sw_acciones")[0].checked = (parametros.sw_acciones=="ON");
		$("#i_sw_datos")[0].checked = (parametros.sw_datos=="ON");
	}

	function Recarga() {
		$("#div_de_datos").load(url_local+"?opcion=update_grupos",function () {
			$("#div_de_usuarios").html("");
			$("tr .row_scripts").on("click",function () {
				var grupoID = $(this).children(":nth-child(1)").html();
				$( "#resultado" ).html("");
				Pon_Datos( { grupoID:grupoID, grupo:$(this).children(":nth-child(2)").html(), sw_listados:$(this).children(":nth-child(3)").html(), sw_acciones:$(this).children(":nth-child(4)").html(), sw_datos:$(this).children(":nth-child(5)").html()} );
				$("#f_opciones").show();
				$("#div_de_usuarios").load(url_local+"?opcion=update_usuarios"+"&grupoID="+grupoID);
			});
		});
	}

	$("#nuevo_elemento").on("click",function () {
		Pon_Datos( { grupoID:parseInt($("#ultimo_id").html(),10)+1, grupo:"", tipo:"", sw_listados:false, sw_acciones:false, sw_datos:false });
		$("#f_opciones").show();
	});
	
	$("#b_Salvar").on("click",function () {
		if ($("#i_grupo").val() == "") { alert("Falta el nombre del grupo"); return; }
		var parametros = {
			opcion:"salvar_datos",
			grupoID:$("#i_id").val(), grupo:$("#i_grupo").val(),
			sw_listados:$("#i_sw_listados")[0].checked, sw_acciones:$("#i_sw_acciones")[0].checked, sw_datos:$("#i_sw_datos")[0].checked };
		$.get(url_local, parametros, function( data ) {
			$("#resultado").html(data);
			Recarga();
		});		
	});

	$("#b_Borrar").on("click",function () {
		if ($("#i_grupo").val() == "") { alert("Seleccione un grupo a eliminar"); return; }
		var parametros = { opcion:"borrar_datos", grupoID:$("#i_id").val() }
		var res=$.get(url_local, parametros, function( data ) {
			$("#resultado").html(data);
			Recarga();
		});		
	});

	Recarga();

</script>
</body>