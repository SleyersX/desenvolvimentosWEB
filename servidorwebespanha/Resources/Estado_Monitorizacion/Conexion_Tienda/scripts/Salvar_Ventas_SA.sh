#!/bin/bash

FECHA=$(date +"%d/%m/%Y %T")
DATE=$(date +"%Y%m%d")
HORA=$(date +"%H%M%S")
BINDIA=/confdia/bin
LOG=/tmp/aux-getsale.log
EXISTE=0
FICHEROTAR=""
unset fileGN
. /confdia/bin/setvari
SHOP=$NUMETIEN

exec &> $LOG

Salida() {
	echo "$1"
	service nfs start 2>/dev/null
	Start_App
	exit $2
}

Stop_App() {
	echo "Parando aplicação..."
	mv $BINDIA/01 $BINDIA/01.old
	killall ventas.exe cierrtef.exe backvent.exe conteos.exe etiqueta.exe oficina.exe
}

Start_App()
{
	echo -n "Iniciando a aplicação novamente..."
	pushd $BINDIA > /dev/null
	touch auto01
	[ -e 01.old ] && mv 01.old 01
	killall dispatch.exe && sleep 5 && echo "Aplicação iniciada."
}
IncrContador()
{
	CONT=0; FILE=$1
	[ ! -z "$NAME_FILE" ] && CONT=$(( ${FILE:6:2} + 1))
	[ $CONT -gt 99 ] && CONT=0
	FILE_BASE=$(printf "M%s%02d" $NUMETIEN $CONT)
	echo $FILE_BASE
}

cd $BINDIA
. ./functions

rm -f /tmp/envioGN*.tgz /tmp/M*gz


echo "INICIO PROCESO SalvarVentas."

#Stop_App

[ ! -d /confdia/logscomu ] && echo "ERROR: não existe diretorio logscomu no caixa" && exit 6

cd /confdia/logscomu

NAME_FILE=`find . -name "*_M*" | sort | tail -1`
NEW_FILE=`IncrContador "${NAME_FILE#*_*_}"`

# Paramos o serviço nfs para que a master não receba informação das escravas
echo "Paramos o serviço de rede para as escravas..." && service nfs stop >/dev/null
echo "Realizamos uma copia de segurança da Base de Dados..."
tar czf /root/BBDD_copia_$NEW_FILE.tgz /confdia/ctrdatos/maepara1.* /confdia/ficcaje/maeemis* /confdia/ficcaje/intdocu1.dat /confdia/ficcaje/hisdocu1.dat 2>/dev/null
RETtar=$?
[ $RETtar -ne 0 ] && echo "ERROR: falha ao executar TAR..." && exit 6

echo "Executamos o binario, que se encarrrgara de extrair as informações de venda da máster..."
chmod 755 /root/Comunicacion_a_fichero
/root/Comunicacion_a_fichero $SHOP
RETCom=$?
#RETCom=1
[ $RETCom -ne 0 ] && echo "ERROR 3: error ao executar ./Comunicacion_a_fichero $SHOP" && exit 3

echo "Vendas atuais obtidas. Iniciamos o serviço nfs novamente..."
service nfs start

echo "Gerando arquivos de venda..."
cd /tmp
rm -f M*pat
[ -e M*vta ] && cat M*vta | gzip > $NEW_FILE.VGZ
[ -e M*sun ] && cat M*sun | gzip > $NEW_FILE.SGZ

FICHEROTAR=$DATE"_"$HORA"_"$NEW_FILE
tar zcf $FICHEROTAR.tgz $NEW_FILE.VGZ $NEW_FILE.SGZ
cp -f $FICHEROTAR.tgz /root/envioGN_$NEW_FILE.tgz
mv $FICHEROTAR.tgz /confdia/logscomu/ -f

#Start_App

DT=$(date +"%d/%m/%Y %T")
echo "$DT: Processo de salvar vendas executado com sucesso."
echo "ERROR 0: PROCESO CORRECTO"
exit 0