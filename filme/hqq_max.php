<?php
if (file_exists("../../cookie/max_time_hqq.txt")) {
   //$time_exp=file_get_contents($base_cookie."max_time_hqq.txt");
   $time_exp=file_get_contents("../../cookie/max_time_hqq.txt");
   $time_now=time();
   if ($time_exp > $time_now) {
     $minutes = intval(($time_exp-$time_now)/60);
     $seconds= ($time_exp-$time_now) - $minutes*60;
     if ($seconds < 10) $seconds = "0".$seconds;
     $msg_captcha=" | Expira in ".$minutes.":".$seconds." min.";
   } else
     $msg_captcha="";
} else {
   $msg_captcha="";
}
print $msg_captcha;
?>