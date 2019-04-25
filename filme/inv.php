<?php
//include("../common.php");
//if (file_exists($base_cookie."render.dat")) unlink ($base_cookie."render.dat");
if (isset($_GET["g-recaptcha-response"])) {
$recaptcha= $_GET["g-recaptcha-response"];
//file_put_contents($base_cookie."render.dat",$recaptcha);
echo $_SERVER['QUERY_STRING'];
die();
}
?>
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,height=device-height,minimum-scale=1">
<link rel="shortcut icon" href="https://www.gstatic.com/recaptcha/admin/favicon.ico" type="image/x-icon"/>
<link rel="canonical" href="https://recaptcha-demo.appspot.com/recaptcha-v2-invisible.php">
<script type="application/ld+json">{ "@context": "http://schema.org", "@type": "WebSite", "name": "reCAPTCHA demo - Invisible", "url": "https://recaptcha-demo.appspot.com/recaptcha-v2-invisible.php" }</script>
<meta name="description" content="reCAPTCHA demo - Invisible" />
<meta property="og:url" content="https://recaptcha-demo.appspot.com/recaptcha-v2-invisible.php" />
<meta property="og:type" content="website" />
<meta property="og:title" content="reCAPTCHA demo - Invisible" />
<meta property="og:description" content="reCAPTCHA demo - Invisible" />
<title>reCAPTCHA demo - Invisible</title>

<header>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
</header>
<main>

<script>
setInterval(function(){
document.getElementById("xxxx").click();
}, 3000);
</script>

    <form action="http://hqq.watch/sec/player/embed_player_9331445831509874.php" method="get" id="demo-form">
        <fieldset>
            <!--<legend>An example form</legend>
            <label class="form-field">Example input A: <input type="text" name="ex-a" value="foo"></label>
            <label class="form-field">Example input B: <input type="text" name="ex-b" value="bar"></label>-->

            <button id="xxxx" class="g-recaptcha form-field" data-sitekey="6Ldf5F0UAAAAALErn6bLEcv7JldhivPzb93Oy5t9" data-callback='onSubmit'>Submit ↦</button>
      <input name="vid" type="text" value="xxxU0dEZnVTbVBPeGd0UUduTUMxUndsQT09" style="display:none;">
      <input name="need_captcha" type="text" value="1" style="display:none;">
      <input name="vid" type="text" value="xxxU0dEZnVTbVBPeGd0UUduTUMxUndsQT09" style="display:none;">
        <input name="need_captcha" type="text" value="0" style="display:none;">
        <input name="iss" type="text" value="ODIuMjEwLjE3OC4yNDE=" style="display:none;">
        <input name="at" type="text" value="2a237eaa0571e7cf4bf0efe47d8ecc88" style="display:none;">
        <input name="autoplayed" type="text" value="yes" style="display:none;">
        <input name="referer" type="text" value="on" style="display:none;">
        <input name="http_referer" type="text" value="aHR0cHM6Ly9ocXEud2F0Y2g=" style="display:none;">
        <input name="pass" type="text" value="" style="display:none;">
        <input name="embed_from" type="text" value="" style="display:none;">
        <input name="hash_from" type="text" value="" style="display:none;">
        <input name="secured" type="text" value="0" style="display:none;">
        <input name="token" type="text" value="03" style="display:none;">
        </fieldset>
    </form>
    <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?&hl=ro" async defer></script>
    <script type="text/javascript">
        function onSubmit(token) {
            document.getElementById("demo-form").submit();
        }
    </script>
    </main>

