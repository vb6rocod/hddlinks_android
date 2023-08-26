<?php
parse_str($sub_link,$x);
unset($x['imdb']);
$sub_link1=http_build_query($x)."&imdb=";
echo '<br>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;font-weight: bold;font-size: 1.5em" align="center" colspan="6">Alegeti o subtitrare (cauta titlu)</td></TR>';
echo '<TR>';
echo '<TD class="mp"><a id="opensub" href="opensubtitles.php?'.$sub_link1.'">opensubtitles</a></td>';
echo '<TD class="mp"><a id="opensub2" href="opensubtitles_new.php?page=1&'.$sub_link1.'">opensub_new</a></td>';
echo '<TD class="mp"><a id="opensub2" href="rest.php?page=1&'.$sub_link1.'">rest.api</a></td>';
echo '<TD class="mp"><a id="titrari" href="titrari_main.php?page=1&'.$sub_link1.'&page=1">titrari.ro</a></td>';
echo '<TD class="mp"><a id="subs" href="subs_main.php?'.$sub_link1.'">subs.ro</a></td>';
echo '<TD class="mp"><a id="subtitrari" href="subtitrari_main.php?'.$sub_link1.'">subtitrari_noi.ro</a></td>';
echo '</TR></TABLE>';
echo '<table border="1" width="100%">';
echo '<TR><TD style="background-color:#0a6996;color:#64c8ff;font-weight: bold;font-size: 1.5em" align="center" colspan="6">Alegeti o subtitrare (cauta imdb id)</td></TR>';
echo '<TR>';
echo '<TD class="mp"><a id="opensub1" href="opensubtitles1.php?'.$sub_link.'">opensubtitles</a></td>';
echo '<TD class="mp"><a id="opensub3" href="opensubtitles1_new.php?page=1&'.$sub_link.'">opensub_new</a></td>';
echo '<TD class="mp"><a id="opensub3" href="rest1.php?page=1&'.$sub_link.'">rest.api</a></td>';
echo '<TD class="mp"><a id="titrari1" href="titrari_main1.php?page=1&'.$sub_link.'&page=1">titrari.ro</a></td>';
echo '<TD class="mp"><a id="subs1" href="subs_main1.php?'.$sub_link.'">subs.ro</a></td>';
echo '<TD class="mp"><a id="subtitrari1" href="subtitrari_main1.php?'.$sub_link.'">subtitrari_noi.ro</a></td>';
echo '</TR></TABLE>';
?>
