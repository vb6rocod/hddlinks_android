<!DOCTYPE html>
<?php
include ("../common.php");

$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
$page = $_GET["page"];
$tip= $_GET["tip"];
$tit=$_GET["title"];
$width="200px";
$height="120px";
if ($tip=="fav")
$page_title=$tit;
else
$page_title="Cautare: ".$tit;
$base=basename($_SERVER['SCRIPT_FILENAME']);
$p=$_SERVER['QUERY_STRING'];
parse_str($p, $output);

if (isset($output['page'])) unset($output['page']);
$p = http_build_query($output);
if (!isset($_GET["page"]))
  $page=1;
else
  $page=$_GET["page"];
$next=$base."?page=".($page+1)."&".$p;
$prev=$base."?page=".($page-1)."&".$p;

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
  var php_file='vimeo_add.php';
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      alert (request.responseText);
      <?php
      if ($tip=="fav")
       echo 'location.reload();';
      ?>
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
$n=0;
$w=0;
echo '<h2>'.$page_title.'</H2>';
$c="";
echo "<a href='".$c."' id='mytest1'></a>";

//https://developers.google.com/youtube/v3/getting-started
if ($tip=="fav") {
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR>';
echo '<TD class="form">'."\n\r";
echo '<form action="" target="_blank">Cautare: ';
echo '<input type="text" id="title" name="title">
<input type="hidden" id="tip" name="tip" value="search">
<input type="hidden" id="page" name="page" value="1">
<input type="submit" value="Cauta !"></form></TD></TR>';
$file=$base_fav."vimeo.dat";
$n=0;
$w=0;
$h="";
if (file_exists($file)) {

  $h .=trim(file_get_contents($file));
  //echo $h;

  $t1=explode("\r\n",$h);
  for ($k=0;$k<count($t1);$k++) {
    $id="";
    $image="";
    $title="";
    $a=explode("#separator",$t1[$k]);
    if ($a) {
      $id=trim($a[1]);
      $title=unfix_t(trim($a[0]));
      $image=trim($a[2]);
    }
  $link="https://vimeo.com/".$id;
  $link .="&title=".urlencode($title);
  $fav_link="mod=del&title=".urlencode(fix_t($title))."&link=".$id."&image=".urlencode($image);
    if ($n == 0) echo "<TR>"."\n\r";
    if ($flash == "flash") {
    echo '<TD class="mp" width="25%">'.
    '<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest1('."'".$link."')".'"'." style='cursor:pointer;'>".
    '<img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title
    .'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'">
    </a>';
    echo '<a onclick="ajaxrequest2('."'".$fav_link."'".')" style="cursor:pointer;">*</a>';
    } else
    echo '<TD class="mp" width="25%">'.
    '<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$link."')".'"'." style='cursor:pointer;'>".
    '<img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title
    .'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'">
    </a>';
    $n++;
    $w++;
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }
  }
  echo '</TABLE>';
}
} else { // search
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR>';
 if ($page>0)
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
 else
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
echo '</TR>';
$ua="Mozilla/5.0 (Windows NT 10.0; rv:89.0) Gecko/20100101 Firefox/89.0";
//https://vimeo.com/search/page:2?q=star+trek

$l="https://vimeo.com/search/page:".$page."?q=".str_replace(" ","%20",$tit);
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $t1=explode('"initial_json":',$h);
  $t2=explode('},"search":',$t1[1]);
  $r=json_decode($t2[0],1);
  //print_r ($r);
//////////////////////////////
  for ($k=0;$k<count($r['data']);$k++) {

    $link="https://vimeo.com".$r['data'][$k]['clip']['uri'];
    preg_match("/\d+/",$link,$m);
    $id=$m[0];

    $title=$r['data'][$k]['clip']['name'];
    $link .="&title=".urlencode($title);
    $durata=$r['data'][$k]['clip']['duration'];
    $durata=gmdate("H:i:s", $durata);
    $image=$r['data'][$k]['clip']['pictures']['sizes'][0]['link'];
    $fav_link="mod=add&title=".urlencode(fix_t($title." (".$durata.")"))."&link=".$id."&image=".urlencode($image);
    if ($n == 0) echo "<TR>"."\n\r";
    //if ($tast == "NU")
    if ($flash == "flash") {
    echo '<TD class="mp" width="25%">'.
    '<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest1('."'".$link."')".'"'." style='cursor:pointer;'>".
    '<img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title." (".$durata.")"
    .'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'">
    </a>';
    echo '<a onclick="ajaxrequest2('."'".$fav_link."'".')" style="cursor:pointer;">*</a>';
    } else
    echo '<TD class="mp" width="25%">'.
    '<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$link."')".'"'." style='cursor:pointer;'>".
    '<img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title." (".$durata.")"
    .'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'">
    </a>';
    $n++;
    $w++;
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }
  }
echo '<TR>';
 if ($page>0)
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
 else
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
echo '</TR>';

  echo '</TABLE>';
}
?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
