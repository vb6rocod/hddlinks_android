<!DOCTYPE html>
<?php
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

function get_value($q, $string) {
   $t1=explode($q,$string);
   return str_between($t1[1],"<string>","</string>");
}
   function generateResponse1($request)
    {
        $context  = stream_context_create(
            array(
                'http' => array(
                    'method'  => "POST",
                    'header'  => "Content-Type: text/xml",
                    'content' => $request
                )
            )
        );
        $response     = file_get_contents("http://api.opensubtitles.org/xml-rpc", false, $context);
        return $response;
    }
function generateResponse($request) {
$ua = $_SERVER['HTTP_USER_AGENT'];
$head = array(
'Content-Type: text/xml',
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://api.opensubtitles.org/xml-rpc");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $request);
$response = curl_exec($ch);
curl_close($ch);
return $response;
}
//$sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".$tit3."&link=".$link_page;
$from=$_GET["from"];
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$imdbid=$_GET["imdb"];
if (isset($_GET["ep_tit"]))
 $ep_tit=unfix_t(urldecode($_GET["ep_tit"]));
else
 $ep_tit="";
$title=unfix_t(urldecode($_GET["title"]));
if ($ep_tit)
  $page_tit=$title." ".$ep_tit;
else
  $page_tit=$title;
$link=$_GET["link"];
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
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
function changeserver(link) {
  on();
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  var the_data = "id="+ link;
  var php_file="opensubtitles_sub.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
       off();
       alert (request.responseText);
       //document.getElementById("mytest1").href=request.responseText;
      //document.getElementById("mytest1").click();
      history.back();
    }
  }
}
</script>
</head>
<body><div id="mainnav">
<H2></H2>
<?php
$year="";
if (!$imdbid) {
  if ($tip == "series") {
    if (!$year)
     $find=$title." serie";
    else
     $find=$title." serie ".$year;
  } else {
    if (!$year)
     $find=$title." movie";
    else
     $find=$title." movie ".$year;
  }
  $url = "https://www.google.com/search?q=imdb+" . rawurlencode($find);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/https:\/\/www.imdb.com\/title\/(tt\d+)/ms', $h, $match))
   $imdbid=str_replace("tt","",$match[1]);
}
//echo $sub_link ="from=".$from."&tip=".$tip."&sez=".$sez."&ep=".$ep."&imdb=".$imdbid."&title=".$title."&link=".$link;
$f=$base_cookie."opensub.dat";
$token="";
if (file_exists($f)) unlink($f);
if (file_exists($f)) {
$token=file_get_contents($f);
} else {
$request="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<methodCall>
<methodName>LogIn</methodName>
<params>
 <param>
  <value>
   <string></string>
  </value>
 </param>
 <param>
  <value>
   <string></string>
  </value>
 </param>
 <param>
  <value>
   <string>en</string>
  </value>
 </param>
 <param>
  <value>
   <string>hd4all</string>
  </value>
 </param>
</params>
</methodCall>";
$response = generateResponse($request);
//echo $response;
//$r=xmlrpc_decode($response,"UTF-8");
//print_r ($r);
if (preg_match("/200 OK/",$response)) {
$token=get_value("token",$response);
file_put_contents($f,$token);
}
}
if ($token) {
if ($tip=="movie") {
$request="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<methodCall>
<methodName>SearchSubtitles</methodName>
<params>
 <param>
  <value>
   <string>".$token."</string>
  </value>
 </param>
 <param>
  <value>
   <array>
    <data>
     <value>
      <struct>
       <member>
        <name>query</name>
        <value>
         <string>".str_replace("&","&amp;",$title)."</string>
        </value>
       </member>
       <member>
        <name>imdbid</name>
        <value>
         <string>".$imdbid."</string>
        </value>
       </member>
       <member>
        <name>sublanguageid</name>
        <value>
         <string>rum,eng</string>
        </value>
       </member>
      </struct>
     </value>
    </data>
   </array>
  </value>
 </param>
 <param>
  <value>
   <struct>
    <member>
     <name>limit</name>
     <value>
      <int>100</int>
     </value>
    </member>
   </struct>
  </value>
 </param>
</params>
</methodCall>";
//echo $request;
$arrsub = array();
$response = generateResponse($request);
//echo $response;
if (preg_match("/200 OK/",$response)) {
$videos=explode("MatchedBy",$response);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
 $MovieKind = get_value("MovieKind",$video);
 $SubFormat = get_value("SubFormat",$video);
 if ($MovieKind == "movie" && $SubFormat == "srt") {
   $SubFileName =get_value("SubFileName",$video);
   $id1 = get_value("IDSubtitleFile",$video);
   $SubLanguageID = get_value("SubLanguageID",$video);
   //if ($SubLanguageID == "rum") break;
   $id2=get_value("IDSubtitleFile",$video);
   array_push($arrsub ,array($SubLanguageID,$SubFileName, $id2));
 }
}
}
} else {
$request="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<methodCall>
<methodName>SearchSubtitles</methodName>
<params>
 <param>
  <value>
   <string>".$token."</string>
  </value>
 </param>
 <param>
  <value>
   <array>
    <data>
     <value>
      <struct>
       <member>
        <name>query</name>
        <value>
         <string>".str_replace("&","&amp;",$title)."</string>
        </value>
       </member>
       <member>
        <name>imdbid</name>
        <value>
         <string>".$imdbid."</string>
        </value>
       </member>
       <member>
        <name>season</name>
        <value>
         <int>".$sez."</int>
        </value>
       </member>
       <member>
        <name>episode</name>
        <value>
         <int>".$ep."</int>
        </value>
       </member>
       <member>
        <name>sublanguageid</name>
        <value>
         <string>rum,eng</string>
        </value>
       </member>
      </struct>
     </value>
    </data>
   </array>
  </value>
 </param>
 <param>
  <value>
   <struct>
    <member>
     <name>limit</name>
     <value>
      <int>100</int>
     </value>
    </member>
   </struct>
  </value>
 </param>
</params>
</methodCall>";
//echo $request;
$arrsub = array();
$response = generateResponse($request);
//echo $response;
if (preg_match("/200 OK/",$response)) {
$videos=explode("MatchedBy",$response);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
 //echo $video;
 $MovieKind = get_value("MovieKind",$video);
 $SubFormat = get_value("SubFormat",$video);
 //echo $MovieKind." ".$SubFormat."<BR>";
 if ($MovieKind == "episode" && $SubFormat == "srt") {
   $SubFileName =get_value("SubFileName",$video);
   $id1 = get_value("IDSubtitleFile",$video);
   $SubLanguageID = get_value("SubLanguageID",$video);
   //if ($SubLanguageID == "rum") break;
   $id2=get_value("IDSubtitleFile",$video);
   array_push($arrsub ,array($SubLanguageID,$SubFileName, $id2));
 }
}
}
}
arsort($arrsub);
//print_r ($arrsub);
$nn=count($arrsub);
$k=intval($nn/10) + 1;
$n=0;
echo '<table border="1" width="100%"><tr><td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="'.($k-0).'"><font size="6"><b>'.$page_tit.'</b></font></TD></TR><TR>';
echo '<TR>';
for ($m=1;$m<$k;$m++) {
   echo '<TD align="center"><font size="4"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></font></td>';
}
echo '<TD></TD></TR>';
foreach ($arrsub as $key => $val) {

  echo '<TR><TD colspan="'.($k-1).'"><font size="4"><a id="myLink'.($n*1).'" href="#" onclick="changeserver('."'".$arrsub[$key][2]."'".');return false;">'.$arrsub[$key][0]." - ".$arrsub[$key][1].'</a></font></TD><TD>'.($n+1).'</TD></TR>'."\r\n";
  $n++;
  //if ($n >9)
}
echo '</table>';
} else {
  echo 'Server error!';
}
echo '</body></html>';

?>
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
