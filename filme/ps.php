<?php
    if (preg_match("/eval\(([a-z0-9_]+)\)\;/",$dec,$m)) {  // extract last code....
     $exp=$m[1];
     $pat="/var\s*".$exp."\s*\=\'(.*?)\'/";
     if (preg_match($pat,$dec,$m)) {
      $dec=$m[1];
     }
    }
    $dec=str_replace("Math.","",$dec);
    $dec=preg_replace_callback(   // if Math[op]  but not Math["round"]
     "/Math\[\s*(\w+)\s*\]/",
     function ($matches) {
      return "\$".$matches[1];
     },
     $dec
    );
    $dec=preg_replace_callback(   // if Math["sqrt"]
     "/Math\[(.*?)\]/",
     function ($matches) {
      return preg_replace("/(\s|\"|\+)/","",$matches[1]);
     },
     $dec
    );
    $dec=preg_replace_callback(   // if "da" + "to" , ['"data"] to .data
     "/\[([a-dt\"\+]+)\]/",
     function ($matches) {
      return ".".preg_replace("/(\s|\"|\+)/","",$matches[1]);;
     },
     $dec
    );
    $dec=str_replace("PI","M_PI",$dec);
    $dec=preg_replace("/\/\*.*?\*\//","",$dec);  // /* ceva */
    $dec=str_replace('r["splice"]','r.splice',$dec);

    $dec=preg_replace_callback(  // this fix $("div:first").data("m0") or $("div:first").data(m0)
     "/(\\$\(\s*\"\s*([a-zA-Z0-9_\.\:\_\-]+)\s*\"\)\.data\s*\(\s*\"?(\w+)\"?)\s*\)/",
     function ($matches) {
      return "\$".trim(str_replace(" ","_",trim($matches[3])));
     },
     $dec
    );

    $dec=preg_replace_callback(  // this fix $("div:first").data("m0",ceva) or $("div:first").data(m0, ceva)
     "/(\\$\(\s*\"\s*([a-zA-Z0-9_\.\:\_\-]+)\s*\"\)\.data\s*\(\s*\"?(\w+)\"?)\s*\,([a-zA-Z0-9-\s\+\)\(\"\$]+)\)/",
     function ($matches) {
      return "\$".trim(str_replace(" ","_",trim($matches[3])))."=".trim($matches[4]);
     },
     $dec
    );
    // next try to fix var op="sqrt",oi="5"; or var oe="sqrt"; to function for "sqrt" or $oi=5
    $pat1=""; // build a pattern with oi|oe ......
    if (preg_match_all("/(var\s*)?((\w+)\s*\=\s*\"(\w+)\"\s*\,?)+;/",$dec,$m)) {
     for ($k=0;$k<count($m[0]);$k++) {
      $find=$m[0][$k];
      preg_match_all("/(\w+)\=\"(\w+)\"/",$find,$n);
      $out="";
      for ($z=0;$z<count($n[0]);$z++) {
       if (is_numeric($n[2][$z])) {
        $out .="\$".$n[1][$z]."=".$n[2][$z].";";
        $pat1 .=$n[1][$z]."|";
       } else {
        if (preg_match("/floor|round|sin|sqrt/",$n[2][$z]))
        $out .="\$".$n[1][$z]."=function(\$a){return ".$n[2][$z]."(\$a);};";
        else {
        $out .="\$".$n[1][$z]."=\$".$n[2][$z].";";
        $pat1 .=$n[1][$z]."|";
        }
        }
      }
     $dec=preg_replace("/".preg_quote($find)."/",$out,$dec,1); // only one replace (good or bad ?)
     }
    }
    $pat1 = substr($pat1, 0, -1); // what I find in vad x=,y=
    $dec=preg_replace_callback(  // this fix +-op to +-$op
     "@(\+|\-|\()\s*(\\$?(".$pat1."))@",
     function ($matches) {
      if ($matches[2][0] !="\$")
       return $matches[1]."\$".$matches[2];
      else
       return $matches[1].$matches[2];
     },
     $dec
    );
?>
