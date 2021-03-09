var month_es=new Array("ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic");
var day_es=new Array("lunes","martes","miercoles","jueves","viernes","sabado","domingo");
var HostName = window.location.hostname;

/* --------------------------------------------------------------------------------------------------------------- */
/* VARIABLES DE ENTORNO */
/* Las configuraciones de las paginas y el array de Paises estan en un fichero llamado Resources/Datos.txt. */
/* --------------------------------------------------------------------------------------------------------------- */
/* UBICACIONES WEB y DIRECTORIOS GENERALES*/
var dir_raiz       = "/";
var www_null       = "#";
var www_index      = dir_raiz+"index.html";
var www_wiki       = dir_raiz+"wiki.html";
var www_downloads  = dir_raiz+"downloads.html";
var www_about      = dir_raiz+"about.html";
var dir_scripts    = dir_raiz+"Resources/styles_js/";
var dir_styles     = dir_raiz+"Resources/styles_js/";
var dir_images     = dir_raiz+"img/";

function Check_Operatividad_Paises() {
	for (ii=0; ii<Paises.length; ii++) {
		aaa = document.getElementById("Flag_"+Paises[ii].Pais);
		bbb = document.getElementById("span_"+Paises[ii].Pais);
		if (Paises[ii].Visible) {
			if (Paises[ii].Operativo) {
				aaa.className = "FLAG ACTIVO";
				bbb.innerHTML = "<img src='" + dir_images +"J0300520.GIF'/>" + Paises[ii].Pais + ": <br> Operativo";
			} else {
				aaa.className = "FLAG INACTIVO";
				bbb.innerHTML = Paises[ii].Pais + ": <br> No Operativo..."; bbb.style.color = "red"; 
			}
		} else  {
			aaa.hidden = true;
		}
	}
}

function Mete_Header( pagina ) {
	var clases = new Array('','','','');
	CABECERA  = '<table style="width:100%" cellspacing="0" cellpadding="0" >';
	CABECERA += '<tbody>';
	CABECERA += '<tr class="Pagina_Cabecera">';
	CABECERA += '<td valign="center" width="170" class="Pagina_Cabecera"><img src="/img/logo_dia2.gif" border="0"></td>';
	CABECERA += '<td valign="bottom">'+document.title+'</td>';
	CABECERA += '</tr></tbody></table>';

	if (pagina < 5) {
		clases[pagina-1] = 'class="active"';
		OPCIONES  = '<li '+clases[0]+'><a href="/">INICIO</a></li>';
		OPCIONES += '<li '+clases[1]+'><a href="/docs/Intervenciones/">INTERVENCIONES</a></li>';
		OPCIONES += '<li '+clases[2]+'><a href="/Resources/Portal/downloads.html">DESCARGAS</a></li>';
		OPCIONES += '<li '+clases[3]+'><a href="/Resources/Portal/about.html">ACERCA DE</a></li>';
	} else {
		OPCIONES  = '<li><a href="/">INICIO</a></li>';
	}

	NAVEGADOR  = '<nav id="head-nav" class="navbar navbar-fixed-top">';
	NAVEGADOR += '<div class="navbar-inner clearfix">';
	NAVEGADOR += '<ul class="nav">';
	NAVEGADOR += OPCIONES;
	NAVEGADOR += '</ul>';
	NAVEGADOR += '</div>';
	NAVEGADOR += '</nav>';

	// Le metemos los datos de la cabecera.
	document.body.innerHTML = CABECERA + NAVEGADOR + document.body.innerHTML;
}

function Crea_Barra_Progreso( ID_Barra, Valor ) {
	return '<div class="box"> <div class="bar bg-one" style="width:'+Valor+'%">'+Valor+'%</div></div>';
}

function tgdiv() {
	var di = document.getElementById('ANTIGUOS');
	var tg = document.getElementById('link1');
	di.className = 'HIDDEN';
	tg.onclick = function(){
		if (di.className == 'HIDDEN'){
			di.className = 'SHOW';
			tg.className="FLECHA ARRIBA"
		}else{
			di.className = 'HIDDEN';
			tg.className="FLECHA ABAJO"
		}
	}
}

function cargarDatos(){
	aaa = document.getElementById("LISTA_RESALTADOS");
	for (ii=0; ii<(Resaltados.length>3?3:Resaltados.length); ii++) {
		aaa.innerHTML=aaa.innerHTML+"<li><a href="+Resaltados[ii].href+">"+Resaltados[ii].Texto+"</a></li>";
	}
	aaa = document.getElementById("LAST_NEWS");
	for (ii=0; ii<(Noticias.length>4?5:Noticias.length); ii++) {
		aaa.innerHTML=aaa.innerHTML + "<li><a class=\"FECHA1\">"+Noticias[ii].Fecha+"</a>" +
			Noticias[ii].Texto +
			(Noticias[ii].href!=www_null?"<br><a href="+Noticias[ii].href+" target='_blank'>Ver detalles...</a>":"") +
			"</li>";
	}
	aaa = document.getElementById("ENLACES_DIRECTOS");
}

function abrirVentana(url) {
	window.open(url, "nuevo", "directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=400, height=400");
}

function INPUT_HIDDEN(Name,Valor,myForm) {	
	var form = document.getElementById(myForm);
	var hiddenField = document.createElement("input"); 
	hiddenField.setAttribute("name", Name);
	hiddenField.setAttribute("type", "hidden");
	hiddenField.setAttribute("value", Valor);
	form.appendChild(hiddenField);
}

function Ir_a_conectar(Tienda, Caja) {
	window.open('conectar.php?Tienda='+Tienda+'&Caja='+Caja,'_blank');
}
