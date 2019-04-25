<!DOCTYPE html>
<?php
include ("../common.php");
$filename = $base_pass."seenowtv.txt";
$cookie=$base_cookie."seenowtv.dat";
$f=$base_pass."seenow.txt";
/*
$l="http://www.seenow.ro/smarttv/home";
$l="http://www.seenow.ro/smarttv/placeholder/list/id/61/title/seenow-filme";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $h = curl_exec($ch);
  curl_close($ch);
$r=json_decode($h,1);
print_r ($r);
die();
*/
$query = $_GET["page"];
$pg="";
$pid="";
$tit="";
$search="";
$available="";
$see_c="";
if($query) {
   $queryArr = explode(',', $query);
   $page= $queryArr[0];
if (sizeof($queryArr) > 1 )
   $search = urldecode($queryArr[1]);
if (sizeof($queryArr) > 2 )
   $tit = urldecode($queryArr[2]);
   $tit=str_replace("\\","",$tit);
   $page_title=$tit;
}
if (!$search) {
if (!is_numeric($page)) $page=1;
$search=$_GET["search"];
$page_title=$_GET["title"];
$tit=$page_title;
}

$rest = substr($search, 0, -8);
$pg_id = substr(strrchr($rest, "-"), 1);
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
if ($t1[0]=="DA") {
  if  (($pg_id==22) || ($pg_id==5036))  {
	$wi="154";
	$hi="86";
  } else {
	$wi="171";
	$hi="96";
 }
} else {
  if  (($pg_id==22) || ($pg_id==5036))  {
	$wi="180";
	$hi="155";
  } else {
	$wi="200";
	$hi="180";
  }
}
} else {
  if  (($pg_id==22) || ($pg_id==5036))  {
	$wi="180";
	$hi="155";
  } else {
	$wi="200";
	$hi="180";
  }
}


?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <title><?php echo $tit; ?></title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
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
  var php_file='tvrplus_e_link.php';
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
</script>
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
function search_arr($array, $key, $value)
{
    $results = array();

    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }

        foreach ($array as $subarray) {
            $results = array_merge($results, search_arr($subarray, $key, $value));
        }
    }

    return $results;
}
function xml_fix($string) {
    $v=str_replace("\u015e","S",$string);
    $v=str_replace("\u015f","s",$v);
    $v=str_replace("\u0163","t",$v);
    $v=str_replace("\u0162","T",$v);
    $v=str_replace("\u0103","a",$v);
    $v=str_replace("\u0102","A",$v);
    $v=str_replace("\u00a0"," ",$v);
    $v=str_replace("\u00e2","a",$v);
    $v=str_replace("\u021b","t",$v);
    $v=str_replace("\u201e","'",$v);
    $v=str_replace("\u201d","'",$v);
    $v=str_replace("\u0219","s",$v);
    $v=str_replace("\u00ee","i",$v);
    $v=str_replace("\u00ce","I",$v);
    $v=str_replace("\u2019","'",$v);
    $v=str_replace("\/","/",$v);
    return $v;
}
$search1=str_replace("&","|",$search);
$search=str_replace("|","&",$search);
$link=$search; //.$page;
$link=str_replace(" ","+",$link);
$link=str_replace("emisiuni-tv","emisiuni-tv-78",$link);
//echo $link;
//$link="http://www.seenow.ro/filme?sort=recent&category=Filme+Romanesti";
//echo $link;
//$link="http://www.seenow.ro/filme?sort=recent&category=Filme+Hollywood";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
if ($pg_id == 9) { //$html = file_get_contents($link);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
}
//echo $html;
$t1=explode('"',$html);
if ( count($t1) < 2 ) 
	$html = base64_decode($html);

$h=str_between($html,'list: ',']}],').']}]';
if ($tit == 'Hustler live') {
	include ("../adult/hustler-live.php");
	$h = base64_decode($html);
}
$videos = json_decode($h,1);
/*
  $t1=explode('textNav floatR',$html);
  if (sizeof($t1)<2) $t1=explode('class="floatR font20 grey',$html);
if (sizeof($t1)>1){
  $t2=explode(">",$t1[1]);
  $pg='Pagina curenta: '.$t2[1];
}
*/

$grid=12;
if  (($pg_id==22) || ($pg_id==5036)) $grid=15;
$tot_items=count($videos);
$tot_pages=round($tot_items/$grid);
if ($tot_pages*$grid<$tot_items)
$tot_pages= $tot_pages + 1;
if ($page > $tot_pages) $page=$tot_pages;
$pg='Pagina curenta: '.$page.' din '.$tot_pages;

  $t1=explode('floatR textNav',$html);
  if (sizeof($t1)<2) $t1=explode('class="floatR font20 grey',$html);
if (sizeof($t1)>1){
  $t2=explode(">",$t1[1]);
  if ($t2[1]) $available=ucfirst(str_replace("-",".",strtolower($t2[1])));
}

echo '<h2>'.$tit.'</h2>';
echo '<table border="1px"  width="100%">';
/*
if ( $page <>"" ) {
echo '<tr>
<TD colspan="3"><form action="tvrplus_e.php">
'.$pg.'
- Salt la pagina:
<input type="text" name="page" id="page">
<input type="hidden" name="search" id="search" value="'.$search.'">
<input type="hidden" name="title" id="title" value="'.$page_title.'">
<input type="submit" value="GO!">
</form></td>
<TD colspan="2" align="right">';
if ($page > 1)
echo '<a href="tvrplus_e.php?page='.($page-1).','.$search1.','.urlencode($page_title).'"> << </a> | <a href="tvrplus_e.php?page='.($page+1).','.$search1.','.urlencode($page_title).'"> >> </a></TD></TR>';
else
echo '<a href="tvrplus_e.php?page='.($page+1).','.$search1.','.urlencode($page_title).'"> >> </a></TD></TR>';
}
*/
//echo $html;
$html=str_between($html,'list: ',']}],').']}]';
if ($tit == 'Hustler live') {
	include ("../adult/hustler-live.php");
	$html = base64_decode($html);
}

$html=xml_fix($html);
//echo $html;
$videos=explode('"id":',$html);
//unset($videos[0]);
//////////////////////////////////////////////////////
//$videos = explode('a class="floatL', $html);

unset($videos[0]);

$videos = array_values($videos);
$n=0;
foreach($videos as $video) {
//$min=min($tot_items,$page*$grid);
//for ($k=($page-1)*$grid;$k<$min;$k++) {
//$video=$videos[$k];
  $t1=explode('url":"',$video);
if ( sizeof($t1)>1) {
  $t2=explode(',',$video);
  //$t3=$t2[1];
  //$t4=explode(",",$t3);
  //$l=$t4[0];
  //$rest = substr($l, 0, -2);
  //$id = substr(strrchr($rest, "-"), 1);
  $id = $t2[0];
  if (strpos($video,'idpl') !== false)
	$id = str_between($video,'id="idpl','"');
//  if (strpos($video,' data-id') !== false)
//	$id = str_between($video,' data-id="','"');

  $t1=explode('thumbnail_path":"',$video);
if ( sizeof($t1)>1) {
  $t2=explode('"',$t1[1]);
  $image=$t2[0];

  $t1=explode('item_title":"',$video);
  //$t2=explode(">",$t1[1]);
  $t3=explode('"',$t1[1]);
  $title=$t3[0];
  $title=str_replace('"','',$title);

  $t1=explode('item_type":"',$video);
  //$t2=explode(">",$t1[1]);
  $t3=explode('"',$t1[1]);
  $type=$t3[0];
}
}
  $rest = substr($search1, 0, -8);
  $pg_id = substr(strrchr($rest, "-"), 1);
  if (! is_numeric($id) ) {
  if (is_numeric($pg_id) ) {
  $l="http://www.seenow.ro/smarttv/placeholder/list/id/".$pg_id."/start/0/limit/999";
  $h=file_get_contents($l);
  $p=json_decode($h,1);
  //print_r ($p);
  $items=$p['items'];
  $items = array_values($items);

// abonament 62, 5514 -->> tv-60
  if ($pg_id == 60) {
  $videos_arr = $items;
  $l="http://www.seenow.ro/smarttv/placeholder/list/id/62/start/0/limit/999";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h=curl_exec($ch);
  curl_close($ch);

$p=json_decode($h,1);
$items=$p['items'];
$items = array_values($items);
$videos_arr = array_merge($videos_arr,$items);

$l="http://www.seenow.ro/smarttv/placeholder/list/id/5514/start/0/limit/999";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"http://www.seenow.ro/");
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:14.0) Gecko/20100101 Firefox/14.0.1');
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h=curl_exec($ch);
  curl_close($ch);

$p=json_decode($h,1);
$items=$p['items'];
$items = array_values($items);
$videos_arr = array_merge($videos_arr,$items);
$items = $videos_arr;
}

  $h=search_arr($items, 'thumbnail', $image);
  if ($h) {
		$id="";
		$arr=$h[0];
		if (array_key_exists("willStartPlayingUrl",$arr)) {
			$t1=$arr['willStartPlayingUrl'];
			$t2=explode('/',$t1);
			$id=$t2[sizeof($t2)-1];
		} else 
		if (array_key_exists("streamUrl",$arr)) {
			$t1=$arr['streamUrl'];
			$t2=explode('=',$t1);
			$id=$t2[sizeof($t2)-1];
		}
	}
  }
}
$link_mp="";
  if (! is_numeric($id) || $type=='placeholder') {
    $t0 = explode('url":"',$video);
    $t1 = explode('"', $t0[1]);
    $l = "http://www.seenow.ro".$t1[0];
    $t2 =  explode('-', $t1[0]);
    // $link = substr($l, 0, -1);
	// $rest = substr($l, 0, -2);
	// $id = substr(strrchr($rest, "-"), 1);
	$id = $t2[(sizeof($t2)-1)];
	$link="tvrplus_e.php?page=1,".urlencode($l).",".urlencode($title);
  } else {
	$link="tvrplus_e_link.php?file=".urlencode($id)."&pg_id=".urlencode($pg_id)."&title=".urlencode($title)."&tit=".urlencode($tit);
	$link_mp="file=".urlencode($id)."&pg_id=".urlencode($pg_id)."&title=".urlencode($title)."&tit=".urlencode($tit);
  }
  if ($n==0) echo '<TR>';
if (strpos($link,"remove") === false) {
  if (!$link_mp)
  echo '<TD class="mp" colspan="1"><a href="'.$link.'" target="_blank"><img src="'.$image.'" width="'.$wi.'" height="'.$hi.'"><BR>'.$title.'</a></TD>';
  else {
   if ($flash !="mp")
    echo '<TD class="mp" colspan="1"><a href="'.$link.'" target="_blank"><img src="'.$image.'" width="'.$wi.'" height="'.$hi.'"><BR>'.$title.'</a></TD>';
   else
    echo '<TD class="mp" colspan="1"><a onclick="ajaxrequest('."'".$link_mp."')".'"'." style='cursor:pointer;'>".'<img src="'.$image.'" width="'.$wi.'" height="'.$hi.'"><BR>'.$title.'</a></TD>';
  }
$n++;
}
if(($pg_id==22) || ($pg_id==5036)){
if ($n==5) {
echo '</TR>';
$n=0;
}
} else {
if ($n==4) {
echo '</TR>';
$n=0;
}
}
}
echo '</TABLE>';
?>
</div>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
