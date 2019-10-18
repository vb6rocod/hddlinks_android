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
<TD width="25%"><a href="filme/gold_s.php?page=1&tip=release&title=filme-seriale.gold&link=" target="_blank">filme-seriale.gold</a></TD>
<TD width="25%"><a href="filme/filmeseriale_eu.php?page=1&tip=release&title=filmeseriale.eu&link=" target="_blank">filmeseriale.eu</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/veziseriale_s.php?page=1&tip=release&title=veziseriale.info&link=" target="_blank">veziseriale.info</a></TD>
<TD width="25%"><a href="filme/filme--online.php?page=1&tip=release&title=filme--online&link=" target="_blank">filme--online</a></TD>
<TD width="25%"><a href="filme/divxfilmeonline_s.php?page=1&tip=release&title=divxfilmeonline&link=" target="_blank">divxfilmeonline</a></TD>
<TD width="25%"><a href="filme/serialeonlinesubtitrate_s.php?page=1&tip=release&title=serialeonlinesubtitrate&link=" target="_blank">serialeonlinesubtitrate</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/filmetop.php?page=1&tip=release&title=filmetop&link=" target="_blank">filmetop</a></TD>
<TD width="25%"><a href="filme/serialeonline.php?page=1&tip=release&title=serialeonline&link=" target="_blank">serialeonline</a></TD>
<TD width="25%"><a href="filme/filmeserialeonline.php?page=1&tip=release&title=filmeserialeonline&link=" target="_blank">filmeserialeonline</a></TD>
<TD width="25%"><a href="filme/rovideo.php?page=1&tip=release&title=rovideo&link=" target="_blank">rovideo</a></TD>
</TR>
<!-- straine -->
<TR>
<TD width="25%"><a href="filme/tvseries_s.php?page=1&tip=release&title=tvseries&link=" target="_blank">tvseries</a></TD>
<TD width="25%"><a href="filme/putlockerfit_s.php?page=1&tip=release&title=putlockerfit&link=" target="_blank">putlockerfit</a></TD>
<TD width="25%"><a href="filme/gomovies_s.php?page=1&tip=release&title=gomovies&link=" target="_blank">gomovies</a></TD>
<TD width="25%"><a href="filme/hdfull_s.php?page=1&tip=release&title=hdfull&link=" target="_blank">hdfull</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/seriestop_s.php?page=1&tip=release&title=seriestop&link=" target="_blank">seriestop</a></TD>
<TD width="25%"><a href="filme/swatchseries_s.php?page=1&tip=release&title=swatchseries&link=" target="_blank">swatchseries</a></TD>
<TD width="25%"><a href="filme/subsmovies_s.php?page=1&tip=release&title=subsmovies&link=" target="_blank">subsmovies</a></TD>
<TD width="25%"><a href="filme/cineplex_s_main.php" target="_blank">cineplex (cont)</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/seriesfreetv_s.php?page=1&tip=release&title=seriesfreetv&link=" target="_blank">seriesfreetv</a></TD>
<TD width="25%"><a href="filme/moviesjoy_s.php?page=1&tip=release&title=moviesjoy&link=" target="_blank">moviesjoy</a></TD>
<TD width="25%"><a href="filme/onmovies_s.php?page=1&tip=release&title=onmovies&link=" target="_blank">onmovies</a></TD>
<TD width="25%"><a href="filme/europix_s.php?page=1&tip=release&title=europix&link=" target="_blank">europix</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/0123netflix_s.php?page=1&tip=release&title=0123netflix&link=" target="_blank">0123netflix</a></TD>
<TD width="25%"><a href="filme/vipmovies_s.php?page=1&tip=release&title=vipmovies&link=" target="_blank">vipmovies</a></TD>
<TD width="25%"><a href="filme/ffmovies_s.php">ffmovies</a></TD>
<TD width="25%"></TD>
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
