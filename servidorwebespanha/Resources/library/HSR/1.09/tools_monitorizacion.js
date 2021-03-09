/* VARIABLES DE ENTORNO */

$.ajaxSetup({ cache: false });

function Bloqueo() {
	$("#Estados").text("");
	$("#div_espera").dialog("open");
	return;
}

function Desbloqueo() {
	$("#div_espera").dialog("close");
	$("#Progreso").css("visibility","hidden");
	return;
}

function SUBMIT(formulario) {
	Bloqueo(); document.getElementById(formulario).submit();
}

function SHOW_LOG(Valor) {
	INPUT_HIDDEN('Fichero_Log',Valor,'myForm');
	INPUT_HIDDEN('myAcciones','Ver Logs Aplicacion','myForm');
	SUBMIT('myForm');
}

function SHOW_DE(Fichero, Form) {
	INPUT_HIDDEN('File_DE', Fichero, 'myForm');
	INPUT_HIDDEN('myAcciones','Diario Electronico','myForm');
	SUBMIT('myForm');
}

function descargarArchivo(contenidoEnBlob, nombreArchivo) {
	var reader = new FileReader();
	reader.onload = function (event) {
		var save = document.createElement('a');
		save.href = event.target.result;
		save.target = '_blank';
		save.download = nombreArchivo || 'archivo.dat';
		var clicEvent = new MouseEvent('click', {
			'view': window,
			'bubbles': true,
			'cancelable': true
		});
		save.dispatchEvent(clicEvent);
		(window.URL || window.webkitURL).revokeObjectURL(save.href);
	}
}

function Ir_a_conectar(Tienda, Caja) {
	window.open('conectar.php?Tienda='+Tienda+'&Caja='+Caja,'_blank');
}

function New_Window(url) {
	window.open(url,'_blank');
}

function Progreso(Valor) {
	$("#id_progreso").attr("value",Valor);
	$("pProgress3").html(Valor+"%");
	$("#Progreso").show();
// 	$("#Progreso").css("visibility","visible");
}
function Progreso_2(donde,Valor,texto) {
	$donde.attr("value",Valor);
	$donde.html(Valor+"% "+texto);
	$donde.show();
}
function Estado(Valor) {
	$("#Estados").html(Valor);
}
function Reload() { Bloqueo(); $.post=""; location.reload(); }
function Desconectar() { window.close(); exit; }
function mySelect(x) { Bloqueo(); x.form.submit(); return; }
function ERROR_CONEXION(Texto) {
	Desbloqueo();
	swal({ type: 'error', title: 'ERROR', html: Texto + "<p><i>Por favor, ponte en contacto con el administrador</i></p>"});	
//	$("#div_espera").html('<BUTTON class="button" title="REINTENTAR" onclick="javascript:Reload();"><img id="Imagen_Error_Conexion" src="/img/Error_Conexion_Editado.png"></BUTTON><font color="red" size="10em"><h3>ERROR DE CONEXION</h3>'+Texto+'<br>');
}
function reloj_servidor(Donde) { $("#"+Donde).load(DIR_RAIZ+'/tools/estado_servidor.php?Opcion=HORA_SERVIDOR'); }
function hdd_servidor(Donde)   { $("#"+Donde).load(DIR_RAIZ+'/tools/estado_servidor.php?Opcion=HDD_LITE'); }
function load_servidor(Donde)  { $("#"+Donde).load(DIR_RAIZ+'/tools/estado_servidor.php?Opcion=LOAD'); }
function prog_servidor(Donde)  { $("#"+Donde).load(DIR_RAIZ+'/tools/estado_servidor.php?Opcion=PROGRESO'); }
function disco_duro_servidor()
	{ Ejecuta_AJAX(DIR_RAIZ+'/tools/estado_servidor.php?Opcion=HDD_LITE', 'HDD_Server'); }
function recargar_estado(Pais)
	{ Ejecuta_AJAX(DIR_RAIZ+'/tools/estado_servidor.php?Opcion=PROGRESO&Pais='+Pais, 'Estado_Servidor'); }
function INPUT_HIDDEN(Name,Valor,myForm) {	
	var form = document.getElementById(myForm);
	var hiddenField = document.createElement("input"); 
	hiddenField.setAttribute("name", Name);
	hiddenField.setAttribute("type", "hidden");
	hiddenField.setAttribute("value", Valor);
	form.appendChild(hiddenField);
}

function Activa_Ayuda() { $("#cuerpo").fadeOut(); $(".ayuda").fadeIn(); }
function Desactiva_Ayuda() { $("#cuerpo").fadeIn(); $(".ayuda").fadeOut(); }
function Show_DIV(Etiqueta) { $("#"+Etiqueta).toggle(); }
function Centrar_Elemento(Etiqueta) {
	var p = $("#"+Etiqueta);
	var f_size = p.width()/2;
	var w_size = p.parent().width()/2;
	if (w_size > f_size)
		p.offset({left:(w_size-f_size)});
}
function Ajustar_Altura(Etiqueta) {
	var p=$("#"+Etiqueta);
	p.height($(document).height()-p.offset().top-50);
}
function Ajusta_2(Etiqueta, Overflow) {
	var p = $("#"+Etiqueta);
	p.height(p.parent().height()-p.offset().top);
	if (Overflow) p.css({'overflow': 'auto'});
}
function Oculta_Ventana(Etiqueta) { $("#"+Etiqueta).visibility="hidden"; }
function borra_tmp(dir) { if (dir) window.open("borrar_tmp.php?dir="+dir,"_blank"); }
function Ejecuta_AJAX(donde_resultado, parametros) {
	$.ajax({
		data:  parametros,
		url:   DIR_RAIZ+'/tools/ejecuta_diferido.php',
		type:  'post',
		beforeSend: function () { $("#"+donde_resultado).html("Processing request, please wait..."); },
		success:  function (response) { $("#"+donde_resultado).html(response); },
		statusCode: { 404: function() { alert( "page not found" ); } }
	});
}
function Carga_Datos(Etiqueta,Request) {
	var obj = $('#'+Etiqueta);
	var container = $(obj).parent();
	$(obj).attr('data', Request);
	var newobj    = $(obj).clone();
	$(obj).remove();
	$(container).append(newobj);
// 	Ajusta_Altura(Etiqueta);
}
function Put_Pagina(php,titulo) {
	Put_SESSION("CHG_SESSION","NEW_PAGINA", titulo);
	Put_SESSION("CHG_SESSION","NEW_PHP", php );
}
function Put_SESSION(Opcion_tmp, Variable, NewValor) {
	$.get(DIR_RAIZ+"/tools/tools_comunes.php",{ Opcion:Opcion_tmp, Var:Variable, Valor:NewValor });
}
function Activa_Opc_Menu(php,pagina) {
	$( '#myForm' ).each(function(){
		this.reset();
	});
	Put_Pagina(php,pagina);
	$("#dialogLoading").dialog("open");
	SUBMIT("myForm");
	/*location.reload();*/
}

function Exec_Remoto(IP, PORT, COMANDO, donde) {
	var parametros={ ip:IP, port:PORT, comando:COMANDO }
	$.ajax({
		data:  parametros,
		url:   DIR_RAIZ+"/tools/exec_ssh2.php",
		type:  "post",
		success:  function (response) { donde.html(response); },
		statusCode: { 404: function() { alert( "page not found" ); } }
	});
}

function Resize_Cuerpo() {
	$("#CUERPO").css({ top: $("#CAB1").height()});
}

function new_window_open(newURL,newTitle) {
	var win = window.open("","_blank", "menubar=0,location=0,toolbar=0,resizable=1,status=0,scrollbars=1");
	win.document.write('<html><head><title>'+newTitle+'</title></head><body height="100%" width="100%" style="margin:0"><h3>Pulse Ctrl+D para salir.</h3><iframe src='+newURL+' style="height:95%; width:100%"></iframe></body></html>');
	return win;
}

function ACTIVA_OPCION_1(Name, Valor, Formul) {
	INPUT_HIDDEN(Name,Valor,Formul);
	$("#"+Formul).submit();
}

function loadRefresh(e) {
	$(e).load($(e).attr("src"), function(response, status, xhr) {
		if ( status == "error" ) {
			var msg = "Sorry but there was an error: ";
			$(e).html( msg + xhr.status + " " + xhr.statusText );
		}
	});
}

function en_background(id,url,ms) {
	$(id).attr("src", url);
	loadRefresh(id);
	if (ms>0) {
		return setInterval(function() { loadRefresh(id); },ms);
	}
}

function Recarga_Cuerpo( targ, url) {
	$("#"+targ).html('<div id="Cargando"><img src="/img/wait.gif"/></div>');
	$("#"+targ).load(url,true);
}

function Graba_Historico(tienda, caja, txt) {
	var parametros= { "OPCION":"GRABA_HISTORICO", "tienda": tienda, "caja": caja, "txt": txt }
	Ejecuta_AJAX("Mensaje_exec1", parametros );
}


/**** FUNCIONES SWAL ****/
function swal_error(titulo, texto) {
	swal({ type: 'error', title: titulo, html: texto});	
}
function swal_warning(titulo, texto) {
	swal({ type: 'warning', title: titulo, html: texto});	
}

function hay_login() {
	if (usuario=="Invitado") {
		swal_error("SESION NO INICIADA", "<p>Es usted un invitado/a. No tiene permisos para acceder a esta informacion</p><p><i>Debe ingresar en el sistema usando <i class='fa fa-sign-in-alt'></i></i></p>");
		return false;
	}
	else {
		return true;
	}
}