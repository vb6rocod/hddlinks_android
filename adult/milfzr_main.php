<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>milfzr</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     //alert (charCode);
     if (charCode == "53"  && e.target.type != "text") {
      document.getElementById("send").click();
    }
   }
document.onkeypress =  zx;
</script>
</head>
<body><div id="mainnav">

<?php
include ("../common.php");
if (file_exists($base_cookie."adult.dat"))
  $val_search=file_get_contents($base_cookie."adult.dat");
else
  $val_search="";
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3"><font size="6"><b>milfzr</b></TD></TR>';
echo '<TR><TD class="cat" colspan="1">'.'<a href="milfzr.php?page=1,,Recente" target="_blank"><b>Recente</b></a></TD>';
echo '<TD class="form" colspan="2"><form action="milfzr.php" target="_blank">Cautare <input type="hidden" name="page1" id="page1" value="1"><input type="text" id="src" name="src" value="'.$val_search.'"><input type="submit" value="Cauta" id="send"></form></td>';
//http://www.milfzr.com/searches.html?q=&page=2
echo '</TR>';

 echo '</table>';
?>
<body><div id="mainnav">
</HTML>
