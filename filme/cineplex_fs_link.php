<?php

error_reporting(0);
//set_time_limit(0);
  function sec2hms ($sec, $padHours = false)
  {
    $hms = "";
    $hours = intval(intval($sec) / 3600);
    $hms .= ($padHours)
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ":"
          : $hours. ":";
    $minutes = intval(($sec / 60) % 60);
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ":";
    $seconds = intval($sec % 60);
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
    return $hms;
  }
    function split_srt($contents)
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
    function delay_srt($contents,$delay_sec)
    {
        $lines = split_srt($contents);
        //print_r($lines);
        //array_shift($lines); // removes the WEBVTT header
        $output = '';
        $i = 0;
        $c=count($lines);
        for ($k=0;$k<$c;$k++) {
        //foreach ($lines as $line) {
          $line=trim($lines[$k]);
            if(preg_match('/(\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d) --> (\d\d):(\d\d):(\d\d)(\.|,)(\d\d\d)/', $line, $match)) {
              $begin = 3600 * $match[1] + 60 * $match[2] + $match[3] - $delay_sec;
              //if ($begin < 0) $begin=0;
              $end   = 3600 *$match[6] + 60 * $match[7] + $match[8] - $delay_sec;

                $begin1=sec2hms($begin,true).",".$match[5];
                $end1=sec2hms($end,true).",".$match[10];
              //echo $begin." ".$begin1."\n";

                if ($begin+$match[5]/1000 >=0) {
                 $i++;
                 $output .= $i;
                 $output .= "\r\n";
                //$line = preg_replace($pattern1, '$1:$2:$3,$4' , $line);
                 $xx=$begin1." --> ".$end1;
                 $output .= $xx . "\r\n";
                 for ($z=$k+1;$z<$k + 10;$z++) {
                   if (trim($lines[$z]) <> "")
                     $output .= trim($lines[$z]) . "\r\n";
                   else
                     break;
                 }
                 $output .= "\r\n";
                }
            }
            //$output .= $line . "\r\n";
        }
        return $output;
    }
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
//echo $jwv;
//echo $skin;
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
if (isset($_POST["token"])) {
$image= $_POST["image"];
$title = unfix_t(urldecode($_POST["title"]));
$subtracks = "";
$id=$_POST["imdb"];
$q=$_POST["q"];
$tip=$_POST["tip"];
$token=$_POST["token"];
$year=$_GET["year"];
$svr=$_POST['serv'];
if ($tip=="series") {
     $sez=$_POST["sez"];
     $ep=$_POST["ep"];
     $ep_tit=unfix_t(urldecode($_POST["ep_tit"]));
     $title=$title." - ".$sez."x".$ep." - ".$ep_tit;
   }
} else {
$image= $_GET["image"];
$title = unfix_t(urldecode($_GET["title"]));
$subtracks = "";
$id=$_GET["imdb"];
$q=$_GET["q"];
$tip=$_GET["tip"];
$token=$_GET["token"];
$svr=$_GET['serv'];
$year=$_GET["year"];
if ($tip=="series") {
     $sez=$_GET["sez"];
     $ep=$_GET["ep"];
     $ep_tit=unfix_t(urldecode($_GET["ep_tit"]));
     $title=$title." - ".$sez."x".$ep." - ".$ep_tit;
   }
}
$t1=explode("|",$q);
//echo $sub;
$cookie=$base_cookie."cineplex.dat";
if (file_exists($base_pass."cineplex_host.txt"))
  $host=file_get_contents($base_pass."cineplex_host.txt");
else
  $host="cinogen.net";
if ($tip=="movie") {
 $title=$title." (".$year.")";
 $l="https://".$host."/movies/getMovieLink?id=".$id."&token=".$token."&oPid=&_=";
} else
 $l="https://".$host."/series/getTvLink?id=".$id."&token=".$token."&s=".$sez."&e=".$ep."&oPid=&_=";
$rh="https://".$host;
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,$rh);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($html,1);
  //print_r ($r);
  //die();
  //http://127.0.0.1/mobile/scripts/filme/royale_fs_link.php?se=&tip=movie&image=http://image.tmdb.org/t/p/w300/5YUYg5q7QfC4IoNwNUtiwdiYKPr.jpg&imdb=tt5164432&title=Love,%20Simon
  //&serv=12&sub=&q=720p|Part1

$arr=$r["jwplayer"];
$label=$t1[0];
for ($k=0;$k<count($arr);$k++) {
  if ($arr[$k]["label"]== $label) {
    $movie=$arr[$k]["file"];
    break;
  }
}
//$movie=str_replace("cineplex.to","cinogen.net",$movie);
$orig=$movie;
$host_movie=parse_url($movie)['host'];
$host_movie_new = preg_replace("/trial\d+/","trial".$svr,$host_movie);
$host_movie_new = preg_replace("/sv\d+/","sv".$svr,$host_movie);
$host_movie_dl=str_replace("sv","dl",$host_movie_new);  // sa mearga doar pe premium
$movie=str_replace($host_movie,$host_movie_new,$orig);
$movie_dl=str_replace($host_movie,$host_movie_dl,$orig);
$amigo=$base_pass."tvplay.txt";
if (file_exists($amigo)) {
  $movie_file=preg_replace("/\\|\/|\?|\:|\s|\'|\"/","_",$title);
  $host_movie_new = preg_replace("/trial\d+/","sv".$svr,$host_movie);
  $host_movie_dl=str_replace("sv","dl",$host_movie_new);
  $movie=str_replace($host_movie,$host_movie_new,$orig);
  $movie_dl=str_replace($host_movie,$host_movie_dl,$orig);
  $movie=str_replace("&end=","&u=",$movie);
  $movie_dl=str_replace("&end=","&u=",$movie_dl)."&file=".$movie_file.".mp4";
}
//$movie = preg_replace("/trial6/","sv6",$movie);   // set to Netherlands
//echo $movie; die();
//$host_movie=parse_url($movie)["host"];
//$ip = gethostbyname($host_movie);
//$movie = str_replace($host_movie,$ip,$movie);
/*
if (!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $ip)) {
  $host_movie_new = str_replace("trial","sv",$host_movie);
  $ip_new = gethostbyname($host_movie_new);
  $movie = str_replace($host_movie,$ip_new,$movie);
}
*/
  //$movie=str_replace("trial6.".$host,"95.211.175.149",$movie);
//$movie=str_replace("trial6.".$host,"sv6.".$host,$movie);
$b = basename($movie);
$y=explode("?",$b);
$movie_name = $y[0];
$t1=explode("end=",$movie);
$delay_movie=$t1[1];
$srt_name=str_replace(".mp4",".srt",$movie_name);
if ($tip=="movie") {
  $movie_delay=$movie."&start=".$delay_movie;
} else {
  $movie_delay=$movie."&start=".$delay_movie;
}
//$movie_file = "ceva.mp4";
//header('Content-type: application/octet-stream');
//header('Content-Disposition: attachment; filename="'.$movie_file.'"');
//header("Location: $movie");
//die();
//////////////////////////////////////////////////////////////////////////////
$h="";
if (file_exists($base_sub."sub_extern.srt")) {
  $h=file_get_contents($base_sub."sub_extern.srt");
} elseif (isset($r["sub"]["url"])) {
  $srt_int=$r["sub"]["url"];
  $cookie=$base_cookie."cineplex.dat";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $srt_int);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,$rh);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
}
if ($h)  {
 if (function_exists("mb_convert_encoding")) {
    $enc=mb_detect_encoding($h);
 if (mb_detect_encoding($h, 'UTF-8', true)== false) $h=mb_convert_encoding($h, 'UTF-8','ISO-8859-2');
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
    $h = str_replace("�","S",$h);
    $h = str_replace("�","s",$h);
    $h = str_replace("�","T",$h);
    $h = str_replace("�","t",$h);
    $h=str_replace("�","a",$h);
	$h=str_replace("�","a",$h);
	$h=str_replace("�","i",$h);
	$h=str_replace("�","A",$h);
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
return $output;
}
$h=fix_srt($h);
   $sub="da";
   $new_file = $base_sub."orig.srt";
   $fh = fopen($new_file, 'w');
   fwrite($fh, $h, strlen($h));
   fclose($fh);
if ($tip=="movie")
  $delay=delay_srt($h,$delay_movie);
else
  $delay=delay_srt($h,$delay_movie);
   $new_file = $base_sub."delay.srt";
   $fh = fopen($new_file, 'w');
   fwrite($fh, $delay, strlen($delay));
   fclose($fh);
}

if ($flash=="mpc") {
 if (strpos($q,"Part") !== false) {
  if (strpos($q,"Part1") !== false) {
    $movie_mpc=$movie;
    $x=file_get_contents($base_sub."orig.srt");
    file_put_contents($base_sub.$srt_name,$x);
  } else {
    $movie_mpc=$movie_delay;
    $x=file_get_contents($base_sub."delay.srt");
    file_put_contents($base_sub.$srt_name,$x);
  }
 } else {
    $movie_mpc=$movie;
    $x=file_get_contents($base_sub."orig.srt");
    file_put_contents($base_sub.$srt_name,$x);
 }
  $mpc=trim(file_get_contents($base_pass."mpc.txt"));
  $c='"'.$mpc.'" /fullscreen "'.$movie_mpc.'"';
  pclose(popen($c,"r"));
  echo '<script type="text/javascript">window.close();</script>';
  die();
}
if ($flash == "direct") {
 if (strpos($q,"Part") !== false) {
  if (strpos($q,"Part1") !== false) {
    $movie_mpc=$movie;
    $x=file_get_contents($base_sub."orig.srt");
    file_put_contents($base_sub.$srt_name,$x);
  } else {
    $movie_mpc=$movie_delay;
    $x=file_get_contents($base_sub."delay.srt");
    file_put_contents($base_sub.$srt_name,$x);
  }
 } else {
    $movie_mpc=$movie;
    $x=file_get_contents($base_sub."orig.srt");
    file_put_contents($base_sub.$srt_name,$x);
 }
header('Content-type: application/vnd.apple.mpegURL');
header('Content-Disposition: attachment; filename="'.$movie_file.'"');
header("Location: $movie_mpc");
} elseif ($flash == "chrome") {
 if (strpos($q,"Part") !== false) {
  if (strpos($q,"Part1") !== false) {
    $movie_mpc=$movie;
    $x=file_get_contents($base_sub."orig.srt");
    file_put_contents($base_sub.$srt_name,$x);
  } else {
    $movie_mpc=$movie_delay;
    $x=file_get_contents($base_sub."delay.srt");
    file_put_contents($base_sub.$srt_name,$x);
  }
 } else {
    $movie_mpc=$movie;
    $x=file_get_contents($base_sub."orig.srt");
    file_put_contents($base_sub.$srt_name,$x);
 }
  $c="intent:".$movie_mpc."#Intent;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
  header("Location: $c");
} elseif ($flash == "mp") {
 if (strpos($q,"Part") !== false) {
  if (strpos($q,"Part1") !== false) {
    $movie_mpc=$movie;
    $x=file_get_contents($base_sub."orig.srt");
    file_put_contents($base_sub.$srt_name,$x);
  } else {
    $movie_mpc=$movie_delay;
    $x=file_get_contents($base_sub."delay.srt");
    file_put_contents($base_sub.$srt_name,$x);
  }
 } else {
    $movie_mpc=$movie;
    $x=file_get_contents($base_sub."orig.srt");
    file_put_contents($base_sub.$srt_name,$x);
 }
  $c="intent:".$movie_mpc."#Intent;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
  $c="intent:".$movie_mpc."#Intent;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";b.decode_mode=1;end";
  echo $c;
} else {
//"playlist[0]": [{
//"image": "'.$image.'",
//'.$sources.'
//'.$subtracks.'
//}],
$p=array();
if (strpos($q,"Part") !== false) {
$p["playlist"][0]["title"]=$title." "."Part1";
$p["playlist"][0]["image"]=$image;
$p["playlist"][0]["sources"][0]["type"]="video/mp4";
$p["playlist"][0]["sources"][0]["file"]=$movie;
if ($sub) {
 $p["playlist"][0]["tracks"][0]["file"]="../subs/orig.srt";
 $p["playlist"][0]["tracks"][0]["label"]="Part1";
 $p["playlist"][0]["tracks"][0]["default"]=true;
}
$p["playlist"][1]["title"]=$title." "."Part2";
$p["playlist"][1]["image"]=$image;
$p["playlist"][1]["sources"][0]["type"]="video/mp4";
$p["playlist"][1]["sources"][0]["file"]=$movie_delay;
if ($sub) {
 $p["playlist"][1]["tracks"][0]["file"]="../subs/delay.srt";
 $p["playlist"][1]["tracks"][0]["label"]="Part2";
 $p["playlist"][1]["tracks"][0]["default"]=true;
}
} else {
$p["playlist"][0]["title"]=$title;
$p["playlist"][0]["image"]=$image;
$p["playlist"][0]["sources"][0]["type"]="video/mp4";
$p["playlist"][0]["sources"][0]["file"]=$movie;
if ($sub) {
 $p["playlist"][0]["tracks"][0]["file"]="../subs/orig.srt";
 $p["playlist"][0]["tracks"][0]["label"]=$title;
 $p["playlist"][0]["tracks"][0]["default"]=true;
}
}
//print_r ($p);
$play=json_encode($p,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//echo $play;
file_put_contents($base_sub."play.rss",$play);
//"https://cdn.jwplayer.com/v2/playlists/Q352cyuc"
echo '
<!doctype html>
<HTML>
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>'.$title.'</title>
<style type="text/css">
body {
margin: 0px auto;
overflow:hidden;
}
body {background-color:#000000;}
</style>
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="../jwplayer.js"></script>
</HEAD>
<body><div id="mainnav">
<div id="container"></div>
<script type="text/javascript">
var player = jwplayer("container");
jwplayer("container").setup({
"playlist": "../subs/play.rss",
    captions: {
        color: "#FFFFFF",
        fontSize: 20,
        edgeStyle: "raised",
        backgroundOpacity: 0
    },
"height": $(document).height(),
"width": $(document).width(),
"skin": {
    "name": "beelden",
    "active": "#00bfff",
    "inactive": "#b6b6b6",
    "background": "#282828"
},
"title": "'.$title.'",
"abouttext": "'.$title.'",
"androidhls": true,
"startparam": "start",
"fallback": false,
"wmode": "direct",
"stagevideo": true
});
player.addButton(
  //This portion is what designates the graphic used for the button
  "https://developer.jwplayer.com/jw-player/demos/basic/add-download-button/assets/download.svg",
  //This portion determines the text that appears as a tooltip
  "Download Video",
  //This portion designates the functionality of the button itself
  //player.getPlaylistItem()["file"]
  function() {
    //With the below code,
    window.location.href = "'.$movie_dl.'";
  },
  //And finally, here we set the unique ID of the button itself.
  "download"
);
</script>
</div></body>
</HTML>
';
}
?>
