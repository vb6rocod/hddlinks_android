<!DOCTYPE html>
<?php
include ("../common.php");
$page_title="facebook favorite";
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
if (isset($_GET["renew"])) {
 $file=$base_fav."facebook.dat";
 $h="";
 if (file_exists($file)) {
  $h=trim(file_get_contents($file));
  $t1=explode("\r\n",$h);
  $out="";
  for ($k=0;$k<count($t1);$k++) {
    $image="";
    $title="";
    $a=explode("#separator",$t1[$k]);
    if ($a) {
      $title=trim($a[0]);
      $image=trim($a[1]);
      if (strpos(@get_headers($image)[0],"403 Forbidden") !== false) {
       $l4="https://www.facebook.com/pg/".$search."/videos/?ref=page_internal";

       $cover="";
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $l4);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
       curl_setopt($ch, CURLOPT_TIMEOUT, 15);
       $h = curl_exec($ch);
       curl_close($ch);
       $h=urldecode($h);
       //echo $h;
       $t1=explode('og:image" content="',$h);
       $t2=explode('"',$t1[1]);
       $cover=urldecode($t2[0]);
       $cover=str_replace("&amp;","&",$cover);
       if ($cover) {
        $out .= $title."#separator".$cover."\r\n";
       } else {
         $out .= $title."#separator".$image."\r\n";
       }
      } else {
         $out .= $title."#separator".$image."\r\n";
      }
    }
  }
 file_put_contents($file,$out);
 }
}
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<title><?php echo $page_title; ?></title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/ro_RO/sdk.js#xfbml=1&version=v4.0&appId=133744210505475&autoLogAppEvents=1"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
function ajaxrequest2(link) {
  var request =  new XMLHttpRequest();

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:del,title:title, link:link}; //Array
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
      location.reload();
    }
  }
}
function ajaxrequest1(link) {
  msg="link1.php?file=" + link;
  window.open(msg);
}
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
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
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}

function isValid(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode,
    self = evt.target;
    if  (charCode == "51"  && evt.target.type != "text") {
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
echo '<h2>'.$page_title.'</H2>';
$c="";
echo "<a href='".$c."' id='mytest1'></a>".'<div id="mainnav">';
echo '<table border="1px" width="100%"><TR><TD class="form">'."\n\r";
echo '<form action="facebook.php" target="_blank">Cautare (user video): ';
echo '<input type="text" id="search" name="search"><input type="hidden" id="page" name="page" value="1"><input type="submit" value="Cauta !"></form></TD>';
echo '<TD class="nav" align="right"><a href="?renew">renew</a></TD></TR></TABLE>';


$file=$base_fav."facebook.dat";
$h="";
if (file_exists($file)) {
echo '<table border="1px" width="100%">'."\n\r";
  $h=trim(file_get_contents($file));
  $t1=explode("\r\n",$h);
  for ($k=0;$k<count($t1);$k++) {
    $image="";
    $title="";
    $a=explode("#separator",$t1[$k]);
    if ($a) {
      $title=trim($a[0]);
      $image=trim($a[1]);
    }

  $add_fav="mod=del&title=".urlencode(fix_t($title))."&image=".$image;
  $playlist="facebook.php?page=1&search=".$title;

  if ($n==0) echo '<TR>';

  if ($tast == "NU")
  echo '<td class="mp" align="center" width="20%"><a href="'.$playlist.'" target="_blank"><img src="'.$image.'" width="160px" height="160px"><BR>'.$title.'</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';
  else {
  echo '<td class="mp" align="center" width="20%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$playlist.'" target="_blank"><img src="'.$image.'" width="160px" height="160px"><BR>'.$title.'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$add_fav.'"></a></TD>';
  $w++;
  }
  $n++;
  if ($n == 5) {
  echo '</tr>';
  $n=0;
  }

}
echo "</table>";
} else {
if ($tast == "DA")
echo '<table border="1px" width="100%"><TR><TD>Apasti tasta 3 pentru a adauga/sterge la favorite</td></TR></TABLE>'."\n\r";
}

?>
<div class="fb-login-button" data-width="" data-size="medium" data-button-type="continue_with" data-auto-logout-link="false" data-use-continue-as="false"></div>

</body>
</html>
