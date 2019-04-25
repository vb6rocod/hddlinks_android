<!DOCTYPE html>
<?php
include ("../common.php");
$page_title="TVonlinePro";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
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
   on();
  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  var the_data = link;
  var php_file='direct_link.php';
  request.open('POST', php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
    off();
    //alert (request.responseText);
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
function prog(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  var the_data = link;
  var php_file='prog.php';
  request.open('POST', php_file, true);			// set the request

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
     prog(val_imdb);
    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
//$(document).on('keydown', '.imdb', isValid);
</script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<style>
#overlay {
    position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5);
    z-index: 2;
    cursor: pointer;
}

#text{
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: 50px;
    color: white;
    transform: translate(-50%,-50%);
    -ms-transform: translate(-50%,-50%);
}
</style>
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
   <a href='' id='mytest1'></a>
   <div id="mainnav">
<H2></H2>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
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
echo '<table border="1px" width="100%">'."\n\r";
$html=file_get_contents("pl/tv_by_Bybanu2.txt");
$r=json_decode($html,1);
//print_r ($r);
  for ($k=0;$k<count($r["LIVETV"]);$k++) {

   $title1=$r["LIVETV"][$k]["channel_title"];
   $title=$title1." (".$r["LIVETV"][$k]["category_name"].")";
   $link=$r["LIVETV"][$k]["channel_url"];

    $val_prog="link=".urlencode(fix_t($title1));
    $link1="direct_link.php?link=".$link."&title=".urlencode($title)."&from=fara&mod=direct";
    $l="link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title))."&from=fara&mod=direct";
  if ($link <> "") {
  if ($n==0) echo '<TR>';
  if ($flash != "mp")
  echo '<td class="cat"  width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link1.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
    else
  echo '<td class="cat"  width="25%">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."', '"."')".'"'." style='cursor:pointer;'>".''.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></a></TD>';
  $w++;
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
  }
}
echo "</table>";
$n=0;
echo "<h2>Alternative</h2>";
echo '<table border="1px" width="100%">'."\n\r";
  for ($k=0;$k<count($r["LIVETV"]);$k++) {

   $title1=$r["LIVETV"][$k]["channel_title"];
   $title=$title1." (".$r["LIVETV"][$k]["category_name"].")";
   $desc=$r["LIVETV"][$k]["channel_desc"];
   $val_prog="link=".urlencode(fix_t($title1));
   $videos=explode('href="',$desc);
   unset($videos[0]);
   $videos = array_values($videos);
   foreach($videos as $video) {
    $t1=explode('"',$video);
    $link=$t1[0];
    $x=parse_url($link);
    //print_r ($x);
    if (isset($x["scheme"]))
     $tip=$x["scheme"];
    else
     $tip="";
  if ($tip) {
    $link1="direct_link.php?link=".$link."&title=".urlencode($title)."&from=fara&mod=direct";
    $l="link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title))."&from=fara&mod=direct";
  if (preg_match("/http/",$link)) {
  if ($n==0) echo '<TR>';
  if ($flash != "mp")
  echo '<td class="cat"  width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link1.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
    else
  echo '<td class="cat"  width="25%">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."', '"."')".'"'." style='cursor:pointer;'>".''.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></a></TD>';
  } else {
  if ($flash != "mp")
  echo '<td class="cat"  width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title.' ('.$tip.')<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></TD>';
    else
  echo '<td class="cat"  width="25%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title.' ('.$tip.')<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a></a></TD>';

  }
  $w++;
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
  }
  }
  }
//}
echo "</table>";
?>
<br></div>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
