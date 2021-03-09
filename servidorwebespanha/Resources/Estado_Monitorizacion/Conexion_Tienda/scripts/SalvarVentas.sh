#!/bin/bash

FECHA=$(date +"%d/%m/%Y %T")
DATE=$(date +"%Y%m%d")
HORA=$(date +"%H%M%S")
BINDIA=/confdia/bin
LOG=/tmp/Comunicacion_a_fichero.log
EXISTE=0
FICHEROTAR=""
unset fileGN

#exec &> $LOG

Salida() {
	echo "$1"
	service nfs start 2>/dev/null
	Start_App
	exit $2
}

# Funcion que trata de arrancar la aplicacion despues de infinalizar la ejecucion de este proceso
Stop_App() {
	echo "Parando la aplicacion..."
	mv $BINDIA/01 $BINDIA/01.old
	killall ventas.exe cierrtef.exe backvent.exe conteos.exe etiqueta.exe oficina.exe
}
Start_App()
{
	echo -n "Iniciando la aplicacion de nuevo..."
	pushd $BINDIA > /dev/null
	touch auto01
	[ -e 01.old ] && mv 01.old 01
	killall dispatch.exe && sleep 5 && echo "Aplicacion iniciada."
}
IncrContador()
{
	CONT=0; FILE=$1
	[ ! -z "$UF" ] && CONT=$(( ${FILE:6:2} + 1))
	[ $CONT -gt 99 ] && CONT=0
	FILE_BASE=$(printf "M%05d%02d" $NUMETIEN $CONT)
	echo $FILE_BASE
}

# Nos movemos al directorio de trabajo
cd $BINDIA
. ./functions

rm -f /tmp/envioGN*.tgz /tmp/M*gz


# Comenzamos con el proceso
echo "INICIO PROCESO SalvarVentas."

#Stop_App

# Control del contador de ficheros creados por cada ejecucion del proceso SalvarVentas
[ ! -d /confdia/logscomu ] && echo "ERROR: no existe directorio logscomu en la caja" && exit 6

cd /confdia/logscomu

UF=`find . -name "*_M*" | sort | tail -1`
NEW_FILE=`IncrContador "${UF#*_*_}"`

# Paramos el nfs para que la amster no reciba informacion de las esclavas
echo "Paramos servicios de red para esclavas..." && service nfs stop >/dev/null
echo "Realizamos copia de seguridad de la BBDD..."
tar czf /root/BBDD_copia_$NEW_FILE.tgz /confdia/ctrdatos/maepara1.* /confdia/ficcaje/maeemis* /confdia/ficcaje/intdocu1.dat /confdia/ficcaje/hisdocu1.dat 2>/dev/null
RETtar=$?
[ $RETtar -ne 0 ] && echo "ERROR: ha fallado el TAR..." && exit 6

echo "Obtenemos las ventas actuales..."
#./Comunicacion_a_fichero $1
#RETCom=$?
RETCom=1
[ $RETCom -ne 0 ] && echo "ERROR 3: error ejecutando ./Comunicacion_a_fichero $1" && exit 3

echo "Ventas actuales obtenidas. Reiniciamos servicios de red..."
service nfs start

echo "Generamos ficheros de ventas..."
cd /tmp
rm -f M*pat
[ -e M*vta ] && cat M*vta | gzip > $NEW_FILE.VGZ
[ -e M*sun ] && cat M*sun | gzip > $NEW_FILE.SGZ

FICHEROTAR=$DATE"_"$HORA"_"$NEW_FILE
tar zcf $FICHEROTAR.tgz $NEW_FILE.VGZ $NEW_FILE.SGZ
cp -f $FICHEROTAR.tgz /root/envioGN_$NEW_FILE.tgz
mv $FICHEROTAR.tgz /confdia/logscomu/ -f

#Start_App

FECHA=$(date +"%d/%m/%Y %T")
echo "$FECHA: FIN DE PROCESO SalvarVentas."
echo "ERROR 0: PROCESO CORRECTO"
exit 0
