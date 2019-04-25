<html>
<?php
include ("../common.php");
$receivedRecaptcha = $_POST['g-recaptcha-response'];
echo $receivedRecaptcha;
file_put_contents($base_cookie."render.dat",$receivedRecaptcha);
$google_secret =  "6LdbpXYUAAAAACXYiM06jV6DvFB7K2fSVJziVZeL";
$verifiedRecaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify?secret='.$google_secret.'&response='.$receivedRecaptcha;
$handle = curl_init($verifiedRecaptchaUrl);
curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); // not safe but works
//curl_setopt($handle, CURLOPT_CAINFO, "./my_cert.pem"); // safe
$response = curl_exec($handle);
$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
curl_close($handle);
if ($httpCode >= 200 && $httpCode < 300) {
  if (strlen($response) > 0) {
        $responseobj = json_decode($response);
        if(!$responseobj->success) {
            echo "reCAPTCHA is not valid. Please try again!";
            }
        else {
            echo "reCAPTCHA is valid.";
        }
    }
} else {
  echo "curl failed. http code is ".$httpCode;
}
?>
  <head>
    <title>reCAPTCHA demo: Explicit render after an onload callback</title>
    <script type="text/javascript">
      var onloadCallback = function() {
        grecaptcha.render('html_element', {
          'sitekey' : '6LfCmh4TAAAAAKog9f8wTyEOc0U8Ms2RTuDFyYP_'
        });
      };
    </script>
  </head>
  <body>
    <form action="?" method="POST">
      <div id="html_element"></div>
      <br>
      <input type="submit" value="Submit">
    </form>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
        async defer>
    </script>
  </body>
</html>
