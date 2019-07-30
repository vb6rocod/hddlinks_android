<!DOCTYPE html>
<?php
include ("../common.php");
$page = $_GET["page"];
$search= $_GET["link"];
$page_title=urldecode($_GET["title"]);
$width="200px";
$height=intval(200*(496/881))."px";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
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
//http://www.hdfilm.ro/index.php?p=filme&gen=Actiune&page=1
echo '<h2>'.$page_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a href="epochtimes.php?page='.($page-1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="epochtimes.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="epochtimes.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';

$n=0;
$l="http://epochtimes-romania.com/ajax_get_videolist_page.php";
$l="http://epochtimes-romania.com/ajax_get_older_tier_articles.php";
$post="page_number=".$page."&page_size=15";
$post="tier_id=0&tier_type=V&page_id=".$page;
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $p=json_decode($html,1);
  $html= $p["html"];
  //die();
  //echo $html;
$videos = explode('<a href="/video', $html);

unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $t1 = explode('href="', $video);
  $t2 = explode('"',$t1[1]);
  $link="http://epochtimes-romania.com/video".$t2[0];

  $t1=explode('&nbsp;',$video);
  $t2=explode("<",$t1[1]);
  $title=trim($t2[0]);
  //$title=fix_s($title);
//http://storage.privesc.eu/thumnails/49569.jpg
  $t1=explode('src="',$video);
  $t2=explode('"',$t1[1]);
  $image=$t2[0];
  if (strpos($image,"http") === false) $image="http:".$image;


  //$descriere = $title;
  //$link="epochtimes_link.php?file=".$link."&title=".urlencode($title);
    $link1="direct_link.php?link=".$link."&title=".urlencode($title)."&from=epoch&mod=direct";
    $l="link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title))."&from=epoch&mod=direct";
  if ($n==0) echo '<TR>';
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="25%"><a href="'.$link1.'" target="_blank"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';
    else
  echo '<td class="mp" align="center" width="25%">'.'<a onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".'<img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a></TD>';

  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a href="epochtimes.php?page='.($page-1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="epochtimes.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="epochtimes.php?page='.($page+1).'&link='.$search.'&title='.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo "</table>";
?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
