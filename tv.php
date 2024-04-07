<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Live TV</title>
<link rel="stylesheet" type="text/css" href="custom.css" />
<style>
td {
    font-style: bold;
    font-size: 20px;
    text-align: left;
}
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="text/javascript">
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  var the_data = "file=" + link;
  var php_file="tv/playlist_del.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
       alert (request.responseText);
       location.reload();
    }
  }
}
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode,
    self = evt.target;
    if  (charCode == "49") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      ajaxrequest(val_fav);
    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
</script>
</head>
<BODY>
<BR><BR>
<table border="1" align="center" width="90%">
<TR>
<th class="cat" colspan="4">Live TV şi emisiuni TV</Th>
</TR>
<TR>
<TD width="25%"></TD>
<TD width="25%"><a href="tv/tvcanale.php" target="_blank">tvcanale</TD>
<!--<TD width="25%"><a href="tv/digi.php" target="_blank">digi-online</a></TD>-->
<!--<TD width="25%"><a href="tv/playlist.php?title=TVR.m3u" target="_blank">TVR Live</a></TD>-->
<TD width="25%"><a href="tv/tvrlive.php" target="_blank">TVR Live</a></TD>
<TD width="25%"><a href="tv/tvrstiri.php" target="_blank">TVR - Stiri</a></TD>

</TR>
<TR>
<TD width="25%"><a href="tv/digi24.php" target="_blank">Digi24 - Stiri</a></TD>
<TD width="25%"><a href="tv/digi24_main.php" target="_blank">Digi24 - Emisiuni</a></TD>
<TD width="25%"><a href="tv/digisport.php?page=1,https://www.digisport.ro/video,DigiSport" target="_blank">DigiSport</a></TD>
<TD width="25%"><a href="tv/protv_stiri.php?page=1,,PROTV" target="_blank">PROTV</a></TD>
</TR>
<TR>
<TD width="25%"><a href="tv/privesc.php?page=1&link=https://www.privesc.eu/arhiva/categorii/Toate&title=privesc.eu" target="_blank">privesc.eu</a></TD>
<TD width="25%"><a href="tv/b1_main.php?page=1&link=&title=B1TV" target="_blank">B1 Emisiuni</a></TD>
<TD width="25%"><a href="tv/europalibera.php?page=1,,europalibera" target="_blank">europalibera</a></TD>
<TD width="25%"><a href="tv/tvrplus_main.php" target="_blank">TVR+ (Emisiuni)</a></TD>
</TR>
<TR>
<TD width="25%"><a href="tv/program.php" target="_blank">ProgramTV</a></TD>
<TD width="25%"><a href="tv/primaplay.php?page=1&link=&title=primaplay" target="_blank">primaplay</a></TD>
<TD width="25%"><a href="tv/protvmd.php?page=1,,ProTV Moldova" target="_blank">PROTV Moldova</a></TD>
<TD width="25%"><a href="tv/inprofunzime.php?page=1,,IN+PROfunzime" target="_blank">IN PROfunzime</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/youtube_fav.php" target="_blank">youtube</a></TD>
<TD width="25%"><a href="filme/youtube_live.php?token=&search=" target="_blank">youtube live</a></TD>
<TD width="25%"><a href="tv/iptv.php" target="_blank">IPTV International</a></TD>
<TD width="25%"><a href="tv/rds.php?title=emisiuni.live&link=https://emisiuni.live/canale-tv-1/" target="_blank">emisiuni.live</TD>
</TR>

<TR>
<TD width="25%"><a href="tv/canalelive.php" target="_blank">Sport</a></TD>
<!--<TD width="25%"><a href="tv/time4tv.php?page=1&tip=release&title=time4tv&link=" target="_blank">time4tv</a></TD>-->
<!--<TD width="25%"><a href="tv/primasport.php" target="_blank">Primasport</TD>-->
<TD width="25%"><a href="tv/telefootball.php" target="_blank">FOTBAL LA TV</TD>
<TD width="25%"><a href="tv/rds.php?title=rds.live&link=https://rds.live/canale-tv/" target="_blank">rds.live</TD>
<TD width="25%"><a href="tv/tvonline.php" target="_blank">tvonline</TD>
</TR>
<TR>
<TD width="25%"><a href="tv/sons-stream.php" target="_blank">sons-stream</a></TD>
<TD width="25%"><a href="tv/sportsonline.php" target="_blank">sportsonline</a></TD>
<TD width="25%"><a href="tv/dlhd.php" target="_blank">DaddyLiveHD</a></TD>
<TD width="25%"><a href="tv/stream2watch.php" target="_blank">stream2watch</a></TD>
</TR>

<?php
include ("common.php");
if (file_exists($base_pass."tvplay.txt")) {
echo '
<TR>
<TD width="25%"><a href="filme/facebook1_fav.php" target="_blank">facebook1</a></TD>
<TD width="25%"><a href="filme/facebook2_fav.php" target="_blank">facebook2</a></TD>
<TD width="25%"></TD>
<TD width="25%"></TD>'
;
echo '</TR>';
}
$list = glob($base_sub."*.srt");
foreach ($list as $l) {
    str_replace(" ","%20",$l);
     unlink($l);
}
if (file_exists($base_sub.".srt")) unlink ($base_sub.".srt");
?>
<!--
<TR>
<TD width="25%"><a href="tv/arconaitv.php" target="_blank">arconaitv</a></TD>
<TD width="25%"><a href="http://hdforall.000webhostapp.com/live/privesc.php?page=1" target="_blank">privesc.eu (alt)</a></TD>
<TD width="25%"></TD>
<TD width="25%"></TD>
</TR>
-->
</table>
<BR>
<table border="1" align="center" width="90%">
<TR>
<th class="cat" colspan="4">My Playlist</Th>
</TR>
<TR>
<?php
//include ("common.php");
$list = glob($base_sub."*.srt");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
$n=0;
$w=0;
if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
$out="";
$base="tv/pl/";
$list = glob($base."*.m3u");
   foreach ($list as $l) {
    //$l = str_replace(" ","%20",$l);
    $title =  basename($l);
    if ($title <> "TVR.m3u") {
    $out=$out."#EXTINF:-1, ".$title."\n"."http://hdforall.000webhostapp.com/live/".$title."\n";
    $link="tv/playlist.php?title=".urlencode($title);
    if (strlen($title) > 25)
    $title1=substr($title,0,22)."...";
    else
    $title1=$title;
    if ($tast == "NU")
    echo '<td align="center" width="25%"><a href="'.$link.'" target="_blank">'.$title1.'</a> <a onclick="ajaxrequest('."'".$title."'".')" style="cursor:pointer;">*</a></TD>';
    else
    echo '<td align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title1.'<input type="hidden" id="fav_myLink'.($w*1).'" value="'.$title.'"></a></TD>';
    $n++;
    $w++;
    }
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
//echo $out;
?>
</TABLE>

<?php
if (!$list || count($list) == 1) {
echo '
<table border="1" align="center" width="90%"><TR><TD>
Adaugati liste m3u cu streamuri live in directorul scripts/tv/pl.
Adaugati manual (copiere) sau de pe PC http://ip:8080/scripts/fm.php, unde "ip" este ip-ul dispozitivului pe care este instalat HD4ALL<BR>Apasati tasta 1 pentru a sterge playlist-ul selectat</TD></TR></TABLE>';
} else {
echo '
<table border="1" align="center" width="90%"><TR><TD>
Apasati tasta 1 pentru a sterge playlist-ul selectat</TD></TR></TABLE>';
}

?>
</BODY>
</HTML>
