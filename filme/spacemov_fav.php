<!DOCTYPE html>
<?php
include ("../common.php");
$page_title="Filme favorite";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
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
  var php_file='spacemov_add.php';
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
var id_link="";
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
        self = evt.target;
        //self = document.activeElement;
        //self = evt.currentTarget;
    //console.log(self.value);
       //alert (charCode);
    if (charCode == "49") {
     //alert (self.id);
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?tip=movie&" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    } else if  (charCode == "51") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      ajaxrequest(val_fav);
    }
    return true;
}
   function zx(e){
     var instance = $.fancybox.getInstance();
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13"  && instance !== false) {
       $.fancybox.close();
       setTimeout(function(){ document.getElementById(id_link).focus(); }, 500);
     }
   }
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
</script>
</head>
<body>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
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

$file=$base_fav."spacemov.dat";
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
   echo '<TD align="center"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></td>';
}
echo '</TR></table>';
$p=0;
echo '<table border="1px" width="100%">'."\n\r";
foreach($arr as $key => $value) {

	$link = $arr[$key]["link"];
    $link1=$link;
    $title11 = $key;
    $title11=html_entity_decode($title11,ENT_QUOTES,'UTF-8');
    $image=$arr[$key]["image"];
    $link='spacemov_fs.php?tip=movie&link='.$link1.'&title='.urlencode(fix_t($title11)).'&image='.$image."&sez=&ep=&ep_tit=";
  if ($n==0) echo '<TR>';
  $fav_link="mod=del&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".urlencode($image);
  if ($tast == "NU") {
  echo '<td class="mp" align="center" width="25%"><a class="imdb" id="myLink'.($p*1).'" href="'.$link.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.unfix_t(urldecode($title11)).'</a>';
  echo '<a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  } else {
  $year="";
  $val_imdb="title=".urlencode(fix_t($title11))."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($p*1).'" href="'.$link.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.unfix_t(urldecode($title11)).'';
  echo '<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'"></a></TD>';
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
