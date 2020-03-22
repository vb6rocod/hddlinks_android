<!DOCTYPE html>
<?php
include ("../common.php");
error_reporting(0);
$cookie=$base_cookie."facebook.dat";
if (file_exists($base_pass."facebook.txt")) {
 $h=trim(file_get_contents($base_pass."facebook.txt"));
 $t1=explode("|",$h);
 $c_user=$t1[0];
 $fb_dtsg=$t1[1];
} else {
 $c_user="";
 $fb_dtsg="";
}
$ceva="14159";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:74.0) Gecko/20100101 Firefox/74.0";
$page=$_GET['page'];
$search=$_GET["search"];
$doc_id=$_GET['doc_id'];
$token=$_GET['next'];
$token_prev=$token;
if ($page==1) {
$ref="https://www.facebook.com/pg/".$search."/videos/?ref=page_internal";
$ref="https://www.facebook.com/".$search;
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Origin: https://www.facebook.com',
'Connection: keep-alive',
);

//echo $href;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ref);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_HEADER,1);
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h = curl_exec($ch);
curl_close($ch);
$t1=explode('pageID":"',$h);
$t2=explode('"',$t1[1]);
$doc_id=$t2[0];
$t1=explode('pageName":"',$h);
$t2=explode('"',$t1[1]);
$pg=$t2[0];
}
//echo "\n".$doc_id."\n";


$p=array('av' => $c_user,
    '__user' => $c_user,
    '__a' => '1',
    '__csr' => '',
    '__beoa' => '0',
    '__pc' => 'PHASED:DEFAULT',
    'dpr' => '1',
    '__rev' => '1001841741',
    '__comet_req' => '0',
    'fb_dtsg' => $fb_dtsg,
    '__spin_r' => '1001841741',
    '__spin_b' => 'trunk',
    '__spin_t' => time(),
    'fb_api_caller_class' => 'RelayModern',
    'fb_api_req_friendly_name' => 'PagesCometChannelTabAllVideosCardImplPaginationQuery',
    'variables' => '{"count":25,"cursor":"'.$token.'","useDefaultActor":false,"id":"'.$doc_id.'"}',
    'doc_id' => '2730244443691106'
);
$post=http_build_query($p);

$l="https://www.facebook.com/api/graphql/";
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post).'',
'Origin: https://www.facebook.com',
'Connection: keep-alive',
'Cookie: c_user='.$c_user.'; xs='.urlencode("145:MQRpz_9AbGBtpw:2:1565597893:".$ceva.":7283").';'
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$h1 = curl_exec($ch);
curl_close($ch);
//echo $h1;
$r=json_decode($h1,1);
$token=$r['data']['node']['all_videos']['page_info']['end_cursor'];

$x=$r['data']['node']['all_videos']['edges'];
$page_title=$search;
$width="200px";
$height=intval(200*(128/227))."px";
$base=basename($_SERVER['SCRIPT_FILENAME']);

$next=$base."?page=".($page+1)."&prev=&next=".$token."&doc_id=".$doc_id."&search=".$search;
$prev=$base."?page=".($page-1)."&prev=&next=".$token_prev."&doc_id=".$doc_id."&search=".$search;
//https://developers.facebook.com/tools/explorer/
?>
<html>
<head>
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
  var php_file='facebook_add.php';
  request.open("POST", php_file, true);			// set the request
  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter
  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      alert (request.responseText);
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
function format_sec($seconds) {
$hours = floor($seconds / 3600);
$mins = floor(($seconds - $hours*3600) / 60);
$s = $seconds - ($hours*3600 + $mins*60);

$mins = ($mins<10?"0".$mins:"".$mins);
$s = ($s<10?"0".$s:"".$s);

return ($hours>0?$hours.":":"").$mins.":".$s;
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
$nextpage=$next;
$prevpage=$prev;
echo '<h2>'.$page_title.'</H2>';
$c="";
echo "<a href='".$c."' id='mytest1'></a>".'<div id="mainnav">';
echo '<table border="1px" width="100%">'."\n\r";
echo '<tr><TD colspan="3" align="right">';
if ($page>100)
echo '<a href="'.$prevpage.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$nextpage.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a href="'.$nextpage.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
//https://www.facebook.com/VoceaBasarabiei/videos/?page=2

$cover="";
  $add_fav="mod=add&title=".urlencode(fix_t($search))."&image=".urlencode(fix_t($cover));
//print_r ($x);
//if (preg_match_all("/\<td class\=\"\S+\"\>\<.*?href\=\"(\S+)\"\s+aria\-label\=\"(.*?)\".*?src\=\"(\S+)\"/ms",$h,$m)) {
for ($k=0;$k<count($x);$k++) {
  $title=$x[$k]['node']['savable_title']['text'];
  $image=$x[$k]['node']['VideoThumbnailImage']['uri'];
  $sec=$x[$k]['node']['playable_duration'];
  $durata=format_sec($sec);
  if ($title)
    $title=$title." (".$durata.")";
  else
    $title=$durata;
   $link1="".urlencode($x[$k]['node']['url'])."&title=".urlencode($title);
  if ($n==0) echo '<TR>';

  if ($tast == "NU") {
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="33%"><a onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';
  else
  echo '<td class="mp" align="center" width="33%"><a onclick="ajaxrequest('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';

  } else {
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="33%"><a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$add_fav.'"></a></TD>';
  else
  echo '<td class="mp" align="center" width="33%"><a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$add_fav.'"></a></TD>';
  $w++;
  }
  $n++;
  if ($n == 3) {
  echo '</tr>';
  $n=0;
  }
}

echo '<tr><TD colspan="3" align="right">';
if ($page>100)
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
