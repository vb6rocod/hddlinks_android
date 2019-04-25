<?php
function _splice($a,$b) {
	return  array_slice($a,$b);
}

function _reverse($a,$b) {
	return  array_reverse($a);
}

function _slice($a,$b) {
	$tS = $a[0];
	$a[0] = $a[$b % count($a)];
	$a[$b] = $tS;
	return  $a;
}


  $l = "https://www.youtube.com/watch?v=RjUlmco7v2M";
  $html="";
  $p=0;
  while($html == "" && $p<10) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $html = curl_exec($ch);
  curl_close($ch);
  $p++;
  }
  //echo $html;
  $html = str_between($html,'ytplayer.config = ',';ytplayer.load');
  $parts = json_decode($html,1);
  $l = "https://s.ytimg.com".$parts[assets][js];
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $html = curl_exec($ch);
  curl_close($ch);
  $tS = explode(';var ',$html);
  foreach($tS as $a) {
  if (strpos($a,'function(a,b){a.splice(0,b)}') !== false) {
	//$fcd = str_between($html,'hqdefault.jpg")};',')};').')};';
	$t1 = explode('={',$a);
	foreach($t1 as $a) if (strpos($a,'function(a,b){a.splice(0,b)}') !== false)
		$t2 = explode('};',$a);
	foreach($t2 as $a) if (strpos($a,'function(a,b){a.splice(0,b)}') !== false)
		$t2 = explode('},',$a);
  }
  }
  $tS = explode(';var ',$html);
  //print_r($tS);
  foreach($tS as $a) {
  if (strpos($a,';'.$t1[0].'.') !== false) {
	//$fcd = str_between($html,'hqdefault.jpg")};',')};').')};';
	$t0 = explode('},',$a);
	foreach($t0 as $a) if (strpos($a,'split("")') !== false) 
	  $t1 = explode('};',$a);
	foreach($t1 as $a) if (strpos($a,'split("")') !== false) 
	  $t3 = explode(';',$a);
  }
  }
  $t3[1] = str_replace('["','.',str_replace(']','',$t3[1]));
$t4 = '<?php'."\n".'function s_dec($s) { '."\n";
foreach($t3 as $a) {
	$t1 = '$sA = '.str_replace('join($s)','implode($sA)',str_replace('split','str_split',str_replace('""','$s',str_replace('(a','($sA',str_between($a,'.',')').'); '))))."\n";
		foreach($t2 as $b) {
			$c=str_between($a,'.','(');
				if (strpos($b.':',$c.':') !== false) {
				  $d=str_between($b,'.','(');
				  if (!$d) $d='slice';
				  $t1 = str_replace($c.'(','_'.$d.'(',$t1);
				}
		}
	$t4 = $t4.$t1;
}
$t4=$t4.'return $sA;'."\n".'};'."\n"."?>\n";
//echo $t4;
$file = fopen("s_dec.php","w");
fwrite($file,$t4);
fclose($file);
include ("s_dec.php");
unlink("s_dec.php");


//https://s.ytimg.com/yts/jsbin/html5player-en_US-vflnSSUZV/html5player.js
?>
