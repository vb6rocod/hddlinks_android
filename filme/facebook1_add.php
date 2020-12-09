<?php
//error_reporting(0);
//62
include ("../common.php");
//$fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".$image;
$mod=$_POST["mod"];
$title=$_POST["canal"];
$image=unfix_t(urldecode($_POST["videoid"]));
$file=$base_fav."facebook1.dat";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0";
$arr=array();
$h="";
if (file_exists($file)) {
  $h=file_get_contents($file);
  $t1=explode("\r\n",$h);
  for ($k=0;$k<count($t1) -1;$k++) {
    $a=explode("#separator",$t1[$k]);
    if ($a) {
      $tit=trim($a[0]);
      $img=trim($a[1]);
      $arr[$tit]["image"]=$img;
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

   $file_img=$image;
   //fclose($fp);
    $arr[$title]["image"]=$file_img;
    echo "Am adaugat canalul ".unfix_t(urldecode($title));
  }
  ksort($arr);
  } else {
   $file_img=$image;
   //fclose($fp);
    $arr[$title]["image"]=$file_img;
    echo "Am adaugat canalul ".unfix_t(urldecode($title));
  }
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    $out =$out.$key."#separator".$arr[$key]["image"]."\r\n";
  }
  //echo $out;
  if ($found) echo "Canalul a fost adaugat deja!";
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
      echo "Am sters canalul ".unfix_t(urldecode($title));
      break;
    }
  }
  if ($arr) {
    ksort($arr);
    $out="";
    //print_r ($arr);
    foreach($arr as $key => $value) {
      $out =$out.$key."#separator".$arr[$key]["image"]."\r\n";
    }
    file_put_contents($file,$out);
   }
 }
}
  
?>
