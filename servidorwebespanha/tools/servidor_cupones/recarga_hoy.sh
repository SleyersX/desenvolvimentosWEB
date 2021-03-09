#!/bin/bash

OPTION="$1"; [ -z "$OPTION" ] && OPTION="GET_OPER"

QUIEN_SOY=`hostname -i`
case "$QUIEN_SOY" in
	"10.208.162.6")
		SERVIDORES=(10.208.160.112 10.208.160.113)
		PRG="/opt/SC/bin/estadisticas_hoy2_tiendas.sh 30"
		PRG_WEB="/opt/SC/bin/estadisticas_hoy2_web.sh 30"
		#PRG="/opt/SC/bin/estadisticas_hoy.sh "
		echo "ESP" > pais.dat
		;;
	"10.246.64.73")
		SERVIDORES=(10.246.64.165 10.246.64.166)
		PRG="/opt/SC/bin/estadisticas_hoy.sh"
		echo "POR" > pais.dat
		;;
	"171.100.1.145")
		SERVIDORES=(10.94.202.38 10.94.202.39)
		PRG="/opt/SC/bin/estadisticas_hoy.sh"
		echo "ARG" > pais.dat
		;;
esac

DDD=` date +%y%m%d`
HORA=`date +%H%M`
FECHA=`date +%Y-%m-%d`

case "$OPTION" in
	"GET_OPER")
		for serv in 1 2; do
#			cp /home/soporteweb/img/No_disponible.jpg porhora$serv.jpg -f
			echo "Obteniendo informacion del servidor $serv..."
			FILE_OPER="my_operaciones_servidor.$serv.dat"; FILE_OPER_WEB="my_operaciones_servidor_web.$serv.dat"
			FILE_RESU="my_serv_cupo.$serv.sql"; FILE_RESU_WEB="my_serv_cupo_web.$serv.sql"
			SERVIDOR="${SERVIDORES[$((serv-1))]}"

			echo "Obteniendo información del servidor $serv..."
			ssh -i /home/soporte/.ssh/id_cupones -lsoporte $SERVIDOR "$PRG" | sort -g > $FILE_OPER
			ssh -i /home/soporte/.ssh/id_cupones -lsoporte $SERVIDOR "$PRG_WEB" | sort -g > $FILE_OPER_WEB

			echo "Parseando informacion del servidor $serv..."
			TABLA="serv_cupo"$serv
			awk -v C1="'" -v FECHA=$FECHA -v HORA=$HORA -v Tabla=$TABLA '
				BEGIN { for (h=0; h<24; h++) for (m=0; m<6; m++) { t[sprintf("%02d%d",h,m)0]=0; } } 
				{ t[substr($1,26,2) substr($1,28,1) 0]++; } 
				END { for (i in t) {if (i <= HORA ) {C=C1 FECHA " " substr(i,1,2)":"substr(i,3,2) C1;
				print "INSERT INTO " Tabla " VALUES (" C "," t[i] ",0) ON DUPLICATE KEY UPDATE Oper = IF(Oper>" t[i] ",Oper," t[i] ");" }}}
			' $FILE_OPER > $FILE_RESU
			awk -v C1="'" -v FECHA=$FECHA -v HORA=$HORA -v Tabla=$TABLA '
				BEGIN { for (h=0; h<24; h++) for (m=0; m<6; m++) { t[sprintf("%02d%d",h,m)0]=0; } } 
				{ t[substr($1,26,2) substr($1,28,1) 0]++; } 
				END { for (i in t) {if (i <= HORA ) {C=C1 FECHA " " substr(i,1,2)":"substr(i,3,2) C1;
				print "INSERT INTO " Tabla " VALUES (" C ",0," t[i] ") ON DUPLICATE KEY UPDATE Web = IF(Web>" t[i] ",Web," t[i] ");" }}}
			' $FILE_OPER_WEB > $FILE_RESU_WEB

			echo "Volcando informacion en mysql..."
			cat $FILE_RESU $FILE_RESU_WEB | mysql soporteremotoweb

			echo "Generando informacion para graficas"
			FILE_DATOS="datos"$serv".dat"; FILE_DATOS_WEB="datos_web"$serv".dat"
			mysql soporteremotoweb -e "SELECT time(id),Oper FROM $TABLA WHERE date(id)=date(now()) AND time(id) BETWEEN '08:00:00' AND '22:00:00'" | grep -v time > $FILE_DATOS
			mysql soporteremotoweb -e "SELECT time(id),web FROM $TABLA WHERE date(id)=date(now()) AND time(id) BETWEEN '08:00:00' AND '22:00:00'" | grep -v time > $FILE_DATOS_WEB
		done
		rm porhora*.jpg -f
		[ -s datos1.dat -a -s datos2.dat ] && gnuplot ./crea.gp
		[ -s datos_web1.dat -a -s datos_web2.dat ] && gnuplot ./crea_web.gp
		;;
	"GET_STAT")
		ssh -i /home/soporte/.ssh/id_cupones -lsoporte ${SERVIDORES[0]} "/usr/local/bin/resumen.sh" | grep ":"
		;;
esac
