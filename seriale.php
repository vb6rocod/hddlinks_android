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

<?php
if ($tast=="DA1" && $flash=="mp")
echo '<TD width="25%"><a href="filme/filmeseriale2_main.php?page=1&file=release&title=filmeseriale.online" target="_blank">filmeseriale.online (toate)</TD>';
else
echo '<TD width="25%"><a href="filme/filmeseriale_main.php?page=1&file=release&title=filmeseriale.online" target="_blank">filmeseriale.online (toate)</TD>';
?>
<TD width="25%"><a href="filme/filmeseriale1_main.php?page=1&file=release&title=filmeseriale.online" target="_blank">filmeseriale.online</TD>
<TD width="25%"><a href="filme/tvhub_s.php?page=1&file=release&title=tvhub" target="_blank">tvhub</TD>
<TD width="25%"><a href="filme/gold_s.php?page=1&file=release&title=filme-seriale.gold" target="_blank">filme-seriale.gold</TD>
</TR>
<TR>
<TD width="25%"><a href="filme/veziseriale_s_fav.php" target="_blank">veziseriale.info</a></TD>
<TD width="25%"><a href="filme/filmeto_s.php?page=1&tip=release&title=filme-online.to" target="_blank">filme-online.to</a></TD>
<TD width="25%"><a href="filme/divxfilmeonline_s_fav.php" target="_blank">divxfilmeonline</a></TD>
<TD width="25%"><a href="filme/filmeseriale-hd.php?page=1&tip=release&title=filmeseriale-hd" target="_blank">filmeseriale-hd</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/filmetop.php?page=1&tip=release&title=filmetop" target="_blank">filmetop</a></TD>
<TD width="25%"><a href="filme/filme--online.php?page=1&tip=release&title=filme--online" target="_blank">filme--online</a></TD>
<TD width="25%"><a href="filme/filmeseriale_eu.php?page=1&tip=release&title=filmeseriale.eu" target="_blank">filmeseriale.eu</a></TD>
<TD width="25%"></TD>
</TR>
<!-- straine -->
<TR>
<TD width="25%"><a href="filme/tvseries_s.php?page=1&file=release&title=tvseries" target="_blank">tvseries</a></TD>
<TD width="25%"><a href="filme/putlockerfit_s.php?page=1&file=release&title=putlockerfit" target="_blank">putlockerfit</TD>
<TD width="25%"><a href="filme/popcorn_s.php?page=1&file=release&title=seriale+noi" target="_blank">Popcorn (torrent)</a></TD>
<TD width="25%"><a href="filme/hdfull_s.php?page=1&file=release&title=hdfull" target="_blank">hdfull</TD>
</TR>
<TR>
<TD width="25%"><a href="filme/seriestop_s.php?page=1&tip=release&title=seriestop" target="_blank">seriestop</a></TD>
<TD width="25%"><a href="filme/swatchseries_s.php?page=1&tip=release&title=swatchseries" target="_blank">swatchseries</a></TD>
<TD width="25%"><a href="filme/subsmovies_s.php?page=1&file=release&title=subsmovies" target="_blank">subsmovies</a></TD>
<TD width="25%"><a href="filme/cineplex_s_main.php" target="_blank">cineplex (cont)</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/openloadmovies_s.php?page=1&file=release&title=openloadmovies" target="_blank">openloadmovies</TD>
<TD width="25%"><a href="filme/seriesfreetv_s.php?page=1&tip=release&title=seriesfreetv" target="_blank">seriesfreetv</a></TD>
<TD width="25%"><a href="filme/moviesjoy_s.php?page=1&tip=release&title=moviesjoy" target="_blank">moviesjoy</a></TD>

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
