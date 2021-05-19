<?php
  //$filelink="https://playtube.ws/embed-5eisel2uv8hc/tt0102768.mp4.html?c1_file=https://serialeonline.io/subtitrarifilme/tt0102768.vtt&c1_label=Romana";
  //$filelink="https://playtube.ws/embed-8dqx1qfmjkvy/tt0115610.mp4.html?c1_file=https://seriale-online.net/subtitrarifilme/tt0115610.vtt&c1_label=Romana";
include ("../common.php");
error_reporting(0);
if (isset($_GET['link'])) {
 $filelink=urldecode($_GET['link']);
 $title=urldecode($_GET['title']);
}
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
   require_once("JavaScriptUnpacker.php");
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:81.0) Gecko/20100101 Firefox/81.0";
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $filelink);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_REFERER,"https://seriale-online.net/");
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   //echo $h;
  $out="";
  $srt="";
  if (preg_match("/eval\(function\(p,a,c,k,e,[r|d]?/",$h)) {
  $jsu = new JavaScriptUnpacker();
  $out = $jsu->Unpack($h);
  }
  //echo $out;
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $h.$out, $s))
    $srt="https:".$s[1];
  if (preg_match("/file\_code\:\'/",$out)) {
  $link=""; // new version
  $t1=explode("file_code:'",$out);
  $t2=explode("'",$t1[1]);
  $file_code=$t2[0];
  $t1=explode("hash:'",$out);
  $t2=explode("'",$t1[1]);
  $hash=$t2[0];
  $l="https://playtube.ws/dl";
  $post="op=playerddl&file_code=".$file_code."&hash=".$hash;
  $head=array('Accept: */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
   'X-Requested-With: XMLHttpRequest',
   'Content-Length: '.strlen($post),
   'Origin: https://playtube.ws',
   'Connection: keep-alive',
   'Referer: https://playtube.ws');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $w=0;
  do {
   $h = curl_exec($ch);
   $y=json_decode($h,1);
   $w++;
  } while (isset($y[0]['error']) && $w < 10);
  curl_close($ch);
  //echo $h;
  //print_r ($y);
  $link=$y[0]['file'];
  $seed=$y[0]['seed'];
  if ($link) {
   $out=str_replace("'",'"',$out);
   $t1=explode('var chars=',$out);
   $t2=explode(';',$t1[1]);
   $cc='var chars='.$t2[0].";";
   $e="\$chars='".$t2[0]."';";
   eval ($e);
   $t1=explode('replace(/[',$out);
   $t2=explode("]",$t1[1]);
   $rep="/[".$t2[0]."]/";
   $x=json_decode($chars,1);
   $seed=preg_replace_callback(
    $rep,
    function ($m) {
      global $x;
      return $x[$m[0]];
    },
    $seed
   );

$out = '<script src="https://playtube.ws/js/tear.js"></script>'."\r\n"."<script>"."\r\n";
//$out .="var chars={'0':'5','1':'6','2':'7','5':'0','6':'1','7':'2'};"."\r\n";
$out .=$cc."\r\n";
$out .='var b="'.$link.'";'."\r\n";
$out .='var c="'.$seed.'";'."\r\n";
$out .='var a=decrypt(b,c);'."\r\n";
$out .='a=a.replace('.$rep.'g,m=>chars[m]);'."\r\n";

//$out .='window.parent.document.getElementById("mytest1").href=a;'."\r\n";

$out .='a2="https://playtube.ws" + btoa(encodeURIComponent("?out=" + a + "&sub='.urlencode(base64_encode($srt)).'"));'."\r\n";
//$out .='window.parent.$.fancybox.close();'."\r\n";
if ($flash <> "flash")
 $out .='parent.ajaxrequest("'.urlencode($title).'",encodeURIComponent(a2));'."\r\n";
else
 $out .='parent.ajaxrequest2("'.urlencode($title).'",encodeURIComponent(a2));'."\r\n";
//$out .='ajaxrequest22(a,"tttttt");'."/r/n";
//$out .='window.parent.document.getElementById("mytest1").href=a;'."\r\n";
$out .='</script>';
echo $out;
} else {
echo '<script>
window.parent.document.getElementById("server").innerHTML = '."'".'<font size="6" color="lightblue">Alegeti un server</font>'."'".';
</script>';
}
} else {
echo '<script>
window.parent.document.getElementById("server").innerHTML = '."'".'<font size="6" color="lightblue">Alegeti un server</font>'."'".';
</script>';
}
?>
