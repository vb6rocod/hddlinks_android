<?php
include ("../common.php");
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
$id=$_POST["id"];
$id=str_replace("&amp;","&",$id);
//echo $id;
//die();
$mod=$_POST["mod"];
//$mod="rar";
//echo $id;
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && $mod=="rar") {
$id=str_replace("[","\[",$id);
$id=str_replace("]","\]",$id);
$id=str_replace("'","\'",$id);
$id=str_replace("/","\\",$id);
}
//if (strpos($base_sub,":") !== false) $id=str_replace("/","\\",$id);
//Madam Secretary - Sezonul 4 (2017)Madam.Secretary.S04E01.1080p-720p.HDTV.X264-DIMENSION.srt
//die();
//if (file_exists($base_sub."sub.zip")) $mod="zip";
//if (file_exists($base_sub."sub.rar")) $mod="rar";
//echo $id;
//die();
if ($mod=="rar") {
   $file_srt=$base_sub."sub.rar";
   $rar_file = rar_open($file_srt);
   if ($rar_file === false)
    die("Failed to open Rar archive");
    //$id="She's.The.Man[2006]DvDrip[Eng]-aXXo.srt";
    //echo $id;
   //$id="Madam Secretary - Sezonul 4 (2017)\Madam.Secretary.S04E01.1080p-720p.HDTV.X264-DIMENSION.srt";
   $entry = rar_entry_get($rar_file, $id);
   if ($entry === false)
    die("Failed to find such entry");

   $stream = $entry->getStream();
   if ($stream === false)
      die("Failed to obtain stream.");

   rar_close($rar_file); //stream is independent from file
$h="";
while (!feof($stream)) {
    $h .= fread($stream, 8192);
}

fclose($stream);
} else {
  $file_srt=$base_sub."sub.zip";
$h = '';
$z = new ZipArchive();
if ($z->open($file_srt)) {
    $fp = $z->getStream($id);
    if(!$fp) exit("Failed to open zip archive\n");

    while (!feof($fp)) {
        $h .= fread($fp, 8192);
    }

    fclose($fp);
}
}
if ($h)  {
 if (function_exists("mb_convert_encoding")) {
    $map = array(
        chr(0x8A) => chr(0xA9),
        chr(0x8C) => chr(0xA6),
        chr(0x8D) => chr(0xAB),
        chr(0x8E) => chr(0xAE),
        chr(0x8F) => chr(0xAC),
        chr(0x9C) => chr(0xB6),
        chr(0x9D) => chr(0xBB),
        chr(0xA1) => chr(0xB7),
        chr(0xA5) => chr(0xA1),
        chr(0xBC) => chr(0xA5),
        chr(0x9F) => chr(0xBC),
        chr(0xB9) => chr(0xB1),
        chr(0x9A) => chr(0xB9),
        chr(0xBE) => chr(0xB5),
        chr(0x9E) => chr(0xBE),
        chr(0x80) => '&euro;',
        chr(0x82) => '&sbquo;',
        chr(0x84) => '&bdquo;',
        chr(0x85) => '&hellip;',
        chr(0x86) => '&dagger;',
        chr(0x87) => '&Dagger;',
        chr(0x89) => '&permil;',
        chr(0x8B) => '&lsaquo;',
        chr(0x91) => '&lsquo;',
        chr(0x92) => '&rsquo;',
        chr(0x93) => '&ldquo;',
        chr(0x94) => '&rdquo;',
        chr(0x95) => '&bull;',
        chr(0x96) => '&ndash;',
        chr(0x97) => '&mdash;',
        chr(0x99) => '&trade;',
        chr(0x9B) => '&rsquo;',
        chr(0xA6) => '&brvbar;',
        chr(0xA9) => '&copy;',
        chr(0xAB) => '&laquo;',
        chr(0xAE) => '&reg;',
        chr(0xB1) => '&plusmn;',
        chr(0xB5) => '&micro;',
        chr(0xB6) => '&para;',
        chr(0xB7) => '&middot;',
        chr(0xBB) => '&raquo;',
    );
    /*
	$h = html_entity_decode(mb_convert_encoding(strtr($h, $map), 'UTF-8', 'ISO-8859-2'), ENT_QUOTES, 'UTF-8');

	$h = str_replace("\xC3\x84\xE2\x80\x9A","\xC4\x82",$h);
	$h = str_replace("\xC3\x84\xC2\x83","\xC4\x83",$h);
    $h = str_replace("\xC4\x82\xC5\xBD","\xC3\x8E",$h);
    $h = str_replace("\xC4\x82\xC2\xAE","\xC3\xAE",$h);
    $h = str_replace("\xC4\xB9\xCB\x98","\xC5\xA2",$h);
    $h = str_replace("\xC4\xB9\xC5\x81","\xC5\xA3",$h);
    $h = str_replace("\xC4\x82\xE2\x80\X9A","\xC3\x82",$h);
    $h = str_replace("\xC4\x82\xCB\x98","\xC3\xA2",$h);
    $h = str_replace("\xC4\xB9\xC5\xBE","\xC5\x9E",$h);
    $h = str_replace("\xC4\xB9\xC5\xBA","\xC5\x9F",$h);
    $h = str_replace("\xC4\x8C\xC5\xA1","\xC5\xA2",$h);
    $h = str_replace("\xC4\x8C\xE2\x80\x99","\xC5\xA3",$h);
    $h = str_replace("\xC4\x8C\xC2\x98","\xC5\x9E",$h);
    $h = str_replace("\xC4\x8C\xE2\x84\xA2","\xC5\x9F",$h);
	$h = str_replace("\xC3\xA2\xE2\x84\xA2\xC5\x9E","\xE2\x99\xAA",$h);
    */
    $enc=mb_detect_encoding($h);
    //file_put_contents($base_sub."default.srt",$h);
 if (mb_detect_encoding($h, 'UTF-8', true)== false) {

      $h=mb_convert_encoding($h, 'UTF-8','ISO-8859-2');
      //file_put_contents($base_sub."default1.srt",$h);
 }
    /*
    $h = str_replace("ª","Ş",$h);
    $h = str_replace("º","ş",$h);
    $h = str_replace("Þ","Ţ",$h);
    $h = str_replace("þ","ţ",$h);
	$h = str_replace("ã","ă",$h);
	//$h = str_replace("Ã","Ă",$h);

    $h = str_replace("Å£","ţ",$h);
    $h = str_replace("Å¢","Ţ",$h);
    $h = str_replace("Å","ş",$h);
	$h = str_replace("Ă®","î",$h);
	$h = str_replace("Ă¢","â",$h);
	$h = str_replace("Ă","Î",$h);
	//$h = str_replace("Ã","Â",$h);
	$h = str_replace("Ä","ă",$h);
	*/
} else {
    $h = str_replace("ª","S",$h);
    $h = str_replace("º","s",$h);
    $h = str_replace("Þ","T",$h);
    $h = str_replace("þ","t",$h);
    $h=str_replace("ã","a",$h);
	$h=str_replace("â","a",$h);
	$h=str_replace("î","i",$h);
	$h=str_replace("Ã","A",$h);
}
    function split_vtt($contents)
    {
        $lines = explode("\n", $contents);
        if (count($lines) === 1) {
            $lines = explode("\r\n", $contents);
            if (count($lines) === 1) {
                $lines = explode("\r", $contents);
            }
        }
        return $lines;
    }
if (strpos($h,"WEBVTT") !== false) {
  //convert to srt;

    function convert_vtt($contents)
    {
        $lines = split_vtt($contents);
        array_shift($lines); // removes the WEBVTT header
        $output = '';
        $i = 0;
        foreach ($lines as $line) {
            /*
             * at last version subtitle numbers are not working
             * as you can see that way is trustful than older
             *
             *
             * */
            $pattern1 = '#(\d{2}):(\d{2}):(\d{2})\.(\d{3})#'; // '01:52:52.554'
            $pattern2 = '#(\d{2}):(\d{2})\.(\d{3})#'; // '00:08.301'
            $m1 = preg_match($pattern1, $line);
            if (is_numeric($m1) && $m1 > 0) {
                $i++;
                $output .= $i;
                $output .= PHP_EOL;
                $line = preg_replace($pattern1, '$1:$2:$3,$4' , $line);
            }
            else {
                $m2 = preg_match($pattern2, $line);
                if (is_numeric($m2) && $m2 > 0) {
                    $i++;
                    $output .= $i;
                    $output .= PHP_EOL;
                    $line = preg_replace($pattern2, '00:$1:$2,$3', $line);
                }
            }
            $output .= $line . PHP_EOL;
        }
        return $output;
    }
    $h=convert_vtt($h);
}
function fix_srt($contents) {
$n=1;
$output="";
$bstart=false;
$file_array=explode("\n",$contents);
if (!preg_match("/\{\d+\}\{\d+\}/",$file_array[0])) {
  foreach($file_array as $line)
  {
    $line = trim($line);
        if(preg_match('/(\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d) --> (\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d)/', $line) && !$bstart)
        {
          $output .= $n;
          $output .= PHP_EOL;
          $output .= $line.PHP_EOL;
          $bstart=true;
          $first=true;
        } elseif($line != '' && $bstart) {
          $output .= $line.PHP_EOL;
          $first=false;
          //$n++;
        } elseif ($line == '' && $bstart) {
          if ($first==true) {
            $line=" ".PHP_EOL;
            $first=false;
          }
          $output .= $line.PHP_EOL;
          $bstart=false;
          $n++;
        }
  }
} else {  // accept sub format.....
  $output=$contents;
}
return $output;
}
$h=fix_srt($h);
   $new_file = $base_sub."sub_extern.srt";
   $fh = fopen($new_file, 'w');
   fwrite($fh, $h, strlen($h));
   fclose($fh);
   echo "Am salvat subtitrarea";
}

?>
