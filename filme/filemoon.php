<!doctype html>
<?php
include ("../common.php");
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="flash";
}
$l=urldecode($_GET['file']);
$host="https://".parse_url($l)['host'];
$title=urldecode($_GET['title']);
if (preg_match("/ref_filemoon/",$l)) {
 $t1=explode("&ref_filemoon=",$l);
 $l=$t1[0];
 $ref_filemoon=parse_url($t1[1])['host'];
} else
 $ref_filemoon=$host;
//echo $l;
//https://weneverbeenfree.com/assets/videoPagesBundle-CQv1AfZY.js
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
/*
Referer: https://bysesukior.com/e/r35jmej2719k
X-Embed-Origin: filmebro.com
X-Embed-Referer: https://filmebro.com/
X-Embed-Parent: https://bysesukior.com/e/r35jmej2719k
*/
// Thanks to Gujal00
//https://github.com/Gujal00/ResolveURL/blob/master/script.module.resolveurl/lib/resolveurl/plugins/byse.py
function base64_urlencode($str,$strip=true) {
  $a=base64_encode($str);
  $a=str_replace("+","-",$a);
  $a=str_replace("_","/",$a);
  if ($strip)
   $a=preg_replace("/\=+$/","",$a);
  return $a;
}
$v_id="cf363c6da0624e99a72bfebbc7804c37";
$d_id="e9d276ab3be845d19876aae380d4d8b7";
$confidence=0.91;
        $t_data = array(
            'viewer_id'=> $v_id,
            'device_id' => $d_id,
            'confidence' => $confidence,
            'iat' => time(),
            'exp' =>time() + 600
        );
$t_bdata=base64_urlencode(json_encode($t_data));

$t_sig=openssl_digest(hash("sha256",$t_bdata),"sha256");

$token=$t_bdata.".".$t_sig;
$r=array("fingerprint" =>array(
  "token" => $token,
  "viewer_id" => $v_id,
  "device_id" => $d_id,
  "confidence" =>$confidence
  ));
//print_r ($r);
$post=json_encode($r);

  $head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:144.0) Gecko/20100101 Firefox/144.0',
  'Accept: */*',
  'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
  'Accept-Encoding: deflate',
  'Referer: '.'https://'.$ref_filemoon.'/',
  'X-Embed-Origin: '.$ref_filemoon,
  'X-Embed-Parent: '.$l);
//print_r ($head);
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:150.0) Gecko/20100101 Firefox/150.0',
'Accept: */*',
'Accept-Language: ro-RO,ro-GB;q=0.9,en;q=0.8',
'Accept-Encoding: deflate',
'Referer: '.'https://'.$ref_filemoon.'/',
'Content-Type: application/json',
'X-Embed-Parent: '.$l,
'Content-Length: '.strlen($post),
'Origin: https://'.$ref_filemoon,
'X-Embed-Origin: '.$ref_filemoon,
'Connection: keep-alive');
  if (preg_match("/alias\=/",$l)) {
    $t1=explode("&alias=",$l);
    $host="https://".$t1[1];
  } else {
    $host="https://".parse_url($l)['host'];
  }
   //$id="45b1x7nh1ymm";
   preg_match("/\/e\/(\w+)/",$l,$m);
   $id=$m[1];
   $l1=$host."/api/videos/".$id."/embed/playback";
   //echo $l;
//post={"fingerprint":{"token":"eyJ2aWV3ZXJfaWQiOiJjZjM2M2M2ZGEwNjI0ZTk5YTcyYmZlYmJjNzgwNGMzNyIsImRldmljZV9pZCI6ImU5ZDI3NmFiM2JlODQ1ZDE5ODc2YWFlMzgwZDRkOGI3IiwiY29uZmlkZW5jZSI6MC42LCJpYXQiOjE3Nzc3MTcwMzMsImV4cCI6MTc3NzcxNzYzM30.5aZb1E9RgzkfC7Vtb83JFx4Tw8S2NV74-p6_EBsF5XM","viewer_id":"cf363c6da0624e99a72bfebbc7804c37","device_id":"e9d276ab3be845d19876aae380d4d8b7","confidence":0.6}}
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_POST,1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
   curl_setopt($ch, CURLOPT_ENCODING,"");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
   curl_setopt($ch, CURLOPT_TIMEOUT, 25);
   $h = curl_exec($ch);
   curl_close($ch);
//echo $h;
//die();
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
