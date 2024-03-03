<?php
class bunny
{
  //private $key_enc="MPPBJLgFwShfqIBx";
  //private $key_dec="hlPeNwkncH0fq9so";
  //private $key_enc="rzyKmquwICPaYFkU"; //09.07.2023
  //private $key_dec="8z5Ag5wgagfsOuhz"; //09.07.2023
  //private $key_enc="FWsfu0KQd9vxYGNB"; //30.07.2023
  //private $key_dec="8z5Ag5wgagfsOuhz"; //30.07.2023
  private $key_enc="Ij4aiaQXgluXQRs6";  // 29.02.2024
  private $key_dec="8z5Ag5wgagfsOuhz";  // 29.02.2024

  private $nineAnimeKey = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  private function cipher($key,$text) {
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
  private function encrypt_bunny($input,$key) {
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
  private function decrypt_bunny($input,$key) {
   //$input=preg_replace('/[\t\n\f\r]/','',$input);
   //$input=preg_replace("/\=?+/","",$input);
   $input=str_replace("_","/",$input);
   $input=str_replace("-","+",$input);
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

  private function ceva($t) {
   $t=preg_replace_callback(
    "/[a-zA-Z]/",
    function ($a1) {
     return chr(($a1[0] <= 'Z' ? 90 : 122) >= ($a1 = ord($a1[0]) + 13) ? $a1 : $a1 - 26);
    },
    $t
   );
  $t=$this->encrypt_bunny($t,$this->nineAnimeKey);
  $o=5;
  $s="";
  for ($h=0;$h<strlen($t);$h++) {
   $c=ord($t[$h]);
   if ($h%$o==1 ||$h%$o==4) $c -=2;
   if ($h%$o==3) $c +=5;
   if ($h%$o==0) $c -=4;
   if ($h%$o==2) $c -=6;
   $s .=chr($c);
  }


  return ($s);
  }
//0 ? o = 0 : u % i == 2 ? o -= 2 :
//u % i == 4 || u % i == 7 ? o += 2 :
//u % i == 0 ? o += 4 :
//u % i == 5 || u % i == 6 ? o -= 4 :
//u % i == 1 ? o += 3 :
//u % i == 3 && (o += 5)
  private function ceva_5($t) {
   $i=8;
   $n="";

   for ($r=0;$r<strlen($t);$r++) {
    $u=ord($t[$r]);
    if ($r % $i==2) $u -=2;
    if ($r % $i==4||$r % $i==7) $u +=2;
    if ($r % $i==0) $u +=4;
    if ($r % $i==5 || $r % $i==6) $u -=4;
    if ($r % $i==1) $u +=3;
    if ($r % $i==3) $u +=5;
    $n .=chr($u);
   }
   return $n;
  }
   
  
  private function ceva1($t) {
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
   $n=$this->encrypt_bunny($n,$this->nineAnimeKey);
   $n=preg_replace_callback(
    "/[a-zA-Z]/",
    function ($a1) {
     return chr(($a1[0] <= 'Z' ? 90 : 122) >= ($a1 = ord($a1[0]) + 13) ? $a1 : $a1 - 26);
    },
    $n
   );
   return $n;
  }
  function encodeVrf_old($text) {
   return urlencode($this->encrypt_bunny($this->ceva($this->encrypt_bunny($this->cipher($this->key_enc,$text),$this->nineAnimeKey)),$this->nineAnimeKey));
  }
  function encodeVrf_2($text) {
   $a=$this->cipher($this->key_enc,$text);

   $b=$this->encrypt_bunny($a,$this->nineAnimeKey);
//echo $b;
   $d=$this->ceva_5($b);
   //echo $d;
   return $d;
  }
  function encodeVrf($text) {
   $a=$this->cipher($this->key_enc,$text);

   $b=$this->encrypt_bunny($a,$this->nineAnimeKey);
   $b=$this->encrypt_bunny($b,$this->nineAnimeKey);
   $b=strrev($b);
   $b=$this->encrypt_bunny($b,$this->nineAnimeKey);
//echo $b;
   $d=$this->ceva_5($b);
   //echo $d;
   return $d;
  }
  function decodeVrf($text) {
    $text=preg_replace('/[\t\n\f\r]/','',$text);
    $text=preg_replace("/\=?+/","",$text);
    return urldecode($this->cipher($this->key_dec,$this->decrypt_bunny($text,$this->nineAnimeKey)));
  }
}
?>
