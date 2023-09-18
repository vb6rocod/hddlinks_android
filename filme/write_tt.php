<?php
include ("../common.php");
$imdb=$_GET['imdb'];
$tt=$base_cookie."tt.txt";
file_put_contents($tt,$imdb);
echo "Am setat imdb ".$imdb;
?>
