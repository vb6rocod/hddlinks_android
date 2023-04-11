<!doctype html>
<?php
error_reporting(0);
$filelink=urldecode($_GET['file']);
$title=urldecode($_GET['title']);
$tip=$_GET['tip'];
//echo $filelink;
//die();
$host="https://".parse_url($filelink)['host'];
$ua = $_SERVER['HTTP_USER_AGENT'];
$head=array('User-Agent: '.$ua,
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Origin: '.$host,
'Connection: keep-alive',
'Referer: '.$host,
'Upgrade-Insecure-Requests: 1',
'Sec-Fetch-Dest: document',
'Sec-Fetch-Mode: navigate',
'Sec-Fetch-Site: none',
'Sec-Fetch-User: ?1');
//die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_URL, $filelink);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);

  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match("/window\.atob/",$h)) {
  $t1=explode("window.atob('",$h);
  $t2=explode("'",$t1[1]);
  $l=base64_decode($t2[0]);
  } else {
  $t1=explode("<iframe",$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $l=$t3[0];
  }
  //echo $l;
if (!preg_match("/captcha\_id/",$h)) {
echo '<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>'.$title.'</title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
';
echo "<a href='' id='mytest1'></a>";
if ($tip=="flash") {
echo '
<script>
const myTimeout = setTimeout(myGreeting, 500);

function myGreeting() {
      document.getElementById("mytest1").href="link1.php?file='.urlencode($l).'&title='.urlencode($title).'";
      document.getElementById("mytest1").click();
}
</script>
';
} else {
echo
'
<script>
function myGO() {
  link1="'.urlencode($l).'";
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

</script>
';
}
} else {
echo '
<script>
window.open("'.$filelink.'");
</script>';
}
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>';
?>
