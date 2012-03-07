<?php

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
  die("Windspeed device not responding");
  // exit();
}

// Set time (+1 day in date is required because the controller wouldn't handle 2012's leap year properly...)
$datetime_to_set = strtotime(date("Y-m-d H:i:s") . " +1 day");
$timestr = date("ymdHis", $datetime_to_set);
$result = get_web_page( $mainurl . "/settime?TIME=" . $timestr );
if ( $result['errno'] != 0 ) {    // Error setting time
  die("Error setting time");
  // exit();
}

?>
