#!/bin/bash

URL=$1
ssh_opciones="-o StrictHostKeyChecking=no -o ConnectTimeout=10 -o TCPKeepAlive=yes -o ServerAliveInterval=10"
IP=$(echo $URL | sed -e 's/^.*IP=\([^&]*\).*$/\1/')
caja=$(echo $URL | sed -e 's/^.*caja=\([^&]*\).*$/\1/')
TIENDA=$(echo $URL | sed -e 's/^.*tienda=\([^&]*\).*$/\1/')
COMANDO=$(echo $URL | sed -e 's/^.*comandoTerminal=\([^&]*\).*$/\1/')
USUARIO=$(echo $URL | sed -e 's/^.*usuario=\([^&]*\).*$/\1/')
IP_USUARIO=$(echo $URL | sed -e 's/^.*IP_usuario=\([^&]*\).*$/\1/')
IP_SERVER="`hostname -i | cut -f1 -d' '`"
PORT=23

PAIS=`ls /home/MULTI/id_rsa.* | cut -f2 -d"."`

PID=$$
date "+%Y%m%d%H%M%S" > /home/soporteweb/tmp/ShellInABox/$PID.pid

case "$PAIS" in
	"ESP") PORT=23;;
	"PAR") PORT=23;;
	"POR") PORT=PORT=1000$caja;;
	"ARG") PORT=PORT=1000$caja;;
	"BRA") PORT=$caja;;
	"CHI") PORT=PORT=1000$caja;;
	"*") PORT=23;;
esac

case $COMANDO in
	"mysql") CMD="mysql n2a -A";;
	"modipara") CMD="cd /confdia/bin;. functions;./modipara;exit";;
	"modiarti20") CMD="cd /confdia/bin;. functions;./modiarti20;exit";;
	"miraBD") CMD="cd /confdia/bin;. functions;./miraBD -v -b;exit";;

	"tailog") CMD="tail -f /usr/local/n2a/var/log/n2a_application.log";;

	"tailog_periferia") CMD="tail -f /usr/local/n2a/var/log/n2a_application.log | grep 'OPERATOR.DISPLAY\|ey.pressed\|Writting.line.to.the.JOURNAL\|getScanData\|S_KEY_POSITION'";;

	"tailDE") CMD="tail -f /usr/local/n2a/var/data/devices/electronicJournal/DiarioElectronico.log";;

	"log_hoy") CMD="LESSSECURE=1 less /usr/local/n2a/var/log/n2a_application.log";;
	
	"LAN_Stress") CMD="source /etc/swd/setvari; for i in \$(seq 1 \$NUMERO_TPVS_TIENDA); do echo -n \"CAJA \$NUMERO_CAJA ---> CAJA \$i: \"; ping -f -c1000 caja_\$i | grep transmitted; done";;
	"less_DE") CMD="LESSSECURE=1 less /usr/local/n2a/var/data/devices/electronicJournal/DiarioElectronico.log";;
	"less_file") FILE=$(echo "$URL" | sed -e 's/^.*lessFichero=\([^&]*\).*$/\1/'); CMD="LESSSECURE=1 less $FILE";;


	"check_conexion_servidores")
		CMD="echo EJECUTANDO CHECK DE SERVIDORES...; echo; for serv in esconcen1 esconcen2 esconcen3 esconcen4 emv1 emv2 testemv sebcard servidorcupones; do printf \"Checking server %-10s: \" \$serv;  ping -q -f -c100 \$serv | grep  transmitted; done; echo; echo PRUEBA FINALIZADA";;

	"ping_balanza_seccion")
		CMD="
			if [ ! -f /usr/local/n2a/etc/setIPBaseResult.cfg ]; then
				echo NO HAY BALANZA DEFINIDA;
			else
				eval `cat /usr/local/n2a/etc/setIPBaseResult.cfg 2>/dev/null`;
				echo Probando balanza en IP: \$balanza_1; echo;
				if [ `ping -q -w2 -c1 $balanza_1 | grep -c 100%\ packet\ loss` -eq 1 ]; then
					echo BALANZA DESCONECTADA;
				else
					echo BALANZA CONECTADA;
				fi;
			fi";;

	"elementos_tienda")
		CMD="
	echo -e \"COMPROBANDO CONEXION CON ELEMENTOS EXTERNOS A TPV (Espere por favor mientras se realiza el chequeo):\n\";
	[ ! -f /usr/local/n2a/etc/setIPBaseResult.cfg ] && echo -e \"\n\033[0;31mTIENDA SIN ELEMENTOS\n\n\";
	for i in \$(grep -e balanza -e pc_tienda -e impresora /usr/local/n2a/etc/setIPBaseResult.cfg 2>/dev/null); do
		printf \"Probando %-15s IP (%s): \" \${i%=*} \${i#*=};
		ping -q -w1 -c1 \${i#*=} >/dev/null 2>&1 && echo -en \"\033[0;32mConectado\" || echo -en \"\033[0;31mNo Conectado\"; echo -e \"\033[0;37m\";
	done; echo; echo";;
	
	
	"cargar_RUC")
		CMD="source /root/functions.sh; scp -q soporte@\$SERVER:/home/MULTI/files_to_copy/root/check_carga_clientes.sh /tmp/; bash /tmp/check_carga_clientes.sh;"
		TXT="CARGA DE RUCs efectuada por " $USUARIO " (" $IP_USUARIO ")";;

	"download_file")
		FICHERO=$(echo $URL | sed -e 's/^.*fichero=\([^&]*\).*$/\1/')
		DESTINO=$(echo $URL | sed -e 's/^.*destino=\([^&]*\).*$/\1/')
		B_DESTINO=`basename $DESTINO`".tmp"
		>/tmp/$B_DESTINO
		URL_DESTINO="/tmp/BBDD_Tiendas/"$B_DESTINO
		FIN='<a href=\"'$URL_DESTINO'\">'$B_DESTINO'</a>';
		CMD="rsync -avzc --progress "$FICHERO" soporte@"$IP_SERVER":"$DESTINO"; clear; ";
		;;

	"download_file_tunel")
		#set -x
		file_datos_descarga=$(echo $URL | sed -e 's/^.*file_datos_descarga=\([^&]*\).*$/\1/');
		eval `cat $file_datos_descarga`
		mkdir -p $DIR_DESTINO $DIR_CONTROL $DIR_BBDD $DIR_VENTAS $DIR_LOGS $DIR_OTROS >/dev/null

		touch $file_descarga
		rm -f $file_porc
		SIZE_ORIGEN=`ls -l $fichero | awk '{print $5}'`
		( rsync --progress $fichero $destino 2>&1 > $file_porc.tmp) &
		PID=`echo $!`
		touch $file_control
		while [ -d /proc/$PID ]; do
			sed "s/\r/\n/g" $file_porc.tmp | awk '/%/ {print $1";"$2";"$3";"$4}' > $file_porc 
			if [ ! -f $file_control ]; then
				kill -9 $PID;
				rm -f $destino;
				break;
			fi
			sleep 1
		done
		sed "s/\r/\n/g" $file_porc.tmp | awk '/%/ {print $1";"$2";"$3";"$4}' > $file_porc
		rm $file_control $file_descarga -f
		exit
		;;	
	
	"reset_app")
		CMD="source /root/functions.sh; echo -e 'PARANDO LA APLICACION N2A...\n\n' && stop n2a && echo -e '\nINICIANDO LA APLICACION...\n' && start n2a && echo 'RESET REALIZADO CORRECTAMENTE...' && Graba_Historico_Now 'RESET APLICACION: reinicio de la aplicacion efectuado usando HSR por ($USUARIO)'";
		# Graba_Historico "RESET DE APLICACION EFECTUADO POR HSR";
		;;	
	"ulti_mens_visu") CMD="tail --lines=1000 -f /usr/local/n2a/var/log/n2a_application.log | grep OPERATOR.DISPLAY";;

	"log_actu") CMD="tail -f /tmp/001_actualiza_remoto.sh.log";;
	
	"htop")
		htop; clear; exit;;

	"salvado_BBDD_actual")
		CMD="source /root/functions.sh && RSYNC_REMOTO /home/MULTI/files_to_copy/root/Salvado_BBDD_Actual.sh /root/Salvado_BBDD_Actual.sh && bash Salvado_BBDD_Actual.sh && exit";
		;;

	"pc_consola")
		ssh -t -p 22 -C -i /home/MULTI/files_to_copy/root/id_pc $ssh_opciones root@$IP -C ""
		exit;
		;;

	"pc_mysql")
		ssh -t -p 22 -C -i /home/MULTI/files_to_copy/root/id_pc $ssh_opciones root@$IP -C "mysql hydra -ptpv"
		exit;
		;;

	"pc_log_hydra")
		CMD="cd /var/log; tail -f \$(ls -t hydra.log* | head -1)"
		ssh -t -p 22 -C -i /home/MULTI/files_to_copy/root/id_pc $ssh_opciones root@$IP -C "$CMD"
		exit;
		;;

	"log_capturador_hoy")
		CMD="cd /usr/share/guc/log/; LESSSECURE=1 less \$(ls -r  | head -1); echo;";
		;;

	*) CMD="";;
esac

ssh -t -p $PORT -C -i $HOME/.ssh/id_dia $ssh_opciones root@$IP -C "$CMD" 2>>/tmp/open_term.log
[ ! -z "$FIN" ] && echo "<span id='status_proceso' style='display:none'>FIN PROCESO</span>$FIN" >/tmp/$B_DESTINO

exit
