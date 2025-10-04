<?php
//error_reporting(0);
//62
include ("../common.php");
//$fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".$image;
//$fav_link="mod=add&title=".urlencode(fix_t($title1))."&imdb=".$imdb."&year=".$year."&image=".$image;
//mod=add&title=Dune%3A+Part+Two&imdb=https://gramaton.io/movies/18618-dune-part-two&year=2024&image=https://image.tmdb.org/t/p/w342/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg
$mod=$_POST["mod"];
$link=$_POST["imdb"];
$link_p=parse_url(urldecode($link))['path'];
$title=$_POST["title"];
$year=$_POST["year"];
$image=urldecode($_POST["image"]);
$file=$base_fav."cineplex_f.dat";
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
      $img=trim($a[3]);
      $yy=trim($a[2]);
      //$arr[$tit]["link"]=$l;
      //$arr[$tit]["image"]=$img;
      $arr[$k]=array($tit,$l,$yy,$img);
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
    $arr[]=array($title,$link,$year,$image);
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
  }
  asort($arr);
  } else {
    //$arr[$title]["link"]=$link;
    //$arr[$title]["image"]=$image;
    $arr[]=array($title,$link,$year,$image);
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
  }
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    //$out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."\r\n";
    $out =$out.$arr[$key][0]."#separator".$arr[$key][1]."#separator".$arr[$key][2]."#separator".$arr[$key][3]."\r\n";
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
      $out =$out.$arr[$key][0]."#separator".$arr[$key][1]."#separator".$arr[$key][2]."#separator".$arr[$key][3]."\r\n";
    }
    file_put_contents($file,$out);
   }
 }
}
/*
$mod=$_POST["mod"];
$link=$_POST["imdb"];
$title=$_POST["title"];
$year=$_POST["year"];
$image=urldecode($_POST["image"]);
$file=$base_fav."cineplex_f.dat";
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
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
  }
  ksort($arr);
  } else {
    $arr[$title]["link"]=$link;
    $arr[$title]["year"]=$year;
    $arr[$title]["image"]=$image;
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
  }
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["year"]."#separator".$arr[$key]["image"]."\r\n";
  }
  //echo $out;
  if ($found) echo "Filmul a fost adaugat deja!";
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
      echo "Am sters filmul ".unfix_t(urldecode($title));
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
*/
?>
