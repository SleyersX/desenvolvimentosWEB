<?php

$Tienda=$_GET["Tienda"];
$Pais=$_GET["Pais"];

require_once("/home/soporteweb/tools/mysql.php");

/*
function string_in() {
	grep -c -o "'$1'" <<< $2 | tr -d "'"
}

function busca_str() {
	for i in $2; do
		if [ "$i" == "$1" ]; then
			return 0
		fi
	done
	return 1
}

function MySQL () {
	[ -z "$LOG_TIENDA" ] && LOG_TIENDA="/tmp/error_mysql.log"
	mysql $COMMAND_OPTIONS_MYSQL $DATABASE -N -e "$1" -h $SQL_SERVER1 2>>$LOG_TIENDA
}

function MySQL_file () {
	mysql $COMMAND_OPTIONS_MYSQL $DATABASE -N -h $SQL_SERVER1 < $1 2>>$LOG_TIENDA
}

function MySQL_sin_N () {
	mysql $COMMAND_OPTIONS_MYSQL $DATABASE -e "$1" -h $SQL_SERVER 2>>$LOG_TIENDA
}

function Select_Tienda () { RESULTADO=`MySQL "$1"`; echo $((RESULTADO+0)); }

Add_Comando() { export COMANDOS=${COMANDOS}" $1;"; echo "Añadido comando: $1" >> $LOG_TIENDA; }

Add_File() { FILES_TO_COPY=${FILES_TO_COPY}" $1" ; echo "Añadido fichero: $1" >> $LOG_TIENDA; }

function Get_Lista_Tiendas () {
	[ -z "$TIPO_LISTA" ] && TIPO_LISTA="DIA"
	[ ! -z "$DIR_TMP" ] && find $DIR_TMP -type f -mtime +3 -delete; # BORRAMOS TEMPORALES ANTIGUOS

	MySQL_file "crea_entorno_mysql.sql"
	MySQL_sin_N "$(cat crea_entorno_multi.sql)\G" | tr -d ' ' | awk -F':' -vB="'" '!/row/ { printf $1 "="B $2 B";" } /row/ { print ""; }' > $LISTA_TIENDAS
	MySQL "select distinct(LPAD(numerotienda,5,'0')) from tmpTiendas order by numerotienda" > $SOLO_TIENDAS

	MySQL "delete from tiendas_especiales where comentario='ECOMMERCE'; load data local infile '/home/stocks/codigosTiendas' into table tiendas_especiales (@tienda) set tienda=@tienda, comentario='ECOMMERCE'"

	### BLOQUEAMOS TIENDAS CON DOBLE IP Y CERRADAS (IP 1.1.1.1 o 2.2.2.2)
	MySQL "delete from Bloqueo; REPLACE Bloqueo (select numerotienda,now() from tmpTiendas where IP in ((select distinct(ip) from IP_Duplicadas)) OR IP in ('1.1.1.1','2.2.2.2'))"

	echo "Inicio de proceso: $(date)" > tmp/inicio
}

function WARNING() { MySQL "REPLACE WARNING_$PAIS VALUES ($TIENDA, $1, $2, '$3', NOW())"; }
*/

##### COMPROBAMOS CONEXION CON CAJA MASTER, INTENTANDO OBTENER ESPACIO DISPONIBLE EN DISCO DURO. #########################
function Check_Caja_Master() {
	global $FICHTMP,
	if (empty($FICHTMP)) { $RESET=1; }

	rm -f $FICHTMP $FILE_PREVIO $FILE_POST -f

	if [ $BLOQUEO -eq 1 ]; then
		Graba_Error "ERROR10: Bloqueo de acceso por problemas conexion..."
		MySQL "UPDATE $TABLE SET Conexion=0, Version='BLOQUEO' where tienda=$TIENDA"
		return 1
	fi

	if [ "$IP" == "1.1.1.1" -o "$IP" == "2.2.2.2" ]; then
		Graba_Error "ERROR1: Direccion incorrecta ($IP)..."
		MySQL "UPDATE $TABLE SET VERSION='IP INCORR.', Conexion=0, ip='$IP' WHERE TIENDA=$TIENDA"
		return 1
	fi

	if [ $ABSOLUTO -eq 0 ]; then
		PING=`ping -c1 -w2 $IP >/dev/null; echo $?`
		if [ $PING -ne 0 ]; then
			MySQL "UPDATE $TABLE SET Conexion=0, Version='NO ROUTER' where tienda=$TIENDA";
			MySQL "UPDATE Paquetes SET PKG='', PKG_Auto='' where tienda=$TIENDA";
			Graba_Error "ERROR1: No acceso router ($IP)..."; return 1;
		fi
	fi

	STATUS=`ssh $CONEXION "df | grep [hs]da | grep -q 100% && echo LLENO && exit; cat /proc/mounts | grep ro, && echo RO && exit; grep -q site=$TIENDA /usr/local/n2a/etc/n2a-context.properties || echo TIENDA_MAL && echo CONECTADO" 2>>$LOG_TIENDA`

	case "$STATUS" in
		"LLENO") Graba_Error "ERROR5: Disco duro LLENO..."; WARNING 1 2 "DISCO DURO LLENO."; return 1;;
		"RO")    Graba_Error "ERROR6: Disco duro en SOLO LECTURA..."; WARNING 1 1 "DISCO DURO MONTADO EN SOLO LECTURA"; return 1;;
		"TIENDA_MAL")
			MySQL "UPDATE $TABLE SET Conexion=0, Version='TIENDA EQUIV.' where tienda=$TIENDA";
			Graba_Error "ERROR7: Numero de tienda equivocado $TIENDA..."; return 1;;
		"CONECTADO") return 0;;
		*)
			MySQL "UPDATE $TABLE SET Conexion=0, Version='MASTER NO.CNX' where tienda=$TIENDA";
			MySQL "UPDATE Paquetes SET PKG='', PKG_Auto='' where tienda=$TIENDA";
			Graba_Error "ERROR4: Caja master no responde..."; return 1;;
	esac

	return 0
}

function Graba_Error() {
	TEXTO="$1"; PROGRESO "${TEXTO:0:20}"
	MySQL "UPDATE $TABLE SET Conexion=0 WHERE Tienda=$TIENDA;"
	Graba_Acceso "$1"
	[ -f $LOG_TIENDA ] && echo "$1" >> $LOG_TIENDA
}

function Graba_Previo() {
	echo "/* DATOS PREVIOS */" > $FILE_PREVIO
	if [ $RESET -eq 1 ]; then
		echo "delete from $TABLE where Tienda=$TIENDA;" >> $FILE_PREVIO
		echo "delete from ModosLocales where Tienda=$TIENDA;" >> $FILE_PREVIO
		echo "DELETE FROM Solo_DATS where Tienda=$TIENDA;" >> $FILE_PREVIO
	fi
	echo "UPDATE $TABLE SET Conexion=1 WHERE TIENDA=$TIENDA;" >> $FILE_PREVIO
	echo "DELETE FROM WARNING_ESP WHERE TIENDA=$TIENDA;" >> $FILE_PREVIO
	echo "DELETE FROM MESSAGE_OUT_ESP where TIENDA=$TIENDA;" >> $FILE_PREVIO
	echo "DELETE FROM MESSAGE_LOCAL_OUT_ESP where TIENDA=$TIENDA;" >> $FILE_PREVIO
	echo "delete from Tienda_Oferta where tienda=$TIENDA;" >> $FILE_PREVIO
	rm -f /home/pendserv/subidos/$TIENDA.resultado_15d
	rm -f /home/colas/info_cajas/colas.$TIENDA.*.json
	rm -f /home/MULTI/tmp/ofertas/$TIENDA.*

	MySQL_file $FILE_PREVIO 
}

function Graba_Post() {
	echo "/* DATOS POST-PROCESO */"
	echo "UPDATE $TABLE SET LastM=NOW(), IP='$IP' WHERE TIENDA=$TIENDA;"
}

function Graba_Acceso () {
	MySQL "REPLACE Accesos_Tiendas VALUES ($TIENDA,'$PAIS',NOW(),'$1');"
}


#function Conecta_Tienda () {
# F_TO_COPY_TDA="files_to_copy.$TIENDA.tgz"
# COMANDO_REMOTO="mv tmp/$PARAM /root/param.dat -f; bash multi.sh > tmp/datos_a_transferir; scp -q tmp/datos_a_transferir soporte@$SERVER:/$FICHTMP"
# nice tar -cz $FILES_TO_COPY > /tmp/$F_TO_COPY_TDA 2>>$LOG_TIENDA
# timeout 180 nice ssh $CONEXION "scp -q soporte@$SERVER:/tmp/$F_TO_COPY_TDA .; tar -xzf $F_TO_COPY_TDA; $COMANDO_REMOTO" 2>>$LOG_TIENDA
# rm /tmp/$F_TO_COPY_TDA -f
#}

function Proceso () {
	[ $WEB -eq 1 ] && _ECHO "<ul>"
	PROGRESO "Check update status..."
	if [ -f $FILE_PREVIO ]; then
		PROGRESO "Actualizacion en curso..."
	else
		PROGRESO "Check update status... OK"

		PROGRESO "Delete previous info...";
		Graba_Previo
		PROGRESO "Check update status... OK"

		PROGRESO "Checking in progress - Master...";
		Check_Caja_Master; CNX=$?
		if [ $CNX -eq 0 ]; then
			PROGRESO "Checking in progress - Master... OK";

			PROGRESO "Getting Data Store..."
			pushd files_to_copy/root >/dev/null
			COMANDO_REMOTO="tar -xzf -;"
			COMANDO_REMOTO="$COMANDO_REMOTO mv tmp/$PARAM /root/param.dat -f;"
			COMANDO_REMOTO="$COMANDO_REMOTO source /root/functions.sh;"
			COMANDO_REMOTO="$COMANDO_REMOTO bash multi.sh > tmp/datos_a_transferir;"
			COMANDO_REMOTO="$COMANDO_REMOTO RSYNC_UPLOAD tmp/datos_a_transferir /$FICHTMP"

			echo "$COMANDO_REMOTO" >>$LOG_TIENDA
			tar -cz $FILES_TO_COPY 2>/dev/null | ssh $CONEXION "$COMANDO_REMOTO" >>$LOG_TIENDA 2>>$LOG_TIENDA
			popd >/dev/null

			if [ -f $FICHTMP ]; then
				PROGRESO "Getting Data Store... OK"
				PROGRESO "Recording data..."
				# PROGRESO "Grabando post"
				Graba_Post > $FILE_POST

				PROGRESO "Grabando resultado..."
				cat $FICHTMP $FILE_POST > $FICHTMP.tmp

				FILE_OFERTAS="$TIENDA.tienda_oferta.dat"
				if [ -f /home/MULTI/tmp/ofertas/$FILE_OFERTAS ]; then
					MySQL "load data local infile '/home/MULTI/tmp/ofertas/$FILE_OFERTAS' INTO TABLE Tienda_Oferta;"
				fi

				cat /tmp/$TIENDA.*.historico_versiones.tmp > /tmp/$TIENDA.TOTAL.historico_versiones.tmp 2>/dev/null
				MySQL "LOAD DATA LOCAL INFILE '/tmp/$TIENDA.TOTAL.historico_versiones.tmp' INTO TABLE Versiones_TPV FIELDS TERMINATED BY ','"
				rm /tmp/$TIENDA.*tmp -f

				if [ $ACTIVAR_REVISION -eq 1 ]; then
					if [ -s /home/pendserv/subidos/$TIENDA.resultado ]; then
						MySQL "load data local infile '/home/pendserv/subidos/$TIENDA.resultado' into table Pend_Serv"
					fi

					F_TEMPORAL=$(mktemp)
					F_PEND_SERV=`find /home/pendserv/resultados/ -maxdepth 1 -name "$TIENDA.resultado.1.*"`;
					if [ ! -z "$F_PEND_SERV" ]; then
						cat $F_PEND_SERV > $F_TEMPORAL
						MySQL "delete from tmp_regularizacion where tienda=$TIENDA;"
						[ -s $F_TEMPORAL ] && MySQL "load data local infile '$F_TEMPORAL' into table tmp_regularizacion"
						rm $F_TEMPORAL -f

						if [ -s /home/pendserv/subidos/$TIENDA.resultado_15d ]; then
							MySQL "load data local infile '/home/pendserv/subidos/$TIENDA.resultado_15d' into table Pend_Serv_15d"
						fi
					fi
				fi

				### AGREGAMOS LA INFO AL LOG DEL KAIBANA
				# if [ -f /home/colas/info_cajas/*.$TIENDA.*.json ]; then
					cat /home/colas/info_cajas/*.$TIENDA.*.json >> /home/colas/tickets.log
					rm /home/colas/info_cajas/*.$TIENDA.*.json -f
				# fi

				mv $FICHTMP.tmp $FICHTMP -f

				if [ `grep -c "FIN CHECK CAJA 1" $FICHTMP` -eq 0 ]; then
					Graba_Acceso "ERROR: Master con problemas..."
					MySQL "REPLACE $TABLE SET Conexion=0, Version='MASTER.NO.CNX' WHERE tienda=$TIENDA AND caja=1"
				else
					Graba_Acceso "Datos recogidos correctamente"
					MySQL_file $FICHTMP
				fi
				PROGRESO "Recording data... OK"
			else
				Graba_Error "ERROR99: Error en el servidor. Imposible conectar tienda"
			fi
		fi
	fi
	rm /tmp/$PARAM -f
	rm $FILE_PREVIO $FILE_POST -f
	rm /home/MULTI/tmp/descargas/$TIENDA-*.* -f
	PROGRESO "Finished"

	[ $WEB -eq 1 ] && _ECHO "</ul>"
}

function Check_Otras_Cajas() {
	N_TPVS="`echo $(MysQL "select caja from $TABLE where tienda=$TIENDA")`"
	[ -z "$N_TPVS" ] && return
}

function PROGRESO(){
	HILO=$(printf '%02d' $((POSICION-2)))
	[ ! -z "$TIENDA" ] && DATOS_TIENDA="($TIENDA-$IP)" || DATOS_TIENDA="FIN"
	([ -f $FILE_KEY ] && echo "$HILO,$N_L,$N_L,Detencion manual del sistema..." || echo "$HILO,$contador,$N_L,$DATOS_TIENDA $1") > $DIR_ACTU/tmp/Hilo_$HILO
	[ ${WEB} -eq 1 ] && _ECHO "<li>$1</li>"
}

function EstadoCtrl () {
	if [ "$EstadoAnterior" != "$1" ]; then
		TIME_TMP=`date +"%Y/%m/%d %H:%M:%S"`
		EstadoAnterior="$1"
	fi
}

function Check_Previo () {
	HORA=$(date +%H%M)
	REPETICIONES=1
	[ $HORA -lt 0845 -o $HORA -gt 2200 ] && REPETICIONES=2
	[ -f $FILE_KEY ] && REPETICIONES=0
	echo $REPETICIONES > estado$TIPO_LISTA
	return $REPETICIONES
}

function Get_Files_Split () {
	N_LINEAS=$(grep -c "" $LISTA_TIENDAS)
	# N_LINEAS=$(grep -c "" $SOLO_TIENDAS)
	N_DIV=$(($N_LINEAS / $N_PARTES))
	pushd $DIR_PARTES; rm $TIPO_LISTA.x* -f ; split -l $N_DIV $LISTA_TIENDAS $TIPO_LISTA.x; popd
}

function Lanza_Hilos_Ejecucion () {
	Cont=2
	rm tmp/Hilo_* -f
	for file in $DIR_PARTES/$TIPO_LISTA.x*; do
		Cont=$((Cont+1))
		bash Hilo_Tiendas $file $Cont 0 "NORMAL" &
	done;
}
?>
