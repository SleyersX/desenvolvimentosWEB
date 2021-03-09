<?php
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
if ($PAIS_SERVER != "ESP" )
	header("Location:/Resources/Estado_Monitorizacion/monitorizacion.php");
?>

<html>
<head>
	<title>Pagina Principal Soporte Remoto</title>
	<meta http-equiv="refresh" CONTENT="60">
	<link rel="stylesheet" type="text/css" href="/Resources/css/miestilo.css" />
	<script src="<?php $DIR_LIBRERIAS; ?>/HSR/scripts.js" runat="server"> </script>
	<script src="/Resources/Datos_Produccion.txt"></script>
</head>

<style type="text/css">
	#ENLACES_DIRECTOS td { width: 140; height: 140; }
	#ENLACES_DIRECTOS td img { width: 95%; height: 95%; }
</style>

<body>

<div id="SUMARIO" class="B-BOX">
	<div id="RESALTADOS" class="B-BOX REDONDO SOMBRA_NEGRA">
		<h3>Resaltados</h3>
		<ul id="LISTA_RESALTADOS"></ul>
	</div>
	<h2>¿Qu&eacute; es la herramienta de soporte remoto?</h2>
	<p> La herramienta de soporte remoto <Q>HSR</Q> es una utilidad para facilitar los soportes a las tiendas: acceso a las tiendas, ejecuci&oacute;n de scripts de forma remota y obtener listados y logs de las TPVs. </p>
	<p> Descubra m&aacute;s acerca de esta herramienta en la secci&oacute;n
	<a href="/wiki" title="Terminos y documentacion de SR"><b>Documentaci&oacute;n</b></a> </p>
</div>

<div id="NOTICIAS" class="B-BOX REDONDO">
	<h2>Ultimas noticias:</h2>
	<ul id="LAST_NEWS"></ul>
</div>

<div id="UTILIDADES">
	<table> <tr>
	<td width=100><img src="/img/Monitorizacion.jpg" height="50%"/></td>
	<td><h2>Acceso a paises:</h2>
		<table id="ENLACES_DIRECTOS">
				<tr>
					<td><a href="http://10.94.202.121"><img src="/img/icono_ARG.png" title="Estado tiendas Argentina" /></a></td>
					<td><a href="http://10.105.186.135"><img src="/img/icono_BRA.png" title="Estado tiendas Brasil" /></a></td>
					<td><a href="/Resources/Estado_Monitorizacion/monitorizacion.php?Pais=ESP"><img src="/img/icono_ESP.png" title="Estado tiendas N2A" /></a></td>
				</tr>
				<tr>
					<td><a href="http://10.246.64.73"><img src="/img/icono_POR.png" title="Estado tiendas Portugal" /></a></td>
					<td><a href="http://10.132.221.18"><img src="/img/icono_CHI.png" title="Estado tiendas China" /></a></td>
					<td><a href="http://10.95.81.137/Resources/Estado_Monitorizacion/monitorizacion.php?Pais=PAR"><img src="/img/icono_PAR.png" title="Estado tiendas Paraguay" /></a></td>
				</tr>
		</table>
	</td> </tr> </table>
	<hr>
	<table> <tr> 
	<td width=100><img src="/img/earth_tools.jpg" height="40%"/></td>
	<td> <h2>Enlaces directos:</h2>
		<div id="ENLACES_DIRECTOS">
			<a href="http://entorno.tpvs/sa/"><img src="/img/logoSA.png" title="Trac de SA"/></a>
			<a href="http://europa.lares.dsd/projects/N2A/"><img src="/img/trac_banner_dia.png" title="Trac de N2A"/></a>
			<a href="http://zeus/SM7/"><img src="/img/HP.jpg" title="HP Service Manager" /></a>
		</div>
	</td> </tr> </table>
</div>

</body>
</html>
