<?php
//error_reporting(0);
//62
include ("../common.php");
//$fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".$image;
//$fav_link="mod=add&title=".urlencode(fix_t($title1))."&imdb=".$imdb."&year=".$year."&image=".$image;
$mod=$_POST["mod"];
$link=$_POST["imdb"];
$title=$_POST["title"];
$year=$_POST["year"];
$image=urldecode($_POST["image"]);
$file=$base_fav."cineplex_s.dat";
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
      $y=trim($a[2]);
      $img=trim($a[3]);
      $arr[$tit]["link"]=$l;
      $arr[$tit]["year"]=$y;
      $arr[$tit]["image"]=$img;
    }
  }
}
if ($mod=="add") {
  $found=false;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    if ($title == $key && parse_url($link)['path']==parse_url($arr[$key]['link'])['path']) {
      $found=true;
      break;
    }
  }
  if (!$found) {
    $arr[$title]["link"]=$link;
    $arr[$title]["year"]=$year;
    $arr[$title]["image"]=$image;
    echo "Am adaugat serialul ".unfix_t(urldecode($title));
  }
  ksort($arr);
  } else {
    $arr[$title]["link"]=$link;
    $arr[$title]["year"]=$year;
    $arr[$title]["image"]=$image;
    echo "Am adaugat serialul ".unfix_t(urldecode($title));
  }
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["year"]."#separator".$arr[$key]["image"]."\r\n";
  }
  //echo $out;
  if ($found) echo "Serialul a fost adaugat deja!";
  file_put_contents($file,$out);
} else {
  $found=false;
  //echo $title;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    if ($title == $key && parse_url($link)['path']==parse_url($arr[$key]['link'])['path']) {
      $found=true;
      //echo $title;
      unset ($arr[$key]);
      echo "Am sters serialul ".unfix_t(urldecode($title));
      break;
    }
  }
  if ($arr) {
    ksort($arr);
    $out="";
    //print_r ($arr);
    foreach($arr as $key => $value) {
      $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["year"]."#separator".$arr[$key]["image"]."\r\n";
    }
    file_put_contents($file,$out);
   }
 }
}
  
?>
