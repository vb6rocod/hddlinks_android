<?php
 /* get youtube video (live or not)
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
 */
function youtube($file) {
function reverse($a,$b) {
	return  array_reverse($a);
}

function length($a,$b) {
	$tS = $a[0];
	$a[0] = $a[$b % count($a)];
	$a[$b] = $tS;
	return  $a;
}
function splice($a,$b) {
	return  array_slice($a,$b);
}
$ua="Mozilla/5.0 (Windows NT 10.0; rv:82.0) Gecko/20100101 Firefox/82.0";
$ua='Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36';
$ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0";
//$ua = $_SERVER['HTTP_USER_AGENT'];
if(preg_match('/youtube\.com\/(v\/|watch\?v=|embed\/)([\w\-]+)/', $file, $match)) {
  $id = $match[2];
  $l = "https://www.youtube.com/watch?v=".$id;
  $html="";
  $p=0;
  //echo $l;
  while($html == "" && $p<10) {

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);

  //$html=file_get_contents($l);
  $p++;
  }
  //echo $html;
  $t1=explode('jsUrl":"',$html);
  $t2=explode('"',$t1[1]);
  $js_url="https://www.youtube.com".$t2[0];
  //echo $js_url."\n";
  //die();
  $parts=array();
  $r1=array();
  if (strpos($html,"ytplayer.config =") !== false) {
    $html = str_between($html,'ytplayer.config = ',';ytplayer.web_player_context_config');
    $parts = json_decode($html,1);
    $r1=json_decode($parts['args']['player_response'],1);
  } else {
    $html=trim(str_between($html,'var ytInitialPlayerResponse = ',';var meta'))."";
    $parts = json_decode($html,1);
    $r1=$parts;
  }
  //echo $html;
  //$parts = json_decode($html,1);



  //print_r ($r1);
  //die();
  //$r1=json_decode($parts[streamingData]

  if (isset($r1['streamingData']["hlsManifestUrl"])) {
      $url=$r1['streamingData']["hlsManifestUrl"];

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h = curl_exec($ch);
      curl_close($ch);

      $a1=explode("\n",$h);
      if (preg_match("/\.m3u8/",$h)) {
       preg_match_all("/RESOLUTION\=(\d+)/i",$h,$m);
       $max_res=max($m[1]);
       for ($k=0;$k<count($a1);$k++) {
        if (strpos($a1[$k],$max_res) !== false) {
         $r=trim($a1[$k+1]);
         break;
        }
       }
      }
    return $r;
  } else {
  $videos=array();
  if (isset($r1['streamingData']['formats'][0]['url'])) {
  for ($k=0;$k<count($r1['streamingData']['formats']);$k++) {
    $videos[$r1['streamingData']['formats'][$k]['itag']]=$r1['streamingData']['formats'][$k]['url'];
  }
  } else if (isset($r1['streamingData']['formats'][0]['cipher'])) {
  for ($k=0;$k<count($r1['streamingData']['formats']);$k++) {
    $t1=explode("url=",$r1['streamingData']['formats'][$k]['cipher']);
    $videos[$r1['streamingData']['formats'][$k]['itag']]=urldecode($t1[1])."&".$t1[0];
  }
  } else if (isset($r1['streamingData']['formats'][0]['signatureCipher'])) {
  for ($k=0;$k<count($r1['streamingData']['formats']);$k++) {
    $t1=explode("url=",$r1['streamingData']['formats'][$k]['signatureCipher']);
    $videos[$r1['streamingData']['formats'][$k]['itag']]=urldecode($t1[1])."&".$t1[0];
  }
  }
  if (isset($r1['streamingData']['adaptiveFormats'][0]['url'])) {
  for ($k=0;$k<count($r1['streamingData']['adaptiveFormats']);$k++) {
    $videos[$r1['streamingData']['adaptiveFormats'][$k]['itag']]=$r1['streamingData']['adaptiveFormats'][$k]['url'];
  }
  }
  //print_r ($videos);
  if (isset($videos['2471']))
    $video= $videos['247'];
  else if (isset($videos['37']))
    $video= $videos['37'];
  else if (isset($videos['22']))
    $video= $videos['22'];
  else if (isset($videos['18']))
    $video= $videos['18'];
  else
    $video="";
  //$audio= $videos['249'];
  if ($video) {
  parse_str($video, $output);
  //print_r ($output);
  if (!isset($output['s'])) {
    $r=$video;
    //$r=$video."|||".$audio;
  } else {
  $sA="";
  $s=$output["s"];
  $tip=$output["sp"];
  //$l = "https://s.ytimg.com".$parts['assets']['js'];
  //$l = "https://www.youtube.com".$parts['assets']['js'];
  //echo "===========".$l;
  $l=$js_url;
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:82.0) Gecko/20100101 Firefox/82.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  preg_match("/a\=a\.split\(\"\"\);(.*?)return a\.join\(\"\"\)/",$h,$m);
  $code=$m[1];
  //echo $code;
  $pat="/(\w+\.(\w+))\((\w+)\,(\w+)\);/";
  preg_match_all($pat,$code,$m);
  $pat1=implode("|", $m[2]);
  preg_match_all("/(".$pat1.")\:function.*?(splice|length|reverse)/",$h,$f);
  global $func;   // if this file is included include ("youtube.php");
  $func = array_combine($f[1], $f[2]);
  $code=preg_replace_callback(
    "/(\w+\.(\w+))\((\w+)\,(\w+)\);/",
    function ($match) {
     global $func;
     return "\$sA=".$func[$match[2]]."(\$sA,".$match[4].");";
    },
    $code
  );
  //echo $s."\n";
  $sA = str_split($s);
  //echo $code;
  eval ($code);
  $sA = implode($sA);
  //echo $sA;
  $signature = $sA;
  $r=$video."&".$tip."=".$signature;
  }
 } else {
  $r="";
 }
 //if ($r) $r =str_replace(parse_url($r)['host'],"redirector.googlevideo.com",$r);
 return $r;
}
} else
  return "";
}
?>
