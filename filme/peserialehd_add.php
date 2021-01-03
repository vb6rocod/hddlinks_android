<?php
//error_reporting(0);
//62
include ("../common.php");
//$fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".$image;
$mod=$_POST["mod"];
$link=$_POST["link"];
$title=$_POST["title"];
$tip=$_POST["tip"];
$image=urldecode($_POST["image"]);
$file=$base_fav."peserialehd_fav.dat";
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
      $tip1=trim($a[3]);
      $arr[$tit]["link"]=$l;
      $arr[$tit]["image"]=$img;
      $arr[$tit]["tip"]=$tip1;
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
    $arr[$title]["tip"]=$tip;
    if ($tip=="movie")
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
    else
    echo "Am adaugat serialul ".unfix_t(urldecode($title));
  }
  ksort($arr);
  } else {
    $arr[$title]["link"]=$link;
    $arr[$title]["image"]=$image;
    $arr[$title]["tip"]=$tip;
    if ($tip=="movie")
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
    else
    echo "Am adaugat serialul ".unfix_t(urldecode($title));
  }
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."#separator".$arr[$key]["tip"]."\r\n";
  }
  //echo $out;
  if ($found) {
  if ($tip=="movie")
  echo "Filmul a fost adaugat deja!";
  else
  echo "Serialul a fost adaugat deja!";
  }
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
      if ($tip=="movie")
      echo "Am sters filmul ".unfix_t(urldecode($title));
      else
      echo "Am sters serialul ".unfix_t(urldecode($title));
      break;
    }
  }
  if ($arr) {
    ksort($arr);
    $out="";
    //print_r ($arr);
    foreach($arr as $key => $value) {
      $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."#separator".$arr[$key]["tip"]."\r\n";
    }
    file_put_contents($file,$out);
   }
 }
}
  
?>
