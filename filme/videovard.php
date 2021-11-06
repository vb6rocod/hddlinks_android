<?php
 /* resolve videovard
 * Copyright (c) 2019 vb6rocod
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * examples of usage :
 * $filelink = input file
 * $link --> video_link
 */

$filelink="https://videovard.sx/e/m97g3y0q9xcs?c1_file=https://seriale-online.net/subtitrari/7482-5-2.vtt&c1_label=Romana";
function get_max_res($h,$l) {
  // $h -- > contents of "master.m3u8"
  // $l -- > "master.m3u8
  if (preg_match("/BANDWIDTH|RESOLUTION/",$h)) {
    // get "dir"
    if (preg_match("/\?/",$l)) {
     $t1=explode("?",$l);
     $l=$t1[0];
    }
    $base1=dirname($l);  // https://aaa.vvv/xxx/ffff
    //echo $base1;
    preg_match("/(https?:\/\/.+)\//",$base1,$m);
    $base2=$m[1];  // https://aaa.vvv
    $pl=array();
    if (preg_match_all ("/^(?!#).+/m",$h,$m)) {
     $pl=$m[0];
     if (substr($pl[0], 0, -2) == "//")
       $base="https:";
     elseif ($pl[0][0] == "/")
       $base=$base2;
     elseif (preg_match("/http(s)?:/",$pl[0]))
       $base="";
     else
       $base=$base1."/"; // ???????????????????????
     if (count($pl) > 1) {
      if (preg_match_all("/\#EXT-X-STREAM-INF.*?(RESOLUTION|BANDWIDTH)\=(\d+)/i",$h,$r)) {
       $max_res=max($r[2]);
       $arr_max=array_keys($r[2], $max_res);
       $key_max=$arr_max[0];
       return $base.$pl[$key_max];
      } else {
        return $base.$pl[0];
      }
     } else {
      return $base.$pl[0];
     }
    } else {
      return $l;
    }
  } else {
   return $l;
  }
}
if (strpos($filelink,"videovard.") !== false) {
  if (preg_match('/(\/\/[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(srt|vtt)))/', $filelink, $s))
    $srt="https:".$s[1];
  $id="";
  $link="";
   if (preg_match("/\/[ef]\/([0-9a-zA-Z\_\-]+)/",$filelink,$m)) {
   $id=$m[1];
   }
   if ($id) {
   include ("tear.php");
   $ua="Mozilla/5.0 (Windows NT 10.0; rv:90.0) Gecko/20100101 Firefox/90.0";
   $ua="MXPlayer";
   $ua = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
   $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Alt-Used: videovard.sx',
   'Connection: keep-alive',
   'Referer: https://videovard.sx');
   $l="https://videovard.sx/api/make/hash/".$id;
   /*
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, $ua);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
   */
  $options = array(
        'http' => array(
        'header'  => array($head),
        'method'  => 'GET'
    )
  );
  $context  = stream_context_create($options);
  $h = @file_get_contents($l, false, $context);
  
   $x=json_decode($h,1);
   if (isset($x['hash'])) {
   $hash=$x['hash'];
   $l="https://videovard.sx/api/player/setup";
   //$post="cmd=get_stream&file_code=".$id."&hash=".$hash;
   $q=array("cmd" => "get_stream",
      "file_code" => $id,
      "hash" => $hash);
   $post=http_build_query($q);
   $head=array('Accept: application/json, text/plain, */*',
   'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
   'Accept-Encoding: deflate',
   'Content-Length: '.strlen($post),
   'Origin: https://videovard.sx',
   'Alt-Used: videovard.sx',
   'Connection: keep-alive',
   'Referer: https://videovard.sx');
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  */
  $options = array(
        'http' => array(
        'header'  => array($head),
        'method'  => 'POST',
        'content' => $post,
    )
  );
  $context  = stream_context_create($options);
  $h = @file_get_contents($l, false, $context);
  //echo $h;
  $y=json_decode($h,1);
  print_r ($y);
  if (isset($y['src'])) {
  $src=$y['src'];
  $seed=$y['seed'];
  // https://videovard.sx/_nuxt/daa1d94.js
  $out='var chars={"0":"5","1":"6","2":"7","5":"0","6":"1","7":"2"};';
  $out=str_replace("'",'"',$out);
  $t1=explode('var chars=',$out);
  $t2=explode(';',$t1[1]);
  $e="\$chars='".$t2[0]."';";
  eval ($e);
  $rep="/[012567]/";
  $x=json_decode($chars,1);
  $seed=preg_replace_callback(
    $rep,
    function ($m) {
      global $x;
      return $x[$m[0]];
    },
    $seed
  );
  $link = decrypt($src,$seed);
  $link=preg_replace_callback(
    $rep,
    function ($m) {
      global $x;
      return $x[$m[0]];
    },
    $link
  );
  }
  }
  }
  echo $link."<BR>";
  $head=array('Origin: https://videovard.sx',
  'Referer: https://videovard.sx');
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h."<BR>";
  */
  $options = array(
        'http' => array(
        'header'  => array($head),
        'method'  => 'GET'
    )
  );
  /*
  $context  = stream_context_create($options);
  $h = @file_get_contents($link, false, $context);
  echo $h."<BR>";
  //echo $h;
  //$h=file_get_contents($link);
  $link=get_max_res($h,$link);
  echo $link;
  die();
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,$filelink);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  echo $h."<BR>";
  die();
  file_put_contents("lava.m3u8",$h);
  preg_match("/http.+/",$h,$s);
  $link=trim($s[0]);
  //$link=str_replace("https","http",$link);
  /*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch,CURLOPT_REFERER,$filelink);
  //curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //$h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  */
  $options = array(
        'http' => array(
        'header'  => array($head),
        'method'  => 'GET'
    )
  );
  /*
  $context  = stream_context_create($options);
  $h = @file_get_contents($link, false, $context);
  //echo $h;
  echo $h."<BR>";
  */
  if ($link)
   $link=$link."|Referer=".urlencode("https://videovard.sx")."&Origin=".urlencode("https://videovard.sx");
  echo '<a href="'.$link.'">xxxx</a>'."<BR>";
  echo '<a href="http://127.0.0.1:8080/scripts/filme/lava.m3u8">yyyyy</a>'."<BR>";
}
echo $link;
?>
