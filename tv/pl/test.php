<?php
$l="romania-tv.m3u";
//$l="Frecvente.m3u";
//$l="tv_channels_baran-yigitalp_plus.m3u";
//$l="weneKCFY.m3u";
//$l="https://raw.githubusercontent.com/onigetoc/iptv-playlists/master/general/tv/us.m3u";
$m3ufile = file_get_contents($l);
$m3ufile = str_replace("\n","",$m3ufile);
echo $m3ufile;
$re = '/#EXTINF:(.+?)[,]\s?(.+?)[\r\n]*?((?:https?|rtmp):\/\/(?:\S*?\.\S*?)(?:[\s)\[\]{};"\'<]|\.\s|$))/s';
//$attributes = '/([a-zA-Z0-9\-]+?)="([^"]*)"/';
$attributes = '/([a-zA-Z0-9\-\_]+?)="([^"]*)"/';
preg_match_all($re, $m3ufile, $m);
print_r ($m);
$out="";
$lines = file($l);
foreach ($lines as $n => $line) {
 if (preg_match("/(#EXTINF)|(^http)/",$line))
  $out .=$line;
}
echo $out;

?>
