<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");

$width="200px";
$height=intval(200*(228/380))."px";

$page_title="CM 2022";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>CM 2022</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  on();
  var the_data = link;
  var php_file='direct_link.php';
  request.open('POST', php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      off();
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
</script>
</head>
<body>
<script>
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
</script>
   <a href='' id='mytest1'></a>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
$n=0;
echo '<h2>'.$page_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
$l="http://sport.tvr.ro/programtv/";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('<tbody>',$h);
  $t2=explode('</table',$t1[1]);
  $h=$t2[0];
  $videos=explode('<tr>',$h);
$r=array();
unset($videos[0]);
$videos = array_values($videos);
//foreach($videos as $key=>$value) {
for ($k=0;$k<count($videos);$k +=2) {
 $t1=explode('<p><strong>',$videos[$k+1]);
 //$t2=explode('<',$t1[2]);
 //$title=trim($t2[0]);

 $t2=explode('<',$t1[1]);
 $title=trim($t2[0]);
 if (preg_match("/Campionatul/i",$title)) {
 $t2=explode('<',$t1[2]);
 $title=trim($t2[0]);
 }

 $title=str_replace("* ","",$title);
 $title=str_replace("*","",$title);
 $t1=explode('<td>',$videos[$k]);
 $t2=explode('<',$t1[1]);
 $ch=$t2[0];
 $t3=explode('<',$t1[2]);
 $day=$t3[0];
 $t4=explode('<',$t1[3]);
 $hour=$t4[0];
 $r[]=array($title,$ch,$day,$hour);
}

for ($m=0;$m<count($r);$m++) {

 $title=$r[$m][0]."<BR>".$r[$m][1]." / ".$r[$m][2]." / ".$r[$m][3];
 if (preg_match("/TVR 1/i",$title)) {
    $link1="direct_link.php?link=https://www.tvrplus.ro/live/tvr-1&title=TVR+1&from=tvrlive&mod=direct";
    $l="link=".urlencode(fix_t("https://www.tvrplus.ro/live/tvr-1"))."&title=".urlencode(fix_t("TVR 1"))."&from=tvrlive&mod=direct";
 } elseif (preg_match("/TVR info/i",$title)) {
    $link1="direct_link.php?link=https://www.tvrplus.ro/live/tvr-info&title=TVR+Info&from=tvrlive&mod=direct";
    $l="link=".urlencode(fix_t("https://www.tvrplus.ro/live/tvr-info"))."&title=".urlencode(fix_t("TVR Info"))."&from=tvrlive&mod=direct";
 } elseif (preg_match("/TVR 2/i",$title)) {
    $link1="direct_link.php?link=https://www.tvrplus.ro/live/tvr-2&title=TVR+Info&from=tvrlive&mod=direct";
    $l="link=".urlencode(fix_t("https://www.tvrplus.ro/live/tvr-2"))."&title=".urlencode(fix_t("TVR 2"))."&from=tvrlive&mod=direct";
  }
  if ($title) {
  if ($n==0) echo '<TR>';
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="25%"><a href="'.$link1.'" target="_blank">'.$title.'</a></TD>';
    else
  echo '<td class="mp" align="center" width="25%">'.'<a onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".''.$title.'</a></TD>';

  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
  }
}
echo "</table>";

?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
