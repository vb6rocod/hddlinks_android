<?php
//$enc="8e9eef797580f56111c94afd9ed613d55a583eb3fe6c73bf764ddf83135f0765da2dea535c6678715c02595f4a7165017e7964780b7748417d4c035471635d0b5f515742620262514c59234672415d590170466a545603427f587d69024456696f34";
function indexOf($hack,$pos) {
    $ret= strpos($hack,$pos);
    return ($ret === FALSE) ? -1 : $ret;
}
function base8($hack) {
$c1=strlen($hack);
for ($i=0;$i<$c1;$i++) {
  if ($hack[$i] > 7) {
    $hack=substr($hack,0,$i);
    $c1=strlen($hack);
    break;
  }
}
$ch1=0;
//$ch1=intval($t3[1],8);
//echo $t3[1]." ".intval("0".$t3[1])." ";
for ($i=0;$i<$c1;$i++) {
  //echo $x[$c-$i-1]."\n";
  $ch1 +=$hack[$c1-$i-1]*pow(8,$i);
}
  return $ch1;
}
function substring($str, $from = 0, $to = FALSE)
{
    if ($to !== FALSE) {
        if ($from == $to || ($from <= 0 && $to < 0)) {
            return '';
        }

        if ($from > $to) {
            $from_copy = $from;
            $from = $to;
            $to = $from_copy;
        }
    }

    if ($from < 0) {
        $from = 0;
    }

    $substring = $to === FALSE ? substr($str, $from) : substr($str, $from, $to - $from);
    return ($substring === FALSE) ? '' : $substring;
}
//$c0=['11|12|13|0|14|3|2|9|16|1|4|8|5|6|15|10|7','split','xXM','length','jLa','substring','aZP','dOD','Poy','write','push','dkF','text','tjf','HIZ','WyX','faz','Gcy','ucs','2|0|5|4|3|1','dhh','pQu','eMz','ZBD','Lnk','fromCharCode','DYl','4|3|0|5|6|2|1','WOa','gzw','cBV','Zft','pow','tcV','EAk','QHb','charCodeAt'];
function ol($enc,$ch11,$ch22,$ch33,$ch44,$ch55,$ch66,$ch77) {
//echo $enc."\n".$ch11."\n".$ch22."\n".$ch33."\n".$ch44."\n".$ch55."\n".$ch66."\n";
//$ch11=dechex($ch11);
//$ch22=dechex($ch22);
//echo $ch11."  ".$ch22."\n";
//$ch33=1;
//$ch11=intval($ch11);
//echo $ch22."\n".base8($ch22)."\n";
//echo $ch11."\n".base8($ch11)."\n";
//$ch22=9020;
$dec="";
$a146=0;
$a145=explode("|",'11|12|13|0|14|3|2|9|16|1|4|8|5|6|15|10|7');
for ($zz=0;$zz<count($a145);$zz++) {
switch($a145[$a146++]){
case'0':
  $a149='';
  continue;
case'1':
  for ($i=0;$i<strlen($a151);$i+=8) {
  $a152=$i*8;
  $a154=substring($a151,$i,$i+8);
  $a157=intval($a154,16);
  $a157=hexdec($a154);
  array_push($a160['ke'],$a157);
  }
   continue;
case'2':
 $a151=substring($a165,0,$a152);
 continue;
case'3':
 $a167=strlen($a165);
 continue;
case'4':
  $a169=$a160['ke'];
  continue;
case'5':
  $a165=substring($a165,$a152);
  continue;
case'6':
  $a151=0;
  continue;
case'7':
  $dec=$a149;
  continue; //a(b) $('#streamurl')text($a149); //$a149 e rezultatul dorit
case'8':
  $a152=72;
  continue;
case'9':
  $a169=array();
  continue;
case'10':
 while($a151<strlen($a165)) {
 $a184=explode('|','5|8|0|12|13|9|10|4|11|6|3|1|7|2');
 $a185=0;
 for ($yy=0;$yy<count($a184);$yy++) {
   switch($a184[$a185++]){
    case'0':
     $a188=0;
     continue;
    case'1':
     $a189=$a192*2+$a193;
     continue;
    case'2':
     $a194+=1;
     continue;
    case'3':
     $a195=$a195^((base8($ch11)-$ch44+4+$ch77)/($ch55-8))^$d1;
     //case'3':a195=a16[c2('0xe')](a16[c2('0xf')](a195,(parseInt('153023032627',8)-411+0x4)/(12-0x8)),d0);
     //case'3':a195=a16[c2('0xe')](a16[c2('0xf')](a195,(parseInt('153023033615',8)-913+0x4)/(12-0x8)),d0);
     //$a195=$a195^2164698430^$d1;
     //case'3':a195=a16[c2('0xe')](a16[c2('0xf')](a195,a199^0x8106ad3e),d0);
     //	case'3':a195=a16[c2('0xe')](a16[c2('0xf')](a195,a199^parseInt('20101526476',8)),d0);
     //case'3':a195=a16[c2('0xe')](a16[c2('0xf')](a195,(parseInt('60305005205',8)-719+0x4)/(11-0x8)),d0)
     continue;
    case'4':
     $a199=0x28a28dec;
     continue;
    case'5':
     $a192=0x40;
     continue;
    case'6':
     $a195=$a188^$a169[$a194%9];
     continue;
    case'7':
    for($i=0;$i<4;$i++){
     $a209=explode('|','2|0|5|4|3|1');
     $a210=0;
     for ($gg=0;$gg<count($a209);$gg++) {
     switch($a209[$a210++]){
      case'0':
       $a213=$a152/9*$i;
       continue;
      case'1':
       $a189=$a189<<$a152/9;
       continue;
      case'2':
       $a222=$a195&$a189;
       continue;
      case'3':
       //if($a227!='%')$a149 .=$a227; //$
       if($a227!='$')$a149 .=$a227;
       //alert ($a149); $a149 text decodat
       continue;
      case'4':
       $a227=chr($a222+$ch33);
       continue;
      case'5':
       $a222=$a222>>$a213;
       continue;
    }
    }
   }
   continue;
   case'8':
      $a193=0x7f;
      continue;
   case'9':
      $mm=0x80;
      $xx=0x3f;
      continue;
   case'10':
      do{
       $a238=explode('|','4|3|0|5|6|2|1');
       $a239=0;
       //while(!![]){
       for ($jj=0;$jj<count($a238);$jj++) {
        switch($a238[$a239++]){
        case'0':
         $a151++;
         continue;
        case'1':
         $a243+=6;
         continue;
        case'2':
         if($a243<30) {
          $a250=$a246&0x3f;
          $a188+=$a250<<$a243;
        }else{
          $a250=$a246&0x3f;
          $a188+=$a250*pow(2,$a243);
        }
         continue;
        case'3':
          $a264=substring($a165,$a151,$a151+2);
          continue;
        case'4':
         if($a151+1>=strlen($a165)){
          $a192=0x8f;
         }
         continue;
        case'5':
         $a151++;
         continue;
        case'6':
         $a246=intval($a264,16);
         $a246=hexdec($a264);
         continue;
      }//break;
      }
      } while($a246>=$a192);
      continue;
   case'11':
     $d1=base8($ch22) + $ch66;
     continue;
   case'12':
     $a243=0;
     continue;
   case'13':
     $a246=0;
     continue;
    }
    //break;
    }
    }
     continue;
case'11':
     $a283=$enc;
    continue;
case'12':
    $a165=ord($a283[0]);
    continue;
case'13':
   $a165=$a283;
   continue;
case'14':
   $a152=0x9*0x8;
   continue;
case'15':
   $a194=0;
   continue;
case'16':
   //$a160=('k'=>$a151,'ke'=>array(););
   $a160=array();
   $a160['k']=$a151;
   $a160['ke']=array();
   continue;
}
}
return $dec;
}

//6SBvkg_tP10~1496475532~82.210.0.0~dhhUpC6U
//6SBvkg_tP10~1496475532~82.210.0.0~dhhUpC6U
?>
