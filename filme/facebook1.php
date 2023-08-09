<!DOCTYPE html>
<?php
include ("../common.php");
//error_reporting(0);

$page=$_GET['page'];
$search=$_GET["search"];
$doc_id=$_GET['doc_id'];
$token=$_GET['next'];
$token_prev=$token;


$page_title=$search;
$width="200px";
$height=intval(200*(128/227))."px";
$base=basename($_SERVER['SCRIPT_FILENAME']);


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
  var php_file='facebook2_add.php';
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
function my_simple_crypt( $string, $secret_key,$secret_iv,$action = 'e' ) {

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }

    return $output;
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
$cookie=$base_cookie."facebook.dat";

if (file_exists($base_pass."facebook.txt") && file_exists($cookie)) {
 $h=trim(file_get_contents($base_pass."facebook.txt"));
 $t1=explode("|",trim($h));
 $key=$t1[0];
 $IV=$t1[1];
 $h=file_get_contents($cookie);
  //echo $h;
  $dec=my_simple_crypt(trim($h),$key,$IV,"d");
  $t2=explode("|",$dec);
  $c_user=$t2[0];
  $fb_dtsg=urldecode($t2[1]);
  $xs=$t2[2];
} else {
 $c_user="";
 $fb_dtsg="";
 $xs="";
}
$n=0;
$w=0;

$l="https://www.facebook.com/".$search."/videos";
//echo $l;
//die();
$ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/83.0";
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Alt-Used: www.facebook.com',
'Connection: keep-alive',
'Cookie: c_user='.$c_user.';xs='.$xs,
'Upgrade-Insecure-Requests: 1');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h1 = curl_exec($ch);
      curl_close($ch);
      //$h1=file_get_contents($l);
      $h1=str_replace('<!--','',$h1);
      $h1=str_replace('-->','',$h1);
      $h1 = html_entity_decode($h1,ENT_QUOTES);
      //echo $h1."\n"."\n";
      //preg_match_all("/base64\,([^\"]+)/",$h1,$m);
      //print_r ($m);
/*
  $h11=preg_replace_callback(
    "/base64\,([^\"]+)/",
    function ($m) {
      return base64_decode($m[1]);
    },
    $h1
  );
  */
//echo $h1;
//die();
$t1=explode('"all_videos":',$h1);
$t2=explode(',"extensions":',$t1[1]);
$h2='{"all_videos":'.$t2[0]."";
//echo $h2;
$t1=explode(',"errors',$h2);
$h2=$t1[0];
$dd=json_decode($h2,1);
//print_r ($dd);
$x =$dd['all_videos']['edges'];
//preg_match_all("/node\"\:\{\"id\"\:\"\d+\"\,\"owner\"\:/",$h2,$m);
//print_r ($m);
//echo count($x);
//print_r ($x);
//die();

echo '<h2>'.$page_title.'</H2>';
$c="";
echo "<a href='".$c."' id='mytest1'></a>".'<div id="mainnav">';
echo '<table border="1px" width="100%">'."\n\r";
$n=0;
$cover="";
  $add_fav="mod=add&title=".urlencode(fix_t($search))."&image=".urlencode(fix_t($cover));
//print_r ($x);
//if (preg_match_all("/\<td class\=\"\S+\"\>\<.*?href\=\"(\S+)\"\s+aria\-label\=\"(.*?)\".*?src\=\"(\S+)\"/ms",$h,$m)) {
for ($k=0;$k<count($x);$k++) {
  $title=$x[$k]['node']['channel_tab_thumbnail_renderer']['video']['savable_title']['text'];
  $image=$x[$k]['node']['channel_tab_thumbnail_renderer']['video']['VideoThumbnailImage']['uri'];
  $sec=$x[$k]['node']['channel_tab_thumbnail_renderer']['video']['playable_duration'];
  //$link1="http://xxx.abc?file=".urlencode($x[$k]['node']['channel_tab_thumbnail_renderer']['video']['creation_story']['attachments'][0]['target']['playable_url'])."&title=".urlencode($title);
  $durata=format_sec($sec);
  if ($title)
    $title=$title." (".$durata.")";
  else
    $title=$durata;
   $link1="".urlencode($x[$k]['node']['channel_tab_thumbnail_renderer']['video']['url'])."&title=".urlencode($title);
  if ($n==0) echo '<TR>';

  if ($tast == "NU") {
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="33%"><a onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';
  else
  echo '<td class="mp" align="center" width="33%"><a onclick="ajaxrequest('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';

  } else {
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="33%"><a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$add_fav.'">*</a></TD>';
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


echo "</table>";
////////////////////////////////////////////////////


?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
