<?php
include ("../common.php");
if (file_exists("../../cookie/max_time_x.txt")) {
   $time_exp=file_get_contents("../../cookie/max_time_x.txt");
   $time_now=time();
   if ($time_exp > $time_now)
     $loc="xmovies8_f.php?page=1&tip=release&title=xmovies8&link=";
   else
     $loc="x1.php";
} else
     $loc="x1.php";
header("Location: $loc");
?>
