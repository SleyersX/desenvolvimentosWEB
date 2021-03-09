<?php
require_once("./comun_administracion.php");

$Scripts = myQUERY("SELECT * from scripts_web where Pais like '%$Pais%' ORDER BY tipo,script");
$Grupos= myQUERY("SELECT * from grupos order by grupoid");
$Paises_Check=array("ARG","BRA","ESP","POR");
$Lista_Tipos=myQUERY("SELECT DISTINCT(Tipo) FROM scripts_web group by Tipo");
$Lista_Grupos=myQUERY("SELECT DISTINCT(grupoNombre) from grupos order by grupoNombre");
$Usuarios= myQUERY("SELECT * from usuarios where grupo in (select grupoid from grupos where gruponombre like '%".@$select_grupos."%') order by usuario");

echo '<form id="myForm" name="myForm" action="'.basename($SCRIPT_FILENAME).'" method="post">';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($Guardar) || isset($guardar_datos))  {
	Actualiza_Datos("UPDATE scripts_x_usuario SET Valor=0");
	if (isset($checkboxvar))
		foreach($checkboxvar as $d) {
			Actualiza_Datos("INSERT INTO scripts_x_usuario SET $d,Comentario='',Valor=1 ON DUPLICATE KEY UPDATE Valor=1");
		}

	$Mensaje=Alert("success", "Datos guardados correctamente");
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($Aplicar_Filtro)) {
	$Mensaje=Alert("success","Filtro aplicado: <br><b>Tipo:</b>".@$select_tipos."<br><b>Grupo:</b>".@$select_grupos);
}

echo '<table style="width:100%">';
echo '<tr>';
echo '<td id="td_Menu" valign="top" width="20%">';

echo '<fieldset><legend>Filtros</legend>';
echo '<p>';
echo '<select class="select_opcion" name="select_tipos">'.Options_Select("Tipo", $Lista_Tipos).'</select>';
echo '</p>';
echo '<p>';
echo '<select class="select_opcion" name="select_grupos">'.Options_Select("Grupo", $Lista_Grupos).'</select>';
echo '</p>';
echo '<button type="submit" name="Aplicar_Filtro">Aplicar filtro</button>';
echo '<button type="submit" name="Reset">Reset Filtro</button>';
echo '</fieldset>';

echo '<fieldset><legend>Menu de opciones</legend>';
echo '<ul>';
echo '<li><a href="./gestion.php">Menu principal</a></li><br>';
// echo '<li><a href="javascript:{}" onclick="Nuevo_Registro()">Nuevo Grupo</a></li>';
echo '<li><a href="javascript:{}" onclick="Salvar_Datos()">Salvar Datos</a></li>';
echo '</fieldset>';

if (isset($Mensaje)) echo $Mensaje;

echo '</td>';

echo '<td id="td_Gestion">';

echo '<fieldset><legend>Listado de Scripts Por Usuario</legend>';
echo '<div id="div_de_datos">';

echo '<table class="table_gestion">';
echo '<thead><tr><th>Listado</th>';
foreach($Usuarios as $k => $d) echo '<th class="rotate" title="'.$d[2].'"><div><span>'.$d[1].'</span></div></th>';
echo '</tr></thead>';
foreach ($Scripts as $d) {
	list($id_script,$script,$tipo,$php) = $d;
	echo '<tr>';
	echo '<td>'.$script.'</td>';
	foreach($Usuarios as $k2 => $d2) {
		list($usuarioid,$usuario,$nombre,$grupo) = $d2;
		$Res = myQUERY("select valor from scripts_x_usuario where id_usuario=$usuarioid and id_script=$id_script");
		$Valor = ($Res[0][0]==1);
		if ($Valor) $checked="checked"; else $checked="";
		echo "<td style='text-align:center'><input type='checkbox' name='checkboxvar[]' value='id_usuario=$usuarioid,id_script=$id_script' ".$checked."/></td>";
	}
	echo '</tr>';
}
echo "</table>";

echo '</div>';
echo '</fieldset>';
echo '</td>';
echo '</tr>';
echo '</table>';
?>

<script type="text/javascript">
function Nuevo_Registro() {
	INPUT_HIDDEN('Nuevo_Registo','Nuevo_Registo','myForm'); $("#myForm").submit();
}

function Borra_Registro(x) {
	if (confirm("¿Desea eliminar este registro?")) {
		INPUT_HIDDEN('Borrar',x.name,'myForm'); $("#myForm").submit();
	}
}

function Salvar_Datos() {
	if (confirm("¿Desea guardar las modificaciones?")) {
		INPUT_HIDDEN('Guardar','Guardar','myForm'); $("#myForm").submit();
	}
}

Ajustar_Altura("div_de_datos");


</script>
</body>