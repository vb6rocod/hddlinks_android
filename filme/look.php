<?php
//die();
include ("../common.php");


  
$ua="Mozilla/5.0 (Windows NT 10.0; rv:86.0) Gecko/20100101 Firefox/86.0";
$ua=file_get_contents($base_pass."firefox.txt");
//$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."lookmovie.txt";
//$cookie="C:/EasyPhp/data/localweb/mobile1/cookie/cookies.txt";
//unlink ($cookie);
$x=file_get_contents($base_cookie."lookmovie_ref1.txt");
$t1=explode("|",$x);
$l1=$t1[0];
$ref=$t1[1];
$csrf=$t1[2];
$token=$_POST['g-recaptcha-response'];
$post="_csrf=".$csrf."&g-recaptcha-response=".$token;
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post),
'Origin: https://'.$ref,
'Connection: keep-alive',
'Referer: https://'.$ref,
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
 //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //$info = curl_getinfo($ch);
  curl_close($ch);
  //print_r ($info);
  //echo $h;
  if (preg_match("/invisible\-recaptcha\-key/",$h)) {
   //echo '<a href="look_captcha.php?host=https://lookmovie.io&cookie=lookmovie.txt&target=lookmovie_s.php&title=lookmovie" target="_blank"><b>Sorry! Use firefox (cookies.txt) to GET COOKIE</b></a>';
   echo 'error';
   die();
  } else {
    echo "OK! GO BACK";
    echo '<script>setTimeout(function(){ history.go(-3); }, 2000);</script>';
  }

?>
