<div class="Aviso" style="font-size:90%">
<?php
switch ($_SESSION['Idioma']) {
case "ESP":
	echo '
<p>Esta p&aacute;gina sirve para realizar b&uacute;squedas de tiendas por diversos filtros. Las b&uacute;squedas se pueden realizar:</p>
<ul>
	<li><b>Por c&oacute;digo de tienda.</b> Hay que escribir el c&oacute;digo de tienda en el cuadro "Tienda".</li>
	<li><b>Por versi&oacute;n.</b> Elegimos la versi&oacute;n de entre las existentes en la red de tiendas del pa&iacute;s.</li>
	<li><b>Por CENTRO, TIPO, SUBTIPO, POBLACION y PROVINCIA.</b> Datos almacenados en el maestro de tiendas del pa&iacute;s</li>
	<li><b>Por IP.</b> Al poner una IP en ese cuadro, haremos una b&uacute;squeda de todos los elementos que hay detr&aacute;s del router de esa tienda</li>
</ul>
<p><i><b>NOTA:</b> Podemos utilizar varios criterios de b&uacute;squeda. Ejemplo: tiendas con versi&oacute;n determinada y que pertenezcan a un determinado centro.</i></p>
<ul style="list-style-type: none;">
	<li style="margin-top:0.5em;"><span class="button">Buscar</span> para acceder a una nueva p&aacute;gina donde podremos ver el resultado de la b&uacute;squeda.</li>
	<li style="margin-top:0.5em;"><span class="button">Limpiar</span> para limpiar los criterios y crear una nueva b&uacute;squeda.</li>
	<li style="margin-top:0.5em;"><span class="button">Avanzada...</span> para acceder a mas opciones de busqueda.</li>
</ul>
</div>
';
	break;
case "ENG":
	echo '
<p>This page is to search store with multiple filters. Searches can be made:</p>
<ul>
	<li><b>By store code. </b>You have to write the code store in the "Store" tab.</li>
	<li><b>By version. </b>We chose the version number in the existing network of stores in the country.</li>
	<li><b>By CENTRO, type, subtype, town and province. </b>Data stored in the master stores in the country</li>
	<li><b>By IP. </b>By putting an IP in that box, we do a search of all the elements behind the router that store</li>
</ul>
<p><i><b>NOTE: </b>We can use multiple criteria. Example: shops with particular version and belonging to a particular center.</i></p>
<ul style="list-style-type: none;">
	<li style="margin-top:0.5em;"><span class="button">Search</span> to access a new page where we can see the search results.</li>
	<li style="margin-top:0.5em;"><span class="button">Clean</span> to clear criteria and create a new search.</li>
	<li style="margin-top:0.5em;"><span class="button">Advanced...</span> to access more search options.</li>
</ul>
</div>
';
	break;
}
?>