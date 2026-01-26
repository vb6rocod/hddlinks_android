<?php
if (isset($_POST['link'])) {
$zz=trim($_POST['link']);
$h=file_get_contents("v1.txt");
file_put_contents("1.txt",$zz);
die();
}

