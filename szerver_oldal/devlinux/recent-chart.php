<?php
header("Content-Type: image/png");

$cmd = '(cat plot-recent-chart.plt; ';
$cmd .= 'php get-recent-chart-data.php | sed -n \'1h;1!H;${;g;s/$/\ne/;p;p;}\')';
$cmd .= ' | gnuplot';

$output = shell_exec($cmd);
print $output;
?>
