<style>
	#TABLE_CONECTAR {
		border-collapse:collapse; width:100%;border:2px solid black
	}
	#TABLE_CONECTAR td { border:2px solid black }
	#TR_TRABAJO { height:790px }
	#TD_DATOS_CAJA { width:25%; }
	#TD_DATOS_CUERPO { width:75%; }
	.GIF_ESPERA { margin:0 0 auto; left:50%; }
</style>
<table id="TABLE_CONECTAR">
<tr><td colspan="2">
<div id="DIV_CABECERA" style="position:relative">
	<div id="CAB1">
		<table width="100%">
		<tr>
			<td id="DIV_PAIS" width="20%">
				<img id="ICONO_PAIS" src="/favicon.ico" ></img>
				<img src="/img/logo_dia2.gif" title="Pagina del Portal" height="50px">
			</td>
			<td width="20%" id="DIV_CENTRO" ></td>
			<td id="DIV_USUARIO"></td>
			<td id="RELOJ_MONITOR"></td>
		</tr>
		</table>

	<!-- BARRA DE MENU -->
		<nav id="head-nav" class="navbar navbar-fixed-top">
		<ul class="nav">
			<li id="li_LISTADOS"><a>LISTADOS<span class="flecha">&#9660</span></a></li>
			<li id="li_ACCIONES"><a>ACCIONES<span class="flecha">&#9660</span></a></li>
			<li id="li_DATOS"><a>DATOS<span class="flecha">&#9660</span></a></li>
		</ul>
		<div class="navbar-inner clearfix"><ul class="nav"></ul></div>
		</nav>
	</div>
</div>
</td></tr>
<tr id="TR_TRABAJO">
	<td id="TD_DATOS_CAJA"><div id="DATOS_CAJA"></div></td>
	<td id="TD_DATOS_CUERPO"><div id="DATOS_CUERPO"></div></td>
</tr>
</table>
