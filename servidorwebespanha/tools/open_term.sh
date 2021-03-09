#!/bin/bash

URL=$1
ssh_opciones="-o StrictHostKeyChecking=no -o ConnectTimeout=10 -o TCPKeepAlive=yes -o ServerAliveInterval=10"
IP=$(echo $URL | sed -e 's/^.*IP=\([^&]*\).*$/\1/')
caja=$(echo $URL | sed -e 's/^.*caja=\([^&]*\).*$/\1/')
COMANDO=$(echo "$URL" | sed -e 's/^.*comandoTerminal=\([^&]*\).*$/\1/')

if [ $caja -lt 10000 ] ; then
	caja=1000$caja
fi

if [ "${IP:0:4}" == "10.4" ]; then
	caja=23
fi

case $COMANDO in
	"mysql") CMD="mysql n2a -A";;
	"modipara") CMD="cd /confdia/bin;. functions;./modipara;exit";;
	"modiarti20") CMD="cd /confdia/bin;. functions;./modiarti20;exit";;
	"miraBD") CMD="cd /confdia/bin;. functions;./miraBD -v -b;exit";;
	"tailog") CMD="tail -f /usr/local/n2a/var/log/n2a_application.log";;
	"tail_DE") CMD="tail -f /usr/local/n2a/var/data/devices/electronicJournal/DiarioElectronico.log";;
	"lan_stress") CMD="source /etc/swd/setvari; for i in \$(seq 1 \$NUMERO_TPVS_TIENDA); do echo -n \"CAJA \$NUMERO_CAJA ---> CAJA \$i: \"; ping -f -c1000 caja_\$i | grep transmitted; done";;
	"less_DE") CMD="less /usr/local/n2a/var/data/devices/electronicJournal/DiarioElectronico.log";;
	"less_file") FILE=$(echo "$URL" | sed -e 's/^.*lessFichero=\([^&]*\).*$/\1/'); CMD="less $FILE";;
	*) CMD="";;
esac


ssh -t -p $caja -C -i $HOME/.ssh/id_dia $ssh_opciones root@$IP -C "$CMD"
