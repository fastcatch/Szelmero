<?php

require("data/db_connection.php");

//$mainurl = "http://zimmer.selfip.net";
$mainurl = "http://velencepart.ath.cx:88";
$dataurl = $mainurl . "/data";

/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
 * array containing the HTTP server response header fields and content.
 */
function get_web_page( $url )
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "other",  // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 20,       // timeout on connect
        CURLOPT_TIMEOUT        => 20,       // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $header;
}


// Check if date is valid and reset if not
$result = get_web_page( $mainurl );
$page = $result['content'];
if ( $result['errno'] != 0 ) {    // Device not available
  // die("Windspeed device not responding");
  exit();
}
$invalid_date = preg_match('/<hr>2000/',$page);
if ( $invalid_date ) {
  // Set time (+1 day in date is monkey patch because the controller wouldn't handle 2012's leap year properly...)
  $datetime_to_set = strtotime(date("Y-m-d H:i:s") . " +1 day");
  $timestr = date("ymdHis", $datetime_to_set);
  $result = get_web_page( $mainurl . "/settime?TIME=" . $timestr );
  if ( $result['errno'] != 0 ) {    // Error setting time
    exit();
  }
}

// Connect to DB
mysql_connect($dbhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

// Get data set
$result = get_web_page( $dataurl );
$page = $result['content'];

// Tidy date-time and insert into DB if not already there
$datalines=explode("<br>",$page);
foreach ($datalines as $currentline)
  {
    $dataentries = explode(",",$currentline);
    if ( sizeof($dataentries) >= 3 )
      {
        // Format date and tidy data
        $formatteddate = date("Y");
        $formatteddate .= "-" . substr($dataentries[0],0,2);
        $formatteddate .= "-" . substr($dataentries[0],2,2);
        $formatteddate .= " " . substr($dataentries[0],4,2);
        $formatteddate .= ":" . substr($dataentries[0],6,2);
        $formatteddate .= ":00";

	// Monkey patch: Adjust by one day for 2012 leap year
        $patched_date = strtotime($formatteddate . " +1 day");
        $formatteddate = date("Y-m-d H:i:s", $patched_date);

        $dataentries[0] = $formatteddate;
        $dataentries[1] = trim($dataentries[1]);
        $dataentries[2] = trim($dataentries[2]);

        // Insert into DB
        $query = "SELECT * FROM rawdata WHERE at='" . $dataentries[0] . "';";
        $result=mysql_query($query);
        $exists=mysql_numrows($result);
        if ( $exists == 0 )
          {
            $query = "INSERT INTO rawdata (at,avgspeed,maxgust) VALUES ('" . $dataentries[0] . "'," . $dataentries[1] . "," . $dataentries[2] . 
");";
            mysql_query($query);
          }
      }
  }

// Tidy up
mysql_close();
?>
