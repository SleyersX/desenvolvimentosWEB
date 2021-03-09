time_file=$(stat /home/MULTI/tmp/inicio | grep Modify | awk '{print $2,$3}')
echo "Inicio de proceso: ${time_file%.*}"
echo "Tiempo en ejecucion: " $(mysql -N -e "select timediff(now(),'$time_file')")
#     "ETA: " $(mysql -N -e "select timediff('$time_file',ADDTIME(now(),INTERVAL 1 HOUR))")
echo

echo ---------------------------------------------------------------------------------------------

cat /home/MULTI/tmp/Hilo_* | awk -F, '
begin { c=0; t=0; }
	{
		c+=$2;
		t+=$3;
		if (c == t) printf "<b style=\"color:blue\">";
		printf "%2d - %3d%% (%3d/%3d) - %s", $1, $2/$3*100, $2,$3,$4
		if (c == t) printf "</b>";
		printf "\n"
	}
END   {printf "\nTOTAL: %3d%% (%5d/%5d)\n",c/t*100,c,t}'

echo ---------------------------------------------------------------------------------------------
echo
echo DESCARGAS:


#ssh soporte@10.208.162.6 "ps ax | grep \"Versiones_Tienda\" | grep -v grep | cut -f2- -d'/'"
cat /home/MULTI/tmp/descargas/* 2>/dev/null
