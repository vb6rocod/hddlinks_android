<html>
<head>
<meta charset="utf-8">
<title>Firstflusonic</title>
</head>
<body>
<H2>Firstflusonic</H2>
<table border="1px" width="100%">
<?php
$n=0;
$m3uFile="Firstflusonic.m3u";
$m3uFile = file($m3uFile);
foreach($m3uFile as $key => $line) {
  $line=trim($line);
  if(strtoupper(substr($line, 0, 7)) === "#EXTINF") {
    if (preg_match("/tvg\-name\=\"(.*?)\"/i",$line,$m)) {
      $title=$m[1];
      if (!$title) {
        $t1=explode(",",$line);
        $title=trim($t1[1]);
      }
    } else {
    $t1=explode(",",$line);
    $title=trim($t1[1]);
    }
    $file = trim($m3uFile[$key + 1]);
    //if (preg_match("/\.m3u8/",$file)) {
    if ($file[0]=="#")  $file = trim($m3uFile[$key + 2]);
    if ($n == 0) echo "<TR>"."\n\r";
    echo '<TD width="20%"><a href="'.$file.'">'.$title.'</a>'."\n";
    $n++;
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }
    //}
  }
}
echo '</table></body></html>';
?>
