<?php
include ("../common.php");
$ua = $_SERVER['HTTP_USER_AGENT'];
$sub=urldecode($_POST["link"]);
//$sub="https://subscene.gdriveplayer.us/?data=aW1Zha%252FXSt4HLFAyvnMO%252Bgnii5SkZKhpuotOewUguSSNLaFkvXGEQ1mqXYfZmoEzETW5wdopAcILFuaPLtCJGVwZSrsP3Bb18UcdF4nUxLSRUR%252FgvaTFJb%252BXPLsPisSQEqFnHv6dHkcnua7LMXR%252BBqHfxbDjNL%252B6P%252B2iaUrRzczw%253D%253D";
$head=array('Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Referer: https://api.hdv.fun/');
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $sub);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_USERAGENT, $ua);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      //curl_setopt($ch, CURLOPT_REFERER, "https://sub1.hdv.fun");
      curl_setopt($ch, CURLOPT_ENCODING,"");
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
      curl_setopt($ch, CURLOPT_TIMEOUT, 25);
      $h = curl_exec($ch);
      curl_close($ch);
      //echo $h;
file_put_contents($base_sub."sub_extern.srt",$h);
?>
