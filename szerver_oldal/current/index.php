<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<META NAME="Description" CONTENT="Velencei tavi (Gárdony) szél adatok: szélsebesség, webkamera (webcam) - a szörf, kite és vitorlázás szerelmeseinek">
<title>Velencei tó - Gárdony szél adatok</title> 
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<script language="javascript" src="calendar.js"></script>
</head>
<body>

<table>
<thead>
<th align=center valign="bottom">Az elmúlt egy óra<br>1 perces átlagai és széllökései</th>
<th align=center>
  <form action="index.php" method="post" name="datumvalaszto">
  <?php
  //get class into the page
  require_once('./classes/tc_calendar.php');
  //instantiate class and set properties
  $myCalendar = new tc_calendar("datum", true, false);
  $myCalendar->autoSubmit(true,"datumvalaszto","");  
  $myCalendar->startMonday(true);
  $myCalendar->setIcon("images/iconCalendar.gif");
  $todays_date=getdate(date("U"));
  $myCalendar->dateAllow('2009-10-07', $todays_date['year'] . '-' . $todays_date['mon'] . '-' . $todays_date['mday'], false);
  $myCalendar->setDate($todays_date['mday'], $todays_date['mon'], $todays_date['year']);

  if (isset($_POST['datum']) && $_POST['datum'] != "0000-00-00") {
    $dt = preg_split("/-/",$_POST['datum']);
    $myCalendar->setDate($dt[2], $dt[1], $dt[0]);
  }

  //output the calendar
  $myCalendar->writeScript();	  
  ?>
  </form>
  <?php print "15 perces átlagok és széllökések"; ?>
</th>
</thead>

<tr>
<td><img src="recent-chart.php"></td>
<td>
  <script language="javascript">
  document.write("<img src=\"daily-chart.php?datum=");
  document.write(document.datumvalaszto.datum.value);
  document.write("\">");
  </script>
</td>
</tr>

<tr height="10"></tr>

<tr>
<td>
  <table align="center" border=1>
  <?php
    require_once('./classes/reading_to_images.php');
    $lastReading = new readingToImages(2);
  ?>
  <thead>
  <th colspan="2" align=center>Utolsó leolvasás: <i><?php print $lastReading->at(); ?></i></th>
  <th align="center"><a href="http://velencepart.ath.cx/images1sif" target="_blank">Webkamera</a></th>
  </thead>

  <tr>
  <td align=center width="150"><strong>1 perces átlag (km/h)</strong></td>
  <td align=center width="150"><strong>Széllökés (km/h)</strong></td>
  <td rowspan=2 align="center" width="150">
    <a href="http://velencepart.ath.cx/images1sif" target="_blank"><img src="http://velencepart.ath.cx/images1sif" alt="webcam" height="110"></a>
  </td>
  </tr>
  <tr align="center">
    <?php 
      print "<td>";
      $lastReading->avg_images();
      print "</td>";
      print "<td>";
      $lastReading->gust_images();
      print "</td>";
    ?>
  </tr>
  </table>
</td>

<td align=center valign=center>
Jelszó/felhasználónév a webkamerához: vendeg/vendeg
<br>
<br>
<a href="technikai.html">További technikai információk...</a>
</td>

</table>

<br>
<hr>
Az oldal a <a href="http://www.f2.hu" target="_blank">tandtsport</a>,
a <a href="http://www.velenceparthotel.hu" target="_blank">Velencepart Hotel</a>
és a <a href="http://www.kryonet.hu" target="_blank">KryoNet Magyarország Kft.</a>
közreműködésével jött létre és üzemel.
</body>
</html>
