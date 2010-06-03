<?php
/*
 * Creates daily chart data using MySQL
 *
 * Daily chart: 15 minute averages and max gusts over the last 24 hours
 *
 */
$dbhost="localhost";
$username="zimmerandras";
$password="1234";
$database="wind";

// Connect to DB
mysql_connect($dbhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

$theDate = ($argc >=2) ? $argv[1] : date("Y-m-d");

$query = "SELECT DATE_FORMAT(at,'%Y%m%d%H%i') as at, avg(avgspeed) as average, max(maxgust) as gust";
$query .= " FROM rawdata";
$query .= " WHERE at>='" . $theDate . "' AND at<DATE_ADD('" . $theDate . "',INTERVAL 1 DAY)";
$query .= " GROUP BY FLOOR(UNIX_TIMESTAMP(AT)/(15*60))";

$result=mysql_query($query);
$rowcnt=mysql_numrows($result);

for ($i=0; $i<$rowcnt; $i++) {
  print mysql_result($result,$i,"at");
  print " ";
  print mysql_result($result,$i,"average");
  print " ";
  print mysql_result($result,$i,"gust");
  print "\n";
}

// Tidy up
mysql_close();
?>
