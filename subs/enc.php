<?php
/*
$h=file_get_contents("test.srt1");
//$h=htmlspecialchars($h);
$h=str_replace("&lrm;","",$h);
$h=preg_replace("/(\d+\:\d+\:\d+\.\d+ --> \d+\:\d+\:\d+\.\d+)(.+)/","$1",$h);
$h=preg_replace("/\<c.*?\>/","",$h);
$h=preg_replace("/\<\/c.*?\>/","",$h);
//$h=str_replace("<c.bg_transparent>","",$h);
//$h=str_replace("</c.bg_transparent>","",$h);
echo $h;
die();
*/
$out='1
00:00:01,000 --> 00:00:01,000
abc
2
00:00:01,100 --> 00:00:20,917
abc
abc';
$b=json_encode($out);
echo $b;
//die();
$a="abc";
$b=json_encode($a);
$b='"a\u0083b\u0098c"';
$c=json_decode($b);
$d=urlencode($c);
echo $d;
//die();
$list = glob("*.srt");
$contents=file_get_contents($list[0]);
//$h=urlencode($contents);
//echo $h;
//die();
$file_array=explode("\n",$contents);
$out="";
  foreach($file_array as $line)
  {
    $line = trim($line);
        if(preg_match('/(\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d) --> (\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d)/', $line))
         $out .=$line."<BR>";
        else
         $out .=json_encode($line)." => ".$line."<BR>".urlencode($line)."<BR>";
  }

  echo $out;
  //die();
//http://www.heliosdesign.ro/dictionar/Diacritice_romanesti
$h=json_encode($contents);
$h=str_replace("\u00e3","\u0103",$h); //mar
$h=str_replace("\u00ba","\u0219",$h);  // si
$h=str_replace("\u00fe","\u021B",$h); //ratiune
$h=str_replace("\u00aa","\u015E",$h); //Si
$h=str_replace("\u00de","\u021A",$h); //NOPTI   (cu virgula)
$h=json_decode($h);
echo $h;
die();
$h=mb_convert_encoding($h, 'ISO-8859-1','UTF-8');
echo $h;
//$h=mb_convert_encoding($h, 'ISO-8859-2','ISO-8859-1');
echo $h;
file_put_contents("xx.txt",$h);
?>
