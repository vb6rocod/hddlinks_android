<?php
//die();
include ("../common.php");
include("rec.php");
$key="6Ley5moUAAAAAJxloiuF--u_uS28aYUj-0E6tSfZ";
$key="6Lc-jrkaAAAAAP03RoRV_-WL0-ETAxXJXGU_62lT";
$co="aHR0cHM6Ly9sb29rbW92aWUuaW86NDQz";
//echo base64_decode($co)."\n";
$sa="submit";
$loc="https://lookmovie.io";
$token=rec($key,$co,$sa,$loc);

  
$ua="Mozilla/5.0 (Windows NT 10.0; rv:86.0) Gecko/20100101 Firefox/86.0";
//$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."lookmovie.txt";
//$cookie="C:/EasyPhp/data/localweb/mobile1/cookie/cookies.txt";
//unlink ($cookie);
$l="https://lookmovie.io/?p=1&r=1";
//die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://afdah.info");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
 //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, implode(":", $DEFAULT_CIPHERS));
  //curl_setopt($ch, CURLOPT_SSLVERSION,$ssl_version);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);
  echo $html;
if (preg_match("/invisible\-recaptcha\-key/",$html)) {
$t1=explode('hidden" name="_csrf" value="',$html);
$t2=explode('"',$t1[1]);
$csrf=$t2[0];
preg_match("/location:\s+(.+)/i",$html,$m);
$l="https://lookmovie.io".trim($m[1]);
echo 'first step: '.$l."<BR>";
sleep(5);

$post="_csrf=".$csrf."&tk=".$token;
echo $post;
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post),
'Origin: https://lookmovie.io',
'Connection: keep-alive',
'Referer: '.$l,
'Upgrade-Insecure-Requests: 1');
//print_r ($head);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
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
  //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, implode(":", $DEFAULT_CIPHERS));
  //curl_setopt($ch, CURLOPT_SSLVERSION,$ssl_version);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  //$info = curl_getinfo($ch);
  curl_close($ch);
  //print_r ($info);
  echo $h;


preg_match("/location:\s+(.+)/i",$h,$m);
$l1=trim($m[1]);
$l1=str_replace("http://","https://",$l1);
if (preg_match("/threat\-protection\/second\?t\=/",$l1)) {
echo 'second step: '.$l1."<BR>";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$l);
  //curl_setopt($ch,CURLOPT_REFERER,"https://afdah.info");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h;
  die();
$t1=explode('name="_csrf',$h);
$t2=explode('value="',$t1[1]);
$t3=explode('"',$t2[1]);
$csrf=$t3[0];
//$token=file_get_contents("look22.txt");
$token=$_GET['g-recaptcha-response'];
$post="_csrf=".$csrf."&g-recaptcha-response=".$token;
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post),
'Origin: https://lookmovie.io',
'Connection: keep-alive',
'Referer: '.$l,
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
preg_match("/location:\s+(.+)/i",$h,$m);
$l11=trim($m[1]);
$l11=str_replace("http://","https://",$l11);
echo 'third step: '.$l11."<BR>";
if (preg_match("/threat\-protection\/second\?t\=/",$l11)) {
echo '<a href="look_captcha.php?host=https://lookmovie.io&cookie=lookmovie.txt&target=lookmovie_s.php&title=lookmovie" target="_blank"><b>GET COOKIE</b></a>';
die();
} else {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l11);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$l);
  //curl_setopt($ch,CURLOPT_REFERER,"https://afdah.info");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  if (preg_match("/invisible\-recaptcha\-key/",$h)) {
   echo '<a href="look_captcha.php?host=https://lookmovie.io&cookie=lookmovie.txt&target=lookmovie_s.php&title=lookmovie" target="_blank"><b>Sorry! Use firefox (cookies.txt) to GET COOKIE</b></a>';
   die();
  } else {
    echo "OK! GO BACK";
  }
 }
} else {
 echo "OK! GO BACK";
}
}
?>
