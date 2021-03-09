#!/bin/bash
#
# Autor: Walter Moura
# Data: 02-06-2020
# Modificado por: Walter Moura
# Data modificacao: 02-06-2020
#
# Script que fara interface entre o PHP e a loja para obter o status, reiniciar, iniciar e stop service
# /etc/init.d/watchdog-monitor-sat 
# 

LOG="/var/www/html/source/app-sat/api/bash/log/error.service.log"
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
    "status")
        RESPONSE=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP "/etc/init.d/watchdog-monitor-sat $SELETOR"`
        RETURN=$?
        if [ $RETURN -eq 0 ]; then
            PID=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP 'pidof monitor-sat'`
            if [ -z $PID ]; then
                echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:['$RESPONSE']." >> $LOG
                echo -n "<script>alert('PDV_$NUM_POS Status watchdog-monitor-sat-services: DOWN -> [$PID] ');</script>"
            else
                echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:['$RESPONSE']." >> $LOG
                echo -n "<script>alert('PDV_$NUM_POS Status watchdog-monitor-sat-services: UP -> [$PID] ');</script>"
            fi
        else
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Erro ao executar comando -> $RETURN." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Erro ao executar comando -> $RETURN');</script>"
        fi
    ;;
    "start")
        PID=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP 'pidof monitor-sat'`
        if [ -z $PID ]; then
            RESPONSE=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP "/etc/init.d/watchdog-monitor-sat $SELETOR"`
            RETURN=$?
            if [ $RETURN -eq 0 ]; then
                echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Service iniciado com sucesso." >> $LOG
                echo -n "<script>alert('PDV_$NUM_POS Service iniciado com sucesso');</script>"
            else
                echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Erro ao executar comando -> $RETURN." >> $LOG
                echo -n "<script>alert('PDV_$NUM_POS Erro ao executar comando -> $RETURN');</script>"
            fi
        else
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Service watchdog-monitor-sat-services: UP -> PID=['$PID']." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Status watchdog-monitor-sat-services: UP -> [$PID] ');</script>"
        fi
    ;;
    "restart")
        RESPONSE=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP "/etc/init.d/watchdog-monitor-sat $SELETOR"`
        RETURN=$?
        if [ $RETURN -eq 0 ]; then
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Service reiniciado com sucesso." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Service reiniciado com sucesso ');</script>"
        else
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Erro ao executar comando -> $RETURN." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Erro ao executar comando -> $RETURN');</script>"
        fi
    ;;
    "stop")
        PID=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP 'pidof monitor-sat'`
        if [ -z $PID ]; then
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Service watchdog-monitor-sat-services: DOWN -> PID=['$PID']." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Status watchdog-monitor-sat-services: DOWN -> [$PID] ');</script>"
        else
            RESPONSE=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP "/etc/init.d/watchdog-monitor-sat $SELETOR"`
            RETURN=$?
            if [ $RETURN -eq 0 ]; then
                echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Service parado com sucesso." >> $LOG
                echo -n "<script>alert('PDV_$NUM_POS Service parado com sucesso ');</script>"
            else
                echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Erro ao executar comando -> $RETURN." >> $LOG
                echo -n "<script>alert('PDV_$NUM_POS Erro ao executar comando -> $RETURN');</script>"
            fi
        fi
    ;;
    "remove")
         RESPONSE=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP "killall monitor-sat ; rm -vf /confdia/descargas/monitor-sat.tgz; rm -vf /confdia/descargas/curl-7.8-1.i386.rpm; rm -vf /etc/init.d/watchdog-monitor-sat; rm -vf /etc/rc[0,1,2,3,4,5,6].d/*watchdog-monitor-sat; rm -rfv /usr/local/monitor-sat/; rm -rvf /srv/Debian6.0/opt/monitor-sat/"`
         RETURN=$?
        if [ $RETURN -eq 0 ]; then
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Service removido com sucesso." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Service removido com sucesso ');</script>"
        else
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:service:$SHOP:$NUM_POS:Erro ao executar comando -> $RETURN." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Erro ao executar comando -> $RETURN');</script>"
        fi
    ;;
    *)
        echo -n "Usage: {start|stop|status|restart|reload|remove} {IP SHOP} {POS} "

    ;;
esac