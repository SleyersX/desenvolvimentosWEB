max=system("awk 'BEGIN {max=0} {if ($2 > max) max=$2;} END {print max}' datos_web1.dat datos_web2.dat")
max_2=system("awk 'BEGIN {max=0} {if ($2 > max) max=$2;} END {print max}' datos_web1.dat.new datos_web2.dat.new")
now=system("date +%H:%M:%S")
pais=system("cat pais.dat")

set style data fsteps
set timefmt "%H:%M:%S"
set yrange [ 0 : max ]
set xdata time
set format x "%H:%M"
set grid
set key left
set boxwidth 0.5 relative
set style fill solid 1.0 border -1
unset xtics
set xtics rotate by -45
set term jpeg

set xlabel "Hora"

set xrange["08:00":now]
set output "porhora_web1.jpg"
set title "OPERACIONES WEB: Servidor 1"
plot 'datos_web1.dat' using 1:2 t "" w boxes lc rgb "forest-greena"

set output "porhora_web2.jpg"
set title "OPERACIONES WEB: Servidor 2"
plot 'datos_web2.dat' using 1:2 t "" w boxes lc rgb "blue"

set xrange["00:00":now]
set yrange [ 0 : max_2 ]

set output "porhora_web1_all.jpg"
set title "OPERACIONES WEB: Servidor 1"
plot 'datos_web1.dat.new' using 1:2 t "" w boxes lc rgb "forest-greena"

set output "porhora_web2_all.jpg"
set title "OPERACIONES WEB: Servidor 2"
plot 'datos_web2.dat.new' using 1:2 t "" w boxes lc rgb "blue"
