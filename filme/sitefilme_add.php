<?php
//error_reporting(0);
//62
include ("../common.php");
//$fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".$image;
$mod=$_POST["mod"];
$link=$_POST["link"];
$link_p=parse_url(urldecode($link))['path'];
$title=$_POST["title"];
function rep_img($tit,$img,$key) {
  $year="";
  unfix_t($tit);
  preg_match("/\(([^\)]*)\)$/",$tit,$y);
  //print_r ($y);
  if (isset($y[0])) {
    $title=trim(preg_replace("/\(([^\)]*)\)$/","",$tit));
    if (preg_match("/[1|2]\d{3}/",$y[1],$z))
     $year=$z[0];
  }
  $tit=trim(preg_replace("/\(([^\)]*)\)$/","",$tit));
  if (preg_match("/\s-\s/",$tit)) {
   $t1=explode(" - ",$tit);
   $tit=$t1[0];
  }
  //echo $tit."\n";
 if ($year)
   $l="https://www.themoviedb.org/search/movie?query=".rawurlencode($tit)."%20y:".$year;
 else
   $l="https://www.themoviedb.org/search/movie?query=".rawurlencode($tit);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match_all("/movie\/(\d+)\-/",$h,$m)) {
  //print_r ($m);
  $tmdb = $m[1][0];
  $l="https://api.themoviedb.org/3/movie/".$tmdb."?api_key=".$key."&append_to_response=credits";
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
   $p=json_decode($h,1);
   if ($p["poster_path"])
    $img="http://image.tmdb.org/t/p/w500".$p["poster_path"];

 }
 return $img;
}
if ($title=="") {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_REFERER,"https://sitefilme.com/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  if (preg_match("/\<title\>([^\<]+)\<\/title\>/i",$html,$m))  {
  $title=$m[1];
  $title=preg_replace("/\s*online subtitrat/i","",$title);
  $title=str_replace("&#8211;","-",$title);
  $t1=explode(" - ",$title);
  $title=$t1[0];
  }

}
$image=urldecode($_POST["image"]);
$file=$base_fav."sitefilme.dat";
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
      //$arr[$tit]["link"]=$l;
      //$arr[$tit]["image"]=$img;
      $arr[$k]=array($tit,$l,$img);
    }
  }
}
if ($mod=="add") {
  $found=false;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    //if ($title == $key) {
    if ($title == $arr[$key][0] && $link_p == parse_url($arr[$key][1])['path']) {
      $found=true;
      break;
    }
  }
  if (!$found) {
    //$arr[$title]["link"]=$link;
    //$arr[$title]["image"]=$image;
    $f_TMDB=$base_pass."tmdb.txt";
    $key = file_get_contents($f_TMDB);
      if (preg_match("/sitefilme/",$image)) {
       $image=rep_img($title,$image,$key);

      }
    $arr[]=array($title,$link,$image);
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
  }
  asort($arr);
  } else {
    //$arr[$title]["link"]=$link;
    //$arr[$title]["image"]=$image;
    $arr[]=array($title,$link,$image);
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
  }
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    //$out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."\r\n";
    $out =$out.$arr[$key][0]."#separator".$arr[$key][1]."#separator".$arr[$key][2]."\r\n";
  }
  //echo $out;
  if ($found) echo "Filmul a fost adaugat deja!";
  file_put_contents($file,$out);
} else {  // delete
  $found=false;
  //echo $title;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    //if ($title == $key) {
    if ($title == $arr[$key][0] && $link_p == parse_url($arr[$key][1])['path']) {
      $found=true;
      //echo $title;
      unset ($arr[$key]);
      echo "Am sters filmul ".unfix_t(urldecode($title));
      break;
    }
  }
  if ($arr) {
    asort($arr);
    $out="";
    //print_r ($arr);
    foreach($arr as $key => $value) {
      //$out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."\r\n";
      $out =$out.$arr[$key][0]."#separator".$arr[$key][1]."#separator".$arr[$key][2]."\r\n";
    }
    file_put_contents($file,$out);
   }
 }
}
  
?>
