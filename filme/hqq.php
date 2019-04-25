<?php
include ("../common.php");

if (file_exists($base_cookie."render.dat")) unlink ($base_cookie."render.dat");
if (isset($_GET["g-recaptcha-response"])) {
$recaptcha= $_GET["g-recaptcha-response"];
file_put_contents($base_cookie."render.dat",$recaptcha);
echo $_SERVER['QUERY_STRING'];
die();
}
?>
<html>
  <head>
    <script type="text/javascript">
        var verifyCallback = function(response) {
            //document.getElementById("btn").style.display = 'block';
            my_form.submit();
            html_element.style.display = 'none';
        };

        var onloadCallback = function() {
            grecaptcha.render('html_element', {
                'sitekey' : '6LfCmh4TAAAAAKog9f8wTyEOc0U8Ms2RTuDFyYP_',
                'callback' : verifyCallback
        });
      };
    </script>
<link href="//c.hqq.tv/styles/cbv2new/theme/main.css?20" rel="stylesheet" type="text/css" />
<link href="//c.hqq.tv/styles/cbv2new/theme/bootstrap.css?7" rel="stylesheet" type="text/css" />

  </head>
  <body>
<script src="//c.hqq.tv/js/video.jquery_plugs/jquery-latest.min.js"></script>
<script type="text/javascript">
var m = 1;
function check(){
    if(document.body.scrollHeight > window.innerHeight){
        var n = ((window.innerHeight)/document.body.scrollHeight).toFixed(2);
        if(m > n){
            $('html').css({'transform': 'scale(' n ',' n ')'});
            var x = (window.innerHeight/document.body.scrollHeight).toFixed(2);
            m = x;
        }
    }
    
}

setInterval(function() {
  check();
}, 2000);

</script>
    <form action="" method="GET" id="my_form" target="_self">
      <input name="vid" type="text" value="WXdvOHY1NTgrOXN2MjExQ3duTUxndz09" style="display:none;">
      <input name="need_captcha" type="text" value="1" style="display:none;">
      <input name="vid" type="text" value="WXdvOHY1NTgrOXN2MjExQ3duTUxndz09" style="display:none;">
        <input name="need_captcha" type="text" value="0" style="display:none;">
        <input name="iss" type="text" value="ODIuMjEwLjE3OC4yNDE=" style="display:none;">
        <input name="at" type="text" value="669f22aa9bb1833c62fbe7589c61c3dc" style="display:none;">
        <input name="autoplayed" type="text" value="yes" style="display:none;">
        <input name="referer" type="text" value="on" style="display:none;">
        <input name="http_referer" type="text" value="aHR0cDovL3d3dy5maWxtZW9ubGluZTIwMTYuYml6L3RoZS1oYXJkLXdheS0yMDE5Lw==" style="display:none;">
        <input name="pass" type="text" value="" style="display:none;">
        <input name="embed_from" type="text" value="" style="display:none;">
        <input name="hash_from" type="text" value="" style="display:none;">
        <input name="secured" type="text" value="0" style="display:none;">
        <input name="gtoken" type="text" value="" style="display:none;">
               <div style="display: flex; align-items: center; justify-content: center;height: 100%;">
            <div>

                <div id="html_element" class="g-recaptcha" style="transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;"></div>
                <br>
                <a href="javascript:void(0);" onclick="my_form.submit();" class="btn btn-primary btn-lg selectFiles" style="display:none;width:300px;" id="btn">Watch video</a>
            </div>
        </div>
    </form>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
        async defer>
    </script>
  </body>
</html>

