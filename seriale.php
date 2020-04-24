<!DOCTYPE html>
<?php
include ("common.php");
if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Seriale</title>
<link rel="stylesheet" type="text/css" href="custom.css" />
<script src="//code.jquery.com/jquery-2.0.2.js"></script>
<style>
td {
    font-style: bold;
    font-size: 20px;
    text-align: left;
}
</style>
<script type="text/javascript">
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "50") {
      <?php
      if (file_exists($base_pass."tmdb.txt"))
       echo 'window.open("filme/search_tmdb.php?page=1");';
      else
       echo 'window.open("filme/search_imdb.php");';
      ?>
    }
   }
document.onkeypress =  zx;
</script>
</head>
<BODY>

<BR><BR>
<table id="data" border="1" align="center" width="90%">
<TR>
<th class="cat" colspan="4">Seriale online</Th>
</TR>
<TR>
<TD width="25%"><a href="filme/filmeseriale_main.php?page=1&tip=release&title=filmeseriale.online&link=" target="_blank">filmeseriale.online</a></TD>
<TD width="25%"><a href="filme/tvhub_s.php?page=1&tip=release&title=tvhub&link=" target="_blank">tvhub</a></TD>
<TD width="25%"><a href="filme/filmeonlinegratis_s.php?page=1&tip=release&title=filmeonlinegratis&link=" target="_blank">filmeonlinegratis</a></TD>
<TD width="25%"><a href="filme/filmeseriale_eu.php?page=1&tip=release&title=filmeseriale.eu&link=" target="_blank">filmeseriale.eu</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/veziseriale_s.php?page=1&tip=release&title=veziseriale.info&link=" target="_blank">veziseriale.info</a></TD>
<TD width="25%"><a href="filme/filme--online.php?page=1&tip=release&title=filme--online&link=" target="_blank">filme--online</a></TD>
<TD width="25%"><a href="filme/divxfilmeonline_s.php?page=1&tip=release&title=divxfilmeonline&link=" target="_blank">divxfilmeonline</a></TD>
<TD width="25%"><a href="filme/serialeonlinesubtitrate_s.php?page=1&tip=release&title=serialeonlinesubtitrate&link=" target="_blank">serialeonlinesubtitrate</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/vezifs_s.php?page=1&tip=release&title=vezifs&link=" target="_blank">vezifs</a></TD>
<TD width="25%"><a href="filme/serialeonline.php?page=1&tip=release&title=serialeonline&link=" target="_blank">serialeonline</a></TD>
<TD width="25%"</TD>
<TD width="25%"><a href="filme/rovideo.php?page=1&tip=release&title=rovideo&link=" target="_blank">rovideo</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/fsgratis_s.php?page=1&tip=release&title=fsgratis&link=" target="_blank">fsgratis</a></TD>
<TD width="25%"><a href="filme/clicksud_main.php" target="_blank">clicksud (seriale romanesti)</a></TD>
<TD width="25%"></TD>
<TD width="25%"></TD>
</TR>
<!-- straine -->
<TR>
<TD width="25%"><a href="filme/tvseries_s.php?page=1&tip=release&title=tvseries&link=" target="_blank">tvseries</a></TD>
<TD width="25%"><a href="filme/gomovies_s.php?page=1&tip=release&title=gomovies&link=" target="_blank">gomovies</a></TD>
<TD width="25%"><a href="filme/hdfull_s.php?page=1&tip=release&title=hdfull&link=" target="_blank">hdfull</a></TD>
<TD width="25%"><a href="filme/5movies_s.php?page=1&tip=release&title=5movies&link=" target="_blank">5movies</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/swatchseries_s.php?page=1&tip=release&title=swatchseries&link=" target="_blank">swatchseries</a></TD>
<TD width="25%"><a href="filme/soap2day_s.php?page=1&tip=release&title=soap2day&link=" target="_blank">soap2day</a></TD>
<TD width="25%"><a href="filme/cineplex_s_main.php" target="_blank">cineplex (cont)</a></TD>
<TD width="25%"></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/seriesfreetv_s.php?page=1&tip=release&title=seriesfreetv&link=" target="_blank">seriesfreetv</a></TD>
<TD width="25%"><a href="filme/europix_s.php?page=1&tip=release&title=europix&link=" target="_blank">europix</a></TD>
<TD width="25%"><a href="filme/ling_s.php?page=1&tip=release&title=ling.online&link=" target="_blank">ling.online</a></TD>
<TD width="25%"><a href="filme/vidcloud9_s.php?page=1&tip=release&title=vidcloud9&link=" target="_blank">vidcloud9</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/ffmovies_s1.php?page=1&tip=release&title=ffmoviess&link=" target="_blank">ffmovies</a></TD>
<TD width="25%"><a href="filme/go2watch_s.php?page=1&tip=release&title=go2watch&link=" target="_blank">go2watch</a></TD>
<TD width="25%"><a href="filme/moviesjoy_s.php?page=1&tip=release&title=moviesjoy&link=" target="_blank">moviesjoy</a></TD>
<TD width="25%"></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/subsmovies_s.php?page=1&tip=release&title=subsmovie&link=" target="_blank">subsmovies</a></TD>
<TD width="25%"><a href="filme/xmovies8_s.php?page=1&tip=release&title=xmovies8&link=" target="_blank">xmovies8</a></TD>
<TD width="25%"><a href="filme/solarmoviesonline_s.php?page=1&tip=release&title=solarmoviesonline&link=" target="_blank">solarmoviesonline</a></TD>
<TD width="25%"><a href="filme/flixtor_s.php?page=1&tip=release&title=flixtor&link=" target="_blank">flixtor</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/movie4k_s.php?page=1&tip=release&title=movie4k&link=" target="_blank">movie4k</a></TD>
<TD width="25%"><a href="filme/yesmovies_s.php?page=1&tip=release&title=yesmovies&link=" target="_blank">yesmovies</a></TD>
<TD width="25%"><a href="filme/vumoo_s.php?page=1&tip=release&title=vumoo&link=" target="_blank">vumoo</a></TD>
<TD width="25%"><a href="filme/dwatchmovies_s.php?page=1&tip=release&title=dwatchmovies&link=" target="_blank">dwatchmovies</a></TD>
</TR>

<?php
if (file_exists($base_pass."tvplay.txt")) {
echo '<TR>';
echo '<TD width="25%"><a href="filme/cineplex_s_main1.php" target="_blank">cineplex</a></TD>';
echo '<TD width="25%"></TD>';
echo '<TD width="25%"></TD>';
echo '<TD width="25%"></TD>';
echo '</TR>';
}
if (file_exists($base_pass."debug.txt")) {
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
echo '<TR>';
echo '<TD width="25%"><a href="filme/archive_ep.php?title='.urlencode("Planet of the Apes").'&link=https://archive.org/embed/PlanetOfTheApesTVSeries&sezon=1&imdb=tt0071033" target="_blank">Planet of the Apes</a></TD>';
echo '<TD width="25%"></TD>';
echo '<TD width="25%"></TD>';
echo '<TD width="25%"></TD>';
echo '</TR>';
}
?>
<!--<TD width="25%"></TD>-->
<!--<TD width="25%"></TD>-->

</table>
<BR><BR>
<?php
if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
if ($tast=="DA") {
echo '
<table id="data" border="0" align="center" width="90%">
<TR><TD width="100%">* Folositi tasta 1 pentru informatii despre film/serial. Apasati "OK" pentru a inchide info.<TD></TR>
<TR><TD>* Folositi tasta 3 pentru a adauga/sterge la favorite (daca exista).<TD></TR>
<TR><TD>* Folositi tasta 2 pentru a accesa direct pagina de "Favorite".<TD></TR>
<TR><TD>* Folositi tasta 5 pentru a simula butonul de cautare.<TD></TR>
</TABLE>';
} else {
echo '
<table id="data" border="0" align="center" width="90%">
<TR><TD>* Folositi ctrl+click pentru informatii despre film/serial. Apasati "OK" pentru a inchide info.<TD></TR>
</TABLE>';
}
if (!file_exists($base_pass."tmdb.txt") && !file_exists($base_pass."omdb.txt")) {
echo '<table border="0" align="center" width="90%">
<TR><TD>* Pentru rezultate mai rapide la "info film/serial" folositi TMDB sau/si OMDB (vezi setari).</TR></TD>
</TABLE>
';
}
?>
</BODY>
</HTML>
