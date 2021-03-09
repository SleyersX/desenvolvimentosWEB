<title>GESTION USUARIOS</title>
<?php

require($_SERVER['DOCUMENT_ROOT']."/config.php");
require($_SERVER['DOCUMENT_ROOT'].$DIR_TOOLS.'head_1.php');
require_once("./comun_administracion.php");
// PREPARAMOS LA VISTA PARA LA TABLA DINAMICA.


$Array_Lista_Campos_Usuarios=array(
"{ index: 'usuarioID', name: 'usuarioID', label:'Id', key: true, width: 50, hidden:true }",
"{ index: 'usuario', name: 'usuario', label:'ID.Usuario', width: 100 }",
"{ index: 'nombre', name: 'nombre', label:'Nombre completo', width: 250 }",
"{ index: 'grupo', name: 'grupo', label:'grupo', key: true, width: 150, hidden:true }",
"{ index: 'logged', name: 'logged', label:'LOGUEADO', key: true, width: 150,
	stype: 'select', searchoptions: { value: ':[All]".Prepara_Lista_Select("logged","tmpUsuarios")."' } }",
"{ index: 'LastLogged', name: 'LastLogged', label:'ULTIMA VEZ', key: true, width: 150 }",
"{ index: 'grupoID', name: 'grupoID', label:'grupoID', width: 50, hidden:true }",
"{ index: 'grupoNombre', name: 'grupoNombre', label:'ROL', key: true, width: 150,
	stype: 'select', searchoptions: { value: ':[All]".Prepara_Lista_Select("grupoNombre","tmpUsuarios")."' } }",
);
$Array_Lista_Campos_Grupos=array(
"{ index: 'grupoID', name: 'grupoID', label:'grupoID', width: 50, hidden:true }",
"{ index: 'grupoNombre', name: 'grupoNombre', label:'Nombre Grupo', width: 200}",
"{ index: 'sw_Listados', name: 'sw_Listados', label:'Listados', width: 50}",
"{ index: 'sw_Acciones', name: 'sw_Acciones', label:'Acciones', width: 50}",
"{ index: 'sw_Datos',    name: 'sw_Datos',    label:'Datos', width: 50}",
);

/*	
+-------------+--------------+------+-----+---------+-------+
| Field       | Type         | Null | Key | Default | Extra |
+-------------+--------------+------+-----+---------+-------+
| usuarioID   | mediumint(9) | NO   |     | 0       |       |
| usuario     | varchar(8)   | NO   |     |         |       |
| nombre      | varchar(100) | NO   |     |         |       |
| grupo       | mediumint(9) | NO   |     | 0       |       |
| logged      | tinyint(1)   | YES  |     | NULL    |       |
| LastLogged  | datetime     | YES  |     | NULL    |       |
| grupoID     | mediumint(9) | NO   |     | 0       |       |
| grupoNombre | varchar(100) | NO   |     | USUARIO |       |
| sw_Listados | tinyint(4)   | NO   |     | 1       |       |
| sw_Acciones | tinyint(4)   | NO   |     | 1       |       |
| sw_Datos    | tinyint(4)   | NO   |     | 1       |       |
+-------------+--------------+------+-----+---------+-------+
*/
$Lista_Campos_Usuarios="";
foreach($Array_Lista_Campos_Usuarios as $k => $d) {
	$Lista_Campos_Usuarios.=($k==0?"":",").$d;
}

$Lista_Campos_Grupos="";
foreach($Array_Lista_Campos_Grupos as $k => $d) {
	$Lista_Campos_Grupos.=($k==0?"":",").$d;
}

?>
<style>
	#jqGridPager * { color:white; }
	.Offline { background:lightgray; color:auto; }
	.Online { background:lightgreen; color:auto; }
</style>

<div>
	<div class="ui-jqgrid " id="d_usuarios" dir="ltr">
		<table id="jqGrid_usuarios" class="F_SIZE_12"></table><div id="jqGridPager_usuarios"></div>
	</div>
	<div class="ui-jqgrid " id="d_grupos" dir="ltr">
		<table id="jqGrid_grupos" class="F_SIZE_12"></table><div id="jqGridPager_grupos"></div>
	</div>
</div>
<script type="text/javascript"> 
// 	$.jgrid.defaults.responsive = true;
	var local_dir=DIR_RAIZ+'/Estado_Monitorizacion/Administracion';

	$(document).ready(function () {
		$("#jqGrid_usuarios").jqGrid({
			caption: '- USUARIOS -',
			url: local_dir+'/json_usuarios.php?callback=?&qwery=orders',
			mtype: "GET",
			datatype: "jsonp",
			colModel: [ <?php echo $Lista_Campos_Usuarios; ?> ],
			sortname:"usuario",
			gridview: true,
			viewrecords: false,
			page: 1,
			height:550,
			autowidth: true,
			rowNum: 50,
			scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
			pager: "#jqGridPager_usuarios",
			search: true,
			refresh: true,
 			sortable: true,
			shrinkToFit: false,
		});

		$('#jqGrid_usuarios').jqGrid('filterToolbar',{ stringResult: true });
		$('#jqGrid_usuarios').navGrid("#jqGridPager_usuarios", {
			search: true, edit:true, add:true, del:true, refresh: true
		});

		$("#jqGrid_grupos").jqGrid({
			caption: '- GRUPOS -',
			url: local_dir+'/json_grupos.php?callback=?&qwery=orders',
			mtype: "GET",
			datatype: "jsonp",
			colModel: [ <?php echo $Lista_Campos_Grupos; ?> ],
			sortname:"grupo",
			gridview: true,
			viewrecords: false,
			page: 1,
			height:650,
			autowidth: true,
			rowNum: 50,
			scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
			pager: "#jqGridPager_grupos",
			search: true,
			refresh: true,
 			sortable: true,
			shrinkToFit: false,
		});

		$('#jqGrid_grupos').jqGrid('filterToolbar',{ stringResult: true });
		$('#jqGrid_grupos').navGrid("#jqGridPager_grupos", {
			search: true, edit:true, add:true, del:true, refresh: true
		});

	});
</script>

</body>
</html>