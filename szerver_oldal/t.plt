reset
unset bars
set boxwidth -2
set style fill solid
set xrange [0:6]
plot 'c:\Users\András\Documents\Arduino\szelmero\szerver_oldal\t.dat' using 1:2:(0):3 with boxerrorbars