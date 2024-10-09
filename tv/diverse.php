<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Diverse</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  var the_data = "link=" + link;
  request.open("POST","diverse.php", true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      //alert (request.responseText);
      location.reload();
    }
  }
}
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
    self = evt.target;
    //alert (charCode);
    //alert (self.id);
    if  (charCode == "51") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      //alert (val_fav);
      ajaxrequest(val_fav);

    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
</script>
</head>
<body>
<?php
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>Diverse</h2>';
echo '<table border="1px" width="100%">'."\n\r";
//echo '<TR><td style="color:black;background-color:deepskyblue;text-align:center" colspan="3"><b>Digi24 Emisiuni</b></TD></TR>';
$n=0;
$link="pl/liste.txt";
$html=file_get_contents($link);
if (isset ($_POST['link'])) {
 $l=urldecode($_POST['link']);
 $x=array();
 if (preg_match_all("/http\S+/",$html,$m)) {
 $x=$m[0];
 foreach ($x as $key=>$value) {
  if ($value==$l) unset ($x[$key]);
 }
 //print_r ($m);
}
$out = implode("\n",$x);
file_put_contents($link,$out);
}

$link="https://raw.githubusercontent.com/vb6rocod/hddlinks/master/liste.txt";
/*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  echo $html;
  */
preg_match_all("/http\S+/",$html,$m);
//print_r ($m);
$w=0;
foreach($m[0] as $video) {
    $link=$video;
    if (preg_match("/username\=/",$video)) {
    $t1=explode("username=",$video);
    $t2=explode("&",$t1[1]);
    $title=$t2[0];
    } else {
     $title=parse_url($video)['host'];
    }
    if (strlen($title) > 25)
    $title1=substr($title,0,22)."...";
    else
    $title1=$title;
    $link1="playlist.php?link=".urlencode($link)."&title=".urlencode($title);
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD class="cat">'.'<a class ="imdb" id="myLink'.$w.'" href="'.$link1.'" target="_blank">'.$title.'';
    echo '<input type="hidden" id="fav_myLink'.$w.'" value="'.urlencode($link).'"></a>'."\r\n";
    echo '</TD>';
    $n++;
    $w++;
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }

}
 if ($n<4) echo "</TR>"."\n\r";
 echo '</table>';
?>

</BODY>
</HTML>
