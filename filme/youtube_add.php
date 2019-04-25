<?php
//error_reporting(0);
//62
include ("../common.php");
//$add_fav="mod=add&kind=".str_replace("youtube#","",$kind)"&id=".$id."&title=".urlencode(fix_t($title))."&image=".$image;

$mod=$_POST["mod"];
$kind=$_POST["kind"];
$id=$_POST["id"];
$title=$_POST["title"];
$image=urldecode($_POST["image"]);
$file=$base_fav."youtube.dat";
$arr=array();
$h="";
if (file_exists($file)) {
  $h=trim(file_get_contents($file));
  $t1=explode("\r\n",$h);
  for ($k=0;$k<count($t1);$k++) {
    $a=explode("#separator",$t1[$k]);
    if ($a) {
      $kind_l=trim($a[0]);
      $id_l=trim($a[1]);
      $tit=trim($a[2]);
      $img=trim($a[3]);
      $arr[$tit]["kind"]=$kind_l;
      $arr[$tit]["id"]=$id_l;
      $arr[$tit]["image"]=$img;
    }
  }
}
if ($mod=="add") {
  $found=false;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    if ($id == $arr[$key]["id"]) {
      $found=true;
      break;
    }
  }
  if (!$found) {
    $arr[$title]["id"]=$id;
    $arr[$title]["kind"]=$kind;
    $arr[$title]["image"]=$image;
    echo "Am adaugat ".unfix_t(urldecode($title));
  }
  ksort($arr);
  } else {
    $arr[$title]["id"]=$id;
    $arr[$title]["kind"]=$kind;
    $arr[$title]["image"]=$image;
    echo "Am adaugat serialul ".unfix_t(urldecode($title));
  }
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    $out =$out.$arr[$key]["kind"]."#separator".$arr[$key]["id"]."#separator".$key."#separator".$arr[$key]["image"]."\r\n";
  }
  //echo $out;
  if ($found) echo "Este deja in lista!";
  file_put_contents($file,$out);
} else {
  $found=false;
  //echo $title;
  if ($arr) {
  $found=false;
  foreach($arr as $key => $value) {
    if ($id == $arr[$key]["id"]) {
      $found=true;
      //echo $title;
      unset ($arr[$key]);
      echo "Am sters ".unfix_t(urldecode($title));
      break;
    }
  }
  if ($arr) {
    ksort($arr);
    $out="";
    //print_r ($arr);
    foreach($arr as $key => $value) {
      $out =$out.$arr[$key]["kind"]."#separator".$arr[$key]["id"]."#separator".$key."#separator".$arr[$key]["image"]."\r\n";
    }
    file_put_contents($file,$out);
   }
 }
}
  
?>
