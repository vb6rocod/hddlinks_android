<?php
//error_reporting(0);
//62
include ("../common.php");
//$fav_link="mod=add&title=".urlencode(fix_t($title11))."&link=".$link1."&image=".$image;
$mod=$_POST["mod"];
$link=$_POST["link"];
$title=$_POST["title"];
$image=urldecode($_POST["image"]);
if ($mod == "add" && !$image) {
  $ch = curl_init("http://www.tvseries.net".$link);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"http://www.tvseries.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close ($ch);
  $t1=explode('<h5 class="text-center',$h);
  $t2=explode('src="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $image=$t3[0];
}
$file=$base_fav."tvseries_f_new.dat";
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
      $arr[$tit]["link"]=$l;
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
    $arr[$title]["image"]=$image;
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
  }
  ksort($arr);
  } else {
    $arr[$title]["link"]=$link;
    $arr[$title]["image"]=$image;
    echo "Am adaugat filmul ".unfix_t(urldecode($title));
  }
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."\r\n";
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
      $out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."\r\n";
    }
    file_put_contents($file,$out);
   }
 }
}
  
?>
