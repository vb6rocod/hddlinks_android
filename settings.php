<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<link rel="stylesheet" type="text/css" href="./custom.css" />
      <title>Setari</title>



</head>
<body>
<H2>Setari</H2>
<?php
error_reporting(0);
$add_reg="";
include ("common.php");
$tip=$_GET["tip"];
if ($tip) {
$user=$_GET["user"];
$user=str_replace("%40","@",$user);
$pass=$_GET["pass"];
if (isset($_GET['ua'])) $ua=$_GET['ua'];
if ($tip=="cineplex") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."cineplex.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="reg") {
$f=$base_pass."vlc.txt";
if (file_exists($f)) {
$mpc=trim(file_get_contents($f));
} else {
$mpc="";
}
$p=dirname($mpc);
$file=$p."/add_url_protocol_mpv.reg";
//C:\\EasyPhp\\localweb\\mobile1\\mpv\\run_in_mpv.bat
$run=$p."/run_in_mpv.bat";
$run=str_replace("/","\\\\",$run);
$out='Windows Registry Editor Version 5.00

[HKEY_CLASSES_ROOT\mpv]
@="URL: mpv"
"URL Protocol"=""

[HKEY_CLASSES_ROOT\mpv\shell]
@=""

[HKEY_CLASSES_ROOT\mpv\shell\open]
@=""

[HKEY_CLASSES_ROOT\mpv\shell\open\command]
@="\"'.$run.'\" \"%1\""';
file_put_contents($file,$out);
$add_reg="click dreapta --> merge pe fisierul ".$file;
} elseif ($tip=="cineplex_serv") {
 $txt=$user;
 $new_file = $base_pass."cineplex_host.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="weather") {
 $txt=$user;
 $new_file = $base_pass."weather.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="player") {
 $txt=$user;
 $new_file = $base_pass."player.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="filmeseriale") {
 $txt=$user;
 $new_file = $base_pass."filmeseriale.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="tmdb") {
 $txt=$user;
 $new_file = $base_pass."tmdb.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="videospider") {
 $txt=$user;
 $new_file = $base_pass."videospider.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="omdb") {
 $txt=$user;
 $new_file = $base_pass."omdb.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
 } elseif ($tip=="youtube") {
 $txt=$user;
 $new_file = $base_pass."youtube.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="mpc") {
 $txt=$user;
 $new_file = $base_pass."mpc.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="vlc") {
 $txt=$user;
 $new_file = $base_pass."vlc.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="html5") {
 $txt=$user;
 $new_file = $base_pass."html5.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="jwv") {
 $txt=$user;
 $new_file = $base_pass."jwv.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="mx") {
 $txt=$user;
 $new_file = $base_pass."mx.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="spicetv") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."spicetv.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="tvpemobil") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."tvpemobil.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="opensubtitles") {
 $txt=$user."|".$pass."|".$ua;
 $new_file = $base_pass."opensubtitles.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="opensubtitlesc") {
 $txt=$user;
 $new_file = $base_pass."opensubtitlesc.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="movietv") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."movietv.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="seenowtv") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."seenowtv.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="see_c") {
 $txt=$user;
 $new_file = $base_pass."see_c.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="noobroom") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."noob_log.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="serialepenet") {
 $txt=$user;
 $new_file = $base_pass."serialepenet.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="antenaplay") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."antenaplay.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="amigo") {
 $txt=$user;
 $new_file = $base_pass."amigo.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="delete") {
 $f=$base_pass."noob_log.txt";
 unlink ($f);
} elseif ($tip=="movie-inn") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."movie-inn.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="airfun") {
 $txt=$user;
 $new_file = $base_pass."airfun.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="grid") {
 $txt=$user;
 $new_file = $base_pass."grid.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
 } elseif ($tip=="scrollbar") {
 $txt=$user;
 $new_file = $base_pass."scrollbar.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
 $da=$base_script."jquery.nicescroll.min.js";
 $nu=$base_script."jquery.nicescroll.min1.js";
 if ($txt=="DA") {
  if (file_exists($nu) && !file_exists($da))
    rename ($nu,$da);
 } else {
  if (file_exists($da) && !file_exists($nu))
    rename ($da,$nu);
 }
 } elseif ($tip=="adult") {
 $txt=$user;
 $new_file = $base_pass."adult.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="tastatura") {
 $txt=$user;
 $new_file = $base_pass."tastatura.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="cineplex") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."cineplex.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="tvhd-online") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."tvhd-online.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
 } elseif ($tip=="tvplay") {
 $txt=$user;
 $new_file = $base_pass."tvplay.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="seenow") {
 $txt=$user;
 $new_file = $base_pass."seenow.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="flash") {
 $txt=$user;
 $new_file = $base_pass."flash.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
} elseif ($tip=="tastatura1") {
 $txt=$user;
 $new_file = $base_pass."tastatura.txt";
 $fh = fopen($new_file, 'w');
 fwrite($fh, $txt);
 fclose($fh);
}
}
$user="";
$pass="";
$f=$base_pass."player.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
} else {
$user="";
$pass="";
}
echo '
<h4>Mod player</h4>
<form action="settings.php">
Selecteaza mod player: <select id="user" name="user">
';
if ($user=="flash") {
echo '
<option value="flash" selected>flash</option>
<option value="mpc">mpv</option>
<option value="mp">mediaplayer</option>';
} elseif ($user=="mp") {
echo '
<option value="flash">flash</option>
<option value="mpc">mpv</option>
<option value="mp" selected>mediaplayer</option>';
} elseif ($user="mpc") {
echo '
<option value="flash">flash</option>
<option value="mpc" selected>mpv</option>
<option value="mp">mediaplayer</option>';
} else {
echo '
<option value="flash">flash</option>
<option value="mpc" selected>mpv</option>
<option value="mp">mediaplayer</option>';
}
echo '</select>
<input type="hidden" name="tip" value="player">
<input type="submit" value="Memoreaza">
</form>
<hr>
';

$user="";
$pass="";
$f=$base_pass."vlc.txt";
if (file_exists($f)) {
$mpc=trim(file_get_contents($f));
} else {
$mpc="";
}
echo '
<p>Instalare mpv (vezi <a href="info.html"><b>Sfaturi</b></a>)</p>
<form action="settings.php">Cale mpv.exe (mpv.exe) - folositi Unix style "C:/"<input type="text" name="user" id="user" size="50" value="'.$mpc.'">
<input type="hidden" name="tip" value="vlc">
<input type="submit" value="Memoreaza">
</form>
';
if (file_exists($mpc) && preg_match("/mpv\.exe/",$mpc) && !preg_match("/\\\\/",$mpc)) {
echo '
<form action="settings.php">
<input type="hidden" name="tip" value="reg">
<input type="submit" value="Genereaza reg file">
</form>';
echo $add_reg;
}
echo '<hr>';
$f=$base_pass."mx.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
} else {
$user="pro";
$pass="";
}
echo '
<h4>Tip MX Player</h4>
<form action="settings.php">
Selecteaza mod: <select id="user" name="user">
';
if ($user=="pro") {
echo '
<option value="pro" selected>Pro</option>
<option value="ad">ad</option>';
} elseif ($user=="ad") {
echo '
<option value="pro">Pro</option>
<option value="ad" selected>ad</option>';
}
echo '</select>
<input type="hidden" name="tip" value="mx">
<input type="submit" value="Memoreaza">
</form>
<hr>
';

$f=$base_pass."tmdb.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
} else {
$user="";
$pass="";
}

echo '
<h4>TMDB Api Key (https://www.themoviedb.org/settings/api)</h4>
<form action="settings.php">
cod:<input type="text" name="user" value="'.$user.'" size="40">
<input type="hidden" name="tip" value="tmdb">
<input type="submit" value="Memoreaza">
</form>
<hr>
';
$f=$base_pass."youtube.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
} else {
$user="";
$pass="";
}

echo '
<h4>Youtube Api Key</h4>
<form action="settings.php">
cod:<input type="text" name="user" value="'.$user.'" size="40">
<input type="hidden" name="tip" value="youtube">
<input type="submit" value="Memoreaza">
</form>
<hr>
';
$f=$base_pass."opensubtitles.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
$ua=$t1[2];
} else {
$user="";
$pass="";
$ua="";
}
echo '
<h4>Setari opensubtitles (see https://www.opensubtitles.org)</h4>
<form action="settings.php">
User:<input type="text" name="user" value="'.$user.'"></BR>
Pass:<input type="text" name="pass" value="'.$pass.'"></BR>
UserAgent:<input type="text" name="ua" value="'.$ua.'">
<input type="hidden" name="tip" value="opensubtitles">
<input type="submit" value="Memoreaza">
</form>
<hr>
';
$f=$base_pass."opensubtitlesc.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$user=trim($h);
} else {
$user="";
}
echo '
<h4>Setari opensubtitles (new api) (see https://opensubtitles.stoplight.io/)</h4>
<form action="settings.php">
API Key:<input type="text" name="user" value="'.$user.'">
<input type="hidden" name="tip" value="opensubtitlesc">
<input type="submit" value="Memoreaza">
</form>
<hr>
';
$f=$base_pass."cineplex.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
} else {
$user="";
$pass="";
}
echo '
<h4>Cont cineplex.to</h4>
<form action="settings.php">
User:<input type="text" name="user" value="'.$user.'"></BR>
Pass:<input type="text" name="pass" value="'.$pass.'">
<input type="hidden" name="tip" value="cineplex">
<input type="submit" value="Memoreaza">
</form>
<hr>
';
$f=$base_pass."cineplex_host.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
} else {
$user="";
$pass="";
}

echo '
<h4>Server cineplex.to</h4>
<form action="settings.php">
cod:<input type="text" name="user" value="'.$user.'" size="40">
<input type="hidden" name="tip" value="cineplex_serv">
<input type="submit" value="Memoreaza">
</form>
<hr>
';
$user="";
$pass="";
$f=$base_pass."weather.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
} else {
$user="";
$pass="";
}

echo '
<h4>Cod Localitate (https://weather.codes/romania/)</h4>
<form action="settings.php">
cod:<input type="text" name="user" value="'.$user.'" size="40">
<input type="hidden" name="tip" value="weather">
<input type="submit" value="Memoreaza">
</form>
<hr>
';
$user="";
$pass="";
$f=$base_pass."adult.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
} else {
$user="";
$pass="";
}
echo '
<h4>Continut (18+)</h4>
<form action="settings.php">
Permite accesul la continut (18+) : <select id="user" name="user">
';
if ($user=="DA") {
echo '
<option value="DA" selected>DA</option>
<option value="NU">NU</option>';
} else {
echo '
<option value="DA">DA</option>
<option value="NU" selected>NU</option>';
}
echo '</select>
<input type="hidden" name="tip" value="adult">
<input type="submit" value="Memoreaza">
</form>
<hr>
';


?>
<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>
<br></body>
</html>
