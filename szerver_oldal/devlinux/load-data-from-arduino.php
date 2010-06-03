<?php
$dbhost="localhost";
$username="zimmerandras";
$password="1234";
$database="wind";

$dataurl = "http://devlinux.kryonet.hu/wind/data";

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
        CURLOPT_USERAGENT      => "spider", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
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

// Connect to DB
mysql_connect($dbhost,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

// Get data set
$result = get_web_page( $dataurl );
$page = $result['content'];

// Tidy date-time and insert into DB if not already there
$datalines=explode("\n",$page);
foreach ($datalines as $currentline)
  {
    $dataentries = explode(",",$currentline);
    if ( sizeof($dataentries) == 3 )
      {
        // Format date and tidy data
        $formatteddate = "20" . substr($dataentries[0],0,2);
        $formatteddate .= "-" . substr($dataentries[0],2,2);
        $formatteddate .= "-" . substr($dataentries[0],4,2);
        $formatteddate .= " " . substr($dataentries[0],6,2);
        $formatteddate .= ":" . substr($dataentries[0],8,2);
        $formatteddate .= ":00"; 
        $dataentries[0] = $formatteddate;
        $dataentries[1] = trim($dataentries[1]);
        $dataentries[2] = trim($dataentries[2]);

        // Insert into DB
        $query = "SELECT * FROM rawdata WHERE at='" . $dataentries[0] . "';";
        $result=mysql_query($query);
        $exists=mysql_numrows($result);
        if ( $exists == 0 )
          {
            $query = "INSERT INTO rawdata (at,avgspeed,maxgust) VALUES ('" . $dataentries[0] . "'," . $dataentries[1] . "," . $dataentries[2] . ");";
            mysql_query($query);
          }
      }
  }

// Tidy up
mysql_close();
?>