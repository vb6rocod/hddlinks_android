<!DOCTYPE html>
<?php
include ("../common.php");
$page_title="Dailymotion favorite";
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
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
  var php_file='dailymotion_add.php';
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
function myFunction1() {
  var request =  new XMLHttpRequest();
  var the_data = "mod=add&kind=user&title=&image=&id=" + f1.search.value;
  var php_file='dailymotion_add.php';
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
function myFunction2() {
  var request =  new XMLHttpRequest();
  var the_data = "mod=add&kind=playlist&title=&image=&id=" + f2.search.value;
  var php_file='dailymotion_add.php';
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
echo '<h2>'.$page_title.'</H2>';
$c="";
echo "<a href='".$c."' id='mytest1'></a>".'<div id="mainnav">';
echo '<table border="1px" width="100%"><TR>'."\n\r";
echo '<td class="cat"><a href="javascript:window.location.reload(true)">Reload</a></TD>';
echo '<TD class="form">'."\n\r";
echo '<form action="dailymotion_search.php" target="_blank">Cautare video: ';
echo '<input type="text" id="search" name="search">
<input type="hidden" id="tip" name="tip" value="video">
<input type="hidden" id="page" name="page" value="1">
<input type="submit" value="Cauta !"></form></TD>';
echo '<TD class="form">'."\n\r";
echo '<form action="dailymotion_search.php" target="_blank">Cautare playlist cu video: ';
echo '<input type="text" id="search" name="search">
<input type="hidden" id="tip" name="tip" value="playlist">
<input type="hidden" id="page" name="page" value="1">
<input type="submit" value="Cauta !"></form></TD>';
echo '</TR><TR>';
echo '<td class="cat"><a href="javascript:window.location.reload(true)">Reload</a></TD>';
echo '<TD class="form">'."\n\r";
echo '<form id="f1" action="" onsubmit="myFunction1();return false">Adauga un user (id): ';
echo '<input type="text" id="search" name="search">
<input type="submit" value="Adauga !"></form></TD>';
echo '<TD class="form">'."\n\r";
echo '<form id="f2" action="" onsubmit="myFunction2();return false">Adauga un playlist (id): ';
echo '<input type="text" id="search" name="search">
<input type="submit" value="Adauga !"></form></TD>';
echo '</TABLE>';
$file=$base_fav."dailymotion.dat";

$h="";
if (file_exists($file)) {
echo '<table border="1px" width="100%">'."\n\r";
  $h=trim(file_get_contents($file));
  $t1=explode("\r\n",$h);
  for ($k=0;$k<count($t1);$k++) {
    $kind="";
    $id="";
    $image="";
    $title="";
    $a=explode("#separator",$t1[$k]);
    if ($a) {
      $kind=trim($a[0]);
      $id=trim($a[1]);
      $title=unfix_t(trim($a[2]));
      $image=trim($a[3]);
    }

  $add_fav="mod=del&kind=".$kind."&id=".$id."&title=".urlencode(fix_t($title))."&image=".$image;
  if ($kind=="playlist")
  $playlist="dailymotion_playlist.php?page=1&id=".$id."&tip=playlist&title=".urlencode(fix_t($title))."&image=".$image;
  if ($kind=="user")
  $playlist="dailymotion_user.php?page=1&id=".$id."&tip=playlist&title=".urlencode(fix_t($title))."&image=".$image;
  $link1="".urlencode("http://dailymotion.com/video/".$id)."&title=".urlencode($title);

  if ($n==0) echo '<TR>';
  if ($kind <> "video") {

  if ($tast == "NU")
  echo '<td class="mp" align="center" width="20%"><a href="'.$playlist.'" target="_blank"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.' ('.$kind.')</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';
  else {
  echo '<td class="mp" align="center" width="20%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$playlist.'" target="_blank"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.' ('.$kind.')<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$add_fav.'"></a></TD>';
  $w++;
  }
  } else {
  if ($tast == "NU") {
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="20%"><a onclick="ajaxrequest1('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.'</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';
  else
  echo '<td class="mp" align="center" width="20%"><a onclick="ajaxrequest('."'".$link1."'".')" style="cursor:pointer;"><img src="'.$image.'" width="160px" height="90px"><BR>'.$title.'</a> <a onclick="ajaxrequest2('."'".$add_fav."'".')" style="cursor:pointer;">*</a></TD>';

  } else {
  if ($flash != "mp")
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
echo "</table>";
} else {
if ($tast == "DA")
echo '<table border="1px" width="100%"><TR><TD>Apasti tasta 3 pentru a adauga/sterge la favorite</td></TR></TABLE>'."\n\r";
}

?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
