<?php
//https://s1.bunnycdn.ru/assets/template_1/min/all.js?636f2814
// var secKey = 'DZmuZuXqa9O0z3b7';
function cipher($key,$text) {
 $u=0;
 $arr=array();
 for ($i=0;$i<256;$i++) {
  $arr[$i]=$i;
 }
 for ($i=0;$i<256;$i++) {
  $u=($u + $arr[$i] + ord($key[$i%strlen($key)])) % 256;
  $r=$arr[$i];
  $arr[$i]=$arr[$u];
  $arr[$u]=$r;
 }
 $out="";
 $u=0;
 $c=0;
 // .toChar()
 for ($i=0;$i<strlen($text);$i++) {
  $c=($c+1)%256;
  $u=($u+$arr[$c])%256;
  $r=$arr[$c];
  $arr[$c]=$arr[$u];
  $arr[$u]=$r;
  $out .="".chr(ord($text[$i]) ^ $arr[($arr[$c]+$arr[$u])%256]);
 }
 return $out;
}
function encrypt_bunny($input,$key) {
  $output="";
  for ($i=0;$i<strlen($input);$i=$i+3) {
    $a=array(-1,-1,-1,-1);
    $a[0]=ord($input[$i]) >> 2;
    $a[1] = (3 & ord($input[$i])) << 4;
    if (strlen($input) > $i+1) {
      $a[1]=$a[1] | (ord($input[$i+1]) >> 4);
      $a[2]=(15 & ord($input[$i+1])) << 2;
    }
    if (strlen($input) > $i+2) {
      $a[2]=$a[2] | (ord($input[$i+2]) >> 6);
      $a[3]=63 & ord($input[$i+2]);
    }
    foreach ($a as $z=>$n) {
     if ($n == -1) $output .= "=";
     else {
      if ($n > -1 && $n< 64) $output .=$key[$n];
     }
    }
  }
  return $output;
}
function encodeVrf_old($text,$key) {
  $nineAnimeKey = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  return urlencode(encrypt_bunny(ceva(encrypt_bunny(cipher($key,$text),$nineAnimeKey)),$nineAnimeKey));
}
function encodeVrf($text,$key) {
 $nineAnimeKey = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
 $a=cipher($key,$text);
 $b=encrypt_bunny($a,$nineAnimeKey);
 $c=encrypt_bunny($b,$nineAnimeKey);
 $d=ceva1($c);
 $e=encrypt_bunny($d,$nineAnimeKey);
 return $e;
}
function decodeVrf($text,$key) {
  $nineAnimeKey = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  $text=preg_replace('/[\t\n\f\r]/','',$text);
  $text=preg_replace("/\=?+/","",$text);
  return urldecode(cipher($key,decrypt_bunny($text,$nineAnimeKey)));
}
function decrypt_bunny($input,$key) {
  $r="";
  $e=0;
  $u=0;
  $t=$input;
  for ($o=0;$o<strlen($t);$o++) {
   $e = $e << 6;
   $i=strpos($key,$t[$o]);
   $e = $e | $i;
   $u +=6;
   if (24 == $u) {
    $r .= chr((16711680 & $e) >> 16);
    $r .= chr((65280 & $e) >> 8);
    $r .=chr(255 & $e);
    $e=0;
    $u=0;
   }
  }
  if (12 == $u) {
   $e = $e >> 4;
   $r .=chr($e);
  } else {
   if (18 == $u) {
     $e = $e >> 2;
     $r .=chr((65280 & $e) >> 8);
     $r .=chr(255 & $e);
   }
  }
  return $r;
}
function ceva($t) {
 $i=7;
 $n="";
 for ($r=0;$r<strlen($t);$r++) {
  $u=ord($t[$r]);
  if ($r % $i==4) $u -=6;
  if ($r % $i==3) $u -=4;
  if ($r % $i==5) $u +=6;
  if ($r % $i==1||$r % $i==0||$r % $i==6) $u -=2;
  if ($r % $i==2) $u +=6;
  $n .=chr($u);
 }
return strrev($n);
}
function ceva1($t) {
 $nineAnimeKey = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
 $i=6;
 $n="";

 for ($r=0;$r<strlen($t);$r++) {
  $u=ord($t[$r]);
  if ($r%$i==1) $u +=5;
  elseif ($r % $i==5) $u -=6;
  elseif ($r % $i==0 || $r%$i==4) $u +=6;
  elseif ($r % $i !=3 || $r%$i !=2) $u -=5;
  $n .=chr($u);

 }
 $n=encrypt_bunny($n,$nineAnimeKey);
    $n=preg_replace_callback(
    "/[a-zA-Z]/",
    function ($a1) {
     return chr(($a1[0] <= 'Z' ? 90 : 122) >= ($a1 = ord($a1[0]) + 13) ? $a1 : $a1 - 26);
    },
    $n
    );
return $n;
}
?>
