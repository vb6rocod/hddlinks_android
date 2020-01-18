<?php
include ("../common.php");
if (file_exists("../../cookie/max_time_cinebloom.txt")) {
   $time_exp=file_get_contents("../../cookie/max_time_cinebloom.txt");
   $time_now=time();
   if ($time_exp > $time_now)
     $loc="cinebloom_s.php?page=1&tip=release&title=cinebloom&link=";
   else
     $loc="cinebloom_x2.php";
} else
     $loc="cinebloom_x2.php";
header("Location: $loc");
?>
