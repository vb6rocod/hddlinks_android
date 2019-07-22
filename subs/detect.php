<?php
$h=file_get_contents("ansi.srt");
if (mb_detect_encoding($h, 'UTF-8', true)== false)
  echo "ansi";
else
  echo "utf";
if (mb_detect_encoding($h, 'UTF-8', true)== false)
  $h=mb_convert_encoding($h, 'UTF-8','ISO-8859-2');
echo $h;
?>
