<?php
/*
 * Creates daily chart using MySQL data set
 *
 * Daily chart: 15 minute averages and max gusts over the given day
 *
 */
require("data/db_connection.php");

// Connect to DB
mysql_connect($dbhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

if (isset($_GET['datum']) && $_GET['datum'] != "") {
  $theDate = $_GET['datum'];
} else {
  $theDate = date("Y-m-d");
}
//DEBUG: $theDate = "2008-04-27";

$query = "SELECT FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(AT)/(15*60))*(15*60),'%H:%i') as at, avg(avgspeed) as average, max(maxgust) as gust";
$query .= " FROM rawdata";
$query .= " WHERE at>='" . $theDate . "' AND at<DATE_ADD('" . $theDate . "',INTERVAL 1 DAY)";
$query .= " GROUP BY FLOOR(UNIX_TIMESTAMP(AT)/(15*60))";

for ($hr=0; $hr<24; $hr++)
{
  $hr_str = str_pad($hr,2,"0",STR_PAD_LEFT);
  for ($minute=0; $minute<60; $minute+=15)
  {
    $serie_at[] = $hr_str . ":" . str_pad($minute,2,"0",STR_PAD_LEFT);;
    $serie_average[] = "";
    $serie_gust[] = "";
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
}

$result=mysql_query($query);
while( $row=mysql_fetch_assoc($result) )
{
  $index = array_search($row["at"],$serie_at);
  $serie_average[$index] = $row["average"];
  $serie_gust[$index] = $row["gust"]-$row["average"];
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
$DailyChart = new pChart(480,320);

$DailyChart->drawFilledRoundedRectangle(0,0,479,319,5,240,240,240);


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
  $DailyChart->setFontProperties("Fonts/tahoma.ttf",8);
  $DailyChart->setGraphArea(40,20,450,295);
  $DailyChart->drawGraphArea(255,255,255,TRUE);

  $DailyChart->setFixedScale(0,70,7);
  $DailyChart->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_START0,hexdec("60"),hexdec("60"),hexdec("60"),FALSE,0,0,FALSE,8);
  $DailyChart->drawTextBox(0,20,20,295,"km/h",90,hexdec("60"),hexdec("60"),hexdec("60"),ALIGN_CENTER,FALSE);

  // Draw the 0 line
  $DailyChart->setFontProperties("Fonts/tahoma.ttf",8);
  $DailyChart->drawTreshold(0,143,55,72,TRUE,TRUE);

  // Draw the Beaufort scale rectangles
  $DailyChart->drawArea($DataSet->GetData(),"B0","B1",hexdec("cc"),hexdec("cc"),hexdec("ff"),50);
  $DailyChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B1",$serie_at[sizeof($serie_at)-1],"B1",hexdec("99"),hexdec("cc"),hexdec("ff"));
  $DailyChart->drawArea($DataSet->GetData(),"B1","B2",hexdec("99"),hexdec("cc"),hexdec("ff"),50);
  $DailyChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B1",$serie_at[sizeof($serie_at)-1],"B2",hexdec("99"),hexdec("cc"),hexdec("ff"));
  $DailyChart->drawArea($DataSet->GetData(),"B2","B3",hexdec("66"),hexdec("cc"),hexdec("99"),50);
  $DailyChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B2",$serie_at[sizeof($serie_at)-1],"B3",hexdec("66"),hexdec("cc"),hexdec("99"));
  $DailyChart->drawArea($DataSet->GetData(),"B3","B4",hexdec("33"),hexdec("ff"),hexdec("33"),50);
  $DailyChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B3",$serie_at[sizeof($serie_at)-1],"B4",hexdec("33"),hexdec("ff"),hexdec("33"));
  $DailyChart->drawArea($DataSet->GetData(),"B4","B5",hexdec("cc"),hexdec("cc"),hexdec("66"),50);
  $DailyChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B4",$serie_at[sizeof($serie_at)-1],"B5",hexdec("cc"),hexdec("cc"),hexdec("66"));
  $DailyChart->drawArea($DataSet->GetData(),"B5","B6",hexdec("ff"),hexdec("cc"),hexdec("66"),50);
  $DailyChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B5",$serie_at[sizeof($serie_at)-1],"B6",hexdec("ff"),hexdec("cc"),hexdec("66"));
  $DailyChart->drawArea($DataSet->GetData(),"B6","B7",hexdec("ff"),hexdec("66"),hexdec("00"),50);
  $DailyChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B6",$serie_at[sizeof($serie_at)-1],"B7",hexdec("ff"),hexdec("66"),hexdec("00"));
  $DailyChart->drawArea($DataSet->GetData(),"B7","B8",hexdec("ff"),hexdec("33"),hexdec("00"),50);
  $DailyChart->setLabel($DataSet->GetData(),$DataSet->GetDataDescription(),"B7",$serie_at[sizeof($serie_at)-1],"B8",hexdec("ff"),hexdec("33"),hexdec("00"));
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
  $DailyChart->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_START0,hexdec("60"),hexdec("60"),hexdec("60"),TRUE,0,0,TRUE,17);
  $DailyChart->setColorPalette(1,hexdec("00"),hexdec("00"),hexdec("99"));
  $DailyChart->setColorPalette(2,hexdec("66"),hexdec("66"),hexdec("99"));
  $DailyChart->drawStackedBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),50);

  // Finish the graph
  //$DailyChart->setFontProperties("Fonts/tahoma.ttf",8);
  //$DailyChart->drawLegend(596,150,$DataSet->GetDataDescription(),255,255,255);
  //$DailyChart->setFontProperties("Fonts/tahoma.ttf",10);
  //$DailyChart->drawTitle(0,0,"Recent data",50,50,50,320,25);
} else {
  $DailyChart->setFontProperties("Fonts/tahoma.ttf",24);
  $DailyChart->drawTextBox(0,0,479,319,"A kiválasztott napra nincs adat",0,0,0,0,ALIGN_CENTER,false);
}
$DailyChart->Stroke();

?>
