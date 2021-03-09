<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!empty($_GET["opcion"])) {
	set_time_limit(0); 	
	ob_implicit_flush(true);
	ob_end_flush();
	require_once("/home/soporteweb/tools/mysql.php");
	require_once("./actualiza_datos.php");
	foreach($_GET as $k => $d) $$k=$d;
	switch($_GET["opcion"]) {
		case "update_usuarios":
			$tmp=myQUERY("SELECT *,(SELECT grupoNombre FROM grupos b WHERE b.grupoid=a.grupo) FROM usuarios a order by nombre");
			if (count($tmp)>0) {
				$listado='<table class="tabla2">
					<thead><tr><th style="display:none">ID</th><th>USUARIO</th><th>NOMBRE</th><th style="display:none">GrupoID</th><th>GRUPO</th><th>LOGGED</th><th>LAST.LOGGED</th></tr></thead>';
				foreach ($tmp as $d) {
					list($usuarioID, $usuario, $nombre, $grupoID, $logged, $LastLogged,$nombregrupo) = $d;
					$listado.='<tr class="row_scripts">';
					$listado.='<td style="display:none">'.$usuarioID.'</td>';
					$listado.='<td>'.$usuario.'</td>';
					$listado.='<td>'.$nombre.'</td>';
					$listado.='<td style="display:none">'.$grupoID.'</td>';
					$listado.='<td>'.$nombregrupo.'</td>';
					$listado.='<td>'.($logged?"LOGGED":"AFK").'</td>';
					$listado.='<td>'.$LastLogged.'</td>';
					$listado.='</tr>';
				}
				$listado.='</table>';
				$tmp=myQUERY("select max(usuarioID) from usuarios");
				$listado.="<span id='ultimo_id' style='display:none'>".$tmp[0][0]."</span>";
			} else
				$listado="No se han encontrado registros...";
			echo $listado;
			break;
			
		case "salvar_datos":
			$tmp=myQUERY("select grupoID from grupos where grupoNombre = '$nombregrupo'");
			$grupoID=$tmp[0][0];
			Actualiza_Datos("
				INSERT INTO usuarios VALUES($userID,'$user','$nombre',$grupoID, 0,'') ON DUPLICATE KEY UPDATE usuario='$user',nombre='$nombre',grupo=$grupoID;
				INSERT INTO scripts_x_usuario (select $userID, id_script,'',0 from scripts_web) on duplicate key update valor=valor;
			");
			$para=0;
			while($para=0) {
				$tmp=myQUERY("select count(*) from usuarios where usuarioID=$userID");
				$para=$tmp[0][0];
				echo "<b class='Mensaje'>Espere por favor...</b>";
			}
			echo "<b class='Mensaje'>DATOS MODIFICADOS!!</b>";
			break;

		case "borrar_datos":
			Actualiza_Datos("DELETE FROM usuarios WHERE usuarioid=$userID; DELETE FROM scripts_x_usuario WHERE id_usuario=$userID");
			$contador=0;
			while($contador<5) {
				$tmp=myQUERY("select count(*) from usuarios where usuarioID=$userID");
				if ($tmp[0][0] == 0) break;
				sleep(1);
				$contador++;
			}
			if ($contador<5) echo "<b class='Mensaje'>REGISTRO ELIMINADO!!</b>";
			else echo "<b class='Mensaje'>ERROR ELIMINANDO REGISTRO!!</b>";
			break;
	}
	exit;
}

require("./comun_administracion.php");

$tmp=myQUERY("select distinct(gruponombre) FROM grupos");
$lista_grupos='<datalist id="datalist_grupos">';
foreach($tmp as $d) $lista_grupos.='<option value="'.$d[0].'">';
$lista_grupos.="</datalist>";

$Alto_Pantalla=1000;
$Alto_Trabajo=$Alto_Pantalla*100/100;

?>
<style>
	#div_de_usuarios { overflow: auto; height: <?php echo $Alto_Trabajo*80/100; ?>; width: 100%}
	#resultado { margin-top: 1em; border-top:1px solid black;}
</style>

<table style="width:100%">
	<tr>
		<td id="td_Menu" valign="top" width="20%">
			<fieldset><legend>Menu de opciones</legend>
			<ul>
				<li><a href="./gestion.php">Menu principal</a></li>
				<br>
				<li><a id="nuevo_elemento" href="javascript:{}">Nuevo Usuario</a></li>
			</ul>
			</fieldset>

			<fieldset id="f_opciones" style="display:none">
				<legend>Datos del grupo</legend>
				<div id="datos_grupo">
					<label style="display:none">ID:</label><input style="display:none" type="text" id="i_id" value="">
					<label>USUARIO:</label><input type="text" id="i_usuario" value="" placeholder="Usuario en LDAP">
					<label>NOMBRE COMPLETO:</label><input size=30 type="text" id="i_nombreusuario" value="" placeholder="Nombre completo del usuario">
					<label style="display:none">ID.GRUPO:</label><input style="display:none" type="text" id="i_grupoID" value="">
					<label>GRUPO:</label><input type="text" list="datalist_grupos" id="i_nombregrupo" value="" placeholder="Grupo al que pertenece el usuario"><?php echo $lista_grupos; ?>
					<hr>
					<input type="button" id="b_Salvar" value="Salvar datos"> <input type="button" id="b_Borrar" value="Eliminar"><br><span id="resultado"></span>
				</div>
			</fieldset>
		</td>
		<td id="td_Gestion">
			<fieldset>
				<legend>Listado de Usuarios</legend>
				<div id="div_de_usuarios"></div>
			</fieldset>
		</td>
	</tr>
</table>

<script type="text/javascript">
	var url_local="<?php echo basename(__FILE__); ?>";
	function Pon_Datos(parametros) {
		$("#i_id").val(parametros.userID);
		$("#i_usuario").val(parametros.user);
		$("#i_nombreusuario").val(parametros.nombre);
		$("#i_grupoID").val(parametros.grupoID);
		$("#i_nombregrupo").val(parametros.nombregrupo);
//		$("#i_logged").val(parametros.logged);
//		$("#i_lastlogged").val(parametros.lastlogged);
	}

	function Recarga() {
		$("#div_de_usuarios").load(url_local+"?opcion=update_usuarios",function () {
			$("tr .row_scripts").on("click",function () {
				var grupoID = 
				$( "#resultado" ).html("");
				Pon_Datos( {
					userID:$(this).children(":nth-child(1)").html(),
					user:$(this).children(":nth-child(2)").html(),
					nombre:$(this).children(":nth-child(3)").html(),
					grupoID:$(this).children(":nth-child(4)").html(),
					nombregrupo:$(this).children(":nth-child(5)").html(),
//					logged:$(this).children(":nth-child(6)").html(),
//					lastlogged:$(this).children(":nth-child(7)").html(),
				} );
				$("#f_opciones").show();
			});
		});
	}

	$("#nuevo_elemento").on("click",function () {
		Pon_Datos( {
			userID:parseInt($("#ultimo_id").html(),10)+1,
			user:"",
			nombre:"",
			grupoID:0,
			nombregrupo:"",
			logged:false,
			lastlogged:"",
		});
		$("#f_opciones").show();
	});
	
	$("#b_Salvar").on("click",function () {
		if ($("#i_usuario").val() == "") { alert("Falta el usuario en LDAP"); return; }
		if ($("#i_nombreusuario").val() == "") { alert("Falta un nombre completo al usuario"); return; }
		if ($("#i_nombregrupo").val() == "") { alert("Falta el grupo donde el usuario estar&aacute;"); return; }
		var parametros = {
			opcion:"salvar_datos",
			userID:$("#i_id").val(), user:$("#i_usuario").val(), nombre:$("#i_nombreusuario").val(), nombregrupo:$("#i_nombregrupo").val() };
		$("#resultado").html("Guardando registro. Espere por favor...");
		$.get(url_local, parametros, function( data ) {
			$("#resultado").html(data);
			Recarga();
		});		
	});

	$("#b_Borrar").on("click",function () {
		if ($("#i_usuario").val() == "") { alert("Seleccione un usuario a eliminar"); return; }
		var parametros = { opcion:"borrar_datos", userID:$("#i_id").val() }
		$("#resultado").html("Borrando registro. Espere por favor...");
		var res=$.get(url_local, parametros, function( data ) {
			$("#resultado").html(data);
			Recarga();
		});		
	});

	Recarga();
</script>
</body>