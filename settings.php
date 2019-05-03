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
include ("common.php");
$tip=$_GET["tip"];
if ($tip) {
$user=$_GET["user"];
$user=str_replace("%40","@",$user);
$pass=$_GET["pass"];
if ($tip=="cineplex") {
 $txt=$user."|".$pass;
 $new_file = $base_pass."cineplex.txt";
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
} elseif ($tip=="omdb") {
 $txt=$user;
 $new_file = $base_pass."omdb.txt";
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
if ($user=="direct") {
echo '
<option value="direct" selected>direct</option>
<option value="flash">flash</option>
<option value="html5">html5</option>
<option value="mpc">mpc|vlc</option>
<option value="mp">mediaplayer</option>';
} elseif ($user=="flash") {
echo '
<option value="direct">direct</option>
<option value="flash" selected>flash</option>
<option value="html5">html5</option>
<option value="mpc">mpc|vlc</option>
<option value="mp">mediaplayer</option>';
} elseif ($user=="html5") {
echo '
<option value="direct">direct</option>
<option value="flash">flash</option>
<option value="html5" selected>html5</option>
<option value="mpc">mpc|vlc</option>
<option value="mp">mediaplayer</option>';
} elseif ($user=="mp") {
echo '
<option value="direct">direct</option>
<option value="flash">flash</option>
<option value="html5">html5</option>
<option value="mpc">mpc|vlc</option>
<option value="mp" selected>mediaplayer</option>';
} else {
echo '
<option value="direct">direct</option>
<option value="flash">flash</option>
<option value="html5">html5</option>
<option value="mpc" selected>mpc|vlc</option>
<option value="mp">mediaplayer</option>';
}
echo '</select>
</BR>
<input type="hidden" name="tip" value="player">
<input type="submit" value="Memoreaza">
</form>
<BR>
';
$user="";
$pass="";
$f=$base_pass."mpc.txt";
if (file_exists($f)) {
$mpc=trim(file_get_contents($f));
} else {
$mpc="";
}
echo '
<form action="settings.php">Cale Media Player Clasic -HC (mpc-hc.exe) copiati aici<input type="text"  size="50" name="user" id="user" value="'.$mpc.'">
<input type="hidden" name="tip" value="mpc">
<BR><input type="submit" value="Memoreaza">
</form>
<BR>
</form>
<BR>';

$user="";
$pass="";
$f=$base_pass."vlc.txt";
if (file_exists($f)) {
$mpc=trim(file_get_contents($f));
} else {
$mpc="";
}
echo '
<form action="settings.php">Cale VideoLan (vlc.exe) copiati aici<input type="text" name="user" id="user" size="50" value="'.$mpc.'">
<input type="hidden" name="tip" value="vlc">
<BR><input type="submit" value="Memoreaza">
</form>
<BR>
</form>
<BR>';
$f=$base_pass."tastatura.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
} else {
$user="NU";
$pass="";
}
echo '
<h4>Tastatura:</h4>
<form action="settings.php">
Folosesc tastatura/telecomanda: <select id="user" name="user">
';
if ($user=="DA") {
echo '
<option value="DA" selected>DA</option>
<option value="NU">NU</option>';
} elseif ($user=="NU") {
echo '
<option value="DA">DA</option>
<option value="NU" selected>NU</option>';
}
echo '</select>
</BR>
<input type="hidden" name="tip" value="tastatura">
<input type="submit" value="Memoreaza">
</form>
<BR>
<hr>
';
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
</BR>
<input type="hidden" name="tip" value="mx">
<input type="submit" value="Memoreaza">
</form>
<BR>
<hr>
';

$f=$base_pass."css.txt";
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
<h4>Mod contur link</h4>
<form action="settings.php">
Alege mod: <select id="user" name="user">
';
if ($user=="ALB") {
echo '
<option value="ALB" selected>MOD MEDIA PLAYER (contur)</option>
<option value="NEGRU">Fara contur</option>';
} else {
echo '
<option value="ALB">MOD MEDIA PLAYER (contur)</option>
<option value="NEGRU" selected>Fara contur</option>';
}
echo '</select>
</BR>
<input type="hidden" name="tip" value="css">
<input type="submit" value="Memoreaza">
</form>
<BR>
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
cod:<input type="text" name="user" value="'.$user.'" size="40"></BR>
<input type="hidden" name="tip" value="tmdb">
<input type="submit" value="Memoreaza">
</form>
<hr>
';
$f=$base_pass."omdb.txt";
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
<h4>OMDB Api Key (http://www.omdbapi.com/apikey.aspx)</h4>
<form action="settings.php">
cod:<input type="text" name="user" value="'.$user.'" size="40"></BR>
<input type="hidden" name="tip" value="omdb">
<input type="submit" value="Memoreaza">
</form>
<hr>
';
$f=$base_pass."filmeseriale.txt";
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
<h4>GoogleCaptcha filmeseriale</h4>
<form action="settings.php">
cod:<input type="text" name="user" value="'.$user.'"></BR>
<input type="hidden" name="tip" value="filmeseriale">
<input type="submit" value="Memoreaza">
</form>
<hr>
';

$f=$base_pass."tvpemobil.txt";
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
<h4>Cont tvpemobil.net</h4>
<form action="settings.php">
User:<input type="text" name="user" value="'.$user.'"></BR>
Pass:<input type="password" name="pass" value="'.$pass.'"></BR>
<input type="hidden" name="tip" value="tvpemobil">
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
Pass:<input type="password" name="pass" value="'.$pass.'"></BR>
<input type="hidden" name="tip" value="cineplex">
<input type="submit" value="Memoreaza">
</form>
<hr>
';
$f=$base_pass."antenaplay.txt";
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
<h4>Cont antenaplay</h4>
<form action="settings.php">
User:<input type="text" name="user" value="'.$user.'"></BR>
Pass:<input type="password" name="pass" value="'.$pass.'"></BR>
<input type="hidden" name="tip" value="antenaplay">
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
</BR>
<input type="hidden" name="tip" value="adult">
<input type="submit" value="Memoreaza">
</form>
<BR>
<hr>
';


?>
<BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR><BR>
<br></body>
</html>
