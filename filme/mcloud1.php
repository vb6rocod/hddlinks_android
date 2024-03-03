<!doctype html>
<?php
$l=urldecode($_GET['id']);  // from bflix or vidsrc.to
$host=parse_url($l)['host'];
require_once("bunny1.php");
$bunny=new bunny();
$mcloud="";
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0',
  'Accept: application/json, text/javascript, */*; q=0.01',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: '.$l,
  'X-Requested-With: XMLHttpRequest',
  'Connection: keep-alive');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $url=json_decode($h,1)['result']['url'];
  $mcloud=$bunny->decodeVrf($url);

   

/////////////////////////////////////////////////
$l=$mcloud;
$title=unfix_t(urldecode($_GET['title']));
$tip=$_GET['tip'];
$host=parse_url($l)['host'];
///////////////////////////////////////////////////////
$r=parse_url($l);
$host=$r['host'];
if (isset($r['query'])) {
 $ls="?".$r['query']."&host=".$r['host'];
} else {
 $ls="?host=".$host;
}
$host="https://".$host;
$dr=$_SERVER['DOCUMENT_ROOT'];
$a=$_SERVER['SCRIPT_NAME'];
$p=dirname($a);
$port=$_SERVER['SERVER_PORT'];
if ($port=="80") {
 $b="http://127.0.0.1".$p."/";
 $c="http://127.0.0.1/e/";
} else {
 $b="http://127.0.0.1:".$port.$p."/";
 $c="http://127.0.0.1:".$port."/e/";
}
$id="";
if (preg_match("/\/(?:f|e|embed)\/([a-z0-9]+)/i",$l,$m)) {
 $id=$m[1];
 $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115.0',
 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
 'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
 'Accept-Encoding: deflate',
 'Connection: keep-alive',
 'Referer: https://vidsrc.to/');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);

  preg_match("/base href\=\"([^\"]+)\"/",$h,$m);
  $site=$m[1];
  $l1=$site."futoken";
  curl_setopt($ch, CURLOPT_URL, $l1);
  $h1 = curl_exec($ch);
  curl_close($ch);
  $h1=str_replace("mediainfo/",$b."write.php?m=",$h1);
  $h1=str_replace("location.search",'"'.$ls.'"',$h1);
  file_put_contents("futoken",$h1);
  $h=str_replace("assetz","assetz1",$h);
  $h=str_replace('src="futoken"','src="'.$b.'futoken"',$h);
  mkdir($dr."/e/".$id);
  file_put_contents($dr."/e/".$id."/".$id.".html",$h);

}

////////////////////////////////////////////////////////
echo '
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>'.$title.'</title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script>
    $( document ).ready(function() {
       // alert( "document loaded" );
    });

    $( window ).on( "load", function() { ';
if ($tip=="flash") {
echo '

const myTimeout = setTimeout(myGreeting, 500);

function myGreeting() {
      document.getElementById("mytest1").href="link1.php?file='.urlencode($mcloud).'&title='.urlencode($title).'";
      document.getElementById("mytest1").click();
}

';
} else {
echo
'

function myGO() {
  link1="'.urlencode($mcloud).'";
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
const myTimeout = setTimeout(myGO, 500);

';
}

echo '
    });
    </script>
</head>
<body>';
echo "<a href='' id='mytest1'></a>";
echo '<iframe id="mcloud" src="'.$c.$id."/".$id.".html".'" style="display: none;"></iframe>';
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
';

?>
