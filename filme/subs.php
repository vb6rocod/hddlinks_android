<?php
//echo $sub_link;
parse_str($sub_link,$x);
$tt=$base_cookie."tt.txt";
if (file_exists($tt)) unlink ($tt);
$imdb=$x['imdb'];
$year=$x['year'];
$tip=$x['tip'];
$title=$x['title'];
if ($tip=="serie" || $tip=="series") $tip="tv";
unset($x['imdb']);
$sub_link1=http_build_query($x)."&imdb=";
echo '<br>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;font-weight: bold;font-size: 1.5em" align="center" colspan="6">Alegeti o subtitrare (cauta titlu)</td></TR>';
echo '<TR>';
//echo '<TD class="mp"><a id="opensub" href="opensubtitles.php?'.$sub_link1.'">opensubtitles</a></td>';
echo '<TD class="mp"><a id="opensub" href="opensubtitles_new.php?page=1&'.$sub_link1.'">opensub_new</a></td>';
echo '<TD class="mp"><a id="opensub2" href="rest.php?page=1&'.$sub_link1.'">rest.api</a></td>';
echo '<TD class="mp"><a id="titrari" href="titrari_main.php?page=1&'.$sub_link1.'&page=1">titrari.ro</a></td>';
echo '<TD class="mp"><a id="subs" href="subs_main.php?'.$sub_link1.'">subs.ro</a></td>';
echo '<TD class="mp"><a id="subtitrari" href="subtitrari_main.php?'.$sub_link1.'">subtitrari_noi.ro</a></td>';
echo '</TR></TABLE>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;font-weight: bold;font-size: 1.5em" align="center" colspan="6">Alegeti o subtitrare (cauta imdb id)</td></TR>';
echo '<TR>';
//echo '<TD class="mp"><a id="opensub1" href="opensubtitles1.php?'.$sub_link.'">opensubtitles</a></td>';
echo '<TD class="mp"><a id="opensub1" href="opensubtitles1_new.php?page=1&'.$sub_link.'">opensub_new</a></td>';
echo '<TD class="mp"><a id="opensub3" href="rest1.php?page=1&'.$sub_link.'">rest.api</a></td>';
echo '<TD class="mp"><a id="titrari1" href="titrari_main1.php?page=1&'.$sub_link.'&page=1">titrari.ro</a></td>';
echo '<TD class="mp"><a id="subs1" href="subs_main1.php?'.$sub_link.'">subs.ro</a></td>';
echo '<TD class="mp"><a id="subtitrari1" href="subtitrari_main1.php?'.$sub_link.'">subtitrari_noi.ro</a></td>';
echo '</TR></TABLE>';
/*
if (!$imdb) {
  if ($tip=="tv")
  $url="https://www.imdb.com/find/?q=".rawurlencode($title)."&s=tt&ttype=tv&exact=true&ref_=fn_tt_ex";
  else
  $url="https://www.imdb.com/find/?q=".rawurlencode($title)."&s=tt&ttype=ft&exact=true&ref_=fn_tt_ex";
  //echo $url."\n";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);

$a='</a><ul class="ipc-inline-list ipc-inline-list--show-dividers ipc-inline-list--no-wrap ipc-inline-list--inline ipc-metadata-list-summary-item__tl base" role="presentation"><li role="presentation" class="ipc-inline-list__item"><span class="ipc-metadata-list-summary-item__li" aria-disabled="false">';
$b=  preg_quote($a);
$z='<div class="ipc-media ipc-media--poster';
$y=  preg_quote($z);
$d='</span></li><li role="presentation" class="ipc-inline-list__item"><span class="ipc-metadata-list-summary-item__li" aria-disabled="false">';
$e=  preg_quote($d);
if ($tip=="tv")
$re='@'.$y.'(.*?)title\/(tt\d+)\/\?ref\_\=fn_tt_tt_\d+\"\>([^\<]+)'.$b."([^\<]+)".$e."([^\<]+)@si";
else
$re='@'.$y.'(.*?)title\/(tt\d+)\/\?ref\_\=fn_tt_tt_\d+\"\>([^\<]+)'.$b."([^\<]+)@si";
//$re=str_replace('"','\"',$re);
//echo $a."\n"."\n".$re."\n";
  preg_match_all($re, $h, $m);
  //print_r ($m);
$bfound=false;
$k=0;
if (count ($m[1])>0) { // find one or more

if (count($m[1])== 1) { // only one
 $bfound=true;
 $k=0;
 if ($tip=="movie")
 $imdb_arr[]=array($m[1][$k],$m[2][$k],$m[3][$k],$m[4][$k],"");
 else
 $imdb_arr[]=array($m[1][$k],$m[2][$k],$m[3][$k],$m[4][$k],$m[5][$k]);
}
if ($year && count($m[1])>1) {  // find many and year is set
//echo $year;
  for ($k=0;$k<count($m[1]);$k++) {
   $year_imdb=substr($m[4][$k],0,4);
   //echo $year_imdb;
   if ($year_imdb == $year) {
    $bfound=true;
    if ($tip=="movie")
     $imdb_arr[]=array($m[1][$k],$m[2][$k],$m[3][$k],$m[4][$k],"");
    else
     $imdb_arr[]=array($m[1][$k],$m[2][$k],$m[3][$k],$m[4][$k],$m[5][$k]);
    break;
   }
   $bfound=false;
  }
}
if ($year && count($m[1])>1 && $bfound==false) {  // find many, year is set but not match, maybe +- one year is good
  for ($k=0;$k<count($m[1]);$k++) {
   $year_imdb=substr($m[4][$k],0,4);
   if (abs($year_imdb-$year) < 2) {
    $bfound=true;
    if ($tip=="movie")
     $imdb_arr[]=array($m[1][$k],$m[2][$k],$m[3][$k],$m[4][$k],"");
    else
     $imdb_arr[]=array($m[1][$k],$m[2][$k],$m[3][$k],$m[4][$k],$m[5][$k]);
    break;
   }
   $bfound=false;
  }
}
if (!$bfound) { // many, year not match
  if ($tip=="movie") {
   for ($k=0;$k<count($m[1]);$k++) {
     $imdb_arr[]=array($m[1][$k],$m[2][$k],$m[3][$k],$m[4][$k],"");
   }
  } else {
    for ($k=0;$k<count($m[1]);$k++) {
     if (preg_match("/serie/i",$m[5][$k])) {
       $imdb_arr[]=array($m[1][$k],$m[2][$k],$m[3][$k],$m[4][$k],$m[5][$k]);
     }
    }
  }
}
}
}
//print_r ($imdb_arr);
if (count($imdb_arr)>1) {
echo '<label id="tt">multiple match. Select one.</label>';
echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\r\n";
  echo "<tr>";
  for ($k=0;$k<min(8,count($imdb_arr));$k++) {
  $title=$imdb_arr[$k][2];
  $year=$imdb_arr[$k][3];
  $imdb=$imdb_arr[$k][1];
  if (preg_match("/src\=\"([^\"]+)/",$imdb_arr[$k][0],$i))
  $img=$i[1];
  else
  $img="blank.jpg";
  if ($tip=="movie")
  echo '<TD class="mp"><a onclick="write_tt('."'".$imdb."'".')" style="cursor:pointer;">'.'<img src="'.$img.'" width="50px" height="74px"><BR>'.$title." (".$year.")".'</a></TD>';
  else
  echo '<TD class="mp"><a onclick="write_tt('."'".$imdb."'".')" style="cursor:pointer;">'.'<img src="'.$img.'"><BR>'.$title."<BR>".$imdb_arr[$k][4]." (".$year.")".'</a></TD>';
}
echo '</TR></TABLE>';
echo '
<script>
function write_tt(tt) {

$.get("write_tt.php?imdb="+ tt, function(data){
  document.getElementById("tt").innerHTML = data;
});
}
</script>';
}
*/
?>
