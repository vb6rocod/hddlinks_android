<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$page1="";
$page1=$_GET["page1"];
if (!$page1) {
$query = $_GET["page"];
if($query) {
   $queryArr = explode(',', $query);
   $page = $queryArr[0];
   $search = $queryArr[1];
   $page_title=urldecode($queryArr[2]);
   $search=str_replace("|","&",$search);
}
} else {
 $search1=$_GET["src"];
file_put_contents($base_cookie."adult.dat",urldecode($search1));
 $search3=str_replace(" ","+",$search1);
 $page_title="Cautare: ".str_replace("+"," ",$search1);
 $search2="http://www.ah-me.com/search/".$search3."/page".$page1.".html";
 //http://www.ah-me.com/search/mom+son/
}
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="text/javascript">
// create the XMLHttpRequest object, according browser
function get_XmlHttp() {
  // create the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;
  if(window.XMLHttpRequest) {		// for Forefox, IE7+, Opera, Safari, ...
    xmlHttp = new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {	// for Internet Explorer 5 or 6
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlHttp;
}

// sends data to a php file, via POST, and displays the received answer
function ajaxrequest(title, link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  on();
  var the_data = "mod=add&title="+ title +"&link="+link;
  var php_file="ah-me_link.php";
  request.open("POST", php_file, true);			// set the request

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
<link rel="stylesheet" type="text/css" href="../custom.css" />
<style>
#overlay {
    position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5);
    z-index: 2;
    cursor: pointer;
}

#text{
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: 50px;
    color: white;
    transform: translate(-50%,-50%);
    -ms-transform: translate(-50%,-50%);
}
</style>
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
$c="";
  echo "<a href='".$c."' id='mytest1'></a>".'<div id="mainnav">';
$n=0;
echo '<H2>'.$page_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
echo '<tr><TD colspan="4" align="right">';
if (!$page1) {
if ($page > 1)
echo '<a href="ah-me.php?page='.($page-1).','.$search.','.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="ah-me.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="ah-me.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
} else {
if ($page1 > 1)
echo '<a href="ah-me.php?page1='.($page1-1).'&src='.$search1.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="ah-me.php?page1='.($page1+1).'&src='.$search1.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="ah-me.php?page1='.($page1+1).'&src='.$search1.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
if (!$page1) {
  $link=$search."page".$page.".html";
} else {
  $link=$search2;
}
//echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
$videos = explode('class="thumb-wrap', $html);

unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t1=explode('href="',$video);
    $t2 = explode('"', $t1[1]);
    $link = $t2[0];

    $t3=explode(">",$t1[2]);
    $t4=explode("<",$t3[1]);
    $title=$t4[0];

    $t1 = explode('src="', $video);
    $t2 = explode('"', $t1[1]);
    $image = $t2[0];
//http://ahmestatic2.fuckandcdn.com/ah-me/thumbs/320x240/774/2478558/5.jpg
//http://ahmestatic2.fuckandcdn.com/ah-me/thumbs/320x240/774/2478524/9.jpg
//http://ahmestatic2.fuckandcdn.com/ah-me/thumbs/320x240/166/370141/1.jpg
//http://ahmestatic2.fuckandcdn.com/ah-me/thumbs/320x240/190/441086/1.jpg
//http://ahmestatic2.fuckandcdn.com/ah-me/thumbs/320x240/166/370141/5.jpg
    //$image = str_replace("https","http",$image);
   $image="../filme/r_m.php?file=".$image;
    //http://ahmestatic2.fuckandcdn.com/ah-me/thumbs/320x240/774/2478557/7.jpg
    //http://ahmestatic2.fuckandcdn.com/ah-me/thumbs/320x240/166/370141/1.jpg
    //$image="http://ahmestatic2.fuckandcdn.com/ah-me/thumbs/320x240/774/2477588/3.jpg";
    //$image="http://ahmestatic2.fuckandcdn.com/ah-me/thumbs/320x240/774/2477588/3.jpg";
    //$image="r.php?file=".$image;
    //http://127.0.0.1/mobile/scripts/adult/r.php?file=http://ahmestatic3.fuckandcdn.com/ah-me/thumbs/320x240/562/1265052/12.jpg
    //$title = htmlspecialchars_decode($t2[0]);

    $data = " (".trim(str_between($video,'class="time">',"<")).")";
  if ($n==0) echo '<TR>';
  if ($flash != "mp") {
  $link = "ah-me_link.php?file=".$link."&title=".urlencode($title);
  echo '<td class="mp" align="center" width="25%"><a href="'.$link.'&title='.$title.'" target="_blank"><img src="'.$image.'" width="180px" height="135px"><BR>'.$title.$data.'</a></TD>';
  } else {
  echo '<td class="mp" align="center" width="25%"><a onclick="ajaxrequest('."'".urlencode($title)."', '".urlencode($link)."')".'"'." style='cursor:pointer;'>".'<img src="'.$image.'" width="180px" height="135px"><BR>'.$title.$data.'</a></TD>';
  }
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo '<tr><TD colspan="4" align="right">';
if (!$page1) {
if ($page > 1)
echo '<a href="ah-me.php?page='.($page-1).','.$search.','.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="ah-me.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="ah-me.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
} else {
if ($page1 > 1)
echo '<a href="ah-me.php?page1='.($page1-1).'&src='.$search1.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="ah-me.php?page1='.($page1+1).'&src='.$search1.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="ah-me.php?page1='.($page1+1).'&src='.$search1.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
echo "</table>";
?>
<br></div>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
