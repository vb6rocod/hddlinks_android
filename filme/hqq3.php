<?php
include ("../common.php");
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."hqq.txt";
$key="6LfCmh4TAAAAAKog9f8wTyEOc0U8Ms2RTuDFyYP_";
$key="6LfCmh4TAAAAAKog9f8wTyEOc0U8Ms2RTuDFyYP_";
//$key="6Ldf5F0UAAAAALErn6bLEcv7JldhivPzb93Oy5t9";
//$key="6LfwW48UAAAAAPOxDGJBARwBjEoVJX2YyXjj1ev_";
//$key="6LfsXx4TAAAAAG7fRIpL2LpS_NLxj1HBlotEDhT7";
//$key="6Ldf5F0UAAAAALErn6bLEcv7JldhivPzb93Oy5t9";
if (file_exists($cookie)) unlink ($cookie);
if (isset($_GET['response'])) {
$q = $_SERVER["QUERY_STRING"];
$post=$q;
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://www.google.com/recaptcha/api/fallback?k='.$key.'',
'Content-Type: application/x-www-form-urlencoded',
'Content-Length: '.strlen($post).'',
'Connection: keep-alive'
);
//6LfCmh4TAAAAAKog9f8wTyEOc0U8Ms2RTuDFyYP_
$l="https://www.google.com/recaptcha/api/fallback?k=".$key;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
//curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt ($ch, CURLOPT_POST, 1);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
$h = curl_exec($ch);
curl_close($ch);
//echo $h;
//die();
$pat='/\<textarea dir\=\"ltr\" readonly\>(.+?)\</';
if (preg_match_all($pat,$h,$m)) {
$token=$m[1][0];
$post="iss=OTUuNzYuMTguMjI1&vid=WkhtUjNXNWNocGRsMGJEdzhqbEc0Zz09&at=cabe56139b8a75b7fd8d629c766e5baf&autoplayed=yes&referer=on&http_referer=aHR0cHM6Ly9maWxtZXNlcmlhbGVoZC5yby90aGUtYm95cy9oZS1zZWxmLXByZXNlcnZhdGlvbi1zb2NpZXR5Lw%3D%3D&pass=&embed_from=&need_captcha=0&secure=0&g-recaptcha-response=";
$post=$post.$token;
$l="https://hqq.tv/sec/player/embed_player_5320147097838386.php?".$post;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_REFERER, "http://hqq.tv/player/embed_player.php?vid=aGtEK1o2bDc0UytacHJiai9HSUV1Zz09");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_HEADER,1);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_NOBODY,0);
$h = curl_exec($ch);
curl_close($ch);
//$h=urldecode($h);
//echo $h;
//die();
if (!preg_match("/gt=/",$h)) {
  echo "Video not found! or bad script";
  echo '<script>setTimeout(function(){ history.go(-2); }, 2000);</script>';
} else {
//file_put_contents("result.txt",urldecode($h));
  $t0=explode('gt=',$h);
  $t1=explode("expires",$t0[1]);
  $t2=explode("path",$t1[1]);
  echo "expires".$t2[0];
  $t1=explode("=",$t2[0]);
  $t2=explode(";",$t1[1]);
  $t3=explode(";",$t1[2]);
  $time1 = strtotime($t2[0]);
  $time2 = time() + $t3[0];
  file_put_contents($base_cookie."max_time_hqq.txt",$time2);
  echo '<script>setTimeout(function(){ history.go(-2); }, 3000);</script>';
}
} else {
  echo "BAD CAPTCHA!";
  echo '<script>setTimeout(function(){ history.go(-2); }, 2000);</script>';
}
} else {

//$cookie = __DIR__ . "\v3.txt";


$l="https://www.google.com/recaptcha/api/fallback?k=".$key;
$head = array(
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Origin: http://waaw.tv'
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_REFERER, "http://waaw.tv");
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_HEADER, 1);
$h = curl_exec($ch);
curl_close($ch);
//echo $h;
$pat='/value\=\"8\"\>\<img class\=\"fbc-imageselect-payload\" src\=\"(.+?)"/';
preg_match_all($pat,$h,$m);
$captchaScrap=$m[1][0];
//echo $captchaScrap;
$pat='/\<div class\=\"rc-imageselect.+?\"\>.+?\<strong\>(.+?)\<\/strong\>/';
preg_match_all($pat,$h,$m);
$text=$m[1][0];
$pat='/method\=\"POST\"\>\<input type\=\"hidden\" name\=\"c\" value\=\"(.+?)\"/';
preg_match_all($pat,$h,$m);
$c=$m[1][0];
$pat='/k\=(.+?)\" alt\=/';
preg_match_all($pat,$h,$m);
$k=$m[1][0];
$l='https://www.google.com'.str_replace("&amp;","&amp;",$captchaScrap);
//echo $l;
echo '
<!DOCTYPE HTML>
<html dir="ltr">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Verificare reCAPTCHA</title>
<link rel="stylesheet" href="fallback__ltr.css" type="text/css" charset="utf-8">
<script type="text/javascript">
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13" && e.target.type == "checkbox") {
      document.getElementById(e.target.id).click();
    } else if (charCode == "49") {
      document.getElementById("cap").submit();
    }
   }
document.onkeypress =  zx;
</script>
</head>
<body>
<div class="fbc">
<div class="fbc-alert"></div>
<div class="fbc-header">
<div class="fbc-logo">
<div class="fbc-logo-img"></div>
<div class="fbc-logo-text">reCAPTCHA</div>
</div>
<div class="fbc-imageselect-candidates"></div>
<div class="fbc-imageselect-message-without-candidate-image">
<label for="response" class="fbc-imageselect-message-text">
<div class="rc-imageselect-candidates">
<div class="rc-canonical-car"></div></div>
<div class="rc-imageselect-desc">Selectati toate imaginile cu <strong>'.$text.'</strong></div>
</label>
</div>
</div><div>
<div class="fbc-imageselect-challenge"><form id="cap" method="GET">
<input type="hidden" name="c" value="'.$c.'"/>
<div class="fbc-payload-imageselect">
<input id="1" class="fbc-imageselect-checkbox-1" type="checkbox" name="response" value="0">
<input id="2" class="fbc-imageselect-checkbox-2" type="checkbox" name="response" value="1">
<input id="3" class="fbc-imageselect-checkbox-3" type="checkbox" name="response" value="2">
<input id="4" class="fbc-imageselect-checkbox-4" type="checkbox" name="response" value="3">
<input id="5" class="fbc-imageselect-checkbox-5" type="checkbox" name="response" value="4">
<input id="6" class="fbc-imageselect-checkbox-6" type="checkbox" name="response" value="5">
<input id="7" class="fbc-imageselect-checkbox-7" type="checkbox" name="response" value="6">
<input id="8" class="fbc-imageselect-checkbox-8" type="checkbox" name="response" value="7">
<input id="9" class="fbc-imageselect-checkbox-9" type="checkbox" name="response" value="8">
<img class="fbc-imageselect-payload" src="'.$l.'" alt="Imagine de verificare reCAPTCHA"/>
</div>
';
if ($tast=="NU")
 echo '<div class="fbc-button-verify"><input type="submit" value="Confirmati"/></div>';
echo '
</form>
';
if ($tast=="DA") {
 echo "* falositi OK pentru selectie, 1 pentru validare.";
}
echo '
</body>
</html>
';
}
?>
