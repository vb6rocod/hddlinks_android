<?php
function get_sh($h) {
  $qgumdtoek="";
  $c="";
  $d="";
  $sh="";
  $a="";
  $b=array();
  //$t1=explode('}catch(e){console.warn(e.message);}',$h);
  //$t1=explode('var loadedmeta',$h);
  //$h=$t1[1];
$t1=explode('canvas.addEventListener',$h);
$h=$t1[2];
  if (preg_match("/var \w+=(\d+)\;/",$h,$m))
   $c = $m[1];
  if (preg_match("/var \w+\=\"(\w{50,})\"/",$h,$m))
   $qgumdtoek =  $m[1];
  if (preg_match("/parseInt\(\w+\[i\]\,(\d+)\)/",$h,$m))
   $d = $m[1];
  $b=preg_split("/[A-Z]/",$qgumdtoek);
  for($i = 0; $i < count($b); $i++) {
    $a .=chr(intval($b[$i], $d)/$c);
  }
  if ($c && $d && $qgumdtoek && $a) {
   if (preg_match_all("/\'(\w{25,})\'/",$a,$m))
    $sh=$m[1][1];
  }
  //$sh="19f49661eccc1007fd6d0b344d94cce797a5c66f";
  return $sh;
}
?>
