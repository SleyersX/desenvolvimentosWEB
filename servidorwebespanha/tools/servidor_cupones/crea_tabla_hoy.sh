pais=`cat pais.dat`
tabla="serv_cupo_hoy_"$pais
fichero=$tabla".sql"

echo "
	delete from $tabla;
	INSERT INTO $tabla (
		select
			sc1.ID 'Tramo'
			,sc1.Oper 'SC1_Oper', sc1.Web 'SC1_Web'
			,sc2.Oper 'SC2_Oper', sc2.Web 'SC2_Web'
			,sc1.Oper+sc2.Oper 'SC1_Total', sc1.Web+sc2.Web 'SC2_Total'
		from serv_cupo1 sc1
			inner join serv_cupo2 sc2 on sc1.id = sc2.id
		where
			DATE(sc1.ID) = DATE(NOW())
			and time(sc1.ID) >= '00:10:00'
			and time(sc1.ID) <= '23:50:00'
	);" > /tmp/$fichero

cat /tmp/$fichero | mysql soporteremotoweb
[ "$pais" != "ESP" ] && cat /tmp/$fichero | mysql soporteremotoweb -h 10.208.162.6
