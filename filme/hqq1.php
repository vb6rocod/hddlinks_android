<?php
    function decodeUN($a) {
        $a=substr($a, 1);
        //echo $a;
        $s2 = "";
        $s3="";
        $i = 0;
        while ($i < strlen($a)) {
            //$s2 += ('\u0' + $a[i:i+3])  // substr('abcdef', 1, 3);
            $s2 = $s2.'\u0'.substr($a, $i, 3);
            $s3 = $s3.chr(intval(substr($a, $i, 3),16));
            $i = $i + 3;
       }
       return $s3;
   }
$adbn=$_POST['adb'];
$v=$_POST['v'];
$hash_img=urldecode($_POST['hash_img']);
$x=$_POST['x'];
$y=$_POST['y'];
$host=$_POST['host'];
$srt=$_POST['srt'];
$post='{"htoken":"","sh":"'.sha1("hqq").'","ver":"4","secure":"0","adb":"'.$adbn.'","v":"'.$v.'","token":"","gt":"","embed_from":"0","wasmcheck":1,"adscore":"","click_hash":"'.urlencode($hash_img).'","clickx":'.$x.',"clicky":'.$y.'}';
//https://seriale-online.net/filme/captain-america-1979/
$l2=$host."/player/get_md5.php";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0',
'Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/json',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post),
'Origin: '.$host,
'Connection: keep-alive',
'Referer: '.$host.'/',
'Sec-Fetch-Dest: empty',
'Sec-Fetch-Mode: cors',
'Sec-Fetch-Site: same-origin'
);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $x=json_decode($h,1);
  if (isset($x['obf_link'])) {
    $file=$x['obf_link'];
    $y=decodeUN($file);
    if (strpos($y,"http") === false && $y) $y="https:".$y;
    if ($y)
      $link=$y.".mp4.m3u8";
    else
     $link="";
  } else {
    $link="";
  }
  echo $link;
  file_put_contents("hqq.txt",$link."&srt=".$srt);
?>
