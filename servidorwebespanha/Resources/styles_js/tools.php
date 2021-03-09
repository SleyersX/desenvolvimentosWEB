<?php
require_once($DOCUMENT_ROOT.$DIR_RAIZ."/styles_js/textos.php");

function myGetText($Texto) {
	switch ($Texto) {
		case "NO_INFO_TPVS": return 
			"NO HAY INFORMACI&Oacute;N DE TPVS...<br>Pulse en <u>Actualizar</u><p><i>NOTA: posiblemente puede estar pasando:<ul><li>La caja master estaba desconectada en el momento de ir a buscar sus datos</li><li>La caja master tiene problemas en el disco duro o est&aacute; lleno.</li><li>La informaci&oacute;n de conexi&oacute;n a la tienda es incorrecta (comprobar direcci&oacute;n IP)</li><li>Algunos datos de la tienda o cajas son erroneos, como por ejemplo, que tenga un numero de tienda diferente.</li></ul><p>Si el problema persiste, p&oacute;ngase en contacto con Ventas Nacional para la gesti&oacute;n de este problema</p></p>";
		case "NO_SCRIPTS_PAIS": return 
			"<p>NO HAY SCRIPTS DEFINIDOS PARA ESTE PAIS.</p><p>P&oacute;ngase en contacto con el administrador de la herramienta de su pa&iacute;s o con Soporte Remoto Nivel 3 de SEDE</p>";
	default: return "";
	}
}

function array_find($needle, $haystack)
{
	foreach ($haystack as $item) if (strpos($item, $needle) !== FALSE) return $item;
	return NULL;
}

function _ECHO ($Texto) { echo @$Texto; _FLUSH(); }

function convert_multi_array($array) {
  $out = implode(",",array_map(function($a) {return implode(",",$a);},$array));
  return ($out);
}

function Sub_Campo($Campo, $Posicion, $Delim) {
	return "SUBSTRING_INDEX(SUBSTRING_INDEX(".$Campo.",'".$Delim."',".$Posicion."),'".$Delim."',1)";
}

function CELL_1($Texto, $Fondo, $Clase="") {
	return '<td class="cell '.$Clase.'" bgcolor="'.$Fondo.'">'.$Texto.'</td>';
}

function Graba_Sin_Tags_File($file, $d) {
	if ($file != NULL) fwrite($file, strip_tags($d));
}

function Linea_Tabla($dato, $Campo_Tienda, $file=NULL, $Termo=false) {
	global $dato_ant, $Color, $PHP_PRECONECTAR;
	$Tiendas_Subcentro=(empty($_SESSION['Tiendas_Subcentro'])?"":$_SESSION['Tiendas_Subcentro']);
	$Linea='<tr>';
	if ($dato[0] != $dato_ant) { $dato_ant = $dato[0]; $Color = !$Color; }
	foreach ($dato as $f => $d) {
		if ( $f == $Campo_Tienda && isset($_SESSION['FILTRO_CENTRO'])) {
			if (strpos($Tiendas_Subcentro,",$d,") === false)
				return NULL;
		}
		$Clase="";
		if (preg_match("/^CONNECT/",$d)) {
			$Connect=1;
			list($stuff, $myTienda, $myCentro) = explode(";",$d);
			continue;
		}
		if (preg_match("/(^.OK)|(^ON)|(\(ON\))|(^.BIEN)|(^.UPD)/",$d)) $Clase="ok";
		if (preg_match("/STOP|\(OFF\)|\(MAL\)/",$d)) $Clase="mal";
		Graba_Sin_Tags_File($file, $d.";");
		if ( $f == $Campo_Tienda ) {
			if (@$Connect==1)
				$href=$PHP_PRECONECTAR."?Tienda=$myTienda&Centro=$myCentro";
			else
				$href=$PHP_PRECONECTAR."?Tienda=$d";
			$d = '<a href="'.$href.'" target="_blank">'.$d.'</a>';
		}
		if ($Termo) {
			if ($d == 0) $Color="lightgrey";
			$Linea.=CELL_1($d, $Color, $Clase);
		} else {
			$Linea.=CELL_1($d, ($Color?'lightgrey':'#A4A4A4'), $Clase);
		}
	}
	Graba_Sin_Tags_File($file, "\n");
	$Linea.='</tr>';
	return $Linea;
}

function Es_Tienda_Centro($Campos, $myCampo_Tienda) {
	$Tiendas_Subcentro=(empty($_SESSION['Tiendas_Subcentro'])?"":$_SESSION['Tiendas_Subcentro']);
	if ( isset($_SESSION['FILTRO_CENTRO']) && $myCampo_Tienda>=0)
		return (strpos($Tiendas_Subcentro,",".$Campos[$myCampo_Tienda].","));
	return true;
}

function Pinta_Tabla( $Cabecera, $Tabla, $Fichero=NULL, $Termo=false) {
	$Activa_Info=0;
	if (@$Fichero != NULL) $file = fopen($Fichero,"w") or die ("No puedo abrir el fichero: $Fichero");
	$tmpTabla='<center><table><thead class="Cabecera_Panel"><tr>';
	if ($Cabecera!=NULL) {
		foreach ($Cabecera as $key => $Valor) {
			if ($Valor == "?") continue;
			if (!strcmp("TIENDA",strtoupper($Valor))) $Activa_Info=$key+1;
			$tmpTabla.='<th style="border-bottom: thin solid black;">'.$Valor.'</th>';
			Graba_Sin_Tags_File($file, $Valor.";"); }
	}
	$tmpTabla.='</tr></thead><tbody>';
	Graba_Sin_Tags_File($file, "\n");
	$dato_ant=$Tabla[0][0];
	$Color=1;
	$Connect=0;
	$Contador=0;
	foreach ($Tabla as $key => $dato) {
		if (Es_Tienda_Centro($dato, $Activa_Info-1) === false) continue;
		$Linea = Linea_Tabla($dato, $Activa_Info-1, $file, $Termo);
		if ($Linea != NULL) { $tmpTabla.=$Linea; $Contador++; }
	}
	$tmpTabla.='</tbody></table></center>';
	if (@$Fichero != NULL) fclose($file);
	if ($Contador==0) return NULL;
	return $tmpTabla;
}

function Get_FileTemp($Nombre, &$Referencia, &$Path_Fichero) {
	global $DOCUMENT_ROOT;
	$Dir_Temp = $_SESSION['DIR_TMP'];
	if (empty($Dir_Temp)) $Dir_Temp="/tmp/anonimo/".getRealIP();
	@mkdir($DOCUMENT_ROOT.$Dir_Temp, 0777);
	$Referencia=$Dir_Temp."/".$Nombre.".csv";
	$Path_Fichero=$DOCUMENT_ROOT.$Referencia;
}
function Get_DireTemp() { return $_SERVER['DOCUMENT_ROOT'].$_SESSION['DIR_TMP']; }

function Show_data2($Nombre, $Query, $Flag_query=true, $Termo=false) {
//	global $mysqli;
	$Pais = GetPais();
//	var_dump($Query);
	@list($Q_Cabecera, $Q_Campos, $Q_Query, $Q_Alarm, $Q_Pais, $Q_Ayuda) = $Query;
	if (preg_match("/ALL|$Pais/", $Q_Pais)) {
		$res=($Flag_query?myQUERY($Q_Query):$Q_Query);
		if (empty($res) || count($res) < 1) return;
		Get_FileTemp($Nombre."$Pais", $Referencia, $Fichero);
		$Color="white";
		$Clase='a_Cab';
		if (!is_null($Q_Alarm)) {
			if (count(myQUERY($Q_Alarm))>0) {
				$Color="pink";
				$Clase=$Clase.' css3-blink"';
			}
		}
		$tmpPinta_Tabla=Pinta_Tabla($Q_Campos, $res, $Fichero, $Termo);
		if ($tmpPinta_Tabla === NULL) return NULL;
		$tmpPanel ='<div id="'.$Nombre.'" class="PANEL">';
		$tmpPanel.='<h3 class="H3_PANEL" style="background-color:'.$Color.';" title="'.$Q_Ayuda.'">';
		$tmpPanel.='<table width=100%><tr>';
		$tmpPanel.='<td><img src="/img/icono_'.$Pais.'.png" width="25" height="25" /></td>';
		$tmpPanel.='<td><a href="'.$Referencia.'" class="'.$Clase.'">'.$Q_Cabecera.'</a></td>';
		if ($Termo) $tmpPanel.='<td>TERMO</td>';
		$tmpPanel.='<td class="a_Cab">('.count($res).')</td>';
		$tmpPanel.='</tr></table>';
		$tmpPanel.='</h3>';
		$tmpPanel.=$tmpPinta_Tabla;
		$tmpPanel.='</div>';
		echo $tmpPanel;
	}
}

function Show_data_sin_query($Nombre, $Query) {
	global $Pais;
	@list($Q_Cabecera, $Q_Campos, $Q_Query, $Q_Alarm, $Q_Pais, $Q_Ayuda) = $Query;
	if (strstr($Q_Pais,"ALL") || strstr($Q_Pais,$Pais)) {
		$Count=count($Q_Query); if ($Count < 1) return;
		Get_FileTemp($Nombre."$Pais", $Referencia, $Fichero);
		$tmpPinta_Tabla=Pinta_Tabla($Q_Campos,$Q_Query, $Fichero);
		if ($tmpPinta_Tabla == NULL) return NULL;
		echo '<div class="PANEL">';
		echo '<h3 class="H3_PANEL" style="background-color:white;" title="'.$Q_Ayuda.'">';
		echo '<a href="'.$Referencia.'" class="a_Cab">'.$Q_Cabecera.' ('.$Count.')</a>';
		echo '</h3>';
		echo $tmpPinta_Tabla;
		echo '</div>';
	}
}

function Parada(&$texto, &$Observaciones) {
	global $mysqli, $Pais;
	$Res=myQUERY("select * from CheckCtrl where Pais='$Pais'");
	$Observaciones=$Res[0][3];
	switch ($Res[0][1]) {
		case "MODO STANDBY": $texto="(Periodo de inactividad forzada: 22:00 a 08:00)"; return 1; break;
		case "PARADA FORZOSA": $texto="Se ha parado manualmente. Espere por favor..."; return 1; break;
		case "EJECUCION": return 0; break;
		default: die ("<big>Error en la BBDD de CheckCtrl...</big>"); break;
	}
}

function Graba_A_Fichero($Campos, $Datos) {
	global $DOCUMENT_ROOT, $DIR_RAIZ;
	$FicheroTmp = "/$DIR_RAIZ/tmp/Listado.csv";
	$file = fopen($DOCUMENT_ROOT.$FicheroTmp,"w") or die ("No puedo abrir el fichero: $FicheroTmp");
	foreach($Campos as $i =>$d) Graba_Sin_Tags_File($file, $d.";");
	Graba_Sin_Tags_File($file, "\n");
	foreach ($Tabla as $key => $dato) {
		foreach ($dato as $i => $d) Graba_Sin_Tags_File($file, $d.";");
		Graba_Sin_Tags_File($file, "\n");
	}
	fclose($file);
}
function getRealIP() { return $_SERVER['REMOTE_ADDR']; }
function getUser()   { return (isset($_SESSION['usuario'])?$_SESSION['usuario']:""); }
function getGrupoUser() { return (isset($_SESSION['grupo_usuario'])?$_SESSION['grupo_usuario']:0); }
function getValidate($CheckGrupos) { return (preg_match("/ ".getGrupoUser()." /",$CheckGrupos)); }

function SoyYo() {
	if (!empty($_SESSION['usuario'])) {
		return (strtoupper($_SESSION['usuario'])=="VMA001ES");
	}
	return false;
}

function PC_al_lado() { return ($_SERVER['REMOTE_ADDR']=="10.208.185.134"); }
function SuperAdmin() { return (isset($_SESSION['grupo'])?($_SESSION['grupo']==2):SoyYo() || PC_al_lado()); }

function GetPais() {
		return (!empty($GLOBALS["PAIS_SERVER"])?$GLOBALS["PAIS_SERVER"]:'ESP');
}

function Acceso_a_todos_servidores_cupones() {
	if (!isset($_SESSION['usuario'])) return false;
	if (preg_match("/vma001es|jsf010es/",$_SESSION['usuario'])) return true;
	return false;
}

function getIP_Absoluta($ip_base, $Caja) {
	global $Pais;
	list($a,$b,$c,$d)=explode(".",$ip_base);
	switch($Pais) {
		case "ESP":
		case "PAR":
			return array(sprintf("%d.%d.%d.%d",$a,$b,$c,($d+$Caja-1)),23);
			break;
		case "POR":
			if ($a==10 && $b==245)
				return array(sprintf("%d.%d.%d.%d",$a,$b,$c,($d+$Caja-1)),23);
			break;
	}
	return array($ip_base,10000+$Caja);
}

function GetFileTemp() {
	$tempfile=tempnam(__FILE__,''); 
	if (file_exists($tempfile)) { 
		unlink($tempfile);
		return realpath(dirname($tempfile)); 
	}
	return null; 
}

function Pinta_Dato( $Texto, $Valor, $Control) {
	if ($Control)
		return '<span class="Pinta_Dato">'.$Texto.' <span class="ok">'.$Valor.'</span></span>';
	else
		return '<span class="Pinta_Dato css3-blink">'.$Texto.' <span class="mal">'.$Valor.'</span></span>';
}

function Datos_Caja_Conexion($con=NULL, $Datos_Caja=NULL) {
	global $Table,$DIR_IMAGE,$Pais, $PHP_CONECTAR;
	if ($con != NULL) { $Tienda=$con->tienda; $Caja=$con->caja; }
	if ($Datos_Caja==NULL) {
		$tmp = myQUERY("select * from $Table where Tienda=$Tienda AND Caja = $Caja");
		$Datos_Caja = $tmp[0];
	}
	@list($Tienda,$Caja,$Conexion,$Version,$Modelo,$Exec,$MSG,$RAM,$HDD,$dummy,$LAN,$NTPVS,$N_APAG,$DAT1,$DAT2,$DAT3,$DAT4,$DAT5,$LastM,$IP,$Temper,$HUB,$PINPAD,$ReleaseDate,$INV_HW_SW,$MySQL,$WSD,$SWD) = $Datos_Caja;
	$tmp = myQUERY("select BIOS from tmpHardware where tienda=".$Tienda." AND Caja=".$Caja);
	$BIOS = (empty($tmp)?"N/D":$tmp[0][0]);

	$Titulo="TIENDA $Tienda - CAJA $Caja";
	$URL=$PHP_CONECTAR."?Tienda=$Tienda&Caja=$Caja&Pais=$Pais";

	$tmp ='<div class="PANEL">';

	if ($con)	 $tmp.='<h3 class="H3_PANEL">TIENDA '.$Tienda.' - CAJA '.$Caja.'</h3>';
	if (!$con) 
		if ($Conexion)
			$tmp.='<button class="b_caja tpv center" src="'.$URL.'";">CAJA '.$Caja.'<br>(Pulse aqu&iacute; para conectar)</button>';
//			$tmp.='<button class="b_caja center" onclick="New_Window(\''.$URL.'\');">CAJA '.$Caja.'<br>(Pulse aqu&iacute; para conectar)</button>';
		else
			$tmp.='<button class="b_caja tpv center" style="color:red" disabled src="" title="La caja est&aacute; apagada o desconectada de red (pulse en actualizar). NOTA: La Fecha que aparece es la de ultimo acceso">CAJA '.$Caja.' (OFF)<br>('.$LastM.')</button>';
	$tmp.='<pre>';
	if ($con) $tmp.='<p class="center">HORA TPV: <span id="Hora_TPV">'.$con->GetHoraTPV().'</span></p>';
	// --------------------------------------------------------------------------------------------- //
	$tmp.= Pinta_Dato("APP:",($Conexion==1?($Exec?"SI":"NO"):"--"), ($Exec==1 || $Conexion==0));
	if ($Pais=="ESP")	$tmp.= Pinta_Dato(" MySQL:",($Conexion==1?($MySQL?"SI":"NO"):"--"), ($MySQL==1 || $Conexion==0));
	else 			$tmp.= Pinta_Dato(" MySQL:","N/A", true);
	$tmp.= Pinta_Dato(" WSD:",($Conexion==1?($WSD?"SI":"NO"):"--"), ($WSD==1 || $Conexion==0));
	$tmp.="<hr>";
	if ($con) $tmp.= Pinta_Dato("VELA:",($con->VELA?"SI":"NO"), true);
	$tmp.="<hr>";
	// --------------------------------------------------------------------------------------------- //
	$tmp.= Pinta_Dato("Version  :",(!$con?$Version:$con->version), true)."<br>";
	if (!$con) $tmp.= Pinta_Dato("Modelo   :", $Modelo, true)."<br>";
	$tmp.=Pinta_Dato("BIOS     :",$BIOS,true)."<br>";
	if (!$con) $tmp.=Pinta_Dato("RAM Total:","$RAM MB",true)."<br>";
	else       $tmp.=Pinta_Dato("RAM Total:","$RAM MB (Uso:".($con->memfree+0)."%)", true)."<br>";
	@list($Uso,$Total) = (@$HDD?explode(" ",$HDD):array("0","N/A"));
	$tmp.=Pinta_Dato("HDD $Total:","Uso: $Uso", ($Uso < 80))."<br>";
	$tmp.="<hr>";
	// --------------------------------------------------------------------------------------------- //
	$tmp.=Pinta_Dato("Temperatura:", sprintf("%d C",$Temper), ($Temper < 60))."<br>";
	$tmp.=Pinta_Dato("Errores LAN:", $LAN, ($LAN<1))."<br>";
	$tmp.=Pinta_Dato("N. Apagados:", $N_APAG, ($N_APAG<1))."<br>";
	$tmp.="<hr>";
	// --------------------------------------------------------------------------------------------- //
	$dato_tmp=myQUERY("select Ver_GUC from capturador where tienda=$Tienda and caja=$Caja");
	$res=myQUERY("select Capturador from Capturadores where tienda=".$Tienda);
	$tmp.="<b>DATOS DEL CAPTURADOR</b><br>";
	if ($con) $tmp.=Pinta_Dato("MODELO:", $res[0][0],true)."<br>";
	if ($con) $tmp.=Pinta_Dato("versionMinimal:", str_replace(" ","   <br>",$con->versionMinimal),true)."<br>";
	$tmp.=Pinta_Dato("Vers. GUC:", $dato_tmp[0][0],true)."<br>";
//	$dato_tmp=myQUERY("select PKG, PKG_Auto from Paquetes where tienda=$Tienda and caja=$Caja");
//	$tmp.=Pinta_Dato("BAT200    :", $dato_tmp[0][0],true)."<br>";
//	$tmp.=Pinta_Dato("AutoInstal:", $dato_tmp[0][1],true);

	// --------------------------------------------------------------------------------------------- //
	if ($con) $tmp.="<hr><div id='div_bat_23'>".utf8_decode($con->bat23)."</div>";
	$tmp.='</pre></div>';

	return $tmp;
}

function Datos_PC_Conexion($tienda) {
	global $Table,$DIR_IMAGE,$Pais, $PHP_CONECTAR;

	$tmp = myQUERY("select Tienda,PC,IP_PC,EXEC_PC>0,MYSQL_PC>0,CAPTUR_PC>0,Version_PC,Unix_PC,HDD_PC,RAM_PC,BIOS_PC,Modelo_PC,LAN_PC,CARGA_PC,TEMP_PC from PC_Tienda where tienda=$tienda");
//	foreach($tmp as $k 
	$Datos_PC = $tmp[0];

	@list($Tienda,$PC,$IP_PC,$Exec,$MySQL,$Captur,$Version_PC,$Unix_PC,$HDD_PC,$RAM_PC,$BIOS_PC,$Modelo_PC,$LAN_PC,$CARGA_PC,$TEMP_PC) = $Datos_PC;
	
	$Titulo="TIENDA $Tienda - PC LINUX 1";
	$URL=$PHP_CONECTAR."?Tienda=$Tienda&PC=1&Pais=$Pais";

	$tmp ='<div class="PANEL PC">';
	$tmp.='<button class="b_caja b_pc center">PC LINUX 1<br>(Pulse aqu&iacute; para ver opciones)</button>';
	$tmp.='<div id="div_opciones_pc" >
				<input class="opc_pc" style="margin-top:1em; width:100%" type="button" value="Consola Linux" id="pc_consola"/><br>
				<input class="opc_pc" style="margin-top:1em; width:100%" type="button" value="Consola MySQL" id="pc_mysql"/><br>
				<input class="opc_pc" style="margin-top:1em; width:100%" type="button" value="LOG Hydra"     id="pc_log_hydra"/><br>
			</div>';
	$tmp.='<pre>';
	// --------------------------------------------------------------------------------------------- //
	$tmp.="IP: <span id='dir_ip_pc'>".$IP_PC."</span><br>";
	$tmp.="<hr>";
	// --------------------------------------------------------------------------------------------- //
	$tmp.= Pinta_Dato("HYDRA:"   ,($Exec?"SI":"NO"), ($Exec==1));
	$tmp.= Pinta_Dato(" MySQL:",($MySQL?"SI":"NO"), ($MySQL==1));
	$tmp.= Pinta_Dato(" CAPT:" ,($Captur?"SI":"NO"), ($Captur==1));
	$tmp.="<hr>";
	// --------------------------------------------------------------------------------------------- //
	$tmp.= Pinta_Dato("Version  :",$Version_PC, true)."<br>";
	$tmp.= Pinta_Dato("Vers. SO :",sprintf("%-15.15s",$Unix_PC), true)."<br>";
	$tmp.= Pinta_Dato("Modelo   :",$Modelo_PC, true)."<br>";
	$tmp.= Pinta_Dato("BIOS     :",$BIOS_PC,true)."<br>";
	$tmp.= Pinta_Dato("RAM Total:","$RAM_PC MB",true)."<br>";
	@list($Uso,$Total) = (@$HDD_PC?explode(" ",$HDD_PC):array("0","N/A"));
	$tmp.=Pinta_Dato("HDD $Total:","Uso: $Uso", ($Uso < 80))."<br>";
	$tmp.="<hr>";
	// --------------------------------------------------------------------------------------------- //
	$tmp.=Pinta_Dato("Temperatura:", sprintf("%d C",$TEMP_PC), ($TEMP_PC < 60))."<br>";
	$tmp.=Pinta_Dato("Errores LAN:", $LAN_PC, ($LAN_PC<1))."<br>";
	$tmp.=Pinta_Dato("Carg.Trabaj:", $CARGA_PC,true)."<br>";
	// --------------------------------------------------------------------------------------------- //
	$tmp.='</pre></div>';

	return $tmp;
}

function Pinta_Resultado($Cabecera, $Res, $Grabar=false) {
	global $DOCUMENT_ROOT, $DIR_RAIZ;
	$tmp='<div class="PANEL"><h3 class="H3_PANEL">';
	if ($Grabar) {
		$Dir_real=$_SESSION['DIR_TMP'];
		$tmpfname = $Cabecera.'.txt';
		file_put_contents($DOCUMENT_ROOT.$Dir_real.$tmpfname, $Res);
		$tmp.='<a href="'.$Dir_real.$tmpfname.'" target="_blank">'.$Cabecera.'</a>';
	} else
		$tmp.=$Cabecera;
	$tmp.='</h3>';
	$tmp.='<pre>'.$Res.'</pre></div>';
	return $tmp;
}

function Pinta_Datos_Caja_Basicos ( $con_tda ) {
	$Queries["meminfo"]=array ("Memoria (meminfo)", NULL, $con_tda->cmdExec('cat /proc/meminfo'), NULL, "");
	$meminfo=$con_tda->cmdExec('cat /proc/meminfo');
	$df = $con_tda->cmdExec('df');
	$DISP_CAJE = $con_tda->GetDispCaje(10);
	$tmp ="<table><tr>";
	$tmp.="<td rowspan='2' valign='top'>".Pinta_Resultado("Memoria (meminfo)",$meminfo,true)."</td>";
	$tmp.="<td valign='top'>".Pinta_Resultado("Discos Duros (df)",$df,true)."</td></tr>";
	$tmp.="<tr><td valign='top'>".Pinta_Resultado("VISOR CAJERA",str_replace("- INFO  - [main] ","",$DISP_CAJE),true)."</td>";
	$tmp.="</tr></table>";
	return $tmp;
}

function get_extension($str) { return pathinfo( $str, PATHINFO_EXTENSION ); }

function FIELDSET_DATOS($legend, $contenido) {
	$Res='<fieldset class="fieldset_datos"><legend>'.$legend.'</legend>';
	$Res.='<div><pre class="datos">'.$contenido.'</pre></div>';
	$Res.='</fieldset>';
	return $Res;
}

function Rellena_Select($Lista, $Nombre, $Texto=NULL) {
	$tmp='<select id="'.$Nombre.'" class="select_opcion" name="'.$Nombre.'" width="100%" onchange="javascript:mySelect(this);">';
	$tmp.='<option value="">-- Escoja '.($Texto?$Texto:'opcion').' --</option>';
	if ($Lista)
		foreach($Lista as $k => $d) {
			if (!empty($d)) $tmp.='<option>'.$k.'</option>';
		}
	$tmp.='</select>';
	return $tmp;
}

function Rellena_Select_From_Table($Campo, $Datos) {
	$Idioma=$_SESSION['Idioma'];
	$Tmp ='<select name="'.$Campo.'">';
	if ($Idioma == "ESP") $Tmp.='<option value="">-- Elija opcion --</option>';
	if ($Idioma == "ENG") $Tmp.='<option value="">-- Select option --</option>';
	if (!empty($Datos)) {
		foreach($Datos as $key => $dato) { $Tmp.='<option value="'.$dato[0].'">'.$dato[0].'</option>'; }
	}
	$Tmp.='</select>';
	return $Tmp;
}

function IFRAME ( $label, $legend, $src ) {
	$tmp='<fieldset id="'.$label.'" ><legend>'.$legend.'</legend>';
	$tmp.='<iframe class="iframe1" width="100%" align="center" src="'.$src.'"></iframe>';
	$tmp.='</fieldset>';
	return $tmp;
}

function IFRAME_CONTENT ( $label, $legend, $content ) {
	$tmp='<fieldset id="'.$label.'" ><legend>'.$legend.'</legend>';
	$tmp.='<iframe class="iframe1" width="100%" align="center">'.$content.'</iframe>';
	$tmp.='</fieldset>';
	return $tmp;
}

function Establece_Ayuda($url) {
	return '<div class="ayuda hidden">
		<iframe src="'.$url.'"></iframe>
			<center><input class="button b_caja" id="Cerrar" value=">>Cerrar<<" onclick="javascript:Desactiva_Ayuda();"></input></center>
		</div>';
}

function Actualizar_Tienda($Tienda,$Pais) {
	$DIR_TMP=(isset($_SESSION['DIR_TMP'])?$_SESSION['DIR_TMP']:"/tmp/");
	$File_Auto=$_SERVER['DOCUMENT_ROOT'].$DIR_TMP."auto-$Pais-$Tienda.dat";
	$tmp=myQUERY("SELECT CONCAT('TIENDA=',LPAD(numerotienda,5,'0'),';IP=',IP,';ENTORNO=',Pais,';RESET=1') ' ' FROM tiendas WHERE pais='$Pais' AND numerotienda=$Tienda");
	$Linea_Tienda=$tmp[0][0];
	_ECHO("Ejecutando herramienta de actualizacion de datos...");
	exec("echo '$Linea_Tienda' > $File_Auto");
	exec("cd /home/MULTI; sudo bash Hilo_Tiendas $File_Auto 1 2>/dev/null");
	_ECHO(" OK.");
}

function Ventana_Flotante($Texto) {
	return '<div id="ventana-flotante">
		<a class="cerrar" href="javascript:{}" onclick="Oculta_Ventana(\'ventana-flotante\')>x</a>
		<div id="contenedor"><div class="contenido">'.$Texto.'</div></div></div>';
}

function Aviso_Info($Texto,$Activo) {
return '
	<div id="Aviso1" class="Aviso" '.($Activo?'':'style="display:none"').'>
		<table><tr>
			<td width="10%" style="text-align:center">
				<img src="/img/icono-info.png"/ width="100%"><br><br>
				<a href="#" id="ocultar_info"> >>Ocultar<< </a><br>
			</td>
			<td width="90%" style="padding-left:2em">'.$Texto.'</td>
		</tr></table>
	</div>
	<script>
		$("#ocultar_info").on("click",function() { $("#Aviso1").hide(); });
	</script>';
}

function Alert($Tipo, $Texto) {
	return '
		<div id="id_warning_'.$Tipo.'" class="alert alert_'.$Tipo.'" >
			<table><tr>
				<td><img class="icono_img" src="/img/'.$Tipo.'.png" /></td>
				<td style="padding-left:20px">'.$Texto.'</td>
			</tr></table>
		</div>';
}

function _FLUSH() { flush(); @ob_flush(); }

function form_size($Size, $Medida) {
	if ($Medida == "KB") return number_format($Size/1024, 0, ',', '.')." KB";
	if ($Medida == "MB") return number_format($Size/(1024*1024), 0, ',', '.')." MB";
	if ($Medida == "GB") return number_format($Size/(1024*1024*1024), 2, ',', '.')." GB";
	return number_format($Size, 0, ',', '.')." bytes";
}
function wc_l($Fichero) { return exec("wc -l $Fichero | awk '{print \$1}'"); }

function form_fecha($Fecha, $Patron=NULL) { return substr($Fecha,6,2)."/".substr($Fecha,4,2)."/".substr($Fecha,0,4); }

function form_hora ($Hora, $Patron=NULL) { return substr($Hora,0,2).":".substr($Hora,2,2).":".substr($Hora,4,2); }

function Graba_Historico($Texto,$Tienda=NULL,$Caja=NULL) {
	global $con,$PAIS_SERVER;
	if ($con) {
		$Tienda=$con->tienda; $Caja=$con->caja;
	}
	if (isset($_SESSION['usuario']))
		$User=$_SESSION['usuario']." (".getRealIP().")";
	else
		$User="Invitado (".getRealIP().")";
	$Values=sprintf("(%d,%d,NOW(),'$PAIS_SERVER','%s - USUARIO: %s')", $Tienda, $Caja, $Texto, $User);
	myQUERY_Actu("INSERT INTO Historico VALUES $Values");
}

function Imprime_Trazas($Imprime) {
	if ($Imprime) {
		echo '<pre>';
			print_r($GLOBALS['_GET']); echo '<hr>';
			print_r($GLOBALS['_POST']); echo '<hr>';
			print_r($GLOBALS['_SESSION']); echo '<hr>';
			print_r($GLOBALS['_SERVER']);  echo '<hr>';
		echo '</pre>';
	}
}

function Get_Oper_Total_Serv_Cupo($Fecha=NULL) {
	$TOTAL = "CONCAT('<text id=\"SC_TOTAL\" >',(SUM(s1.Oper)+SUM(s2.OPer)),'</text>')";
	$SERVIDOR1 = "CONCAT('<text id=\"SC_T_SV1\">',SUM(s1.Oper),'</text>')";
	$SERVIDOR2 = "CONCAT('<text id=\"SC_T_SV2\">',SUM(s2.Oper),'</text>')";
	if ($Fecha==NULL) $Fecha="NOW()"; else $Fecha="'$Fecha'";
	return myQUERY("select $TOTAL AS Total, $SERVIDOR1 AS SC1, $SERVIDOR2 AS SC2
			from serv_cupo1 s1, serv_cupo2 s2
			where DATE(s1.ID) = DATE($Fecha) and s1.id=s2.id");
}

function Prepara_Lista_Select($Campo, $Tabla) {
	$Variable="";
	$tmp=myQUERY("select distinct($Campo) from $Tabla where $Campo<>'' order by 1");
	if (count($tmp)>0)
		foreach($tmp as $k => $d) { $Variable.=";".$d[0].":".$d[0]; }
	return $Variable;
}


function array2csv(array &$array)
{
	if (count($array) == 0) {
		return null;
	}
	ob_start();
	$df = fopen("php://output", 'w');
	foreach ($array as $row) {
		fputcsv($df, $row);
	}
	fclose($df);
	return ob_get_clean();
}

function download_send_headers($filename) {
	$now = gmdate("D, d M Y H:i:s");
	header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
	header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
	header("Last-Modified: {$now} GMT");

	// force download  
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");

	// disposition / encoding on response body
	header("Content-Disposition: attachment;filename={$filename}");
	header("Content-Transfer-Encoding: binary");
}

function Crea_Dir_Temporal( $usuario , $entrada ) {
	$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
	if ($usuario == "Invitado")
		$DirTmp = '/tmp/Invitado/'.$_SERVER['REMOTE_ADDR'].'/';
	else
		$DirTmp = '/tmp/usuario/'.$usuario.'/';
	if (!is_dir($DOCUMENT_ROOT.$DirTmp))
		mkdir($DOCUMENT_ROOT.$DirTmp);
//	$res=shell_exec("sudo chmod 777 ".$DOCUMENT_ROOT."/.".$DirTmp." -fR");
//	if ($entrada)
//		file_put_contents($DOCUMENT_ROOT.$DirTmp."entry", "Entrada desde la IP: ".$_SERVER['REMOTE_ADDR'], FILE_APPEND );
	$_SESSION['DIR_TMP'] = $DirTmp;
}


function Registra_login($usuario, $comentario){
	$f_inicio=(!empty($_SESSION['F_Inicio'])?"'".$_SESSION['F_Inicio']."'":"'".date("Y-m-d H:i:s")."'");
	$sql="INSERT INTO login_usuarios VALUES ('".$usuario."','".$_SERVER["REMOTE_ADDR"]."',".$f_inicio.",NULL,'".$comentario."')";
	//echo $sql;
	if ($GLOBALS["PAIS_SERVER"] == 'ESP') myQUERY_remoto("10.208.162.6",$sql);
	else myQUERY($sql);
}

function Usuario_sale($usuario, $comentario){
//	$sql="REPLACE login_usuarios SET VALUES ('".$usuario."','".$_SERVER["REMOTE_ADDR"]."',NOW(),NULL,'".$comentario."')";
	myQUERY_Actu($sql);
//	if ($GLOBALS["PAIS_SERVER"] == 'ESP') myQUERY_remoto("10.208.162.6",$sql,true);
//	else myQUERY($sql);
}

function Prepara_URL_open_term($con_tda, $comandoTerminal=NULL) {
	$url= 'http://'.$SERVER_ADDR.':8080/?IP='.$con_tda->GetIP().'&caja='.$con_tda->GetPort().'&usuario='.getUser().'&IP_usuario='.$_SERVER['REMOTE_ADDR'];
	$url.=($comandoTerminal?"&comandoTerminal=".$comandoTerminal:"");
	return $url;
}

?>
