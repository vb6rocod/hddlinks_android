<!DOCTYPE html>
<?php
error_reporting(0);
$s="";
include ("../common.php");
include ("../util.php");
if (isset($_GET['q'])) {
  $s=$_GET['q'];
  $page_title="Cautare: ".urldecode($s);
} else
  $page_title="Cautare IMDB film/serial/actor/regizor..";
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
     msg="imdb.php?tip=movie&" + val_imdb;
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
    msg="imdb.php?tip=movie&" + val_imdb;
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
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><TD class="cat"><a href="">Cautare film/serie/regizor etc...</a></TD>
<TD class="form">';
echo '<form action="search_imdb.php" target="_blank">';
echo '<input type="text" id="q" name="q" value="" size="40">
<input type="submit" id="send" value="Cauta"></form></TD></TR></TABLE>';
if ($s) {
//echo $s;
//$s="Star trek";
//$s="Spielberg";
//$s="Fars";
//$s="tea leoni";
$s=strtolower($s);
$letter=$s[0];
$rep = str_replace(" ","_",$s);
$s=str_replace(" ","+",$s);
$l="https://sg.media-imdb.com/suggests/".$letter."/".$s.".json";
//$l="https://v2.sg.media-imdb.com/suggests/h/hello.json";
$h=file_get_contents($l);
$h=str_replace("imdb\$".$rep."(","",$h);
$h = substr($h, 0, -1);
//echo $h;
$result=json_decode($h,1);
//print_r ($result);
$r=$result['d'];
echo '<table border="1px" width="100%">'."\n";
$w=0;
for ($k=0;$k<count($r);$k++) {
  echo '<TR>';
  $tit=$r[$k]['l'];
  if (isset($r[$k]['i'][0]))
    $image= $r[$k]['i'][0];
  else
    $image="";
  $id=$r[$k]['id'];
  $rel = 'Releated:<BR>';
  if (isset($r[$k]['s'])) $rel .= $r[$k]['s']."<BR>";
  if (isset($r[$k]['y'])) $rel .= $r[$k]['y']."<BR>";
  if (isset($r[$k]['yr'])) $rel .= $r[$k]['yr']."<BR>";
  if (isset($r[$k]['q'])) $rel .= $r[$k]['q'];
  if (isset($r[$k]['y'])) $year=$r[$k]['y'];
  
  $val_imdb="title=".urlencode(fix_t($tit))."&year=".$year."&imdb=".$id;
  $val_add="title=".urlencode(fix_t($tit));
  if ($id[0] == 't') //movie/series/video
   echo '<td class="mp" align="center"><a class="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$val_add."'".')" style="cursor:pointer;" onmousedown="isKeyPressed(event)"><img id="myLink'.($w*1).'" src="'.$image.'" width="200px" height="280px"><BR>'.$tit.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"></a></TD>';
  else
   echo '<td class="mp" align="center"><a class="imdb" href="search_imdb_ref.php?p='.$id.'&title='.urlencode(fix_t($tit)).'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.$tit.'</a></TD>';
  echo '<TD valign="top" style="padding-left: 10px;">';
  echo $rel;
  echo "</TD>";
  echo "</TR>";
  $w++;
}
echo "</TABLE>";
}
?>
</BODY>
</HTML>
