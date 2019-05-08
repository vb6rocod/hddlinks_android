<?php
$list = glob("*.srt");
$contents=file_get_contents($list[0]);
$file_array=explode("\n",$contents);
$out="";
  foreach($file_array as $line)
  {
    $line = trim($line);
        if(preg_match('/(\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d) --> (\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d)/', $line))
         $out .=$line."<BR>";
        else
         $out .=json_encode($line)." => ".$line."<BR>";
  }
  echo $out;
?>
