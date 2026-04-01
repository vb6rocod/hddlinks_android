<!doctype html>
<?php
include ("../common.php");
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="flash";
}
$l5=urldecode($_GET['file']);
$title=urldecode($_GET['title']);
echo '
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>'.$title.'</title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<style>
#myId {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background-color: red;
  transition: 0.2s;
  position: absolute;
}
</style>
</head>
<body>
';
if (file_exists("v1.html"))
  unlink("v1.html");
if (file_exists("v1.txt"))
  unlink("v1.txt");
  $ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:133.0) Gecko/20100101 Firefox/133.0";
  $head=array('User-Agent: '.$ua,
  'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Connection: keep-alive',
  'Referer: https://vidsrc-embed.ru/',
  'Origin: https://vidsrc-embed.ru',
  'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l5);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('player_iframe" src="',$h);
  $t2=explode('"',$t1[1]);
  $l1=fixurl($t2[0]);
  $host="https://".parse_url($l1)['host'];

   $head=array('User-Agent: '.$ua,
   'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Connection: keep-alive',
   'Referer: https://vidsrc-embed.ru/',
   'Upgrade-Insecure-Requests: 1',
   'Sec-Fetch-Dest: iframe',
   'Sec-Fetch-Mode: navigate',
   'Sec-Fetch-Site: cross-site');

   $ch = curl_init();
   //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   curl_setopt($ch, CURLOPT_URL, $l1);
   //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
   $h = curl_exec($ch);
   //echo $h;
   $t1=explode("src: '",$h);
   $t2=explode("'",$t1[1]);
   $l=fixurl($t2[0],$l1);
   //echo $l."===========";
   curl_setopt($ch, CURLOPT_URL, $l);
   $h = curl_exec($ch);
   //$h=str_replace("/tprs/","https://cloudnestra.com/tprs/",$h);
   //echo $h;
   //file: '

   curl_close($ch);
   preg_match_all("/src\=\"(\/[^\"]+)/",$h,$m);
   //print_r ($m);
   $h=preg_replace_callback(
    "/src\=\"(\/[^\"]+)/",
    function ($a1) {
     return ' src="https://cloudnestra.com'.$a1[1];
    },
    $h
    );
    //https://cloudnestra.com/pjs/pjs_main_drv_cast.261225.js?_=1766737926
    $h=preg_replace("/https\:\/\/cloudnestra\.com\/pjs\/pjs\_main\_drv\_cast\.\d+\.js/","pjs_main_drv_cast.2611251.js",$h);
    //echo $h;
    $t1=explode('.get("',$h);
    $t2=explode('"',$t1[1]);
    $l_pass="https://cloudnestra.com".$t2[0];
    file_put_contents("v1.txt",$l_pass);
    //$h=str_replace('.get("/','.get("https://cloudnestra.com/',$h);
    file_put_contents("v1.html",$h);
//echo '<iframe src="v1.html" style="display:none;"></iframe>';
//echo '<div id="myId"></div>';
echo '<iframe id="myframe" src="v1.html"></iframe>';
echo "<a href='' id='mytest1'></a>";
if ($flash=="flash") {
echo '
<script>
const myTimeout = setTimeout(myGreeting, 5000);

function myGreeting() {
      document.getElementById("myframe").src="";
      document.getElementById("mytest1").href="link1.php?file='.urlencode($l5).'&title='.urlencode($title).'";
      document.getElementById("mytest1").click();
}
</script>
';
} else {
echo
'
<script>
function myGO() {
  link1="'.urlencode($l5).'";
  link="'.urlencode($title).'";
  on();
  var request =  new XMLHttpRequest();
  var the_data = "link=" + link1 + "&title=" + link;
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
      document.getElementById("myframe").src="";
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
      history.go(-1);
    }
  }
}
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
const myTimeout = setTimeout(myGO, 5000);

</script>
';
}
//echo '<div id="rr"></div>';
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
';
?>
