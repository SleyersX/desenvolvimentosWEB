SERVER1="10.208.160.112"
ssh -i /home/soporte/.ssh/id_cupones -lsoporte $SERVER1 "/usr/local/bin/resumen.sh" | grep ":"
