#!/bin/bash

STATUS_8080=`ps -ax 2>/dev/null | grep -v grep | grep shellinaboxd | grep "-p 8080" | wc -l`
STATUS_8085=`ps -ax 2>/dev/null | grep -v grep | grep shellinaboxd | grep "-p 8085" | wc -l`

if [ $STATUS_8080 -eq 0 ]; then
	nohup shellinaboxd -d -s /:root:root:/home/soporteweb/Resources/Estado_Monitorizacion/Conexion_Tienda/scripts:'bash ./open_term.sh  ${url}' -p 8080 -t --css=/etc/shellinabox/options-available/00_White\ On\ Black.css >>/tmp/8080.log 2>>/tmp/8080.log &
fi

if [ $STATUS_8085 -eq 0 ]; then
	nohup shellinaboxd -d -s /:root:root:/home/soporteweb/Resources/Estado_Monitorizacion/Conexion_Tienda/scripts:'bash ./open_term.sh  ${url}' -p 8085 -t --css="/etc/shellinabox/options-available/01+Color Terminal.css" >>/tmp/8085.log 2>>/tmp/8085.log &
fi

