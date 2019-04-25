<?php
$file=$_POST["file"];
if (file_exists("pl/".$file)) {
  unlink ("pl/".$file);
  echo 'Am sters fisierul '.$file;
} else {
  echo "Nu am gasit fisierul ".$file;
}
?>
