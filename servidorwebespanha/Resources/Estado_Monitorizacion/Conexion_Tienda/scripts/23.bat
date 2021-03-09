source /usr/local/n2a/etc/n2a-context.properties
eval `cat /usr/local/n2a/etc/setIPBaseResult.cfg`

echo "     PARAMETROS INSTALACION"
echo ""
echo "PAIS:                     ESPANA"
echo "MODELO TPV:           BEETLE $(detectpos)"
echo "RAID PRESENTE:                "`[ "$workstationMaster" == "true" ] && echo "SI" || echo "NO"`

[ ! -z $ip_base ] && printf "IP BASE TIENDA:%17s\n" $ip_base

echo "NUMERO DE CAJA:                $workstation"
echo "NUMERO SUPERMERCADO:       $site"
echo ""
IMPRESORA_CUPONES="NO"
for puerto in 1 2 3 4 5 6 7 8; do
	quehay=`grep :$puerto: /var/log/inventario/last/periferia | cut -f4 -d':' | cut -f1 -d' '`
	[ "$quehay" = "CKO" ] && echo "PUERTO $puerto              BALANZA $quehay" && continue
	[ "$quehay" = "EPELSA" ] && echo "PUERTO $puerto           BALANZA $quehay" && continue
	[ "$quehay" = "TM88II" ] && echo "PUERTO $puerto  IMPRESORA CUPONES TM 88" && IMPRESORA_CUPONES="SI" && continue
	[ "$quehay" = "BA66" ] && echo "PUERTO $puerto       VISOR CAJERA BA 66" && VISOR_CAJERA="DISPLAY BA66" && continue
	[ "$quehay" = "PSC330" ] && echo "PUERTO $puerto           CAPTURADOR PSC" && continue
	[ "$quehay" = "TH230" -o "$quehay" = "ND77" ] && echo "PUERTO $puerto         IMPRESORA $quehay" && continue
	[ "$quehay" = "SCANNER" ] && echo "PUERTO $puerto                 SCANNER" && continue
	[ "$quehay" = "BA63" ] && echo "PUERTO $puerto     VISOR CLIENTE BA 63" && VISOR_CLIENTE="DISPLAY BA63" && continue
	echo "PUERTO $puerto                        "
done

echo ""
echo "VISOR CAJERA:       ${VISOR_CAJERA}"
echo "VISOR CLIENTE:      ${VISOR_CLIENTE}"
echo "IMPRESORA CUPONES:            $IMPRESORA_CUPONES"
SITE_TYPE_ID=`mysql n2a -e "select SITE_TYPE_ID from SITE" | tail -1`
	[ $SITE_TYPE_ID -eq 1 ] && echo -e "IMPRESION CUPONES:      PARALELO\nTIPO TIENDA:                 DIA"
	[ $SITE_TYPE_ID -eq 2 ] && echo -e "IMPRESION CUPONES:      PARALELO\nTIPO TIENDA:          FRANQUICIA"
	[ $SITE_TYPE_ID -eq 3 ] && echo -e "IMPRESION CUPONES:      SERIE\nTIPO TIENDA:           SCHLECKER"
	[ $SITE_TYPE_ID -eq 4 ] && echo -e "IMPRESION CUPONES:      SERIE\nTIPO TIENDA:      FRANQUICIA SCH"

RES=`mysql n2a -e "select IF(UNIT_CONTROL_FLAG OR SITE_ID IS NULL,'SI','NO') from SITE_PARAMETERS_FRANCHISEE" | tail -1`; [ -z "$RES" ] && RES="SI"
echo "CONTROL UNITARIO:             $RES"
echo "NUMERO TPVS:                   "$(mysql n2a -e "select count(*) from WORKSTATION where ACTIVE_STATUS_FLAG=1" | tail -1)
echo "FECHA:                "$(grep SetupDate= /usr/local/n2a/etc/hardwareSummary.properties | cut -f1 -d' ' | cut -f2 -d'=')
echo "HORA:                      "$(grep SetupDate= /usr/local/n2a/etc/hardwareSummary.properties | cut -f2 -d' ' | tr -d '\')
echo ""
echo "TEF ACTIVO:                   "$(grep -q "tef.active=true" /usr/local/n2a/etc/n2a-config.properties && echo SI || echo NO)
eval `grep ^SERVER /usr/local/n2a/lib/i386/gcc32/tef/sitefpos.cfg`
[ $SERVER -eq 0 ] && echo "TIPO SERVIDOR TEF:       PRUEBAS" || echo "TIPO SERVIDOR TEF:          REAL"
echo ""
echo "PUERTO SEBCARD:             "$(grep "finandia.port=" /usr/local/n2a/etc/n2a-config.properties | cut -f2 -d'=')
[ ! -z "$balanza_1" ] && printf "\nBALANZA IP:%21s" $balanza_1
RES=`mysql n2a -e "select count(*) from SITE_PARAMETERS_SCALE;"`
id [ $RES -eq 0 ]; then
	echo "SERVIDOR BALANZA:          SI"
else
	echo "SERVIDOR BALANZA:          NO"
fi