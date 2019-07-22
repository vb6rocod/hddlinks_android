<?php
//error_reporting(0);
//62
include ("../common.php");
//$fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".$image;
$mod=$_POST["mod"];
$link=$_POST["link"];
$title=$_POST["title"];
$image=$_POST["image"];
$width=$_POST["width"];
$height=$_POST["height"];
$target=$_POST["file"];
$file=$base_fav."adult_fav.dat";
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
      $wi=trim($a[3]);
      $he=trim($a[4]);
      $tar=trim($a[5]);
      $arr[$tit]["link"]=$l;
      $arr[$tit]["image"]=$img;
      $arr[$tit]["width"]=$wi;
      $arr[$tit]["height"]=$he;
      $arr[$tit]["target"]=$tar;
    }
  }
}
if ($mod=="add") {
  $found=false;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    if ($title == $key) {
      $found=true;
      break;
    }
  }
  if (!$found) {
    $arr[$title]["link"]=$link;
    $arr[$title]["image"]=$image;
    $arr[$title]["width"]=$width;
    $arr[$title]["height"]=$height;
    $arr[$title]["target"]=$target;
    echo "Am adaugat ".unfix_t(urldecode($title));
  }
  ksort($arr);
  } else {
    $arr[$title]["link"]=$link;
    $arr[$title]["image"]=$image;
    $arr[$title]["width"]=$width;
    $arr[$title]["height"]=$height;
    $arr[$title]["target"]=$target;
    echo "Am adaugat ".unfix_t(urldecode($title));
  }
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."#separator".$arr[$key]["width"]."#separator".$arr[$key]["height"]."#separator".$arr[$key]["target"]."\r\n";
  }
  //echo $out;
  if ($found) echo "Clipul a fost adaugat deja!";
  file_put_contents($file,$out);
} else {
  $found=false;
  //echo $title;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    if ($title == $key) {
      $found=true;
      //echo $title;
      unset ($arr[$key]);
      echo "Am sters clipul ".unfix_t(urldecode($title));
      break;
    }
  }
  if ($arr) {
    ksort($arr);
    $out="";
    //print_r ($arr);
    foreach($arr as $key => $value) {
      $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."#separator".$arr[$key]["width"]."#separator".$arr[$key]["height"]."#separator".$arr[$key]["target"]."\r\n";
    }
    file_put_contents($file,$out);
   }
 }
}
  
?>
