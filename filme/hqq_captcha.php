<?php
include ("../common.php");
$filelink=urldecode($_GET["file"]);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
    function decodeUN($a) {
        $a=substr($a, 1);
        //echo $a;
        $s2 = "";
        $s3="";
        $i = 0;
        while ($i < strlen($a)) {
            //$s2 += ('\u0' + $a[i:i+3])  // substr('abcdef', 1, 3);
            $s2 = $s2.'\u0'.substr($a, $i, 3);
            $s3 = $s3.chr(intval(substr($a, $i, 3),16));
            $i = $i + 3;
       }
       return $s3;
   }

function unicodeString($str, $encoding=null) {
    if (is_null($encoding)) $encoding = ini_get('mbstring.internal_encoding');
    return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/u', function($match) use ($encoding) {
        return mb_convert_encoding(pack('H*', $match[1]), $encoding, 'UTF-16BE');
    }, $str);
}
function indexOf($hack,$pos) {
    $ret= strpos($hack,$pos);
    return ($ret === FALSE) ? -1 : $ret;
}
function aa($data){
   $OI="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
   //var o1,o2,o3,h1,h2,h3,h4,bits,i=0,
   $i=0;
   $c1="";
   $c2="";
   $c3="";
   $h1="";
   $h2="";
   $h3="";
   $h4="";
   $bits="";
   $enc="";
   do {
     $h1 = indexOf($OI,$data[$i]);
     $i++;
     $h2 = indexOf($OI,$data[$i]);
     $i++;
     $h3 = indexOf($OI,$data[$i]);
     $i++;
     $h4 = indexOf($OI,$data[$i]);
     $i++;
     //echo $h1." ".$h2." ".$h3." ".$h4."\n";
     $bits=$h1<<18|$h2<<12|$h3<<6|$h4;
     $c1=$bits>>16&0xff;
     $c2=$bits>>8&0xff;
     $c3=$bits&0xff;
     //echo $c1." ".$c2." ".$c3."\n";
     if($h3==64){
       $enc .=chr($c1);
     }
     else
     {
       if($h4==64){
         $enc .=chr($c1).chr($c2);
       }
       else {
         $enc .=chr($c1).chr($c2).chr($c3);
       }
     }
   }
   while($i < strlen($data));
return $enc;
}

function bb($s){
  $ret="";
  $i=0;
  for($i=strlen($s)-1;$i>=0;$i--) {
    $ret .=$s[$i];
  }
return $ret;
}
    function K12K($a, $typ) {
        $codec_a = array("G", "L", "M", "N", "Z", "o", "I", "t", "V", "y", "x", "p", "R", "m", "z", "u",
                   "D", "7", "W", "v", "Q", "n", "e", "0", "b", "=");
        $codec_b = array("2", "6", "i", "k", "8", "X", "J", "B", "a", "s", "d", "H", "w", "f", "T", "3",
                   "l", "c", "5", "Y", "g", "1", "4", "9", "U", "A");
        if ('d' == $typ) {
            $tmp = $codec_a;
            $codec_a = $codec_b;
            $codec_b = $tmp;
        }
        $idx = 0;
        while ($idx < count($codec_a)) {
            $a = str_replace($codec_a[$idx], "___",$a);
            $a = str_replace($codec_b[$idx], $codec_a[$idx],$a);
            $a = str_replace("___", $codec_b[$idx],$a);
            $idx += 1;
        }
        return $a;
    }

    function xc13($arg1) {
        $lg27 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        $l2 = "";
        $l3 = array(0, 0, 0, 0);
        $l4 = array(0, 0, 0);
        $l5 = 0;
        while ($l5 < strlen($arg1)) {
            $l6 = 0;
            while ($l6 < 4 && ($l5 + $l6) < strlen($arg1)) {
                $l3[$l6] = strpos($lg27,$arg1[$l5 + $l6]);
                $l6 += 1;
            }
            $l4[0] = (($l3[0] << 2) + (($l3[1] & 48) >> 4));
            $l4[1] = ((($l3[1] & 15) << 4) + (($l3[2] & 60) >> 2));
            $l4[2] = ((($l3[2] & 3) << 6) + $l3[3]);

            $l7 = 0;
            while ($l7 < count($l4)) {
                if ($l3[$l7 + 1] == 64)
                    break;
                $l2 .= chr($l4[$l7]);
                $l7 += 1;
            }
            $l5 += 4;
        }
        return $l2;
    }
function decode3($w,$i,$s,$e){
$var1=0;
$var2=0;
$var3=0;
$var4=[];
$var5=[];
while(true){
if($var1<5)
     array_push($var5,$w[$var1]); //$var5.push($w[$var1]); //array_push($var5,$w[$var1]) ????
else if($var1<strlen($w))
     array_push($var4,$w[$var1]); //$var4.push($w[$var1]);
$var1++;
if($var2<5)
     array_push($var5,$i[$var2]); //$var5.push($i[$var2]);
else if($var2<strlen($i))
     array_push($var4,$i[$var2]); //$var4.push($i[$var2]);
$var2++;
if($var3<5)
     array_push($var5,$s[$var3]); //$var5.push($s[$var3]);
else if($var3<strlen($s))
     array_push($var4,$s[$var3]); //$var4.push($s[$var3]);
$var3++;
//if (len(w) + len(i) + len(s) + len(e) == len(var4) + len(var5) + len(e)):
if(strlen($w)+strlen($i)+strlen($s)+strlen($e) == count($var4) + count($var5) +strlen($e))
  break;
}
$var6=join('',$var4);
$var7=join('',$var5);
//print_r ($var5);
//die();
$var2=0;
$result=[];
//echo chr(intval(substr($var6,$var1,2),36)-$ad);
for($var1=0;$var1<count($var4);$var1=$var1+2){
   $ad=-1;
   if(ord($var7[$var2])%2)  //if (ord(var7[var2]) % 2):
     $ad=1;
array_push($result,chr(intval(substr($var6,$var1,2),36)-$ad));  //chr(int(var6[var1:var1 + 2], 36) - ll11))
$var2++;
if($var2>=count($var5))
$var2=0;
}
return join('',$result);
}
$ua="Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10', #'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:21.0) Gecko/20100101 Firefox/21.0";
  if (preg_match("/(hqq|netu)(\.tv|\.watch)\/player\/embed_player\.php\?vid=(?P<vid>[0-9A-Za-z]+)/",$filelink,$m))
    $vid=$m["vid"];
  elseif (preg_match("/(hqq|netu)(\.tv|\.watch)\/watch_video\.php\?v=\?vid=(?P<vid>[0-9A-Za-z]+)/",$filelink,$m))
    $vid=$m["vid"];
  elseif (preg_match("/(hqq|netu)(\.tv|\.watch)\/player\/hash\.php\?hash=\d+/",$filelink)) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $filelink);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch,CURLOPT_ENCODING, '');
      curl_setopt($ch, CURLOPT_REFERER, "http://hqq.watch/");
      //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      $h1 = curl_exec($ch);
      curl_close($ch);
      $h1=urldecode($h1);
      //echo urldecode("%3c");
      //echo $h1;
      //vid':'
     preg_match("/vid\s*\'\:\s*\'(?P<vid>[^\']+)\'/",$h1,$m);
     $vid=$m["vid"];
     }
$l="http://hqq.watch/player/embed_player.php?vid=".$vid."&autoplay=no";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $l);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_REFERER, "https://hqq.tv");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
      //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
      //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
      //curl_setopt($ch, CURLOPT_HEADER,1);
      //curl_setopt($ch, CURLOPT_NOBODY,1);
      $h = curl_exec($ch);
      curl_close($ch);

preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h,$m);
$h= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h,$m);
$h= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);
$t1=explode(";;",$h);
$h=$t1[1];
preg_match_all("/;}\('(\w+)','(\w*)','(\w*)','(\w*)'\)\)/",$h,$m);
$h= decode3($m[1][0],$m[2][0],$m[3][0],$m[4][0]);

$l="http://hqq.watch/player/ip.php?type=json";
$x=file_get_contents($l);
//echo $x;
//die();
$iss=str_between($x,'ip":"','"');
$vid=str_between($h,'videokeyorig = "','"');
$at=str_between($h,'at=','&');
$http_referer=str_between($h,'http_referer=','&');
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <meta charset="utf-8">
      <title>Rezolvati captcha</title>
   	  <link rel="stylesheet" type="text/css" href="../custom.css" />
     <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<script type="text/javascript">
// create the XMLHttpRequest object, according browser
function get_XmlHttp() {
  // create the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;
  if(window.XMLHttpRequest) {		// for Forefox, IE7+, Opera, Safari, ...
    xmlHttp = new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {	// for Internet Explorer 5 or 6
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlHttp;
}

// sends data to a php file, via POST, and displays the received answer
function ajaxrequest1(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  document.getElementById("server").innerHTML = '<font size="6" color="red">Asteptati..................</font>';
  var the_data = link;
  var php_file="hqq2.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
       //alert (request.responseText);
       document.getElementById("demo").innerHTML = request.responseText;
       document.getElementById("server").innerHTML = '<font size="6" color="lightblue">Rezolvati captcha</font>';

    }
  }
}

</script>


</head>
<body>
<BR>
<label id="server"><font size="6" color="lightblue">Rezolvati captcha, copiati adresa rezultata in campul de mai jos</font></label><BR>
<BR>
<BR>
<a id="mytest1" href="http://hqq.watch/sec/player/embed_player_04416513754224205.php?iss=<?php echo $iss; ?>&vid=<?php echo $vid; ?>&at=<?php echo $at; ?>&autoplayed=yes&referer=on&http_referer=<?php echo $http_referer; ?>&pass=&embed_from=&need_captcha=0&secured=0" target="_blank"><font size="6" color="yellow">Rezolvati captcha</font></a>
<BR><input type="text" id="gt1" value="" size="70" onpaste="setTimeout(msg2,1000)">(paste)<BR>
<textarea id="gt" rows="5" cols="50"></textarea>(1)
<BR>
<p><font size="4">Ctrl+L = selecteaza adresa; Ctrl+C = copiaza adresa; Ctrl+W = Inchide tab-ul; Ctrl+V = paste</font></p>
<div id="demo"></div>
<script>
function zx(e){
    var charCode = (typeof e.which == "number") ? e.which : e.keyCode
    if (charCode == "49") {
	navigator.clipboard.readText().then(clipText =>
  	document.getElementById("gt").innerText = clipText);
  	setTimeout(msg1,1000);
    } else if (charCode == "50") {
      document.getElementById("mytest1").click();
    }
    return true;
}
function msg1() {
     val_l= $('textarea#gt').html();
     msg="file=" + encodeURIComponent(val_l);
     ajaxrequest1(msg);
}
function msg2() {
     val_l= document.getElementById("gt1").value;
     msg="file=" + encodeURIComponent(val_l);
     ajaxrequest1(msg);
}
document.onkeypress =  zx;
</script>
</body>
</html>
