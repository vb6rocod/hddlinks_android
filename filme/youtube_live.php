<!DOCTYPE html>
<?php
include ("../common.php");
$token = $_GET["token"];
$search=$_GET["search"];
$next="";
$prev="";
$page_title="Youtube Live";
$key="";
if (file_exists($base_pass."youtube.txt"))
  $key=trim(file_get_contents($base_pass."youtube.txt"));
if ($token)
$l2="https://www.googleapis.com/youtube/v3/search?part=snippet&eventType=live&maxResults=25&pageToken=".$token."&order=relevance&q=muzica%7Cmusic%7Cpop%7Cdance%7Crock%7Crelax%7Cdisco&regionCode=ro&relevanceLanguage=ro&type=video&key=".$key;
else
$l2="https://www.googleapis.com/youtube/v3/search?part=snippet&eventType=live&maxResults=25&order=relevance&q=muzica%7Cmusic%7Cpop%7Cdance%7Crock%7Crelax%7Cdisco&regionCode=ro&relevanceLanguage=ro&type=video&key=".$key;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $p=json_decode($html,1);
  //print_r ($p);
  if (isset($p["nextPageToken"])) $next=$p["nextPageToken"];
  if (isset($p["prevPageToken"])) $prev=$p["prevPageToken"];

?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
function ajaxrequest2(link) {
  var request =  new XMLHttpRequest();
  var the_data = link;
  var php_file='youtube_add.php';
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      alert (request.responseText);
      //location.reload();
    }
  }
}
function ajaxrequest1(link) {
  msg="link1.php?file=" + link;
  window.open(msg);
}
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  on();
  var the_data = "link=" + link;
  var php_file="link1.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
        off();
       //alert (request.responseText);
       document.getElementById("mytest1").href=request.responseText;
       document.getElementById("mytest1").click();
    }
  }
}
</script>
<script type="text/javascript">
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode,
    self = evt.target;
    if  (charCode == "51") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      ajaxrequest2(val_fav);
    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
//$(document).on('keydown', '.imdb', isValid);
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
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
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
$w=0;
$nextpage="youtube_live.php?token=".$next."&search=".$search;
$prevpage="youtube_live.php?token=".$prev."&search=".$search;
echo '<h2>'.$page_title.'</H2>';
$c="";
echo "<a href='".$c."' id='mytest1'></a>".'<div id="mainnav">';
echo '<table border="1px" width="100%">'."\n\r";
echo '<tr><TD colspan="5" align="right">';
if ($prev)
echo '<a href="'.$prevpage.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$nextpage.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="'.$nextpage.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';

for ($k=0;$k<min(sizeof($p["items"]),25);$k++) {
	//$id = str_between($video,"<id>http://gdata.youtube.com/feeds/api/videos/","</id>");
  $link = "";
  $tip="";
  $kind="";
  $id="";
  	$title = $p["items"][$k]["snippet"]["title"];
  	if (isset($p["items"][$k]["snippet"]["thumbnails"]["default"]["url"]))
	  $image = $p["items"][$k]["snippet"]["thumbnails"]["default"]["url"];
    else
      $image="blank.jpg";
	$kind =$p["items"][$k]["id"]["kind"];
  if ($kind=="youtube#video") {
    if (isset($p["items"][$k]["id"]["videoId"]))
     $id=$p["items"][$k]["id"]["videoId"];
	$link = $id;
	$tip="search";
  }
  if ($kind=="youtube#playlist") {
    if (isset($p["items"][$k]["id"]["playlistId"]))
     $id=$p["items"][$k]["id"]["playlistId"];
	$link = $id;
	$title="(playlist) ".$title;
	$tip="playlist";
  }
  if ($kind=="youtube#channel") {
    if (isset($p["items"][$k]["snippet"]["channelId"]))
      $id=$p["items"][$k]["snippet"]["channelId"];
    $title="(channel) ".$title;
	$link = $id;
	$tip="channel";
  }
  $add_fav="mod=add&kind=".str_replace("youtube#","",$kind)."&id=".$id."&title=".urlencode(fix_t($title))."&image=".$image;
  $playlist="yt_playlist.php?token=&id=".$id."&kind=".str_replace("youtube#","",$kind)."&title=".urlencode(fix_t($title))."&image=".$image;
  $link1="".urlencode("http://www.youtube.com/watch?v=".$id)."&title=".urlencode($title);
  if ($id) {
  if ($n==0) echo '<TR>';
  if ($kind <> "youtube#video") {

  if ($tast == "NU")
  echo '<td class="mp" align="center" width="20%"><a href="'.$playlist.'" target="_blank"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.'</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';
  else {
  echo '<td class="mp" align="center" width="20%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$playlist.'" target="_blank"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$add_fav.'"></a></TD>';
  $w++;
  }
  } else {
  if ($tast == "NU") {
  if ($flash == "flash")
  echo '<td class="mp" align="center" width="20%"><a onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.'</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';
  else
  echo '<td class="mp" align="center" width="20%"><a onclick="ajaxrequest('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.'</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';

  } else {
  if ($flash == "flash")
  echo '<td class="mp" align="center" width="20%"><a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$add_fav.'"></a></TD>';
  else
  echo '<td class="mp" align="center" width="20%"><a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$add_fav.'"></a></TD>';
  $w++;
  }
  }
  $n++;
  if ($n == 5) {
  echo '</tr>';
  $n=0;
  }
  }
}
echo '<tr><TD colspan="5" align="right">';
if ($prev)
echo '<a href="'.$prevpage.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$nextpage.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="'.$nextpage.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo "</table>";
?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
