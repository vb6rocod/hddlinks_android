<!DOCTYPE html>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
require ("common.php");
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $os="win";
} else {
    $os="linux";
}
if (file_exists($base_pass."player.txt")) {
   $mod=file_get_contents($base_pass."player.txt");
} else
   $mod="";
// seting tast
$f=$base_pass."tastatura.txt";
if ($os=="win")
  file_put_contents($f,"NU");
else
  file_put_contents($f,"DA");
// mod player
$f=$base_pass."player.txt";
// cale mpv
$f=$base_pass."vlc.txt";
$h=@file_get_contents($f);
$info_update="";
if ($os=="win") {
if (!file_exists($f) || !file_exists($h) || !file_exists(dirname($h)."/add_url_protocol_mpv.reg")) {
$info_update="<p>Pentru o buna vizionare instalati mpv! Citeste cum, la <b>Sfaturi</b></p>";
}
}
$f=$base_pass."adult.txt";
if (!file_exists($f)) {
$adult="NU";
//echo '<h2 style="background-color:deepskyblue;color:black"><center>Activati sau dezactivati sectiunea Adult din setari!</center></h2>';
} else {
$h=file_get_contents($f);
$t1=explode("|",$h);
$adult=$t1[0];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>HD4ALL</title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="custom.css?123" />
<style>
td {
    font-style: bold;
    font-size: 20px;
}
</style>
<script type="text/javascript">
function ajaxrequest(url) {
  var request =  new XMLHttpRequest();
  on();
  var the_data = 'url='+url;
  var php_file='update.php';
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      off();
      alert (request.responseText);
      location.reload();
    }
  }
}
   function zx(e){
     var instance = $.fancybox.getInstance();
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13"  && instance !== false) {
       $.fancybox.close();
       setTimeout(function(){ document.getElementById(id_link).focus(); }, 500);
     } else if (charCode == "49" && e.target.type != "text") {
       msg="tv/w.php";
       document.getElementById("fancy").href=msg;
       document.getElementById("fancy").click();
     } else if (charCode == "50" && e.target.type != "text") {
       window.open("https://fiber.google.com/speedtest/");
    }
   }
document.onkeypress =  zx;
</script>

</head>
<body>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
<script>
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
</script>
<?php
//error_reporting(0);
function is_valid_date($value, $format = 'dd.mm.yyyy'){
    if(strlen($value) >= 6 && strlen($format) == 10){

        // find separator. Remove all other characters from $format
        $separator_only = str_replace(array('m','d','y'),'', $format);
        $separator = $separator_only[0]; // separator is first character

        if($separator && strlen($separator_only) == 2){
            // make regex
            $regexp = str_replace('mm', '(0?[1-9]|1[0-2])', $format);
            $regexp = str_replace('dd', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp);
            $regexp = str_replace('yyyy', '(19|20)?[0-9][0-9]', $regexp);
            $regexp = str_replace($separator, "\\" . $separator, $regexp);
            if($regexp != $value && preg_match('/'.$regexp.'\z/', $value)){

                // check date
                $arr=explode($separator,$value);
                $day=$arr[0];
                $month=$arr[1];
                $year=$arr[2];
                if(@checkdate($month, $day, $year))
                    return true;
            }
        }
    }
    return false;
}
$p=$_SERVER['SCRIPT_FILENAME'];
$p=str_replace("\\","/",$p);
$script_directory = substr($p, 0, strrpos($p, '/'));
$f_version=$script_directory."/version_m.txt";
if (file_exists($f_version)) {
  $curr_vers=trim(file_get_contents($script_directory."/version_m.txt"));
  $l="http://hdforall.freehostia.com/version_m.txt";
  $l="https://raw.githubusercontent.com/vb6rocod/hddlinks/master/version_m.txt";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $t=explode("\n",$h);
  $avb_vers=trim($t[0]);
  $serv_update=trim($t[1]);
$valid_date = is_valid_date($avb_vers);
if ($valid_date) {
if ($avb_vers <> $curr_vers) {
  $info = "O nouă versiune este disponibilă (".$avb_vers.")! Apasati aici pentru actualizare.";
  echo '<p><a onclick="ajaxrequest('."'".$serv_update."')".'"'." style='cursor:pointer;'>".'<font size="4">'.$info.'</font></a></p>';
} else {
  $info = "";
}
} else {
  $info="Eroare citire data versiune disponibila!";
  echo '<p>'.$info.'</p>';
}
}
// patch install
$base_url=dirname($_SERVER['SCRIPT_FILENAME']);
if (!file_exists($base_url."/subs"))  @mkdir($base_url."/subs", 0777);
$parent_url= dirname($base_url);
if (!file_exists($parent_url."/data"))  @mkdir($parent_url."/data", 0777);
if (!file_exists($parent_url."/cookie"))  @mkdir($parent_url."/cookie", 0777);
if (!file_exists($parent_url."/parole"))  @mkdir($parent_url."/parole", 0777);
$dr=$_SERVER['DOCUMENT_ROOT'];
if (!file_exists($dr."/e"))  @mkdir($dr."/e", 0777);
?>
<BR><BR>
<table align="center" width="90%">
<tr>
<TD width="50%"><font size="5"><?php echo '<a onclick="ajaxrequest('."'".$serv_update."')".'"'." style='cursor:pointer;'>"; ?>HD4ALL</a>
<?php
if (file_exists("../scriptsb/index.php"))
echo ' | <a href="../scriptsb/index.php">HD4ALL (new)</a></font></TD>';
else
echo '</font></TD>';
?>
<TD align="right"><a href="settings.php?&tip=" target="_blank"><font size="5">Setări<?php if (!file_exists($base_pass."mx.txt")) echo" (nou! Setati MX Player!)"; ?></font></a></TD>
</TR>
</TABLE>
<?php
if ($adult == "DA") {
echo '
<table border="1px" align="center" width="90%">
<TR>
<td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="4">HD4ALL</TD>
</TR>
<TR>
<TD align="center" width="25%"><a href="filme.php">Filme</a></TD>
<TD align="center" width="25%"><a href="seriale.php">Seriale</a></TD>
<TD align="center" width="25%"><a href="tv.php">Live TV & Emisiuni</a></TD>
<TD align="center" width="25%"><a href="adult.php">Adult</a></TD>';
} else {
echo '
<table border="1" align="center" width="90%">
<TR>
<td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">HD4ALL</TD>
</TR>
<TR>
<TD align="center" width="33%"><a href="filme.php">Filme</a></TD>
<TD align="center" width="33%"><a href="seriale.php">Seriale</a></TD>
<TD align="center" width="33%"><a href="tv.php">Live TV & Emisiuni</a></TD>';
}
?>
</TR>
<TR>
<TD align="center" colspan="4"><a href='#' onclick='location.reload(true); return false;'>reload page...</a></td>
</TR>
</TABLE>
<BR><BR>
<table border="0px" align="center" width="90%">
<TR><TD align="right"><a href="info.html"><font size="5">Sfaturi</font></a></TR></TABLE>
<?php
echo $info_update;
if (file_exists($base_pass."player.txt")) {
   $mod=file_get_contents($base_pass."player.txt");
} else
   $mod="";


$list = glob($base_cookie."*.dat");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
$list = glob($base_fav."*.list");
   foreach ($list as $l) {
    str_replace(" ","%20",$l);
    unlink($l);
}
$firefox = $base_pass."firefox.txt";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
if (!file_exists($firefox))
  file_put_contents($firefox,$ua);
if (!file_exists($base_pass."tmdb.txt"))
  file_put_contents($base_pass."tmdb.txt","d0e6107be30f2a3cb0a34ad2a90ceb6f");
?>
<br>
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
