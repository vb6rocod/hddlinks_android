<?php
include ("../common.php");
$cookie=$base_cookie."hqq.txt";
if (file_exists($cookie)) unlink ($cookie);
$ua="Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10', #'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:21.0) Gecko/20100101 Firefox/21.0";
$ua = $_SERVER['HTTP_USER_AGENT'];
$l=urldecode($_POST["file"]);

//file_put_contents($base_script."subs/l.txt",$l);
$l=str_replace("&amp;","&",$l);
$l=str_replace("https","http",$l);
$l=str_replace("gt;","gt",$l);
//file_put_contents($base_cookie."cc.txt",$l);
//$l=str_replace("pajalusta.club","hqq.watch",$l);
//$token=$_GET['g-recaptcha-response'];
//https://hqq.tv/sec/player/embed_player.php?vid=1&need_captcha=1&gtoken=03&http_referer=http%253A%252F%252F127.0.0.1%252Fmobile1%252Fscripts%252Ffilme%252Fhqq_captcha.php&g-recaptcha-response=03AHaCkAYgvYe6hUOsKVwIRqvJyz6EiRiFEl8p8lyyljuHRyn033B27PGHl2A--5qP6vguRSBALC6pwprsQZ2NzLTp3BzD1WxQRX4XOI70PAE4bn1GoDOmknlpnTsLQVHLUUGk7c_Tw-_KEqjvDOx2YSbMFLQTTQ75dvl-iJOPWI5vnru3H19FSXWu0N4sNd8XbFu2fAbMcvLOQ7lnS1MPzH1ry-zx2Cz9f624sZ6B3c6Hur8Di247e_GL7kGFWUZON4E4O-57yTyy1Olst_isNDXUcIwy55WRhUn2clyx3bA75iHIqeREDP1-H-PvotsZRReRJ0xgum8XMzMnxqOqL0AdOr7BtD40X1jgCA3ialKQwjmS7YMzjkYoHm4K7TPcGZ-o2YomXx5ogwLj5Lw4frQVf_A7WY-JJ4EVTtk2RVUPxUJbBh_hBLQYCk_SWhHjN4wZtAWo3MOV#iss=NzguOTYuODIuNDA=
//$l="http://hqq.watch/sec/player/embed_player.php?vid=1&g-recaptcha-response=".$token;
//$l="https://hqq.tv/sec/player/embed_player.php?vid=1&need_captcha=1&gtoken=03&http_referer=&g-recaptcha-response=03AHaCkAaB6Fg3NJuSjYS9CYChQ0U5EcInp9-eJvTm_URaU7u88nIJK-82pjgXB9SRnmEouGZwKiVSH3T8uZ3Yk0zVAktiu_6dkMFkCsAusW5yLimtmTWbuZN69KPrj0bAAqYiOI2dhHuq9hIRvUu08PYq_-q8E9kDJEYteSNvA9mXl0K11oyVmlNjHB5geMbZsDGrIJzt2PuNhoL7wj66jQqbmg6Z4SqpiIp9Zn3s3W4utM4h0yqi_t5IDYNzC-LWCQqpeftKaRGhnp85crgK7UlHRJ5eiYMTRtjLODfphwasn0fZ2QQPneeTlaRK0aDXmIWfe0_mWkYfyu8Xgoh8pTxcmenhtRszz8bbx_A_fzitUTvVyRTySFQ5zgd3NLQAeDXnM9dfN0xmamLA6VypPD3E-zspTHFeY2-uBf3kwCxywqFXagNfyYZGaFYWRAvTegHhJKa_7A3M#iss=NzguOTYuODIuNDA=";
//$l="http://hqq.watch/sec/player/embed_player.php?vid=1&need_captcha=1&gtoken=03&http_referer=http%253A%252F%252F127.0.0.1%252Fmobile1%252Fscripts%252Ffilme%252Fhqq_captcha.php&g-recaptcha-response=03AHaCkAax8gmzwliGIOdylx9V95hmKhfxlvTGsU-Aw_Bo-F5xifdu3NQ5rZk9doXDtAy7ZjGcZwcdhWupSruJF02Oa1Tpv-4LCunw-mFa5aURTKdt3Crq8_8T78XjRCHFG8hndHvavsW3eq-KF4f73EapePx4DyvPCWaWvYw9Fc3E2PO4fU5xLkXBQZIfZV-EJB0G5Ldii_t8H-4fI_FeA0o-gXhIZisbEz1qrxQrL3r_fzd4AUujAH_Fx80evy0e_GVI2oAAt6vP3mtAaM_Na3kYq829jyLgTx8Gz_0shKJSeAwk7MN6kg9vmRBbLCRm1_QtnrSIoZ5rtPZoANI4X8L_f58dO2Q666RJMdcd8v82dfA_lqPbX1DmuUdahiJciXQe7RMYGGHsuL14UO6qc3qG-jDjDiYfPtINtLHTzXFlxVbMbG-jl0M76TfQU8dC5hWZax8OOcSG";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://hqq.watch/player/embed_player.php?vid=aGtEK1o2bDc0UytacHJiai9HSUV1Zz09");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_HEADER,1);
      curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_NOBODY,1);
      $h = curl_exec($ch);
      curl_close($ch);

$h=urldecode($h);
//echo $h;
if (!preg_match("/gt\=([a-zA-Z0-9]+)/",$h,$m))
  echo "Video not found! or bad script";
else {
//file_put_contents("result.txt",urldecode($h));
  $t1=explode("expires",$h);
  $t2=explode("path",$t1[1]);
  $gt=$m[1];
  echo "expires".$t2[0]."<BR>"."gt=".$m[1];
  $t1=explode("=",$t2[0]);
  $t2=explode(";",$t1[1]);
  $t3=explode(";",$t1[2]);
  $time1 = strtotime($t2[0]);
  $time2 = time() + $t3[0];
  file_put_contents($base_cookie."max_time_hqq.txt",$time2);
  $y=file_get_contents($cookie);
  $y=str_replace("hqq.watch","hqq.tv",$y);
  file_put_contents($cookie,$gt);
}
//echo $h;
?>
