<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
include ("../util.php");
$p=$_GET["p"];
$page_title=unfix_t(urldecode($_GET['title']));
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
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

function ajaxrequest(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  //link=document.getElementById('server').innerHTML;
  var the_data = link;
  //alert(the_data);
  var php_file="search_imdb_add.php";
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
    if (charCode == "97" || charCode == "49") {
     //alert (self.id);
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    } else if  (charCode == "99" || charCode == "51") {
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
     } else if (charCode == "53" && e.target.type != "text") {
      document.getElementById("send").click();
     } else if (charCode == "50" && e.target.type != "text") {
      document.getElementById("fav").click();
    }
   }
function isKeyPressed(event) {
  if (event.ctrlKey) {
    id = "imdb_" + event.target.id;
    val_imdb=document.getElementById(id).value;
    msg="imdb.php?" + val_imdb;
    document.getElementById("fancy").href=msg;
    document.getElementById("fancy").click();
  }
}
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
</script>

</head>
<body>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
<?php
echo '<h2>'.$page_title.'</h2>';
if (file_exists($base_pass."tmdb.txt"))
  $key=file_get_contents($base_pass."tmdb.txt");
else
  $key="";
$l="https://api.themoviedb.org/3/person/".$p."/combined_credits?api_key=".$key."&language=en-US";

  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  //curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close ($ch);
//echo $h;
$result=json_decode($h,1);
  //print_r ($result);
$r=$result['cast'];
echo '<table border="1px" width="100%">'."\n";
$w=0;
$n=0;
for ($k=0;$k<count($r);$k++) {
  $media_type=$r[$k]['media_type'];
  if ($media_type == "person") {
   $tit=$r[$k]['name'];
   if ($r[$k]['profile_path'])
    $image="http://image.tmdb.org/t/p/w500".$r[$k]['profile_path'];
   else
    $image="blank.jpg";
  } else if ($media_type == "tv") {
   $tit=$r[$k]['name']." as.. ".$r[$k]['character'];
   $tit1=$r[$k]['name'];
   if ($r[$k]['poster_path'])
    $image="http://image.tmdb.org/t/p/w500".$r[$k]['poster_path'];
   else
    $image="blank.jpg";
  } else {
   $tit=$r[$k]['title']." as.. ".$r[$k]['character'];
   $tit1=$r[$k]['title'];
   if ($r[$k]['poster_path'])
    $image="http://image.tmdb.org/t/p/w500".$r[$k]['poster_path'];
   else
    $image="blank.jpg";
  }
  $id=$r[$k]['id'];
  $y="";
  $year="";
if ($media_type != "person") {
$y=$r[$k]['release_date'];
if ($y) $y=substr($y, 0, 4);  //2007-06-22
if (!$y) {
$y=$r[$k]["first_air_date"]." - ".$r[$k]["last_air_date"];
$y1 = substr($r[$k]["first_air_date"],0,4);
$y2 = substr($r[$k]["last_air_date"],0,4);
$y=$y1;
}
$year=$y;
} else
$year="";
$id=$r[$k]['id'];
  $val_imdb="title=".urlencode(fix_t($tit1))."&year=".$year."&tip=".$media_type;
  $val_add="title=".urlencode(fix_t($tit1));
  if ($n==0) echo '<TR>';
  if ($media_type != "person") //movie/series/video
   echo '<td class="mp" align="center" width="25%"><a class="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$val_add."'".')" style="cursor:pointer;" onmousedown="isKeyPressed(event)"><img id="myLink'.($w*1).'" src="'.$image.'" width="200px" height="280px"><BR>'.$tit.' ('.$year.')<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"></a></TD>';
  else
   echo '<td class="mp" align="center" width="25%"><a class="imdb" href="search_tmdb_ref.php?p='.$id.'&title='.urlencode(fix_t($tit)).'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.$tit.'</a></TD>';
  $w++;
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo "</TABLE>";
?>
</body>
</html>
