 <?php
/* resolve powvideo "splice"
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
 * $filelink = "https://powvideo.net/o4xa8jywtx07";
 * $link --> video_link
 */
error_reporting(0);
include ("obfJS.php");
require_once("JavaScriptUnpacker.php");
$filelink=$_GET['file'];
if (strpos($filelink, "powvideo.") !== false || strpos($filelink, "povvideo.") !== false) {
    preg_match('/(powvideo|povvideo)\.(net|cc|co)\/(?:embed-|iframe-|preview-|)([a-z0-9]+)/', $filelink, $m);
    $id       = $m[3];
    $host=parse_url($filelink)['host'];
    $filelink="https://".$host."/embed-".$id.".html";
    $ua = $_SERVER["HTTP_USER_AGENT"];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $filelink);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    if (preg_match("/location:\s*(http.+)/i",$h,$m))
      $host=parse_url(trim($m[1]))['host'];
    $head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
     'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
     'Accept-Encoding: deflate',
     'Connection: keep-alive',
     'Referer: https://'.$host.'/preview-'.$id.'-1280x665.html',
     'Cookie: ref_url='.urlencode($filelink).'; e_'.$id.'=123456789',
     'Upgrade-Insecure-Requests: 1');
    $l = "https://".$host."/iframe-" . $id . "-954x562.html";

    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_NOBODY,0);
    $h = curl_exec($ch);
    curl_close($ch);
} elseif (preg_match("/str?eamplay\./i",$filelink)) {
    preg_match('/(?:\/\/|\.)(str?eamplay\.(?:to|club|top|me))\/(?:embed-|player-)?([0-9a-zA-Z]+)/', $filelink, $m);
    $id=$m[2];
    $ua       = $_SERVER["HTTP_USER_AGENT"];
    $host=parse_url($filelink)['host'];
    $l1="https://".$host."/embed-".$id.".html";
    $ua = $_SERVER["HTTP_USER_AGENT"];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_NOBODY,1);

    curl_setopt($ch, CURLOPT_REFERER, $filelink);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    if (preg_match("/location:\s*(http.+)/i",$h,$m))
      $host=parse_url(trim($m[1]))['host'];
    $head=array('Cookie: file_id=13136922; ref_yrp=; ref_kun=1; BJS0=1');
    $l="https://".$host."/player-".$id.".html";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    curl_setopt($ch, CURLOPT_REFERER, $l1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
}

    $jsu   = new JavaScriptUnpacker();
    $out   = $jsu->Unpack($h);
    $srt="";
    if (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.mp4))/', $out, $m)) {
        $link = $m[1];
        $t1   = explode("/", $link);
        $a145 = $t1[3];
    if (preg_match('/([\.\d\w\-\.\/\\\:\?\&\#\%\_]*(\.(srt|vtt)))/', $out, $xx)) {
        $srt = $xx[1];
    if (strpos("http", $srt) === false && $srt)
        $srt = "https://".$host.$srt;
    }

    $enc=$h;
    $h = obfJS();
    $h=str_replace("/js","https://".$host."/js",$h);
    $h=str_replace('function getCalcReferrer',"var xxx='';function getCalcReferrer",$h);
    if (preg_match("/r\[\'join\'\]\(\'\'\);\}\);(return\s*([a-zA-Z0-9_]+));\}\);(return\s*([a-zA-Z0-9_]+)\[\'length\'\])/",$h,$m)) {
     $h=str_replace($m[1],"xxx +='|'+".$m[2]."['file'];".$m[1],$h);
     $replace="
      var request = new XMLHttpRequest();
      var the_data = '';
      var php_file='streamplay.php?link=' + encodeURIComponent(xxx +'|".$srt."');
      request.open('GET', php_file, true);
      request.send(the_data);
     ";
     $h=str_replace($m[3],$replace."parent.$.fancybox.close();".$m[3],$h);
    } elseif (preg_match("/r\[\'join\'\]\(\'\'\);\}\);(return\s*([a-zA-Z0-9_]+));\}\);(return\s*([a-zA-Z0-9_]+));/",$h,$m)) {
     //$h=str_replace($m[1],"xxx +='|'+".$m[2].";".$m[1],$h);
     $replace="
      var request = new XMLHttpRequest();
      var the_data = '';
      var php_file='streamplay.php?link=' + encodeURIComponent(".$m[4]." +'|".$srt."');
      request.open('GET', php_file, true);
      request.send(the_data);
     ";
     $h=str_replace($m[3],$replace."parent.$.fancybox.close();".$m[3],$h);
    }
    echo $h;
} else {
  echo '<script>
  parent.$.fancybox.close();
  </script>';
}

?>
