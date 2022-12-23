<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$host=$_GET['host'];
$fix=$_GET['fix'];
$page_title="Filme favorite";
$width="200px";
$height="278px";
$add_target="onionplay_f_add.php";
$fs_target="onionplay_fs.php";
$file=$base_fav."onionplay_f.dat";
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
var id_link="";
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  var the_data = link;
  var php_file='<?php echo $add_target; ?>';
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
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
    self = evt.target;
    if (charCode == "49") {
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?" + val_imdb;
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
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$w=0;
$n=0;
echo '<H2>'.$page_title.'</H2>';
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
      //$arr[$tit]["link"]=$l;
      //$arr[$tit]["image"]=$img;
      $arr[$k]=array($tit,$l,$img);
    }
  }
}
if ($arr) {
$n=0;
$w=0;
$nn=count($arr);
$k=intval($nn/10) + 1;
echo '<table border="1px" width="100%"><tr>'."\n\r";
for ($m=1;$m<$k;$m++) {
   echo '<TD align="center"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></td>';
}
echo '</TR></table>';
echo '<table border="1px" width="100%">'."\n\r";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:95.0) Gecko/20100101 Firefox/95.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Cookie: _ga=GA1.1.238192412.1599210100; _js_datr=UiDGXydiuVrQoP7WfPCjEPFp; __atuvc=0%7C41%2C0%7C42%2C0%7C43%2C0%7C44%2C1%7C45; _ym_uid=1582124103637524699; _ym_d=1635325499; _ga_NHDEZ3LMR5=GS1.1.1608507498.1.0.1608507498.60; _hjid=5dad3d29-5449-41ee-b759-461662a02d3c; cX_P=kixru44z4zqi4e91; sc_is_visitor_unique=rx11849134.1609874042.F52CF4D32A424F9BB6E9BC378CA70AF9.2.2.2.2.2.2.2.2.2-12096247.1609499921.3.3.3.3.2.1.1.1.1; __gads=ID=9404faa27f9171f8-22d8b4b15dba004c:T=1612557705:S=ALNI_MbHte3wNPzuvHG76vr9bLfqn21_3A; _ga_4Y92J21ZFR=GS1.1.1630144471.3.1.1630144497.0; _ga_9ZBLTKLKK0=GS1.1.1615106758.1.1.1615108141.0; _ga_5S4R5BDE8S=GS1.1.1633897355.2.0.1633897365.0; _ga_0WGNHNHYZS=GS1.1.1615711605.1.0.1615711609.0; _ga_PVLYD1EH1L=GS1.1.1616408660.1.0.1616408668.0; HstCfa4529398=1618562419370; HstCla4529398=1619980849748; HstCmu4529398=1618562419370; HstPn4529398=1; HstPt4529398=2; HstCnv4529398=2; HstCns4529398=2; ai_user=5CFRa|2021-04-25T08:45:14.198Z; _clck=1w9rrjr; cto_bundle=lnKgWF9EazRTJTJGQ0ZIbldHMVdRZWxiSTZLcE5CZnNlQ1I1V2hpWTl4UTlYcnc2JTJGWTdvSVYyRFF3bGRTOUlwZnFpcWs5QUdhUmlVNEQ4VDNOSzNiaGtQNTg2QWhma1NNNWVnc0MycENIeHFqV3F4UVhoM0VzTjNyVk55eUV4Tk5CTTQ5bDhyZXRoYUV3MFZLV0p1RHpqZUhTYXZTSlFLdGZtSTNnZUFvcWpEZUZTRXRjJTNE; HstCfa4533164=1621364947339; HstCla4533164=1621364947339; HstCmu4533164=1621364947339; HstPn4533164=1; HstPt4533164=1; HstCnv4533164=1; HstCns4533164=1; _ga_0CLRKRJFKB=GS1.1.1621364947.1.0.1621364949.0; __utma=111872281.238192412.1599210100.1627208922.1627208922.1; __utmz=111872281.1627208922.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); csm-hit=tb:s-XAFQYFK0V4EPBB1DJH2N|1630147890762&t:1630147899688&adb:adblk_no; HstCfa4518200=1630264093671; HstCla4518200=1630580782689; HstCmu4518200=1630264093671; HstPn4518200=1; HstPt4518200=11; HstCnv4518200=6; HstCns4518200=6; _ga_TNN38RF6S1=GS1.1.1636455736.1.1.1636455754.0; _ga_JJ8C3FEJHM=GS1.1.1637323126.1.1.1637323343.0; dom3ic8zudi28v8lr6fgphwffqoz0j6c=17b722ad-50b5-4b00-88fe-10bc19ad3b5c%3A1%3A1',
'Upgrade-Insecure-Requests: 1',
'Sec-Fetch-Dest: document',
'Sec-Fetch-Mode: navigate',
'Sec-Fetch-Site: none',
'Sec-Fetch-User: ?1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
foreach($arr as $key => $value) {
    $imdb="";
	$link = urldecode($arr[$key][1]);
    $title = unfix_t(urldecode($arr[$key][0]));
    $image=urldecode($arr[$key][2]);
    $orig=urldecode($arr[$key][2]);
    $link=$host.parse_url($link)['path'];
    //$image=$host.parse_url($image)['path'];
    // content/uploads/2020/09/eD1YeAGlYvdxab3PZV66wAyd2Dx-185x278.jpg
    // zeroneplus.stream/2021/02/gKnhEsjNefpKnUdAkn7INzIFLSu-185x278.jpg
    $image=str_replace("content/uploads/","",$image);
    $image=str_replace(parse_url($host)['host'],"zeroneplus.stream",$image);
    $image=str_replace("onionplay.co","zeroneplus.stream",$image);
    $image=str_replace("onionplay.is","zeroneplus.stream",$image);
    // onionplay-network.stream
 //////////////////////////////////////////////////////////////////////
 //echo $orig."\n";
 if (!preg_match("/(onionplay\-network\.stream)|(tmdb\.)/",$orig) && $fix=="yes") {
  $link=str_replace("/watch-","/",$link);
  $link=str_replace("-onionplay","",$link);
  //echo $link;
  curl_setopt($ch, CURLOPT_URL, $link);
  $html = curl_exec($ch);
  //echo $html;
  $out="";
  $t1=explode("= [",$html);
  $t2=explode("]",$t1[1]);
  $e="\$c=array(".$t2[0].");";
  eval ($e);
  //print_r ($c);
  $t1=explode("parseInt(value) -",$html);
  $t2=explode(")",$t1[1]);
  $d=$t2[0];
  for ($k=0;$k<count($c);$k++) {
   $out .=chr($c[$k]-$d);
  }
  if (preg_match("/img itemprop\=\"image\" src\=\"(.*?)\"/",$out,$m)) {
    $image=$m[1];
    $h=str_replace($orig,$image,$h);
    file_put_contents($file,$h);
  } else {
    //$image="blank.jpg";
    unset ($arr[$key]);
    asort($arr);
    $out1="";
    //print_r ($arr);
    foreach($arr as $key => $value) {
      //$out1 =$out1.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."\r\n";
      $out1 =$out.$arr[$key][0]."#separator".$arr[$key][1]."#separator".$arr[$key][2]."\r\n";
    }
    file_put_contents($file,$out1);
  }


 }
 /////////////////////////////////////////////////////////////////////
  $rest = substr($title, -6);
  if (preg_match("/\((\d{4})\)/",$rest,$m)) {
   $year=$m[1];
   $tit_imdb=trim(str_replace($m[0],"",$title));
  } else {
   $year="";
   $tit_imdb=$title;
  }
    $link=$host.parse_url($link)['path'];
    $last_good="https://".$host;
    $link_f=$fs_target.'?tip=movie&link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image."&sez=&ep=&ep_tit=&year=".$year."&last=".$last_good;
  if ($n==0) echo '<TR>'."\r\n";
  $val_imdb="tip=movie&title=".urlencode(fix_t($tit_imdb))."&year=".$year."&imdb=".$imdb;
  $fav_link="file=&mod=del&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
  //$image="r_m.php?file=".$image;
  if ($tast == "NU") {
    echo '<td class="mp" width="25%"><a href="'.$link_f.'" id="myLink'.$w.'" target="_blank" onmousedown="isKeyPressed(event)">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
    echo '<a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a>'."\r\n";
    echo '</TD>'."\r\n";
  } else {
    echo '<td class="mp" width="25%"><a class ="imdb" id="myLink'.$w.'" href="'.$link_f.'" target="_blank">
    <img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
    echo '<input type="hidden" id="fav_myLink'.$w.'" value="'.$fav_link.'"></a>'."\r\n";
    echo '</TD>'."\r\n";
  }
  $w++;
  $n++;
  if ($n == 4) {
  echo '</tr>'."\r\n";
  $n=0;
  }
}
curl_close ($ch);
  if ($n < 4 && $n > 0) {
    for ($k=0;$k<4-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }
echo '</TABLE>';
}
?>
</body>
</html>
