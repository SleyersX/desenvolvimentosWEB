#!/bin/bash -x
# Herramienta de desatasco de ficheros de CISS
# Esta herramienta mueve los ficheros problematicos de CISS a una carpeta
# especial para que puedan ser revisados mas adelante.
#
# Errores de salida:
# 1. No se ha podido crear el directorio de soporte para CISS.
# 2. La ruta de soporte no se corresponde con un directorio.
# 3. No se pudo mover el fichero al directorio de soporte.
# 4. Error en CISS (java exception)
# 5. No hay certificado produccion. Hacer BAT 126.
# 6. WSD no estaba funcionando. Ha sido lanzado y hay que esperara (entre 2 y 3
#    minutos) para comprobar si se procesan los mensajes mensajes pendientes.
# 7. Error de conexión con CISS. No se puede enviar el fichero a CISS. Esto
#    puede deberse a un error de conexion o a que CISS este desconectado o a que
#    el CWS esté caído.
# 8. La versiib de la TPV no es valida. Esta herramienta es para SA31.
#
# Autor: Samuel Rodriguez Sevilla
# Fecha: 12 de mayo de 2014

CISS=/confdia/ws/ciss
DEST="${CISS}/support"
OUTP="${CISS}/output"
CERTS=/etc/certs
INST_CERT=${CERTS}/installcertificate.pem
PROD_CERT=${CERTS}/certificate.pem

LOG=/confdia/ficcaje/fix_ciss.log

exec 1>> "${LOG}"
exec 2>> "${LOG}"

date

grep -q "SA31" /confdia/bin/setvari || exit 8

# Comprobamos si el certificado es correcto
diff ${INST_CERT} ${PROD_CERT} > /dev/null && exit 5

# Comprobamos si esta funcionando el WSD
pidof wsd || { /etc/init.d/wsd start; exit 6; }

# si no existe el directorio de soporte para CISS lo creamos
if [ ! -e "${DEST}" ]; then
	mkdir -v "${DEST}" || exit 1
fi

if [ ! -d "${DEST}" ]; then
	echo "${DEST} no es un directorio"
	exit 2
fi

# Comprobamos error de CISS
grep "techError" /confdia/ficcaje/error.log | tail -n1 | grep 'java' && exit 4 

# Comprobamos error de comunicaciones
grep "techError" /confdia/ficcaje/error.log | tail -n1 | grep 'Unable to connect to the remote server' && exit 7 

# Capturamos el fichero que esta atascando la cola
erroneous_file=$(grep 'Executing method.*https...brcissap' /var/log/wsd.log | sed -n 's/.*\([0-9]\{18\}-.*_req.xml\)/\1/p' | tail -n1)

mv -v "${OUTP}/${erroneous_file}" "${DEST}" || exit 3

# Forzamos el reprocesamiento de la cola
/etc/init.d/wsd stop
while pidof wsd; do sleep 1; done
/etc/init.d/wsd start