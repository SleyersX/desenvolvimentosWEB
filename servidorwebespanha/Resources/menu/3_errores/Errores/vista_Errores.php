<title>ERRORES</title>
<?php

$No_Carga_ssh2=true;
require($_SERVER['DOCUMENT_ROOT']."/config.php");

// if (SoyYo()) { echo '<pre>'; print_r($_SESSION); print_r($_COOKIE); print_r($_SERVER); echo '</pre>'; }

$Queries["INTERVENCION_IBTP"]=array ("TIENDAS/CAJAS IBTP=0<br>(ARTICULOS NO CISS)",
	array("Tienda", "Caja", "Cantidad"),"select * from Intervencion_IBTP where errores>0","","BRA","");

$Queries["Warnings"]=array ("W A R N I N G S",
	array("Tienda","Caja","Centro","Warning", "Consulta"),
	"select w.Tienda, w.Caja, t.centro, (if(w.tipo in (1,2,3), CONCAT('<span style=\"color:red;\">',w.Warning,'</span>'),Warning)), $LastM
	from WARNING_ESP w
		JOIN tiendas t ON t.numerotienda=w.tienda and t.pais='$Pais' and date(LastM)=date(now()) 
	WHERE w.tienda<>0  order by w.tipo, t.centro, w.tienda, w.caja",
	"select Tienda,Caja,Warning,$LastM from WARNING_ESP where tienda<>0 and date(LastM)=date(now()) order by tienda, caja limit 1",
	"ESP",
	 "ERRORES GRAVES EN TPV - NOTIFICAR EL PROBLEMA DETECTADO\n\n
	Estas TPVs est&aacute;n afectadas por un error grave, en caso de afectaci&oacute;n de los diferentes errores recomendamos notificar a los correspondientes departamentos el problema\n\n
	- HORA TPV INCORRECTA: Notificar a Ventas Nacional en caso de 2 horas de diferencia con hora actual se rechazar&aacute; el pago TEF o habr&aacute; problemas con las barreras del parking.\n\n
	- ERRORES EN DISCO DURO: Notificar a Soporte TPVs para revisar si los errores pueden recuperarse remotamente\n\n
	- APLICACION O MYSQL PARADO:En caso de tener alguno de estos servicios parados ponerse en contacto con Soporte TPVs para la recuperaci&oacute;n del servicio\n\n
	- CDMANAGER PARADO: indica que no ser&aacute; capaz de recibir el paquete enviado por el Distribuidor de Software.\nLa forma de solucionar esta situaci&oacute;n es por intervenci&oacute;n de Soporte Remoto Nivel 3");

$Queries["Local_Out_Error"]=array ( "MESSAGE_LOCAL_OUT_ERROR",
	array("Cola", "Cajas","Mensajes"), "select Queue,Count(*),SUM(Mensajes) from M_L_O_E_Tiendas group by queue order by 3 desc", "", "ESP");


$Dias_Interrupt=30;
$Queries["GN_Interrupt"]=array ( "Interrupciones GN/Cargas<br>(Ultimos $Dias_Interrupt dias)",
	array("Tienda", "Tipo","Fecha"),
	"select Tienda,IF(Tipo=38,'GN','CT'),Fecha from GN_Interrupt where DATE(Fecha)>=DATE(ADDDATE(NOW(),INTERVAL -$Dias_Interrupt day)) and tipo = 40 order by 2 asc, 3 desc",
	"select Tienda,IF(Tipo=38,'GN','CT'),Fecha from GN_Interrupt where DATE(Fecha)>=DATE(ADDDATE(NOW(),INTERVAL -$Dias_Interrupt day)) and tipo = 40 order by 2 asc, 3 desc",
	"");

$Queries["FIDE_ONLINE"]=array ( "CAJAS SIN FIDE. ONLINE",
	array("Tienda", "Caja","Version"), "select Tienda,Caja,Version from $Table where DAT5=0 and conexion=1 order by 1,2", NULL, "POR BRA ARG CHI");

$Queries["Diferentes_versiones_tienda"]=array ("CAJAS CON DIFERENTE VERSION <br>A LA MASTER Y CONECTADAS",
	array("Tienda","Caja","Version","Caja","Version","Error"),
		"select c1.Tienda, c1.caja, c1.Version, c2.Caja, c2.Version, substring(s.DAT_ADIC2,1,50)
		from $Table c1, $Table c2, tmpTiendas t, Solo_DATS s 

		WHERE c1.tienda=s.tienda AND c2.caja=s.caja and c1.Tienda=c2.Tienda and c1.Caja=1 and c2.Caja<>1 and c1.Version<>c2.Version and (t.Pais='$Pais' and t.numerotienda=c1.tienda) and c1.conexion and c2.conexion and t.centro<>'SEDE'",
	"",
	"ALL",
	"Esta vista nos muestra las diferencias de versiones entre cajas de una misma tienda\nLa TPV ir&aacute; paulativamente actualizando hasta quedar en la &uacute;ltima versi&oacute;n.\nPara ello, solo es necesario hacer descanso en la TPV");

$Queries["SEDES"]=array ("CORRECION SEDES",
	array("Estado","Cantidad"),
	"select IF(DAT4<>0,'ACTUALIZADO','NO ACTUALIZADO'),count(*) from $Table group by 1",
	NULL,
	"","");

$tmp_q1="Tienda,NTPVS"; for($i=1; $i<10; $i++) { $tmp_q1.=",(if(c_$i='','',IF(c_$i=1,'ON','<b style=\"color:red\">OFF</b>')))"; }
$tmp_q2="Tienda,NTPVS"; for($i=1; $i<10; $i++) { $tmp_q2.=",IFNULL(MAX(CASE WHEN Caja = $i THEN Conexion END),'') as c_$i"; }
$Queries["BRA_TPVS_ONLINE"]=array ("TPVS ON-LINE / OFF-LINE",
        array("Tienda","N.TPVS","Caja 1","Caja 2","Caja 3","Caja 4","Caja 5","Caja 6","Caja 7","Caja 8","Caja 9"),
        "select $tmp_q1 from ( select $tmp_q2 from ChecksBRA group by tienda ) as tmp",
        NULL,
        "BRA","");

$Queries["Modos_Locales"]=array ("MODOS LOCALES",
	array("Tienda","Caja","Estado","Desde..."),
	"select Tienda, Caja, Tipo, BEGIN_DATE from ModosLocales WHERE end_date is null AND Tipo='ML' order by 3,1,2",
	"",
	"ESP",
	"Esta importante vista nos ofrece una lista de cajas que est&aacute;n en MODO LOCAL en el momento de obtener la informaci&oacute;n de la tienda.\n\nLos motivos por los que una caja se encuentra en esta situaci&oacute;n pueden ser los siguientes:\n\n* La caja ha arrancado antes que la caja master, pero a&uacute;n no ha recuperado l&iacute;nea por diversas razones (avisar a la tienda para que pulsen la tecla de recuperar l&iacute;nea o revisar conexi&oacute;n con caja master)\n\n* La caja tiene mensajer&iacute;a pendiente o bloqueada (avisar a Ventas Nacional para tramitar el borrador de mensajes pendientes)\n");

$Queries["EBD"]=array ("ERRORES EN BASE DE DATOS (HOY)",
	array("Tienda", "Caja", "Centro","Version", "Fecha", "EBD"),
	"select EBD.Tienda, EBD.Caja, a.centro, c.Version, TIME(EBD.Fecha), EBD.Error from EBD join tmpTiendas a ON EBD.Tienda=a.numerotienda join $Table c ON c.Tienda=EBD.Tienda AND c.Caja=EBD.Caja WHERE DATE(EBD.Fecha)=DATE(NOW()) order by EBD.Error,EBD.Tienda,EBD.Fecha",
	NULL,
	"ESP",
	"Esta vista nos muestra los errores en base de datos de hoy en las tiendas");

$Queries["Messages_out"]=array ("MESSAGES_OUT",
	array("Tienda","Caja","Mensaje","Cantidad"),
	"select Tienda,Caja,Mensaje,MSG from MESSAGE_OUT_ESP where msg > 0 order by MSG desc,Caja asc",
	NULL,
	"",
	"Problemas de colas de mensajeria.\n\nCAUSAS:\n\ta) Cajas mal configuradas.\n\tb) Alguna caja de la tienda sin encender o con problemas en arranque\n\nCONSECUENCIAS:\n\t- Lentitud de procesos en facturacion.\n\t- Cajas en modo local.\n");

$Umbral_LAN=1000;
$Queries["Errores_LAN"]=array ("Errores LAN/Carrier<br>($Umbral_LAN+)",
	array("Tienda","Caja","HUB?","LAN Errors"),
	"select Tienda,Caja,HUB,LAN from $Table where LAN > $Umbral_LAN order by Tienda,Caja,LAN desc",
	NULL,
	"ALL",
	"Esta vista refleja errores fisicos de LAN: tarjeta de red y cable LAN.\nRecomendamos con elevado numero de errores una revision de la infraestructura de la caja/tienda, ya que puede ocasionar errores de actualizacion de datos en caja master/servidores de cupones");

$Temper="CAST(Temper AS DECIMAL)";
$COLOR_TEMPER="CONCAT('<b><font color=\"',IF($Temper > 80,'red',IF($Temper>74,'orange','')),'\">',$Temper,' C</font></b>')";
$Queries["Temperatura"]=array ("Temper. (70 C+)",
	array("Tienda","Caja","Modelo","Temp."),
	"select Tienda,Caja, Modelo, $COLOR_TEMPER from $Table where $Temper > 70 and conexion order by $Temper desc,Tienda,Caja",
	NULL,
	"ALL",
	"TEMPERATURA\n\nEsta vista sirve para ver la alta temperatura de trabajo de ciertas cajas\n\nRECOMENDACIONES:\n- Revisar ventilacion de TPV\n- Revisar ventiladores.");

$Valor_HDD=Sub_Campo("HDD",1,"%");
$COLOR_HDD="CONCAT('<b><font color=\"',IF($Valor_HDD > 80,'red',IF($Valor_HDD > 70,'orange','')),'\">',HDD,'</font></b>')";
$Queries["HDD"]=array ("Disco duro (80%+)", 
	array("Tienda","Caja","Disco Duro"),
	"select Tienda,Caja, $COLOR_HDD from Discos_Llenos where $Valor_HDD > 80",
	"",
	"ALL",
	"ESPACIO OCUPADO DE DISCO DURO\n\nEsta vista refleja las TPVs que tienen ocupada gran parte de su disco duro.\n\nRECOMENDACIONES:\n- Avisar a Soporte Remoto Nivel 3 para revisar problemas en la caja.");

$WHERE="CONEXION and ($Exec='STOP' OR $MySQL='STOP' OR $WSD='STOP' OR $SWD='STOP') and Tienda <> 0";
$Queries["Servicios_Parados"]=array ("SERVICIOS PARADOS",
	array("Tienda","Caja","Version","APP", "MYSQL", "WSD", "SWD", "Ultima<br>Consulta"),
	"select Tienda, Caja, Version, $Exec, $MySQL, $WSD, $SWD, $LastM from $Table where $WHERE order by Tienda, Caja",
	"select * from $Table where $WHERE limit 1",
	"",
	"SERVICIOS/DEMONIOS ACTIVOS/INACTIVOS EN LA CAJA:\n\nIndica si los diferentes servicios que residen en la caja y que son necesarios para su correcto funcionamiento estan habilitados o tienen problemas. En caso de problemas, avisad a soporte");

$Queries["Cajas_Mal_Config"]=array ("CAJAS MAL CONFIGURADAS",
	array("Tienda","Caja","Error:<br>LOCAL-SERVER/MASTER","Ultima Consulta"),
	"select Tienda, Caja, DAT1, $LastM from $Table where CONEXION and DAT1 <> '' order by Tienda, Caja, DAT1",
	"select * from $Table where CONEXION and DAT1 <> '' limit 1",
	"POR ARG BRA",
	"CAJAS CON EL CODIGO DE TIENDA MAL CONFIGURADO:\n\nAS-400:XXXXX - Indica la IP de esta tienda corresponde a otra en los sistemas centrales. Recomendamos en este caso revision en la pantalla de mantenimiento.\n\nCAJA:XXXXX - Indica que la caja esclava tiene un numero de tienda diferente que la master. Recomendamos en este caso ejecutar bat 26");

$Queries["Numero_TPVs_MAL"]=array ("CAJAS MAL CONFIGURADAS<br>NUMERO TPVS MAL DEFINIDO",
	array("Tienda","Caja"," BAT26 ","N.TPVs<br>Tienda","Consulta"),
	"select a.Tienda, a.Caja, a.NTPVS, b.NTPVS, TIME(a.LastM) from $Table a, $Table b where a.CONEXION and a.Tienda = b.Tienda and (a.NTPVS <> b.NTPVS and b.Caja=1) and a.NTPVs > 1 and b.version <= '02.50.00' order by 1,2,3",
	NULL,
	"ESP",
	"CAJAS CON EL NUMERO DE WORKSTATIONS MAL\n\nEsto viene causado a menudo por la ejecucion de un BAT 26 en modo local por la caja (si es una esclava) o introducir el numero mal de TPVs (si es master)\n\nRECOMENDACIONES:\n- Ejecutar BAT 26 en cajas afectadas\n" );

$Server_Cant=Sub_Campo(Sub_Campo("$Table.DAT5",-2,";"),-1,":");
$Caja_1_Cant=Sub_Campo(Sub_Campo("$Table.DAT5",-1,";"),-1,":");
$Queries["WSD_COLAS"]=array ("POSIBLES INCIDENCIAS CUPONES<br>(Informacion de notificaciones pendientes)",
	array("Tienda","Tipo<br>Tienda","Caja","Version","Caja<br>->Server","1er.Msg<br>->Server","Caja<br>->Master","WSD","Puerto","CONECTADA","Consulta"),
	"select Tienda, SUBSTRING(Tipo,1,3), Caja, $Table.Version, $Server_Cant, DAT2, $Caja_1_Cant, $WSD, DAT3, $Conexion, $LastM
	from tiendas, $Table
	where numerotienda=tienda and (".$Server_Cant." > 10 or ".$Caja_1_Cant." > 10 or DAT3='MAL' or NOT WSD) AND $Table.Version AND Conexion
	group by tienda, caja
	order by tienda, caja",
	NULL,
	"POR ARG BRA",
	"POSIBLES INCIDENCIAS DE CUPONES y SERVIDOR DE CUPONES:\n\nCaja -> Server : Son ficheros XML pendientes de enviar al servidor de cupones. Si existen demasiados, indica problemas de conexion entre tienda y servidor de cupones o que el WSD esta caido en la caja. En ese caso, avisad a soporte.\n\n1er.Msg Server : Fecha del primer mensaje bloqueado en la cola (relacionado con el campo anterior)\n\nCaja -> Master : Ficheros que existen en la caja esclava pendientes de ser transferidos a la caja master. Esto indica problemas de red, modos locales o servicio WSD caido. Avisad a soporte.\n\nWSD : Estado de salud del servicio de atencion de cupones en la caja.\n\nPuerto : Si la caja tiene problema en el puerto de acceso al servidor de cupones, es porque no se ha realizado un bat 26 correctamente. Indicad a la tienda que ejecuten bat 26.");

$F_U_T=Sub_Campo("DAT2",-5,";"); $F_U_F=Sub_Campo("DAT2",-4,";");
$E_C=Sub_Campo("DAT2",-3,";");
$C_O=Sub_Campo("DAT2",-2,";");
$T_O=Sub_Campo("DAT2",-1,";");
$Umbral_C_O=1; $Umbral_T_O=300; $Umbral_E_C=5;
$CAMPO_MSG="IF($F_U_F<>'',CONCAT($C_O,' (',$F_U_F,')'),$C_O)";
$CAMPO_TRX="IF($F_U_T<>'',CONCAT($T_O,' (',$F_U_T,')'),$T_O)";
$Queries["OUTBOX"]=array("POSIBLES INCIDENCIAS CUPONES",
	array("Tienda","Caja", "Centro","Version", "MSG Pdtes($Umbral_C_O+)<br>Caja->SC ","WSD?","Err.Sincr.SC <br>($Umbral_E_C+ Hoy)","TRX Pdtes($Umbral_T_O+)<br>Caja->Mast","Consulta"),
	"select Tienda, Caja, tiendas.Centro, $Table.Version, $CAMPO_MSG, $WSD, $E_C, $CAMPO_TRX, $LastM
		from $Table
		join tiendas on tiendas.numerotienda=$Table.tienda AND Pais='$Pais'
		where ($C_O > $Umbral_C_O OR $T_O > $Umbral_T_O OR $E_C > $Umbral_E_C)
		order by 3,1,2",
	NULL,
	"",
	"POSIBLES INCIDENCIAS DE CUPONES y SERVIDOR DE CUPONES:\n\nMSG Pdtes Caja->SC : Indica ficheros XML pendientes de enviar al servidor de cupones. Esto es posible debido a problemas de conexion caja - servidor (ver errores LAN).\n\nWSD? : Indica el estado de salud del servicio de cupones en la caja. Si esta OFF, avisad a SR3.\n\nErr.Sincr.SC : Son errores TIMEOUT y de conexion con peticiones 'sincronas'. Si existen muchos mensajes, hay que revisar la conexion de la caja con el servidor.\n\nTRX Pdtes : Son ficheros de transacciones pendientes de enviar entre esclava y master (indica problemas de comunicaciones en gestor noche).");


$Queries["MAESTRO_CLIENTES"]=array ("M.Clientes<br>(> 200MB)",
	array("Tienda", "Version", "Tamaño"),
	"select Tienda, Version, CONCAT(FORMAT((DAT4/1024),0),' MB') from $Table WHERE DAT4  > 200000 and CAJA=1 order by 2 desc, 3 desc",
	"",
	"POR",
	"");



if (isset($Queries))
	foreach ($Queries as $key => $dato) {
		Show_data2($key, $dato); echo PHP_EOL;
	}
if (isset($Queries_s))
	foreach ($Queries_s as $key => $dato) {
		Show_data_sin_query($key, $dato);
	}
?>
