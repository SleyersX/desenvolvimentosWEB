[ -z "$TIENDA" ] && source /root/functions.sh

if [ $CAJA -eq 1 ]; then
        echo "Descargando fichero en TPV..."
        Download "/home/Versiones_Tienda/ClientesPY_tabs.csv.gz" "/tmp/ClientesPY_tabs.csv.gz"
        if [ -f /tmp/ClientesPY_tabs.csv.gz ]; then
                echo "Descomprimiendo fichero descargado..."
                pushd /tmp > /dev/null
                gunzip ClientesPY_tabs.csv.gz
                echo "Incorporando clientes a la base de datos..."
                mysql n2a -e "
                        drop  table if exists FISCAL_ENTITY_tmp;
                        create table FISCAL_ENTITY_tmp like FISCAL_ENTITY;
                        load data local infile '/tmp/ClientesPY_tabs.csv' into table FISCAL_ENTITY_tmp (@c1,@c2) set FISCAL_ENTITY_CODE=LPAD(@c1,9,'0'), FISCAL_NAME=@c2, POSTAL_CODE=0;
                        update FISCAL_ENTITY a join FISCAL_ENTITY_tmp b on a.FISCAL_ENTITY_CODE=b.FISCAL_ENTITY_CODE set a.FISCAL_NAME=b.FISCAL_NAME where a.FISCAL_ENTITY_CODE=b.FISCAL_ENTITY_CODE;
                        delete from FISCAL_ENTITY_tmp where FISCAL_ENTITY_CODE in (select FISCAL_ENTITY_CODE from FISCAL_ENTITY);
                        insert into FISCAL_ENTITY (FISCAL_ENTITY_CODE, FISCAL_NAME, POSTAL_CODE) (select FISCAL_ENTITY_CODE, FISCAL_NAME, POSTAL_CODE from FISCAL_ENTITY_tmp);
                        drop table if exists FISCAL_ENTITY_tmp;
                "
                N_CLIENTES_BBDD=`mysql n2a -N -e "select count(*) from FISCAL_ENTITY"`
                N_CLIENTES_FILE=`cat /tmp/ClientesPY_tabs.csv | wc -l`
                echo "PROCESO CORRECTO. Incorporados $N_CLIENTES_FILE (Total $N_CLIENTES_BBDD)"
        fi
fi
