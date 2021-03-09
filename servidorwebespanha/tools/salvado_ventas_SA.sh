#!/bin/bash

Tienda=$1
DIR_TMP=$2
SERVER=$3

FECHA=`date +%Y-%m-%d`
Flag_Ejec="/root/Comunicacion_a_fichero_$FECHA"
F_Tienda=`printf "%05d" $Tienda`

mkdir -p /root/ventas_GN
cd /root/ventas_GN
if [ -f $Flag_Ejec ] ; then
	echo 'ERROR: salvado ya ejecutado hoy...';
else
	scp -q soporte@$SERVER:$Dir_Orig/$F_Salvar $Dir_Dest/$F_Salvar
	if [ ! -s $Dir_Dest/$F_Salvar ]; then
		echo 'ERROR: al transferir la herramienta de salvado de datos'
	else 
		service nfs stop
			tar czf BBDD.tgz /confdia/ctrdatos/maepara1.* /confdia/ficcaje/maeemis* /confdia/ficcaje/intdocu1.dat /confdia/ficcaje/hisdocu1.dat;
			chmod 755 /root/Comunicacion_a_fichero;
			cd /confdia/bin; . ./functions; . ./setvari;
			/root/Comunicacion_a_fichero $Tienda 2>&1 | tee -a /tmp/Comunicacion_a_fichero.$FECHA.log
			service nfs start;
			cd /tmp;
			tar cz M$F_Tienda* | ssh soporte@$SERVER \\\"cd /$DOCUMENT_ROOT/$DIR_TMP/; tar xzf -\\\";
			for f in M$F_Tienda*; do
				mv \$f /root/ventas_GN/\$f.$FECHA;
			done;
			touch $Flag_Ejec;
		fi
	fi\"
