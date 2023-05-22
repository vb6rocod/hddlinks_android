<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Adult</title>
<link rel="stylesheet" type="text/css" href="custom.css" />
<style>
td {
    font-style: bold;
    font-size: 20px;
    text-align: left;
}
</style>
<script>
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "50" && e.target.type != "text") {
      window.open("adult/adult_fav.php");
    }
   }
document.onkeypress =  zx;
</script>
</head>
<BODY>
<BR><BR>


<?php
include ("common.php");
$f=$base_pass."adult.txt";
if (!file_exists($f)) {
//echo '<h2 style="background-color:deepskyblue;color:black"><center>Activati sau dezactivati sectiunea Adult din setari!</center></h2>';
} else {
$h=file_get_contents($f);
$t1=explode("|",$h);
$adult=$t1[0];
}
if ($adult=="DA") {
echo '
<table border="1" align="center" width="90%">
<TR>';
if ($tast=="NU")
echo '<th class="cat" colspan="4"><a href="adult/adult_fav.php" target="_blank">Adult</a></Th>';
else
echo '<th class="cat" colspan="4">Adult</Th>';
echo '
</TR>

<TR>
<TD width="25%"><a href="tv/playlist.php?title=playlist+xxx.m3u8" target="_blank">playlist xxx</a></TD>
<TD width="25%"><a href="adult/portalultautv.php?tip=release&page=1&title=portalultautv&link=" target="_blank">portalultautv</a></TD>
<TD width="25%"><a href="adult/redtube_main.php" target="_blank">redtube</a></TD>
<TD width="25%"><a href="adult/xhamster_main.php" target="_blank">xhamster</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/xnxx_main.php" target="_blank">xnxx</a></TD>
<TD width="25%"><a href="adult/xvideos_main.php" target="_blank">xvideos</a></TD>
<TD width="25%"><a href="adult/4tube_main.php" target="_blank">4tube</a></TD>
<TD width="25%"><a href="adult/milfzr.php?tip=release&page=1&title=milfzr&link=" target="_blank">milfzr</a></TD>
</TR>

<TR>
<TD width="25%"></TD>
<TD width="25%"><a href="adult/pornburst_main.php" target="_blank">pornburst</a></TD>
<TD width="25%"><a href="adult/xfreehd.php?tip=release&page=1&title=xfreehd&link=" target="_blank">xfreehd</a></TD>
<TD width="25%"><a href="adult/incestvidz_main.php" target="_blank">incestvidz</a></TD>
</TR>


<TR>
<TD width="25%"><a href="adult/pandamovie_main.php" target="_blank">pandamovie</a></TD>
<TD width="25%"><a href="adult/tvporn_main.php" target="_blank">tvporn</a></TD>
<TD width="25%"><a href="adult/fapbox_main.php" target="_blank">fapbox</a></TD>
<TD width="25%"><a href="adult/pornhub_main.php" target="_blank">porhub</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/tnaflix_main.php" target="_blank">tnaflix</a></TD>
<TD width="25%"><a href="adult/jizzbunker_main.php" target="_blank">jizzbunker</a></TD>
<TD width="25%"><a href="adult/pornfree_main.php" target="_blank">pornfree.tv</a></TD>
<TD width="25%"><a href="adult/spankbang.php?tip=release&page=1&title=spankbang&link=" target="_blank">spankbang</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/thumbzilla_main.php" target="_blank">thumbzilla</a></TD>
<TD width="25%"><a href="adult/hellmoms_main.php" target="_blank">hellmoms</a></TD>
<TD width="25%"><a href="adult/bitporno.php?tip=release&page=1&title=bitporno&link=" target="_blank">bitporno</a></TD>
<TD width="25%"></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/youjizz_main.php" target="_blank">youjizz</a></TD>
<TD width="25%"><a href="adult/drtuber_main.php" target="_blank">drtuber</a></TD>
<TD width="25%"><a href="adult/2freesex_main.php" target="_blank">2freesex</a></TD>
<TD width="25%"><a href="adult/tube8_main.php" target="_blank">tube8</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/nuvid_main.php" target="_blank">nuvid</a></TD>
<TD width="25%"><a href="adult/bravoporn_main.php" target="_blank">bravoporn</a></TD>
<TD width="25%"><a href="adult/porn300_main.php" target="_blank">porn300</a></TD>
<TD width="25%"><a href="adult/zbporn_main.php" target="_blank">zbporn</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/fuqer.php?tip=release&page=1&title=fuqer&link=" target="_blank">fuqer</a></TD>
<TD width="25%"><a href="adult/collectionofbestporn.php?tip=release&page=1&title=collectionofbestporn&link=" target="_blank">collectionofbestporn</a></TD>
<TD width="25%"><a href="adult/porndroids_main.php" target="_blank">porndroids</a></TD>
<TD width="25%"><a href="adult/youporn.php?tip=release&page=1&title=youporn&link=" target="_blank">youporn</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/pefilme_main.php" target="_blank">pefilme</a></TD>
<TD width="25%"></TD>
<TD width="25%"><a href="adult/pornmaki_main.php" target="_blank">pornmaki</a></TD>
<TD width="25%"><a href="adult/proporn_main.php" target="_blank">proporn</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/pornhost_main.php" target="_blank">pornhost</a></TD>
<TD width="25%"><a href="adult/handjobhub_main.php" target="_blank">handjobhub</a></TD>
<TD width="25%"></TD>
<TD width="25%"><a href="adult/dansmovies_main.php" target="_blank">dansmovies</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/pornheed_main.php" target="_blank">pornheed</a></TD>
<TD width="25%"><a href="adult/pornrabbit_main.php" target="_blank">pornrabbit</a></TD>
<TD width="25%"></TD>
<TD width="25%"><a href="adult/tubepornclassic.php?tip=release&page=1&title=tubepornclassic&link=" target="_blank">tubepornclassic</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/trannytube_main.php" target="_blank">trannytube</a></TD>
<TD width="25%"><a href="adult/eporner_main.php" target="_blank">eporner</a></TD>
<TD width="25%"></TD>
<TD width="25%"><a href="adult/porntrex_main.php" target="_blank">porntrex</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/streamporn_main.php" target="_blank">streamporn</a></TD>
<TD width="25%"><a href="adult/xopenload_main.php" target="_blank">xopenload</a></TD>
<TD width="25%"><a href="adult/trannytube_tv_main.php" target="_blank">trannytube.tv</a></TD>
<TD width="25%"><a href="adult/astube_main.php" target="_blank">astube</a></TD>
</TR>

<TR>
<TD width="25%"><a href="adult/mangovideo_main.php" target="_blank">mangovideo</a></TD>
<TD width="25%"><a href="adult/familyporn_main.php" target="_blank">familyporn</a></TD>
<TD width="25%"><a href="adult/porndoe.php?page=1&tip=release&link=https://porndoe.com/videos&title=porndoe" target="_blank">porndoe</a></TD>
<TD width="25%"><a href="adult/xozilla_main.php" target="_blank">xozilla</a></TD>
</TR>
';

echo '
</table>
';
}
?>
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
<TR><TD>* Folositi tasta 5 pentru a simula butonul de cautare.<TD></TR>
<TR><TD>* Folositi tasta 3 pentru a adauga/sterge la favorite.<TD></TR>
<TR><TD>* Folositi tasta 2 pentru a accesa pagina de "Favorite".<TD></TR>
</TABLE>';
}
?>
</BODY>
</HTML>
