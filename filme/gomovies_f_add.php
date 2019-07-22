<?php
//error_reporting(0);
include ("../common.php");
$mod=$_POST["mod"];
$link=$_POST["link"];
$title=$_POST["title"];
$image=$_POST["image"];
$year=$_POST["year"];

$file=$base_fav."gomovies_f.dat";
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
      $y=trim($a[3]);
      $arr[$tit]["link"]=$l;
      $arr[$tit]["image"]=$img;
      $arr[$tit]["year"]=$y;
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
    $arr[$title]["year"]=$year;
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
  }
  ksort($arr);
  } else {
    $arr[$title]["link"]=$link;
    $arr[$title]["image"]=$image;
    $arr[$title]["year"]=$year;
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
  }
  $out="";
  foreach($arr as $key => $value) {
    $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."#separator".$arr[$key]["year"]."\r\n";
  }
  if ($found) echo "Filmul a fost adaugat deja!";
  file_put_contents($file,$out);
} else {
  $found=false;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    if ($title == $key) {
      $found=true;
      unset ($arr[$key]);
      echo "Am sters filmul ".unfix_t(urldecode($title));
      break;
    }
  }
  if ($arr) {
    ksort($arr);
    $out="";
    foreach($arr as $key => $value) {
      $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."#separator".$arr[$key]["year"]."\r\n";
    }
    file_put_contents($file,$out);
   }
 }
}
  
?>
