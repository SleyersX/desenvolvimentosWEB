<?php

$Textos_Locale=array(

"SELECT_LANGUAGE"=>array(
	"ESP"=>"Seleccione idioma",
	"ENG"=>"Select language"),

"TITLE_LANGUAGE"=>array(
	"ESP"=>"Pulse aqui para cambiar el idioma...",
	"ENG"=>"Click here to change language..."),

"AYUDA_PRE_CONECTAR"=>array(
	"ESP"=>"
		<p>En esta pagina se pueden ver los datos generales de la tienda, los elementos conectados y los datos de las TPVs configuradas en la caja master</p>
		<ul>
			<li>Pulse en el boton de la IP para poder cambiar la IP de la tienda temporalmente (<i>Solo Administradores, V. Nacional y V. Internacional</i>)</li>
			<li>Pulse en el boton de la CAJA para conectar directamente con la caja en remoto. Si el boton esta rojo, es que no hay acceso a esa caja.</li>
			<li>Pulse el boton de <b>CERRAR</b> para salir de esta pagina.</li>
			<li>Pulse el boton de <b>ACTUALIZAR</b> para recargar los datos de la tienda en ese momento. (<i>Esta operacion puede llevar varios segundos completarse</i>).</li>
		</ul>",
	"ENG"=>"
		<p>In this page you can see the general store data, the connected elements and POS data set in the POS-master</p>
		<ul>
			<li>Click the button of the <b>IP</b> to change temporarily the IP Address of the store (<i> Only Administrators, V. Nacional and V. Internacional</i>)</li>
			<li>Click the button on `POS` to connect directly to the POS remotely. If the button is red, there is no access to that POS.</li>
			<li>Click the <b>Close</b> button to exit this page.</li>
			<li>Clcil the <b>UPDATE</b> button to reload the data from the store at the time. (<i>This operation may take several seconds to complete</i>). </li>
		</ul>"),

"NO_INFO_TPVS"=>array(
	"ESP"=>"
		NO HAY INFORMACIOacute;N DE TPVS...<br> Pulse en <u>Actualizar</u>
		<p><i>NOTA: posiblemente puede estar pasando:
		<ul>
			<li>La caja master estaba desconectada en el momento de ir a buscar sus datos</li>
			<li>La caja master tiene problemas en el disco duro o est&aacute; lleno.</li>
			<li>La informaci&oacute;n de conexi&oacute;n a la tienda es incorrecta (comprobar direcci&oacute;n IP)</li>
			<li>Algunos datos de la tienda o cajas son erroneos, como por ejemplo, que tenga un numero de tienda diferente.</li>
		</ul>
		<p>Si el problema persiste, p&oacute;ngase en contacto con Ventas Nacional para la gesti&oacute;n de este problema</p>",
	"ENG"=>"
		<b>NO DATA STORE ... </b><br> Press <u> Update </u>
		<p><i>NOTE: this may be happening:
		<ul>
			<li> The POS master was disconnected at the time of fetching data </li>
			<li> The POS master having problems on hard drive or it is full. </li>
			<li> The connection information to the store is incorrect (see IP address) </li>
			<li> Some store data on POS are wrong, such as having a number of different store. </li>
		</ul>
		<p>If the problem persists, please contact Ventas Naciona/Internacional to manage this problem </p>"),

"NO_SCRIPTS_PAIS"=>array(
	"ESP"=>"<p>NO HAY SCRIPTS DEFINIDOS PARA ESTE PAIS.</p><p>P&oacute;ngase en contacto con el administrador de la herramienta de su pa&iacute;s o con Soporte Remoto Nivel 3 de SEDE</p>",
	"ENG"=>"<p>NO SCRIPTS FOR THIS COUNTRY.</p><p>If the problem persists, please contact Ventas Naciona/Internacional to manage this problem </p>")

);

function pTextos($Texto) {
	global $Textos_Locale;
	if (empty($_SESSION["Idioma"])) $_SESSION["Idioma"]="ESP";
	return $Textos_Locale[$Texto][$_SESSION["Idioma"]];
}

?>