<!DOCTYPE html>
<?php
include ("../common.php");
$page_title="Seriale favorite";
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
function ajaxrequest(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  var the_data = link;
  var php_file='123movies_s_add.php';
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
</script>
<script type="text/javascript">
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
        self = evt.target;
        //self = document.activeElement;
        //self = evt.currentTarget;
    //console.log(self.value);
       //alert (charCode);
    if (charCode == "97" || charCode == "49") {
     //alert (self.id);
     id = "imdb_" + self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?tip=series&" + val_imdb;
     window.open(msg);
    } else if  (charCode == "99" || charCode == "51") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      ajaxrequest(val_fav);
    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
//$(document).on('keydown', '.imdb', isValid);
</script>
</head>
<body>
<H2></H2>
<div id="mainnav">
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
$w=0;
$n=0;
$w=0;
echo '<H2>'.$page_title.'</H2>';

$file=$base_fav."123movies_s_new.dat";
$arr=array();
$h="";
if (file_exists($file)) {
  $h=file_get_contents($file);
  $t1=explode("\r\n",$h);
  for ($k=0;$k<count($t1) -1;$k++) {
    $a=explode("#separator",$t1[$k]);
    if ($a) {
      $tit=trim($a[0]);
      $l=trim($a[1]);
      $img=trim($a[2]);
      $arr[$tit]["link"]=$l;
      $arr[$tit]["image"]=$img;
    }
  }
}
if ($arr) {
//print_r ($arr);
$n=0;
$nn=count($arr);
//echo "nn=".$nn;
$k=intval($nn/10) + 1;
echo '<table border="1px" width="100%"><tr>'."\n\r";
for ($m=1;$m<$k;$m++) {
   echo '<TD align="center"><font size="4"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></font></td>';
}
echo '</TR></table>';
$p=0;
echo '<table border="1px" width="100%">'."\n\r";
foreach($arr as $key => $value) {

	$link = $arr[$key]["link"];
    $link1=$link;
    $title11 = $key;
    $image=$arr[$key]["image"];
    //$link='filmeto_s_ep.php?tip=serie&link='.$link1.'&title='.urlencode(fix_t($title11)).'&image='.urlencode($image);
    $link="123movies_s_ep.php?tip=tv&title=".urlencode(fix_t($title11))."&link=".urlencode($link1)."&image=".$image."&sez=&ep=%ep_tit=";

  if ($n==0) echo '<TR>';
  $fav_link="mod=del&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".urlencode($image);
  if ($tast == "NU") {
  echo '<td align="center" width="25%"><a id="myLink'.($p*1).'" href="'.$link.'" target="_blank"><img src="'.$image.'" width="160px" height="200px"><BR><font size="4">'.unfix_t(urldecode($title11)).'</font></a>';
  echo '<a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  } else {
  $year="";
  $val_imdb="title=".urlencode(fix_t($title11))."&year=".$year."&imdb=";
  echo '<td align="center" width="25%"><a class="imdb" id="myLink'.($p*1).'" href="'.$link.'" target="_blank"><img src="'.$image.'" width="160px" height="200px"><BR><font size="4">'.unfix_t(urldecode($title11)).'</font><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'"></a></TD>';
  $w++;
  }
  $p++;
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo '</TABLR>';
}
?>
</div>
<br></body>
</html>
