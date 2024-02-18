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


<TD width="25%"><a href="filme/voxfilmeonline.php?page=1&tip=release&title=voxfilmeonline&link=" target="_blank">voxfilmeonline</a></TD>
<TD width="25%"><a href="filme/topfilmeonline.php?page=1&tip=release&title=topfilmeonline&link=" target="_blank">topfilmeonline</a></TD>
<TD width="25%"><a href="filme/divxfilmeonline.php?page=1&tip=release&title=divxfilmeonline&link=" target="_blank">divxfilmeonline</a></TD>
<TD width="25%"><a href="filme/filmehd.php?page=1&tip=release&title=filmehd&link=" target="_blank">filmehd</a></TD>

</TR>
<TR>
<TD width="25%"><a href="filme/filmeseriale_filme.php?page=1&tip=release&title=filmeseriale&link=" target="_blank">filmeseriale.online</a></TD>
<TD width="25%"><a href="filme/filmeonline2016.php?page=1&tip=release&title=filmeonline2016&link=" target="_blank">filmeonline2016</a></TD>
<TD width="25%"><a href="filme/serialeonline_f.php?page=1&tip=release&title=serialeonline&link=" target="_blank">serialeonline</a></TD>
<TD width="25%"><a href="filme/fsonline_f.php?page=1&tip=release&title=fsonline&link=" target="_blank">fsonline</a></TD>
<!--<TD width="25%"><a href="filme/portalultautv_main.php" target="_blank">portalultautv</a></TD>-->
</TR>


<!--- desene si filme...-->
<TR>
<TD width="25%"><a href="filme/upmovies_f.php?page=1&tip=release&title=upmovies&link=" target="_blank">upmovies</a></TD>
<TD width="25%"><a href="filme/emovies_f.php?page=1&tip=release&title=emovies&link=" target="_blank">emovies</a></TD>
<TD width="25%"><a href="filme/tugaflix_f.php?page=1&tip=release&title=tugaflix&link=" target="_blank">tugaflix</a></TD>
<TD width="25%"><a href="filme/fshd_f.php?page=1&tip=release&title=fshd&link=" target="_blank">fshd.ro</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/sitefilme_f.php?page=1&tip=release&title=sitefilme&link=" target="_blank">sitefilme</a></TD>
<TD width="25%"><a href="filme/vezionline_f.php?page=1&tip=release&title=vezionline&link=" target="_blank">vezionline</a></TD>
<TD width="25%"><a href="filme/fmovies_f.php?page=1&tip=release&title=fmovies&link=" target="_blank">fmovies</a></TD>
<TD width="25%"></TD>
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
<TD width="25%"><a href="filme/streamflix_f.php?page=1&tip=release&title=streamflix&link=popular" target="_blank">streamflix</a></TD>
<TD width="25%"><a href="filme/onionplay_f.php?page=1&tip=release&title=onionplay&link=" target="_blank">onionplay</a></TD>
<TD width="25%"><a href="filme/azseries_f.php?page=1&tip=release&title=azseries&link=" target="_blank">azseries</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/zoechip_f.php?page=1&tip=release&title=zoechip&link=" target="_blank">zoechip</a></TD>
<TD width="25%"><a href="filme/lookclub_f.php?page=1&tip=release&title=lookclub&link=" target="_blank">lookclub</a></TD>
<TD width="25%"><a href="filme/fsharetv.php?page=1&tip=release&title=fsharetv&link=" target="_blank">fsharetv</a></TD>
<TD width="25%"><a href="filme/soap2dayz_f.php?page=1&tip=release&title=soap2dayz&link=" target="_blank">soap2dayz</a></TD>
</TR>

<TR>
<!--<TD width="25%"><a href="filme/uniquestream_f.php?page=1&tip=release&title=uniquestream&link=" target="_blank">uniquestream</a></TD>-->
<TD width="25%"><a href="filme/ridomovies_f.php?page=1&tip=release&title=ridomovies&link=" target="_blank">ridomovies</a></TD>
<TD width="25%"><a href="filme/goojara_f.php?page=1&tip=release&title=goojara&link=" target="_blank">goojara</a></TD>
<TD width="25%"><a href="filme/bflix_f.php?page=1&tip=release&title=bflix&link=" target="_blank">bflix</a></TD>
<TD width="25%"><a href="filme/flixhq_f.php?page=1&tip=release&title=flixhq&link=" target="_blank">flixhq</a></TD>
</TR>

<TR>
<TD width="25%"></TD>
<TD width="25%"><a href="filme/idlixian_f.php?page=1&tip=release&title=idlixian&link=" target="_blank">idlixian</a></TD>
<TD width="25%"></TD>
<TD width="25%"></TD>

</TR>
<?php
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
