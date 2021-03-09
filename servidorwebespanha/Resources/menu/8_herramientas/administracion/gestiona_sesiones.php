<?php
require_once("./comun_administracion.php");

// echo '<form id="myForm" name="myForm" action="'.basename($SCRIPT_FILENAME).'" method="post">';
echo '<fieldset> <legend>Listado de Sesiones Activas</legend>';

$UsuaCone=myQUERY("SELECT u.nombre, g.gruponombre, s.id_sesion, s.f_inicio, s.f_fin, s.ip, s.id_script
	FROM sesiones s
		JOIN usuarios u ON u.usuario=s.usuario
		JOIN grupos g   ON g.grupoID=u.grupo
	WHERE DATE(F_INICIO)=CURDATE() AND F_FIN IS NULL
	ORDER BY 2, 1");

if (count($UsuaCone)==0) {
	echo Alert("warning", "No hay sesiones activas");
}
else {
	echo '<h2>Usuarios conectados en este momento...</h2>';
	echo '<table class="lista_ficheros">';
	echo '<thead><tr><th>USUARIO</th><th>GRUPO</th><th>Direccion IP</th><th>Id. Sesion</th><th>F. LOGON</th><th>Que esta haciendo</th></tr></thead>';
	foreach($UsuaCone as $Usua) {
		list($USUARIO, $GRUPONOMBRE, $ID_SESION, $F_INICIO, $F_FIN, $IP, $SCRIPT) = $Usua;
		echo "<tr><td>$USUARIO</td><td>$GRUPONOMBRE</td><td>$IP</td><td>$ID_SESION</td><td>$F_INICIO</td><td>$SCRIPT</td></tr>";
	}
	echo "</table>";
}

echo "</fieldset></form>";

die();

// --------------------------------------------------------------------- MODIFICACIONES
if (@$guardar) {
	foreach($l_grupos as $k => $d) {
		$sw_l=(@$d[1]==="on"?1:0); $sw_a=(@$d[2]==="on"?1:0);$sw_d=(@$d[3]==="on"?1:0);
		myQUERY("UPDATE grupos SET gruponombre='$d[0]', sw_listados=$sw_l, sw_acciones=$sw_a, sw_datos=$sw_d where grupoid=$k");
	}
	$Mensaje="DATOS GUARDADOS!!";
}
// --------------------------------------------------------------------- ALTAS
if (@$nuevo) {
	echo '<fieldset id="fieldset_captura_datos">';
	echo '<legend>Nuevo Grupo</legend>';
	echo '<table>';
	echo '<tr><td>Nombre del grupo:</td>';
	echo '    <td><input id="input_grupo" type="text" name="capt_grupo" value=""/></td></tr>';
	echo '<tr><td>Acceso a Listados:</td>';
	echo '    <td><input type="checkbox" name="capt_sw_l" /></td></tr>';
	echo '<tr><td>Acceso a Acciones:</td>';
	echo '    <td><input type="checkbox" name="capt_sw_a" /></td></tr>';
	echo '<tr><td>Acceso a datos:</td>';
	echo '    <td><input type="checkbox" name="capt_sw_d" /></td></tr>';
	echo '<tr><td colspan="2"><center>
		<input class="button" type="submit" name="guardar_datos" value="Guardar datos"/>
		<input class="button" type="submit" name="Cancelar" value="Cancelar"/></center></td></tr>';
	echo '</table>';
	echo '</fieldset>';
}
if (@$guardar_datos) {
	$Res=myQUERY("SELECT MAX(grupoid) FROM grupos");
	$grupoid=$Res[0][0]+1;
	$t1=(@$capt_sw_l?1:0); $t2=(@$capt_sw_a?1:0); $t3=(@$capt_sw_d?1:0);
	$Query="INSERT INTO grupos VALUES ($grupoid,'$capt_grupo',$t1,$t2,$t3) ON DUPLICATE KEY UPDATE grupoNombre='$capt_grupo',sw_Listados=$t1,sw_Acciones=$t2,sw_Datos=$t3";
	myQUERY($Query);
	$Mensaje="NUEVOS DATOS GUARDADOS!!";
}

// --------------------------------------------------------------------- BORRAR
if (@$Borrar) {
	myQUERY("DELETE FROM grupos WHERE grupoid=$Borrar");
	myQUERY("DELETE FROM scripts_x_grupo WHERE id_grupo=$Borrar");
	$Mensaje="DATOS BORRADOS!!";
}

$Grupos= myQUERY("SELECT * from grupos order by grupoid");

?>
<script type="text/javascript">
function Borra_Registro(x) {
	if (confirm("¿Desea eliminar este grupo y todos los usuarios que pertenecen a el?")) {
		INPUT_HIDDEN('Borrar',x.name,'myForm'); $("#myForm").submit();
	}
}

function Salvar_Datos(x) {
	if (confirm("¿Desea guardar las modificaciones?")) {
		INPUT_HIDDEN('guardar',x.name,'myForm'); $("#myForm").submit();
	}
}
</script>
</body>