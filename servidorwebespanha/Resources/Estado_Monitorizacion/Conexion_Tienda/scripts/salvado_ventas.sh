#!/bin/bash

TIENDA=$1

umount /tmp/tunnel_$TIENDA 2>/dev/null
DIR_TUNEL=`/usr/bin/tda_tunnel $TIENDA`
if [ -z "$DIR_TUNEL" ]; then
	echo "ERROR: no ha sido posible conectar con la tienda"
	exit
fi

echo "COMPROBACION DE ESTADO PREVIO A SALVADO DE VENTAS:"
echo "   - Ficheros de fin de dia..."

echo -n "   - Caja en SELECIONE PROGRAMA..."
if [ `tac $DIR_TUNEL/usr/local/n2a/var/log/n2a_application.log | grep "OPERATOR.DISPLAY ---->[[:space:]]" | head -1 | grep -c "SELECCIONE"` -eq 0 ]; then
	echo "FALLO."
	exit
fi
echo "OK"
