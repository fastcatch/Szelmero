<?php
/*
 * Creates recent chart using MySQL data set
 *
 * Recent chart: 1 minute averages and max gusts over the last 60 minutes
 *
 */
 
require("data/db_connection.php");

// Connect to DB
mysql_connect($dbhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

$theDate = date("Y-m-d H:i");
// DEBUG: $theDate = "2008-04-27 13:10";

$query = "SELECT FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(AT)/(1*60))*(1*60),'%H:%i') as at, avg(avgspeed) as average, max(maxgust) as gust";
$query .= " FROM rawdata";
$query .= " WHERE at<='" . $theDate . "' AND at>=DATE_SUB('" . $theDate . "',INTERVAL 60 MINUTE)";
$query .= " GROUP BY FLOOR(UNIX_TIMESTAMP(AT)/(1*60))";

$result=mysql_query($query);
while( $row=mysql_fetch_assoc($result) )
{
  $serie_at[] = $row["at"];
  $serie_average[] = $row["average"];
  $serie_gust[] = $row["gust"]-$row["average"];
  $beaufort_0[] =  0;
  $beaufort_1[] =  6;
  $beaufort_2[] = 11;
  $beaufort_3[] = 19;
  $beaufort_4[] = 29;
  $beaufort_5[] = 39;
  $beaufort_6[] = 50;
  $beaufort_7[] = 62;
  $beaufort_8[] = 75;
}

// Tidy up DB
mysql_close();

/*
 * Now comes the fun part: charting
 *
 */

// Standard inclusions   
include("pChart/pData.class");
include("pChart/pChart.class");

// Dataset definition 
$DataSet = new pData;
$RecentChart = new pChart(480,320);
$RecentChart->drawFilledRoundedRectangle(0,0,479,319,5,240,240,240);

if ( mysql_num_rows($result) > 0 ) {
  $DataSet->AddPoint($serie_at,"Serie1");
  $DataSet->SetSerieName("Time","Serie1");
  $DataSet->AddPoint($serie_average,"Serie2");
  $DataSet->SetSerieName("Average","Serie2");
  $DataSet->AddPoint($serie_gust,"Serie3");
  $DataSet->SetSerieName("Gust","Serie3");

  $DataSet->AddPoint($beaufort_0,"B0");
  $DataSet->AddPoint($beaufort_1,"B1");
  $DataSet->AddPoint($beaufort_2,"B2");
  $DataSet->AddPoint($beaufort_3,"B3");
  $DataSet->AddPoint($beaufort_4,"B4");
  $DataSet->AddPoint($beaufort_5,"B5");
  $DataSet->AddPoint($beaufort_6,"B6");
  $DataSet->AddPoint($beaufort_7,"B7");
  $DataSet->AddPoint($beaufort_8,"B8");

  $DataSet->AddAllSeries();
  $DataSet->RemoveSerie("Serie1");
  $DataSet->SetAbsciseLabelSerie("Serie1");

  // Initialise the graph
  $RecentChart->setGraphArea(40,20,450,295);
  $RecentChart->drawGraphArea(255,255,255,TRUE);
  $RecentChart->setFontProperties("Fonts/tahoma.ttf",8);
  $RecentChart->setFixedScale(0,70,7);
  $RecentChart->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,hexdec("60"),hexdec("60"),hexdec("60"),FALSE,0,0,FALSE,15);
  $RecentChart->drawTextBox(0,20,20,295,"km/h",90,hexdec("60"),hexdec("60"),hexdec("60"),ALIGN_CENTER,FALSE);

  // Draw the 0 line
  $RecentChart->setFontProperties("Fonts/tahoma.ttf",8);
  $RecentChart->drawTreshold(0,143,55,72,TRUE,TRUE);

  // Draw the Beaufort scale rectangles
  $RecentChart->drawArea($DataSet->GetData(),"B0","B1",hexdec("cc"),hexdec("cc"),hexdec("ff"),50);
  $RecentChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B1",$serie_at[sizeof($serie_at)-1],"B1",hexdec("99"),hexdec("cc"),hexdec("ff"));
  $RecentChart->drawArea($DataSet->GetData(),"B1","B2",hexdec("99"),hexdec("cc"),hexdec("ff"),50);
  $RecentChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B1",$serie_at[sizeof($serie_at)-1],"B2",hexdec("99"),hexdec("cc"),hexdec("ff"));
  $RecentChart->drawArea($DataSet->GetData(),"B2","B3",hexdec("66"),hexdec("cc"),hexdec("99"),50);
  $RecentChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B2",$serie_at[sizeof($serie_at)-1],"B3",hexdec("66"),hexdec("cc"),hexdec("99"));
  $RecentChart->drawArea($DataSet->GetData(),"B3","B4",hexdec("33"),hexdec("ff"),hexdec("33"),50);
  $RecentChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B3",$serie_at[sizeof($serie_at)-1],"B4",hexdec("33"),hexdec("ff"),hexdec("33"));
  $RecentChart->drawArea($DataSet->GetData(),"B4","B5",hexdec("cc"),hexdec("cc"),hexdec("66"),50);
  $RecentChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B4",$serie_at[sizeof($serie_at)-1],"B5",hexdec("cc"),hexdec("cc"),hexdec("66"));
  $RecentChart->drawArea($DataSet->GetData(),"B5","B6",hexdec("ff"),hexdec("cc"),hexdec("66"),50);
  $RecentChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B5",$serie_at[sizeof($serie_at)-1],"B6",hexdec("ff"),hexdec("cc"),hexdec("66"));
  $RecentChart->drawArea($DataSet->GetData(),"B6","B7",hexdec("ff"),hexdec("66"),hexdec("00"),50);
  $RecentChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B6",$serie_at[sizeof($serie_at)-1],"B7",hexdec("ff"),hexdec("66"),hexdec("00"));
  $RecentChart->drawArea($DataSet->GetData(),"B7","B8",hexdec("ff"),hexdec("33"),hexdec("00"),50);
  $RecentChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B7",$serie_at[sizeof($serie_at)-1],"B8",hexdec("ff"),hexdec("33"),hexdec("00"));
  $DataSet->RemoveSerie("B0");
  $DataSet->RemoveSerie("B1");
  $DataSet->RemoveSerie("B2");
  $DataSet->RemoveSerie("B3");
  $DataSet->RemoveSerie("B4");
  $DataSet->RemoveSerie("B5");
  $DataSet->RemoveSerie("B6");
  $DataSet->RemoveSerie("B7");
  $DataSet->RemoveSerie("B8");

  // Draw the bar graph
  $RecentChart->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,hexdec("60"),hexdec("60"),hexdec("60"),TRUE,0,0,TRUE,15);
  $RecentChart->setColorPalette(1,hexdec("00"),hexdec("00"),hexdec("99"));
  $RecentChart->setColorPalette(2,hexdec("66"),hexdec("66"),hexdec("99"));
  $RecentChart->drawStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),50);

  // Finish the graph
  //$RecentChart->setFontProperties("Fonts/tahoma.ttf",8);
  //$RecentChart->drawLegend(596,150,$DataSet->GetDataDescription(),255,255,255);
  //$RecentChart->setFontProperties("Fonts/tahoma.ttf",10);
  //$RecentChart->drawTitle(0,0,"Recent data",50,50,50,320,25);
} else {
  $RecentChart->setFontProperties("Fonts/tahoma.ttf",36);
  $RecentChart->drawTextBox(0,0,479,319,"Technikai hiba",0,255,0,0,ALIGN_CENTER,false);
}
$RecentChart->Stroke();

?>
