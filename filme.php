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
<TD width="25%"><a href="filme/tvhub_f.php?page=1&tip=release&title=tvhub&link=" target="_blank">tvhub</a></TD>
<TD width="25%"><a href="filme/filmehd_main.php" target="_blank">filmehd</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/filmeseriale_filme.php?page=1&tip=release&title=filmeseriale&link=" target="_blank">filmeseriale.online</a></TD>
<TD width="25%"><a href="filme/filmeonline2016_main.php" target="_blank">filmeonline2016</a></TD>
<TD width="25%"><a href="filme/filme-seriale_f.php?page=1&tip=release&title=filme-seriale&link=" target="_blank">filme-seriale</a></TD>
<TD width="25%"><a href="filme/filmenoihd_main.php" target="_blank">filmenoihd</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/filmeonline_biz_main.php" target="_blank">filmeonline.biz</a></TD>
<TD width="25%"><a href="filme/f-hd_main.php" target="_blank">f-hd</a></TD>
<TD width="25%"><a href="filme/filmeonline2019_main.php" target="_blank">filmeonline2019</a></TD>
<TD width="25%"><a href="filme/filme-bune.php?page=1&tip=release&title=filme-bune&link=" target="_blank">filme-bune</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/pefilme_main.php" target="_blank">pefilme</a></TD>
<TD width="25%"><a href="filme/topfilmenoi_main.php" target="_blank">topfilmenoi</a></TD>
<TD width="25%"><a href="filme/divxfilmeonline.php?page=1&tip=release&title=divxfilmeonline&link=" target="_blank">divxfilmeonline</a></TD>
<TD width="25%"><a href="filme/filmeseriale_eu_f.php?page=1&tip=release&title=filmeseriale.eu&link=" target="_blank">filmeseriale.eu</a></TD>

</TR>
<!--- desene si filme...-->
<TR>
<TD width="25%"><a href="filme/vezionline_main.php" target="_blank">vezionline</a></TD>
<TD width="25%"><a href="filme/filmeserialehd_f.php?page=1&tip=release&title=filmeserialehd&link=" target="_blank">filmeserialehd</a></TD>
<TD width="25%"><a href="filme/deseneledublate.php?page=1&tip=release&title=desenedublate&link=" target="_blank">deseneledublate</a></TD>
<TD width="25%"><a href="filme/desenefaine_main.php" target="_blank">desenefaine.ro</a></TD>
</TR>
<!-- straine -->
<TR>
<TD width="25%"><a href="filme/tvseries_f.php?page=1&tip=release&title=tvseries&link=" target="_blank">tvseries</a></TD>
<TD width="25%"><a href="filme/putlockerfit_f.php?page=1&tip=release&title=putlockerfit&link=" target="_blank">putlockerfit</a></TD>
<TD width="25%"><a href="filme/dwatchmovies_f.php?page=1&tip=release&title=dwatchmovies&link=" target="_blank">dwatchmovies</a></TD>
<TD width="25%"><a href="filme/0123netflix_f.php?page=1&tip=release&title=0123netflix&link=" target="_blank">0123netflix</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/hdfull_f.php?page=1&tip=release&title=hdfull&link=" target="_blank">hdfull</a></TD>
<TD width="25%"><a href="filme/cineplex_f_main.php" target="_blank">cineplex (cont)</a></TD>
<TD width="25%"><a href="filme/vikv.php?page=1&tip=release&title=vikv&link=" target="_blank">vikv</a></TD>
<TD width="25%"><a href="filme/lookmovie_f.php?page=1&tip=release&title=lookmovie&link=" target="_blank">lookmovie</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/onmovies_f.php?page=1&tip=release&title=onmovies&link=" target="_blank">onmovies</a></TD>
<TD width="25%"><a href="filme/spacemov.php?page=1&tip=release&title=spacemov&link=" target="_blank">spacemov</a></TD>
<TD width="25%"><a href="filme/hdbest.php?page=1&tip=release&title=hdbest&link=" target="_blank">hdbest</a></TD>
<TD width="25%"><a href="filme/moviesjoy_f.php?page=1&tip=release&title=moviesjoy&link=" target="_blank">moviesjoy</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/vipmovies_f.php?page=1&tip=release&title=vipmovies&link=" target="_blank">vipmovies</a></TD>
<TD width="25%"><a href="filme/ffmovies_f.php">ffmovies</a></TD>
<TD width="25%"><a href="filme/gomovies_f.php?page=1&tip=release&title=gomovies&link=" target="_blank">gomovies</a></TD>
<TD width="25%"><a href="filme/soap2day_f.php?page=1&tip=release&title=soap2day&link=" target="_blank">soap2day</a></TD>
</TR>

<TR>
<TD width="25%"><a href="filme/subsmovies_f.php?page=1&tip=release&title=subsmovie&link=" target="_blank">subsmovies</a></TD>
<TD width="25%"></TD>
<TD width="25%"></TD>
<TD width="25%"></TD>
</TR>
<?php
if (file_exists($base_pass."tvplay.txt")) {
echo '
<TR>
<TD width="25%"><a href="filme/cineplex_f_main1.php" target="_blank">cineplex</a></TD>
<TD width="25%"></TD>';
echo '<TD width="25%"></TD>
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
