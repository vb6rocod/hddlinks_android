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
if ($tip=="series") {
     $sez=$_GET["sez"];
     $ep=$_GET["ep"];
     $ep_tit=unfix_t(urldecode($_GET["ep_tit"]));
     $title=$title." - ".$sez."x".$ep." - ".$ep_tit;
   }
}
$t1=explode("|",$q);
//echo $sub;
$cookie=$base_cookie."chillax.dat";
if ($tip=="movie")
 $l="https://chillax.ws/movies/getMovieLink?id=".$id."&token=".$token."&oPid=&_=";
else
 $l="https://chillax.ws/series/getTvLink?id=".$id."&token=".$token."&s=".$sez."&e=".$ep."&oPid=&_=";
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,"https://chillax.ws");
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
if (strpos($movie,"plink?") !== false) {
  $l="https://chillax.ws".$movie;
  //http://trial1.chillax.ws/mv/tt724069/720p.mp4?st=gQnl4H3YX-VTZX69ByVaBw&e=1539156640&end=610
  //http://trial1.chillax.ws/mv/tt2660888/720p.mp4?st=Qe6EcAUZL8DhMy4xyxgY0w&e=1539156720&end=610
  //$l='https://chillax.ws/player/native.html?{"v":"'.$movie.'"}';
  //$l="https://chillax.ws/player/native.html?%7B%22v%22:%22/video/plink?pid=723946&u=255579&sig=1E-rW6zNjWHemGBbF_ipiA&ex=1539154370&res=720%22%7D
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,"https://chillax.ws");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $t1=explode('Location:',$html);
  $t2=explode("\n",$t1[1]);
  $movie=trim($t2[0]);
  //$movie=str_replace("&end=","&a=",$movie);
  //echo $movie;
  //die();
  //http://trial.cdn1.chillax.ws/video/724102?st=znwRc20_A-cliUM3JQrqsA&e=1539160053&end=610&res=720
  //http://trial.cdn1.chillax.ws/video/724102?st=znwRc20_A-cliUM3JQrqsA&e=1539160053&end=610&res=720
  //http://trial1.chillax.ws/mv/tt7656570/720p.mp4?st=znwRc20_A-cliUM3JQrqsA&e=1539160053&end=610
//https://chillax.ws/player/native.html?%7B%22v%22:%22/video/plink?pid=723946&u=255579&sig=1E-rW6zNjWHemGBbF_ipiA&ex=1539154370&res=720%22%7D
//https://chillax.ws/player/native.html?%7B%22v%22:%22/video/plink?pid=723946&u=255579&sig=1E-rW6zNjWHemGBbF_ipiA&ex=1539154370&res=720%22%7D
//https://chillax.ws/player/videojs5.html?%7B%22v%22:%22/video/plink?pid=723946&u=255579&sig=1E-rW6zNjWHemGBbF_ipiA&ex=1539154370&res=720%22%7D
  //$movie=$l;
  //$movie=str_replace("&end=610","",$movie);
  //echo $movie;
  //die();
}
$b = basename($movie);
$y=explode("?",$b);
$movie_name = $y[0];
if (strpos($movie_name,".mp4") !== false)
  $srt_name=str_replace(".mp4",".srt",$movie_name);
else
  $srt_name = $movie_name.".srt";
if ($tip=="movie") {
  $movie_delay=$movie."&start=610";
} else {
  $movie_delay=$movie."&start=310";
}

//////////////////////////////////////////////////////////////////////////////
$h="";
if (file_exists($base_sub."sub_extern.srt")) {
  $h=file_get_contents($base_sub."sub_extern.srt");
} elseif (isset($r["sub"]["url"])) {
  $srt_int=$r["sub"]["url"];
  $cookie=$base_cookie."chillax.dat";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $srt_int);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,"https://chillax.ws");
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
    //$enc=mb_detect_encoding($h, 'UTF-8', true);
    //echo $enc;
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
  $delay=delay_srt($h,610);
else
  $delay=delay_srt($h,310);
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
  $c="intent:".$movie_mpc."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";b.decode_mode=1;end";
  $c="intent:".$movie_mpc."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";b.decode_mode=1;end";
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
</script>
</div></body>
</HTML>
';
}
?>
