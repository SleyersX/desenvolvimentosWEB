#!/bin/bash

source /usr/local/n2a/etc/n2a-context.properties

DIR_FILES_OUT="/usr/local/n2a/var/data/communications/out"
FECHHORA_ACTUAL=$(date "+%Y%m%d-%H%M")
FECHA=$(date "+%Y%m%d")
DIR_VENTAS="/root/ventas_GN"; mkdir -p $DIR_VENTAS 2>/dev/null
DIR_TMP="$DIR_VENTAS/$FECHHORA_ACTUAL"; mkdir -p $DIR_TMP 2>/dev/null

FILE_MARCA="$DIR_VENTAS/generado_$FECHA"

if [ -f $FILE_MARCA ]; then
	echo "SALVADO YA EFECTUADO HOY!!!"
	exit 1
fi
touch $FILE_MARCA 

FILE_BASE="M$site""00"

if [ -f /tmp/$FILE_BASE.VGZ ]; then
	echo "PENDIENTES FICHEROS POR RECOGER"
	exit 1
fi

cd $DIR_FILES_OUT
N_FILES=$(dir *.vta | wc -w)

# echo "Encontrados $N_FILES pendientes de enviar...."

if [ $N_FILES -gt 0 ] ; then
	mv *.vta $DIR_TMP -fb
	mv *.sun $DIR_TMP -fb
# 	cp *.vta $DIR_TMP -fb
# 	cp *.sun $DIR_TMP -fb
	cd $DIR_TMP
	cat *.vta | gzip > $DIR_VENTAS/$FILE_BASE.VGZ
	cat *.sun | gzip > $DIR_VENTAS/$FILE_BASE.SGZ

	# DIARIO ELECTRONICO
	pushd /usr/local/n2a/var/data/devices/electronicJournal/history >/dev/null
		DE_NEW=`find . -newer $FILE_MARCA -type f | cut -b3-`
		[ ! -z "$DE_NEW" ] && (echo "Generando fichero D.E..."; tar czf $DIR_VENTAS/$FILE_BASE.LGZ $DE_NEW)
	popd >/dev/null

	cp $DIR_VENTAS/$FILE_BASE.VGZ $DIR_VENTAS/$FILE_BASE.SGZ $DIR_VENTAS/$FILE_BASE.LGZ /tmp/ -f
# 	echo "Generados ficheros de emision $DIR_VENTAS/$FILE_BASE.VGZ y $DIR_VENTAS/$FILE_BASE.SGZ:"

	mv $DIR_VENTAS/$FILE_BASE.VGZ $DIR_VENTAS/$FILE_BASE-$FECHHORA_ACTUAL.VGZ -fb
	mv $DIR_VENTAS/$FILE_BASE.SGZ $DIR_VENTAS/$FILE_BASE-$FECHHORA_ACTUAL.SGZ -fb

	echo "SALVADO CORRECTO"

	exit 0
else
	echo "NO HAY FICHEROS DE VENTAS POR SALVAR"
	exit 1
fi
