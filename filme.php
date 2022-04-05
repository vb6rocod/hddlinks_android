<!DOCTYPE html>
<?php
include ("common.php");
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Filme</title>
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
     } else if (charCode == "51" && e.target.type != "text") {
      window.open("filme/filme_fav.php");
    }
   }
document.onkeypress =  zx;
</script>
</head>
<BODY>

<BR><BR>

<table id="data" border="1px" align="center" width="90%">
<TR>
<?php
if ($tast=="NU")
 echo '<th class="cat" colspan="4"><a href="filme/filme_fav.php" target="_blank">Filme online</a></Th>';
else
 echo '<th class="cat" colspan="4">Filme online</Th>';
?>
</TR>
<TR>


<TD width="25%"><a href="filme/voxfilmeonline_main.php" target="_blank">voxfilmeonline</a></TD>
<TD width="25%"><a href="filme/topfilmeonline_main.php" target="_blank">topfilmeonline</a></TD>
<TD width="25%"><a href="filme/divxfilmeonline.php?page=1&tip=release&title=divxfilmeonline&link=" target="_blank">divxfilmeonline</a></TD>
<TD width="25%"><a href="filme/filmehd_main.php" target="_blank">filmehd</a></TD>

</TR>
<TR>
<TD width="25%"><a href="filme/filmeseriale_filme.php?page=1&tip=release&title=filmeseriale&link=" target="_blank">filmeseriale.online</a></TD>
<TD width="25%"><a href="filme/filmeonline2016_main.php" target="_blank">filmeonline2016</a></TD>
<TD width="25%"><a href="filme/serialeonline_f.php?page=1&tip=release&title=serialeonline&link=" target="_blank">serialeonline</a></TD>
<TD width="25%"><a href="filme/portalultautv_main.php" target="_blank">portalultautv</a></TD>
</TR>


<!--- desene si filme...-->
<TR>
<TD width="25%"><a href="filme/filmehd_f.php?page=1&tip=release&title=filmehd&link=" target="_blank">filmehd.to</a></TD>
<TD width="25%"><a href="filme/fsonline_f.php?page=1&tip=release&title=fsonline&link=" target="_blank">fsonline</a></TD>
<TD width="25%"><a href="filme/deseneledublate.php?page=1&tip=release&title=desenedublate&link=" target="_blank">deseneledublate</a></TD>
<TD width="25%"><a href="filme/allmoviesforyou_f.php?page=1&tip=release&title=allmoviesforyou&link=" target="_blank">allmoviesforyou</a></TD>
</TR>

<!-- straine -->
<TR>
<TD width="25%"><a href="filme/tvseries_f.php?page=1&tip=release&title=tvseries&link=" target="_blank">tvseries</a></TD>
<TD width="25%"><a href="filme/moviewetrust_f.php?page=1&tip=release&title=moviewetrust&link=" target="_blank">moviewetrust</a></TD>
<TD width="25%"><a href="filme/cineplex_f_main.php" target="_blank">cineplex (cont)</a></TD>
<TD width="25%"><a href="filme/ling_f.php?page=1&tip=release&title=ling.online&link=" target="_blank">ling.online</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/lookmovie_f.php?page=1&tip=release&title=lookmovie&link=" target="_blank">lookmovie</a></TD>
<TD width="25%"><a href="filme/bmovies_f.php?page=1&tip=release&title=bmovies&link=" target="_blank">bmovies</a></TD>
<TD width="25%"><a href="filme/vidcloud9_f.php?page=1&tip=release&title=vidcloud9&link=" target="_blank">vidcloud9</a></TD>
<TD width="25%"><a href="filme/solarmovie_f.php?page=1&tip=release&title=solarmovie&link=" target="_blank">solarmovie</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/zoechip_f.php?page=1&tip=release&title=zoechip&link=" target="_blank">zoechip</a></TD>
<TD width="25%"><a href="filme/foumovies_f.php?page=1&tip=release&title=foumovies&link=" target="_blank">foumovies</a></TD>
<TD width="25%"><a href="filme/onionplay_f.php?page=1&tip=release&title=onionplay&link=" target="_blank">onionplay</a></TD>
<TD width="25%"><a href="filme/streamlord_f.php?page=1&tip=release&title=streamlord&link=" target="_blank">streamlord</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/uniquestream_f.php?page=1&tip=release&title=uniquestream&link=" target="_blank">uniquestream</a></TD>
<TD width="25%"><a href="filme/sockshare_f.php?page=1&tip=release&title=sockshare&link=" target="_blank">sockshare</a></TD>
<TD width="25%"><a href="filme/fsharetv.php?page=1&tip=release&title=fsharetv&link=" target="_blank">fsharetv</a></TD>
<TD width="25%"><a href="filme/hdonline_f.php?page=1&tip=release&title=hdonline&link=" target="_blank">hdonline</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/yifytv_f.php?page=1&tip=release&title=yifytv&link=" target="_blank">yifytv</a></TD>
<TD width="25%"><a href="filme/flixtor_f.php?page=1&tip=release&title=flixtor&link=" target="_blank">flixtor</a></TD>
<TD width="25%"><a href="filme/gerryreid_f.php?page=1&tip=release&title=gerryreid&link=" target="_blank">gerryreid</a></TD>
<TD width="25%"><a href="filme/lookclub_f.php?page=1&tip=release&title=lookclub&link=" target="_blank">lookclub</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/goojara_f.php?page=1&tip=release&title=goojara&link=" target="_blank">goojara</a></TD>
<TD width="25%"></TD>
<TD width="25%"><a href="filme/trailers_f.php?page=1&tip=release&title=trailers&link=" target="_blank">trailers</a></TD>
<TD width="25%"><a href="filme/ask4movie_f.php?page=1&tip=release&title=ask4movie&link=" target="_blank">ask4movie</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/soap2day_f.php?page=1&tip=release&title=soap2day&link=" target="_blank">soap2day</a></TD>
<TD width="25%"><a href="filme/europix_f.php?page=1&tip=release&title=europix&link=" target="_blank">europix</a></TD>
<TD width="25%"><a href="filme/esubmovie.php?page=1&tip=release&title=esubmovie&link=" target="_blank">esubmovie</a></TD>
<TD width="25%"><a href="filme/123files.php?page=1&tip=release&title=123files&link=" target="_blank">123files</a></TD>
</TR>


<TR>
<TD width="25%"><a href="filme/c1ne_f.php?page=1&tip=release&title=c1ne&link=" target="_blank">c1ne</a></TD>
<TD width="25%"><a href="filme/azseries_f.php?page=1&tip=release&title=azseries&link=" target="_blank">azseries</a></TD>
<TD width="25%"><a href="filme/m4uhd_f.php?page=1&tip=release&title=m4uhd&link=" target="_blank">m4uhd</a></TD>
<TD width="25%"><a href="filme/vexmovies_f.php?page=1&tip=release&title=vexmovies&link=" target="_blank">vexmovies</a></TD>
</TR>




<?php
if (file_exists($base_pass."tvplay.txt")) {
echo '
<TR>
<TD width="25%"><a href="filme/cineplex_f_main1.php" target="_blank">cineplex</a></TD>
<TD width="25%"></TD>
<TD width="25%"></TD>
<TD width="25%"></TD>';
echo '</TR>';
} else {
//echo '<TD width="25%"></TD><TD width="25%"></TD>';
//echo '<TD width="25%"></TD><TD width="25%"></TD>';
}
?>

</TR>
</table>
<BR>
<BR>
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
<TR><TD>* In aceasta pagina folositi tasta 2 pentru cautare, 3 pentru favorite.<TD></TR>
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
