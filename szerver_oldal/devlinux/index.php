<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<script language="javascript" src="calendar.js"></script>
<script language="javascript">
function tegnap(){
  tc_setDay("datum",1,"/");
}
</script>
</head>
<body>
<table>
<thead>
<th align=center>Utols&oacute; 60 perc</th>
<th align=center>
  <form action="daily-chart.php" method="get" name="datumvalaszto">
  <?php
  //get class into the page
  require_once('classes/tc_calendar.php');

  //instantiate class and set properties
  $myCalendar = new tc_calendar("datum", true);
  $myCalendar->startMonday(true);
  $myCalendar->setIcon("images/iconCalendar.gif");
  
  $todays_date=getdate(date("U"));
  $myCalendar->setDate($todays_date['mday'], $todays_date['mon'], $todays_date['year']);
// $myCalendar->setDate(27, 4, 2008);
  
  //output the calendar
  $myCalendar->writeScript();	  
  ?>
  <br>
  <table align=center>
<!--
  <tr align=center>
  <td align=center><INPUT type="button" value="Tegnap" onclick="javascript:tegnap();"></td>
  <td align=center><INPUT type="button" value="Ma" onclick=""></td>
  </tr>
-->  
  <tr>
  <td colspan="2" align=center><INPUT type="button" value="Grafikon friss&iacute;t&eacute;s" onclick="window.location.reload(false);"></td>
  </tr></table>
  </form>
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
</table>

</body>
</html>