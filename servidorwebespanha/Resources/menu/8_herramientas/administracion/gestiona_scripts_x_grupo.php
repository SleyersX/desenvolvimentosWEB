<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!empty($_GET["opcion"])) {
	require_once("/home/soporteweb/tools/mysql.php");
	require_once("./actualiza_datos.php");
	foreach($_GET as $k => $d) $$k=$d;
	switch($_GET["opcion"]) {
	
		case "update_scripts_x_grupos":
			$listado='<table class="tabla2"><thead><tr><th class="oculto">ID_SCRIPT</th><th>SCRIPT</th>';
			$tmp = myQUERY("select grupoid, gruponombre from grupos order by grupoid");
			foreach($tmp as $k => $d) {
				$Lista_Grupos[$d[0][0]]=$d;
				$listado.="<th>".$d[1]."</th>";
			}
			$listado.="</tr></thead>";
			$tmp = myQUERY("select * from scripts_web order by script");
			foreach($tmp as $k => $d) {
				list($id_script,$script,$tipo,$php,$pais,$descripcion) = $d;
				$listado.="<tr class='row_grupos'>";
				$listado.="<td id='id_script' class='oculto'>".$id_script."</td><td>".$script."</td>";
				$tmp1 = myQUERY("SELECT id_grupo,valor FROM scripts_x_grupo WHERE id_script=$id_script order by id_grupo") ;
				foreach($tmp1 as $d1) {
					list($id_grupo, $valor) = $d1;
					$listado.="
						<td class='valor ".($valor?"c_ON":"c_OFF")."' title='Pulse aqu&iacute; para cambiar el estado (ON/OFF)'>
							<span id='id_grupo' class=''>".$id_grupo."</span>
							<span id='s_valor'>".($valor?"ON":"OFF")."</span>
						</td>";
				}
				$listado.="</tr>";
			}
			$listado.='</table>';

			echo $listado;
			break;

		case "salvar_datos":
			Actualiza_Datos("UPDATE scripts_x_grupo SET Valor=$valor WHERE id_grupo=$id_grupo AND id_script=$id_script");
/*
			$contador=0;
			while($contador<5) {
				$tmp=myQUERY("select valor from scripts_x_grupo WHERE id_grupo=$id_grupo AND id_script=$id_script");
				if ($Valor==$tmp[0][0]) break;
				sleep(1);
				$contador++;
			}
*/
			echo "DATOS MODIFICADOS!!";
			break;
	}
	exit;
}

require("./comun_administracion.php");

$Alto_Pantalla=(!empty($_SESSION['screen_height'])?$_SESSION['screen_height']:1000);
$Alto_Trabajo=$Alto_Pantalla*85/100;

?>
<style>
	.oculto { display: none; }
	#div_de_datos { overflow: auto; height: <?php echo $Alto_Trabajo*100/100; ?>; width: 100%; }
	.valor { font-weight: bold; text-align: center !important;}
	.c_ON { background-color: lightgreen; } 
	.c_OFF { background-color: red; } 
	.tabla2 tr.row_grupos:hover  { border:2px solid blue !important;}
	#div_de_datos .tabla2 td { border:1px solid gray !important;}
	#div_de_datos .tabla2 th { border:1px solid gray !important; text-align: center !important;}
</style>

<table style="width:100%">
	<tr>
		<td id="td_Menu" valign="top" width="10%">
			<fieldset><legend>Menu de opciones</legend>
			<ul>
				<li><a href="./gestion.php">Menu principal</a></li>
				<div id="resultado"></div>
			</ul>
			</fieldset>
		</td>
		<td id="td_Gestion" width="90%">
			<fieldset style="width:100%">
				<legend>Listado de Scripts x Grupos</legend>
				<div id="div_de_datos" ></div>
			</fieldset>
		</td>
	</tr>
</table>

<script type="text/javascript">
	var url_local="<?php echo basename(__FILE__); ?>";
	var cambio_en_proceso=false;

	function Pon_Datos(parametros) {
		$("#i_id_script").val(parametros.id_script);
		$("#i_opcion").val(parametros.opcion);
		$("#i_sw_listados")[0].checked = (parametros.sw_listados=="ON");
		$("#i_sw_acciones")[0].checked = (parametros.sw_acciones=="ON");
		$("#i_sw_datos")[0].checked = (parametros.sw_datos=="ON");
	}

	function Recarga() {
		$("#div_de_datos").load(url_local+"?opcion=update_scripts_x_grupos",function () {
			$(".row_grupos td.valor").on("click",function (e) {
				if (cambio_en_proceso) { alert("Espere a que termine la modificacion de datos en el servidor"); return; }
				e.preventDefault();
				var id_script=$(this).parent().find("#id_script");
				var s_valor=$(this).find("#s_valor");
				var id_grupo=$(this).find("#id_grupo");
				console.log(id_script.html(),id_grupo.html(),s_valor.html());
				if (s_valor.html() == "ON") {
					s_valor.html("OFF"); $(this).removeClass("c_ON").addClass("c_OFF");
				} else {
					s_valor.html("ON"); $(this).removeClass("c_OFF").addClass("c_ON");
				}
				$("#resultado").html("Modificando valores<br>Espere por favor...");
				cambio_en_proceso=true;
				var parametros = { opcion:"salvar_datos", id_grupo:id_grupo.html(), id_script:id_script.html(), valor:(s_valor.html()=="ON")}
				$.get(url_local, parametros, function( data ) {
					$("#resultado").html(data);
					Recarga();
					cambio_en_proceso=false;
				});
				
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

<?php
/*
require_once("./comun_administracion.php");

$Scripts = myQUERY("SELECT * from scripts_web where Pais like '%$Pais%' ORDER BY tipo,script");
$Grupos= myQUERY("SELECT * from grupos order by grupoid");
$Paises_Check=array("ARG","BRA","ESP","POR");
$Lista_Tipos=myQUERY("SELECT DISTINCT(Tipo) FROM scripts_web group by Tipo");
$Lista_Grupos=myQUERY("SELECT 	(grupoNombre) from grupos order by grupoNombre");

echo '<form id="myForm" name="myForm" action="'.basename($SCRIPT_FILENAME).'" method="post">';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($Guardar))  {
	Actualiza_Datos("UPDATE scripts_x_grupo SET Valor=0");
	foreach($checkboxvar as $id_grupo => $d)
		foreach($d as $id_script => $d2)
			Actualiza_Datos("UPDATE scripts_x_grupo SET Valor=1 WHERE id_grupo=$id_grupo AND id_script=$id_script");
	$Mensaje=Alert("success", "Datos guardados correctamente");
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($guardar_datos)) {
	echo "";
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (isset($Borrar)) {
	echo "";
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

if (isset($Nuevo_Registo)) {
	echo '<fieldset>';
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
		<input type="submit" name="guardar_datos" value="Guardar datos"/>
		<input type="submit" name="Cancelar" value="Cancelar"/></center></td></tr>';
	echo '</table>';
	echo '</fieldset>';
}

echo '</td>';

echo '<td id="td_Gestion">';

echo '<fieldset><legend>Listado de Grupos</legend>';
echo '<div id="div_de_datos">';

echo '<table class="table_gestion">';
echo '<thead><tr><th>Script</th>';
foreach($Grupos as $k => $d) {
	list($grupoid,$nombregrupo,$sw_listados,$sw_acciones,$sw_datos) = $d;
	if (!empty($select_grupos) && $select_grupos != $nombregrupo) continue;
	echo "<th>$nombregrupo</th>";
}
echo '</tr></thead>';
$Query="";
$_POST['checkboxvar']=array();
foreach ($Scripts as $k => $d) {
	list($id_script,$script,$tipo,$php) = $d;
	if (!empty($select_tipos) && $tipo != $select_tipos) continue;
	echo '<tr>';
	echo '<td>'.$script.'</td>';
	foreach($Grupos as $k2 => $d2) {
		list($grupoid,$nombregrupo,$sw_listados,$sw_acciones,$sw_datos) = $d2;
		if (!empty($select_grupos) && $select_grupos != $nombregrupo) continue;
		Actualiza_Datos("INSERT INTO scripts_x_grupo VALUES ($grupoid,$id_script,'',0) ON DUPLICATE KEY UPDATE Valor=Valor");
		$Res = myQUERY("select valor from scripts_x_grupo where id_grupo=$grupoid and id_script=$id_script");
		$Valor = ($Res[0][0]==1);
		if ($Valor) $checked="checked"; else $checked="";
		echo "<td style='text-align:center'><input type='checkbox' name='checkboxvar[$grupoid][$id_script]' ".$checked. " value=$Valor oncheck=\"javascript:this.value=1;\"/></td>";
	}
	echo '</tr>';
}
echo "</table>";

echo '</div>';
echo '</fieldset>';
echo '</td>';
echo '</tr>';
echo '</table>';
*/
?>
