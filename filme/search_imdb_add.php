<?php
include ("../common.php");
$title=unfix_t(urldecode($_POST['title']));
file_put_contents($base_cookie."filme.dat",urldecode($title));
file_put_contents($base_cookie."seriale.dat",urldecode($title));
echo "am setat la cautare filme/seriale: ".$title;
?>
