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
<TD width="25%"><a href="filme/onemagia_f.php?page=1&tip=release&title=onemagia&link=" target="_blank">onemagia</a></TD>
<!--<TD width="25%"><a href="filme/portalultautv_main.php" target="_blank">portalultautv</a></TD>-->
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
<TD width="25%"><a href="filme/moviewetrust_f.php?page=1&tip=release&title=moviewetrust&link=popular" target="_blank">moviewetrust</a></TD>
<TD width="25%"><a href="filme/cineplex_f_main.php" target="_blank">cineplex (cont)</a></TD>
<TD width="25%"><a href="filme/ling_f.php?page=1&tip=release&title=ling.online&link=" target="_blank">ling.online</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/lookmovie_f.php?page=1&tip=release&title=lookmovie&link=" target="_blank">lookmovie</a></TD>
<TD width="25%"><a href="filme/2embed_f.php?page=1&tip=release&title=2embed&link=" target="_blank">2embed</a></TD>
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
<TD width="25%"><a href="filme/ask4movie_f.php?page=1&tip=release&title=ask4movie&link=" target="_blank">ask4movie</a></TD>
<TD width="25%"><a href="filme/flixtor_f.php?page=1&tip=release&title=flixtor&link=" target="_blank">flixtor</a></TD>
<TD width="25%"><a href="filme/emovies_f.php?page=1&tip=release&title=emovies&link=" target="_blank">emovies</a></TD>
<TD width="25%"><a href="filme/lookclub_f.php?page=1&tip=release&title=lookclub&link=" target="_blank">lookclub</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/goojara_f.php?page=1&tip=release&title=goojara&link=" target="_blank">goojara</a></TD>
<TD width="25%"><a href="filme/moviesnipipay_f.php?page=1&tip=release&title=moviesnipipay&link=" target="_blank">moviesnipipay</a></TD>
<TD width="25%"><a href="filme/moviehab_f.php?page=1&tip=release&title=moviehab&link=" target="_blank">moviehab</a></TD>
<TD width="25%"><a href="filme/esubmovie.php?page=1&tip=release&title=esubmovie&link=" target="_blank">esubmovie</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/c1ne_f.php?page=1&tip=release&title=c1ne&link=" target="_blank">c1ne</a></TD>
<TD width="25%"><a href="filme/azseries_f.php?page=1&tip=release&title=azseries&link=" target="_blank">azseries</a></TD>
<TD width="25%"><a href="filme/xemovies_f.php?page=1&tip=release&title=xemovies&link=" target="_blank">xemovies</a></TD>
<TD width="25%"></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/fmovies_f.php?page=1&tip=release&title=fmovies&link=" target="_blank">fmovies.co</a></TD>
<TD width="25%"><a href="filme/hdmoviebox_f.php?page=1&tip=release&title=hdmoviebox&link=" target="_blank">hdmoviebox</a></TD>
<TD width="25%"><a href="filme/tv88_f.php?page=1&tip=release&title=tv88&link=" target="_blank">tv88</a></TD>
<TD width="25%"><a href="filme/flixhq_f.php?page=1&tip=release&title=flixhq&link=" target="_blank">flixhq</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/moviebox_f.php?page=1&tip=release&title=moviebox&link=" target="_blank">moviebox</a></TD>
<TD width="25%"><a href="filme/bflix_f.php?page=1&tip=release&title=bflix&link=" target="_blank">bflix</a></TD>
<TD width="25%"></TD>
<TD width="25%"></TD>
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
