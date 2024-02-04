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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<style>
#myId {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background-color: red;
  transition: 0.2s;
  position: absolute;
}
</style>
</head>
<body>
';
echo '<div id="myId"></div>';
echo "<a href='' id='mytest1'></a>";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/118.0',
'Referer: https://voxfilmeonline.biz',
);
//echo $l;
//$pattern = '''(?:watch_video\.php\?v=|.+?vid=|e/|f/)([a-zA-Z0-9]+)''';
//http://netu.wiztube.xyz/player/embed_player.php?vid=YrfYaQUUMMEn&autoplay=yes
//$l="https://strcdn.org/e/eTg3U3ZiM1B1MnJEUGlaaEhvL3ZzUT09?adfree=1";
//http://div.str1.site/e/Sk5RVlJDL2lTdHc4b3BQN1lXUW51dz09?autoplay=yes


  $host="https://".parse_url($l)['host'];
  //$l=$host."/e/275205227265208246260265226204211238194271217271255";
  if (preg_match("/hash\=(\w+)/",$l,$m))
    $l=$host."/e/".$m[1];
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
  //echo urldecode($h);
  //die();
  if (preg_match("/file2sub\(\"([^\"]+)\"/",$h,$s))
    $srt=$s[1];
  else
    $srt="";
  preg_match("/orig_vid\s*\=\s*\"([^\"]+)\"/",$h,$m);
  $v=$m[1];
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
  echo '<BR>Click in triunghi sau mutati cercul rosu sus/jos dreapta/stanga, si apasati enter.';

echo '
<script>
    var adbn ="'.$adbn.'";
    var hash_img="'.$hash_img.'";
    var host = "'.$host.'";
    var v = "'.$v.'";
    var srt = "'.$srt.'";
    var flash = "'.$flash.'";
function test(x,y) {
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
    function(data,status){
      handleAjaxResponse(data);
    });
}
function handleAjaxResponse(responseData) {
 document.getElementById("rr").innerHTML=responseData;
 if (flash=="flash") {
  const myTimeout = setTimeout(myGreeting, 500);
 } else {
  const myTimeout = setTimeout(myGO, 500);
 }
}
function myGreeting() {
      document.getElementById("mytest1").href="link1.php?file='.urlencode($l).'&title='.urlencode($title).'";
      document.getElementById("mytest1").click();
}
function myGO() {
  link1="'.urlencode($l).'";
  link="'.urlencode($title).'";
  on();
  var the_data = "link=" + link1 + "&title=" + link;
  $.post("link1.php",the_data,
  function(data,status){
    off();
    document.getElementById("mytest1").href=data;
    document.getElementById("mytest1").click();
    history.go(-1);
  });
}
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
';
echo '
document.onkeydown = detectKey;
function detectKey(e) {
    var posLeft = document.getElementById("myId").offsetLeft;
    var posTop = document.getElementById("myId").offsetTop;
    e = e || window.event;
    e.preventDefault();
    if (e.keyCode == "38") {
        // up arrow
        document.getElementById("myId").style.marginTop  = (posTop-28)+"px";
    }
    else if (e.keyCode == "40") {
        // down arrow
        document.getElementById("myId").style.marginTop  = (posTop+28)+"px";
    }
    else if (e.keyCode == "37") {
       // left arrow
        document.getElementById("myId").style.marginLeft  = (posLeft-28)+"px";
    }
    else if (e.keyCode == "39") {
       // right arrow
        document.getElementById("myId").style.marginLeft  = (posLeft+28)+"px";
    }
    else if (e.keyCode == "13") {
      test(posLeft,posTop);
    }
}

$(document).ready(function() {
    document.getElementById("myId").style.marginTop="200px";
    document.getElementById("myId").style.marginLeft="200px";
    $("img").on("click", function(event) {
        var x = event.pageX - this.offsetLeft;
        var y = event.pageY - this.offsetTop;
        test(x,y);
    });
});
</script>
';


echo '<div id="rr"></div>';
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
';
?>
