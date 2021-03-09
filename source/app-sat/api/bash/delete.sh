#!/bin/bash
#
# Autor: Walter Moura
# Data: 02-06-2020
# Modificado por: Walter Moura
# Data modificacao: 02-06-2020
#
# Script que fara interface entre o PHP e a loja para remover o serviço de monitoramento
# /etc/init.d/watchdog-monitor-sat 
# 

LOG="/var/www/html/source/app-sat/api/bash/log/error.uninstall-service.log"
SELETOR=$1
SHOP=$2
IP_SHOP=$3
NUM_POS=$4
USER="root"
PASSWD="root"
CONFIG_SSH="-oKexAlgorithms=+diffie-hellman-group1-sha1 -oConnectTimeout=1 -oStrictHostKeyChecking=no"
PORT="1000$NUM_POS"
echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:Executando o programa." >> $LOG
echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:['$SELETOR']:['$SHOP']:['$IP_SHOP']:['$NUM_POS']." >> $LOG

case $SELETOR in
    "remove")
        sshpass -p $PASSWD ssh $CONFIG_SSH $IP_SHOP -p $PORT -l $USER 'killall monitor-sat ; rm -vf /confdia/descargas/monitor-sat.tgz; rm -vf /confdia/descargas/curl-7.8-1.i386.rpm; rm -vf /etc/init.d/watchdog-monitor-sat; rm -vf /etc/rc[0,1,2,3,4,5,6].d/*watchdog-monitor-sat; rm -rfv /usr/local/monitor-sat/; rm -rvf /srv/Debian6.0/opt/monitor-sat/'
        RETURN=$?
        if [ $RETURN -eq 0 ]; then
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:Servico removido com sucesso ['$SELETOR']:['$SHOP']:['$IP_SHOP']:['$PORT']." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Serviço removido com sucesso -> $RETURN');</script>|1"
        else
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:Falha ao remover servico ['$SELETOR']:['$SHOP']:['$IP_SHOP']:['$PORT']." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Falha ao remover serviço -> $RETURN');</script>|0"
        fi
    ;;
    *)
        echo -n "Usage: {remove} {SHOP} {IP SHOP} {POS} "
    ;;
esac