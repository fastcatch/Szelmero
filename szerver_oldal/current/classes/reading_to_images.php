<?php

class readingToImages {
  var $at;
  var $avg;
  var $gust;
  var $digits;
  
  function readingToImages($digits)
  {
    require("data/db_connection.php");
    // Connect to DB
    mysql_connect($dbhost,$username,$password);
    @mysql_select_db($database) or die( "Unable to select database");
    $query = "SELECT FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(AT)/(1*60))*(1*60),'%Y-%m-%d %H:%i') as at, avgspeed as average, maxgust as gust";
    $query .= " FROM rawdata";
    $query .= " ORDER BY at DESC";
    $query .= " LIMIT 1";
    $result=mysql_query($query);
    // Extract data
    $row=mysql_fetch_assoc($result);
    $this->at = $row["at"];
    $this->avg = $row["average"];
    $this->gust = $row["gust"];
    $this->digits = $digits;
  }
  
  function avg_images()
  {
    $chars = str_split($this->avg);
    for ($i=0; $i<$this->digits-sizeof($chars);  $i++) {
      print "<img src=\"images/x.png\" alt=\" \">";
    }
    for ($i=0; $i<sizeof($chars); $i++) {
      print "<img src=\"images/" . $chars[$i] . ".png\" alt=\"" . $chars[$i] . "\">";
    }
  }
  
  function gust_images()
  {
    $chars = str_split($this->gust);
    for ($i=0; $i<$this->digits-sizeof($chars);  $i++) {
      print "<img src=\"images/x.png\" alt=\" \">";
    }
    for ($i=0; $i<sizeof($chars); $i++) {
      print "<img src=\"images/" . $chars[$i] . ".png\" alt=\"" . $chars[$i] . "\">";
    }
  }
  
  function at()
  {
    return $this->at;
  }
}

?>