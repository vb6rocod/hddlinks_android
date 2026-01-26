<!doctype html>
<?php
include ("../common.php");
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="flash";
}
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
  if (file_exists("1.txt")) unlink("1.txt");
  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: https://filemoon.sx/',
  'X-Embed-Parent: https://filemoon.sx');
  if (preg_match("/alias\=/",$l)) {
    $t1=explode("&alias=",$l);
    $host="https://".$t1[1];
  } else {
    $host="https://".parse_url($l)['host'];
  }
   $id="45b1x7nh1ymm";
   preg_match("/\/e\/(\w+)/",$l,$m);
   $id=$m[1];
   $l1=$host."/api/videos/".$id."/embed/playback";
   //echo $l;

   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
echo '<script>';
echo "const jj='".trim($h)."';";
echo '
(async () => {
const e = JSON.parse(jj).playback;
    function ft(e) {
        const t = e.replace(/-/g, "+").replace(/_/g, "/"),
            r = t.length % 4 === 0 ? 0 : 4 - t.length % 4,
            n = t + "=".repeat(r);
        let a;
        a=atob(n);
        const o = new Uint8Array(a.length);
        for(let x = 0; x < a.length; x += 1) o[x] = a.charCodeAt(x);
        return o
    };
    function xn(e){
        const t = e.map(ft),
            r = t.reduce((o, x) => o + x.length, 0),
            n = new Uint8Array(r);
        let a = 0;
        for(const o of t) n.set(o, a), a += o.length;
        return n
    };


        const r = xn(e.key_parts);

            n = ft(e.iv);
            a = ft(e.payload);
            o = new Uint8Array(r.slice());

            x = new Uint8Array(n.slice());
            d = new Uint8Array(a.slice());
        s=await window.crypto.subtle.importKey("raw", o, {
            name : "AES-GCM"
        }, false, ["encrypt", "decrypt"]);

        var p = await
        window.crypto.subtle.decrypt({
            name : "AES-GCM",
            iv : x
        }, s, d);
        K = new TextDecoder().decode(p);
        $.post( "filemoon_link.php", { link: K} );
})();

</script>
';
//echo '<div id="myId"></div>';
echo "<a href='' id='mytest1'></a>";
if ($flash=="flash") {
echo '
<script>
const myTimeout = setTimeout(myGreeting, 500);

function myGreeting() {
      document.getElementById("mytest1").href="link1.php?file='.urlencode($l).'&title='.urlencode($title).'";
      document.getElementById("mytest1").click();
}
</script>
';
} else {
echo
'
<script>
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

</script>
';
}
//echo '<div id="rr"></div>';
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
';
?>
