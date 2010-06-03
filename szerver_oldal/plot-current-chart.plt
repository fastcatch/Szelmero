reset
#set term png small xffffff x0000000
#set output '/var/www/wind/chart.png'

#
# Color-code the Beaufort ranges
#
set obj 1 rectangle back  from graph 0, first 0   to  graph 1, first 6
set obj 2 rectangle back  from graph 0, first 6   to  graph 1, first 11
set obj 3 rectangle back  from graph 0, first 11  to  graph 1, first 19
set obj 4 rectangle back  from graph 0, first 19  to  graph 1, first 29
set obj 5 rectangle back  from graph 0, first 29  to  graph 1, first 39
set obj 6 rectangle back  from graph 0, first 39  to  graph 1, first 50
set obj 7 rectangle back  from graph 0, first 50  to  graph 1, first 62
set obj 8 rectangle back  from graph 0, first 62  to  graph 1, first 75
 
set obj 1 fillcolor rgb "#ccccff"
set obj 2 fillcolor rgb "#99ccff"
set obj 3 fillcolor rgb "#66cc99"
set obj 4 fillcolor rgb "#33ff33"
set obj 5 fillcolor rgb "#cccc66"
set obj 6 fillcolor rgb "#ffcc66"
set obj 7 fillcolor rgb "#ff9966"
set obj 8 fillcolor rgb "#ff6600"
  
#
# Request solid fill with no border
#
set style rectangle fillstyle solid 1.0 noborder

set xdata time
set timefmt "%H"
#set xrange ["0":"24"]
set xtics axis "0",3*(60*60),"24"
set mxtics default
set format x "%H"
set autoscale xfix

set key off				# no legend
#show grid

set yrange [0:75] 
set y2range [0:75]
unset bars
set ylabel "km/h"
set y2label "Beaufort"
set style fill empty
set y2tics border ("F1" 3, "F2" 9, "F3" 15, "F4" 24, "F5" 34, "F6" 45, "F7" 55, "F8" 68)
set offset 60,60

# Data columns: X Avg Max
set timefmt "%H:%M"
unset boxwidth
plot 'C:\TEMP\data.txt' using 1:3 with boxes fs solid 0.75,\
     '' using 1:2 with boxes fs solid 0.25
unset multiplot
