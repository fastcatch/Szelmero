reset
set term png small xffffff x0000000 size 320,240
#outputs to stdout!

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
set obj 7 fillcolor rgb "#ff6600"
set obj 8 fillcolor rgb "#ff3300"
  
#
# Request solid fill with no border
#
set style rectangle fillstyle solid 1.0 noborder

set key off				# no legend

#
# Set up Y axis
#
set yrange [0:75] 
set y2range [0:75]
set ylabel "km/h"
set y2label "Beaufort"
set style fill empty
set y2tics border ("F1" 3, "F2" 9, "F3" 15, "F4" 24, "F5" 34, "F6" 45, "F7" 55, "F8" 68)
set offset 60,60

#
# Set up X axis
#
set xdata time
set timefmt "%Y%m%d%H%M"
set xtics axis 15*60
set mxtics default
set format x "%H:%M"
set autoscale xfix
set boxwidth 1

#
# Plot data
# Data columns: X Avg Max
#
#plot '-' using 1:3 with boxes lt rgb "#FFFFFF", '' using 1:2 with boxes lt rgb "#000000"
plot '-' using 1:3 with boxes lt rgb "#FFFFFF", '' using 1:2 with filledcurve x1 lt rgb "#000000"
