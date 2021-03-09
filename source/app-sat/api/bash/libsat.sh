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

LOG="/var/www/html/source/app-sat/api/bash/log/error.libsat.log"
SELETOR=$1
SHOP=$2
IP_SHOP=$3
NUM_POS=$4
NUM_SAT=$5
USER="root"
PASSWD="root"
CONFIG_SSH="-oKexAlgorithms=+diffie-hellman-group1-sha1 -oConnectTimeout=1 -oStrictHostKeyChecking=no"
CONFIG_REDE="/var/www/html/source/app-sat/api/processo/xml/config_rede.xml"
TESTE_SEFAZ="/var/www/html/source/app-sat/api/processo/xml/teste_sefaz.xml"
PORT="100$NUM_POS"

echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:Executando o programa." >> $LOG
echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:['$SELETOR']:['$SHOP']:['$IP_SHOP']:['$PORT']." >> $LOG
case $SELETOR in
    "log")
        CODIGO_ATIVACAO=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP '. /usr/local/monitor-sat/monitor-sat.ini; echo $codigo'`
        COUNT_LOG=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP "ls -ltr /srv/Debian6.0/opt/monitor-sat/log/satlog.txt | wc -l"`
        RETURN=$?
        if [ $RETURN -eq 0 ]; then
            if [ $COUNT_LOG -ge 1 ]; then
                echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Arquivo de log encontrado ['$COUNT_LOG']." >> $LOG
                echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Removendo arquivo ['/srv/Debian6.0/opt/monitor-sat/log/satlog.txt']." >> $LOG
                REMOVE_LOG=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP "rm -vf /srv/Debian6.0/opt/monitor-sat/log/satlog.txt"`
                RETURN=$?
                if [ $RETURN -eq 0 ]; then
                    echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Arquivo ['/srv/Debian6.0/opt/monitor-sat/log/satlog.txt'] removido com sucesso." >> $LOG
                    echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Gerando Log ['/srv/Debian6.0/opt/monitor-sat/satlog.txt']." >> $LOG
                    SSH_CMD="chroot /srv/Debian6.0/ /opt/monitor-sat/getLog ${CODIGO_ATIVACAO}"
                    sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP $SSH_CMD > /dev/null 2>&1
                    RETURN=$?
                    if [ $RETURN -eq 0 ]; then
                        echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Arquivo de log gerado com sucesso." >> $LOG
                        echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Copiando arquivo para a pasta TEMP." >> $LOG
                        TEMP_FILE=`echo "$SHOP""_""$NUM_POS""_""$NUM_SAT""_log.txt"`
                        sshpass -p $PASSWD scp $CONFIG_SSH -P $PORT $USER@$IP_SHOP:/srv/Debian6.0/opt/monitor-sat/log/satlog.txt /var/www/html/source/app-sat/api/bash/temp/$TEMP_FILE
                        RETURN=$?
                        if [ $RETURN -eq 0 ]; then
                            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Arquivo de log copiado com sucesso." >> $LOG
                            #echo "https://10.106.68.78/source/app-sat/api/processo/processa_download.php?sat=$NUM_SAT&file=$TEMP_FILE"
                            echo "<script>window.open('https://10.106.68.78/source/app-sat/api/processo/processa_download.php?sat=$NUM_SAT&file=$TEMP_FILE');</script>";
                        else
                            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Arquivo de log copiado com sucesso." >> $LOG
                        fi
                    else
                        echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Erro ao gerar arquivo ['/srv/Debian6.0/opt/monitor-sat/satlog.txt'] - > $RETURN." >> $LOG
                    fi
                else
                    echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Erro ao remover arquivo ['/srv/Debian6.0/opt/monitor-sat/log/satlog.txt'] - > $RETURN." >> $LOG
                fi
            else
                echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Arquivo de log não encontrado ['$COUNT_LOG']." >> $LOG
                echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Gerando Log ['/srv/Debian6.0/opt/monitor-sat/satlog.txt']." >> $LOG
                SSH_CMD="chroot /srv/Debian6.0/ /opt/monitor-sat/getLog ${CODIGO_ATIVACAO}"
                sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP $SSH_CMD > /dev/null 2>&1
                RETURN=$?
                if [ $RETURN -eq 0 ]; then
                    echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Arquivo de log gerado com sucesso." >> $LOG
                    echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Copiando arquivo para a pasta TEMP." >> $LOG
                    TEMP_FILE=`echo "$SHOP""_""$NUM_POS""_""$NUM_SAT""_log.txt"`
                    sshpass -p $PASSWD scp $CONFIG_SSH -P $PORT $USER@$IP_SHOP:/srv/Debian6.0/opt/monitor-sat/log/satlog.txt /var/www/html/source/app-sat/api/bash/temp/$TEMP_FILE
                    RETURN=$?
                    if [ $RETURN -eq 0 ]; then
                        echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Arquivo de log copiado com sucesso." >> $LOG
                        #echo "https://10.106.68.78/source/app-sat/api/processo/processa_download.php?sat=$NUM_SAT&file=$TEMP_FILE"
                        echo "<script>window.open('https://10.106.68.78/source/app-sat/api/processo/processa_download.php?sat=$NUM_SAT&file=$TEMP_FILE');</script>";
                    else
                        echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Arquivo de log copiado com sucesso." >> $LOG
                    fi
                else
                    echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Erro ao gerar arquivo ['/srv/Debian6.0/opt/monitor-sat/satlog.txt'] - > $RETURN." >> $LOG
                fi
            fi
        else
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:log:$SHOP:$PORT:Erro ao executar comando -> $RETURN." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Erro ao executar comando -> $RETURN');</script>"
        fi
    ;;
    "update")
        CODIGO_ATIVACAO=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP '. /usr/local/monitor-sat/monitor-sat.ini; echo $codigo'`
        SELECIONE_PROGRAMA=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP 'PID=$(pidof dispatch.exe);if [ -z $PID ]; then echo 0 ;else echo 0; fi'`
        if [ $SELECIONE_PROGRAMA -eq 0 ]; then
            SSH_CMD="chroot /srv/Debian6.0/ /opt/monitor-sat/updateSAT ${CODIGO_ATIVACAO}"
            RESPONSE=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP $SSH_CMD`
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:update:$SHOP:$NUM_POS:['$RESPONSE']." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Status Atualização -> [$RESPONSE]');</script>"
        else
            echo -n "<script>alert('PDV_$NUM_POS -> Precisa estar em selecione programa.');</script>"
        fi
    ;;
    "config_rede")
        CODIGO_ATIVACAO=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP '. /usr/local/monitor-sat/monitor-sat.ini; echo $codigo'`
        sshpass -p $PASSWD scp $CONFIG_SSH -P$PORT $CONFIG_REDE $USER@$IP_SHOP:/srv/Debian6.0/opt/monitor-sat/
        RETURN=$?
        if [ $RETURN -eq 0 ]; then
            SSH_CMD="chroot /srv/Debian6.0/ /opt/monitor-sat/configRede ${CODIGO_ATIVACAO} /opt/monitor-sat/config_rede.xml"
            RESPONSE=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP $SSH_CMD`
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:config_rede:$SHOP:$NUM_POS:['$RESPONSE']." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Status Configuração de Rede -> [$RESPONSE]');</script>"
        else
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:config_rede:$SHOP:$NUM_POS:Erro ao enviar arquivo ['$XML_CONFIG']:[$RETURN]." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS -> Erro ao enviar arquivo XML_CONFIG.');</script>"
        fi 
    ;;
    "resync")
        SSH_CMD="bash /usr/local/monitor-sat/monitor-sat.sh"
        sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP $SSH_CMD
        RETURN=$?
        if [ $RETURN -eq 0 ]; then
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:config_rede:$SHOP:$NUM_POS:['$RESPONSE']." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Status Sincronização -> [OK]');</script>"
        else
            echo -n "<script>alert('PDV_$NUM_POS -> Erro ao sincronizar dados.');</script>"
        fi
    ;;
    "teste_sefaz")
        CODIGO_ATIVACAO=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP '. /usr/local/monitor-sat/monitor-sat.ini; echo $codigo'`
        sshpass -p $PASSWD scp $CONFIG_SSH -P$PORT $TESTE_SEFAZ $USER@$IP_SHOP:/srv/Debian6.0/opt/monitor-sat/ 
        RETURN=$?
        if [ $RETURN -eq 0 ]; then
            SSH_CMD="chroot /srv/Debian6.0/ /opt/monitor-sat/testeSefaz ${CODIGO_ATIVACAO} /opt/monitor-sat/teste_sefaz.xml"
            RESPONSE=`sshpass -p $PASSWD ssh $CONFIG_SSH -p $PORT -l $USER $IP_SHOP $SSH_CMD | awk -F "|" '{print $2"|"$3"|"$7"|"$8"|"$9}'`
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:teste_sefaz:$SHOP:$NUM_POS:['$RESPONSE']." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS Status Teste SEFAZ -> [$RESPONSE]');</script>"
        else
            echo "[$(date +%d/%m/%Y" - "%H:%M:%S)]:libsat:config_rede:$SHOP:$NUM_POS:Erro ao enviar arquivo ['$TESTE_SEFAZ']:[$RETURN]." >> $LOG
            echo -n "<script>alert('PDV_$NUM_POS -> Erro ao enviar arquivo TESTE_SEFAZ.');</script>"
        fi
    ;;
    *)
        echo -n "Usage: {log|update|resync|teste_sefaz} {IP SHOP} {POS} "

    ;;
esac
