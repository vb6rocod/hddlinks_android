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
<TD width="25%"><a href="filme/filmeseriale_main.php?page=1&tip=release&title=filmeseriale.online&link=http://www.filmeserialeonline.org/seriale/" target="_blank">filmeseriale.online</a></TD>
<TD width="25%"><a href="filme/veziseriale_s.php?page=1&tip=release&title=veziseriale&link=" target="_blank">veziseriale</a></TD>
<TD width="25%"><a href="filme/fshd_s.php?page=1&tip=release&title=fshd&link=" target="_blank">fshd.ro</a></TD>
<TD width="25%"><a href="filme/serialeonline.php?page=1&tip=release&title=serialeonline&link=" target="_blank">serialeonline</a></TD>
</TR>


<!-- straine -->
<TR>
<TD width="25%"><a href="filme/tvseries_s.php?page=1&tip=release&title=tvseries&link=" target="_blank">tvseries</a></TD>
<TD width="25%"><a href="filme/goojara_s.php?page=1&tip=release&title=goojara&link=" target="_blank">goojara</a></TD>
<TD width="25%"><a href="filme/emovies_s.php?page=1&tip=release&title=emovies&link=" target="_blank">emovies</a></TD>
<TD width="25%"><a href="filme/lookmovie_s.php?page=1&tip=release&title=lookmovie&link=" target="_blank">lookmovie</a></TD>
</TR>
<TR>
<TD width="25%"><a href="filme/cineplex_s_main.php" target="_blank">cineplex (cont)</a></TD>
<TD width="25%"><a href="filme/moviewetrust_s.php?page=1&tip=release&title=moviewetrust&link=" target="_blank">moviewetrust</a></TD>
<TD width="25%"></TD>
<TD width="25%"><a href="filme/diverse.php" target="_blank">diverse</a></TD>
</TR>



<?php

if (file_exists($base_pass."debug.txt")) {
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
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
