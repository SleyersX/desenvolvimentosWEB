max=system("awk 'BEGIN {max=1} {if ($2 > max) max=$2;} END {print max}' datos1.dat datos2.dat")
max2=system("awk 'BEGIN {max=0} {if ($2 > max) max=$2;} END {print max}' datos1.dat.new datos2.dat.new")
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
set output "porhora1_b.jpg"
set title "SERVIDOR CUPONES 1: Conexiones cada 10 minutos"
plot 'datos1.dat' using 1:2 t "" w boxes lc rgb "forest-greena"  

set output "porhora2_b.jpg"
set title "SERVIDOR CUPONES 2: Conexiones cada 10 minutos"
plot 'datos2.dat' using 1:2 t "" w boxes lc rgb "blue"

set xrange["00:00":"23:50"]
set output "porhora1_b_all.jpg"
set title "SERVIDOR CUPONES 1: Conexiones cada 10 minutos"
plot 'datos1.dat.new' using 1:2 t "" w boxes lc rgb "forest-greena"  

set output "porhora2_b_all.jpg"
set title "SERVIDOR CUPONES 2: Conexiones cada 10 minutos"
plot 'datos2.dat.new' using 1:2 t "" w boxes lc rgb "blue"
