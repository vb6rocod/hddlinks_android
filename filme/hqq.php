<!doctype html>
<?php
include ("../common.php");
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="flash";
}
//echo strlen("a30f9b86de567958d13efffd0e19b79e14cfeb9a");
//$l="https://realyplayonli.xyz/e/c2IzL1FZQWZ3dnM1N01BZ3RPSmZtQT09";
//$l="https://strcdn.org/e/eTg3U3ZiM1B1MnJEUGlaaEhvL3ZzUT09?adfree=1";
$l=urldecode($_GET['file']);
$title=urldecode($_GET['title']);
echo '
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>'.$title.'</title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<body>
';
echo "<a href='' id='mytest1'></a>";
echo '<div id="rr"></div>';
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0',
'Referer: https://voxfilmeonline.biz',
);
//echo $l;
//$pattern = '''(?:watch_video\.php\?v=|.+?vid=|e/|f/)([a-zA-Z0-9]+)''';
//http://netu.wiztube.xyz/player/embed_player.php?vid=YrfYaQUUMMEn&autoplay=yes
//$l="https://strcdn.org/e/eTg3U3ZiM1B1MnJEUGlaaEhvL3ZzUT09?adfree=1";
//http://div.str1.site/e/Sk5RVlJDL2lTdHc4b3BQN1lXUW51dz09?autoplay=yes
preg_match("/((?:(watch_video|embed_player)\.php\?v\=)|(\?vid\=)|(\/[e|f]\/))([a-zA-Z0-9]+)/",$l,$m);
//print_r ($m);
$v=$m[5];
$host="https://".parse_url($l)['host'];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //die();
  if (preg_match("/file2sub\(\"([^\"]+)\"/",$h,$s))
    $srt=$s[1];
  else
    $srt="";
  preg_match("/\'videoid\'\:\s*\'([^\']+)\'/",$h,$m);
  $video_id=$m[1];
  preg_match("/\'videokey\'\:\s*\'([^\']+)\'/",$h,$m);
  $video_key=$m[1];
  preg_match("/adbn\s*\=\s*\'([^\']+)\'/",$h,$m);
  $adbn=$m[1];
  $post='{"videoid": "'.$video_id.'", "videokey": "'.$video_key.'", "width": 400, "height": 400}';
  //echo $post;
  //die();
  //Idea from https://github.com/Gujal00/ResolveURL
  $l1=$host."/player/get_player_image.php";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0',
'Referer: '.$l,
'Origin: '.$host,
'Content-Type: application/json',
'X-Requested-With: XMLHttpRequest'
);
//echo $post;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  $r=json_decode($h,1);
  //print_r ($r);
  $hash_img=$r['hash_image'];
  $image=$r['image'];
  echo '<img src="'.$image.'">';
  echo '<BR>Click in triunghi';
echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>';
echo '
<script>
$(document).ready(function() {
    var adbn ="'.$adbn.'";
    var hash_img="'.$hash_img.'";
    var host = "'.$host.'";
    var v = "'.$v.'";
    var srt = "'.$srt.'";
    $("img").on("click", function(event) {
        var x = event.pageX - this.offsetLeft;
        var y = event.pageY - this.offsetTop;
        //alert("X Coordinate: " + x + " Y Coordinate: " + y);

    $.post("hqq1.php",
    {
      adb: adbn,
      hash_img: hash_img,
      v: v,
      x: x,
      y: y,
      srt: srt,
      host: host
    },
    function(data,status){';
    echo 'document.getElementById("rr").innerHTML=data;';
//////////////////////////////////////////
if ($flash=="flash") {
echo '

const myTimeout = setTimeout(myGreeting, 500);

function myGreeting() {
      document.getElementById("mytest1").href="link1.php?file='.urlencode($l).'&title='.urlencode($title).'";
      document.getElementById("mytest1").click();
}

';
} else {
echo
'

function myGO() {
  link1="'.urlencode($l).'";
  link="'.urlencode($title).'";
  on();
  var request =  new XMLHttpRequest();
  var the_data = "link=" + link1 + "&title=" + link;
  var php_file="link1.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      off();
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
      history.go(-1);
    }
  }
}
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
const myTimeout = setTimeout(myGO, 500);

';
}
//////////////////////////////////////////
echo '
    });
    });
});
</script>
';
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
';
?>
