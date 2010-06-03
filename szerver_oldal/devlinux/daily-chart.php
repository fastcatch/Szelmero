<?php
header("Content-Type: image/png");

$theDate = isset($_GET['datum']) ? $_GET['datum'] : "";

$cmd = '(cat plot-daily-chart.plt; ';
$cmd .= 'php get-daily-chart-data.php ' . $theDate . ' | sed -n \'1h;1!H;${;g;s/$/\ne/;p;p;}\')';
$cmd .= ' | gnuplot';

$output = shell_exec($cmd);

print $output;
?>
