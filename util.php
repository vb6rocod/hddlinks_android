<?php
function rr($js_code) {
                $js_code = str_replace(array(
                    ")+(",
                    "![]",
                    "!+[]",
                    "[]"
                ), array(
                    ").(",
                    "(!1)",
                    "(!0)",
                    "(0)"
                ), $js_code);
return $js_code;
}

function getClearanceLink($content, $url) {
  sleep (4);
  preg_match_all('/name="\w+" value="(.+?)"/', $content, $matches);
        $params = array();
        list($params['s'],$params['jschl_vc'], $params['pass']) = $matches[1];
$uri = parse_url($url);

$host=$uri["host"];
$result="";
$t1=explode('id="cf-dn',$content);
$t2=explode(">",$t1[1]);
$t3=explode("<",$t2[1]);
$cf=$t3[0];

preg_match("/f\,\s?([a-zA-z0-9]+)\=\{\"([a-zA-Z0-9]+)\"\:\s?([\/!\[\]+()]+|[-*+\/]?=[\/!\[\]+()]+)/",$content,$m);

eval("\$result=".rr($m[3]).";");
$pat="/".$m[1]."\.".$m[2]."(.*)+\;/";
preg_match($pat,$content,$p);
$t=explode(";",$p[0]);
for ($k=0;$k<count($t);$k++) {
 if (substr($t[$k], 0, strlen($m[1])) == $m[1]) {
   if (strpos($t[$k],"function(p){var p") !== false) {
     $a1=explode ("function(p){var p",$t[$k]);
     $t[$k]=$a1[0].$cf;
     $line = str_replace($m[1].".".$m[2],"\$result ",rr($t[$k])).";";
     eval($line);
   } else if (strpos($t[$k],"(function(p){return") !== false) {
     $a1=explode("(function(p){return",$t[$k]);
     $a2=explode('("+p+")")}',$a1[1]);
     $line = "\$index=".rr(substr($a2[1], 0, -2)).";";
     eval ($line);
     $line=str_replace($m[1].".".$m[2],"\$result ",rr($a1[0])." ".ord($host[$index]).");");
     eval ($line);
   } else {
     $line = str_replace($m[1].".".$m[2],"\$result ",rr($t[$k])).";";
     eval($line);
   }
 }
}
$params['jschl_answer'] = round($result, 10);
return sprintf("%s://%s/cdn-cgi/l/chk_jschl?%s",
                $uri['scheme'],
                $uri['host'],
                http_build_query($params)
            );
}
    function getClearanceLink_old($content, $url)
    {
        /*
         * 1. Mimic waiting process
         */
        sleep(4);

        /*
         * 2. Extract "jschl_vc" and "pass" params
         */
        preg_match_all('/name="\w+" value="(.+?)"/', $content, $matches);


        $params = array();
        //list($params['jschl_vc'], $params['pass']) = $matches[1];
        list($params['s'],$params['jschl_vc'], $params['pass']) = $matches[1];
        // Extract CF script tag portion from content.
        $cf_script_start_pos    = strpos($content, 's,t,o,p,b,r,e,a,k,i,n,g,f,');
        $cf_script_end_pos      = strpos($content, '</script>', $cf_script_start_pos);
        $cf_script              = substr($content, $cf_script_start_pos, $cf_script_end_pos-$cf_script_start_pos);
        /*
         * 3. Extract JavaScript challenge logic
         */
        preg_match_all('/:[\/!\[\]+()]+|[-*+\/]?=[\/!\[\]+()]+/', $cf_script, $matches);
        //print_r ($matches);

            /*
             * 4. Convert challenge logic to PHP
             */
            $php_code = "";
            foreach ($matches[0] as $js_code) {
                // [] causes "invalid operator" errors; convert to integer equivalents
                $js_code = str_replace(array(
                    ")+(",
                    "![]",
                    "!+[]",
                    "[]"
                ), array(
                    ").(",
                    "(!1)",
                    "(!0)",
                    "(0)"
                ), $js_code);
                //echo $js_code;
                $php_code .= '$params[\'jschl_answer\']' . ($js_code[0] == ':' ? '=' . substr($js_code, 1) : $js_code) . ';';
            }
            //$php_code=str_replace("*",".",$php_code);
            //echo $php_code;
            /*
             * 5. Eval PHP and get solution
             */
             //$php_code="\$params['jschl_answer']=+(((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+(0)).((!0)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).(+(0)).((!0)+!(!1)+!(!1)+!(!1)).(+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)).(+!(!1)))/+(((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+(0)).(+!(!1)).(+(0)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).(+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).(+!(!1)));";
             //$php_code="\$params['jschl_answer']-=+(((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+(0)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).(+(0)).(+(0)).((!0)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)))/+(((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+(0)).((!0)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).(+(0)).((!0)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)).((!0)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)));";
             //$php_code="\$params['jschl_answer']*=+(((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+(0)).((!0)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).(+(0)).((!0)+!(!1)+!(!1)+!(!1)).(+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)))/+(((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+(0)).((!0)+!(!1)).((!0)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)+!(!1)).((!0)+!(!1)+!(!1)+!(!1)).(+(0)));";
            eval($php_code);

            // toFixed(10).
            $params['jschl_answer'] = round($params['jschl_answer'], 10);
            //print_r ($params);
            // Split url into components.
            $uri = parse_url($url);
            // Add host length to get final answer.
            //echo $uri['host'];
            $params['jschl_answer'] += strlen($uri['host'])  ;
            //$params['jschl_answer'] += strlen("www2.123netflix.pr") ;
            /*
             * 6. Generate clearance link
             */
             //echo http_build_query($params);
            return sprintf("%s://%s/cdn-cgi/l/chk_jschl?%s",
                $uri['scheme'],
                $uri['host'],
                http_build_query($params)
            );
    }
function getIMDBSeason($tt_imdb_series,$season_serie) {
  $l="https://www.imdb.com/title/".$tt_imdb_series."/episodes?season=".$season_serie;
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close ($ch);
  if (preg_match("/class\=\"poster\".*?src\=\"(.*?\.jpg)/ms",$h,$p))
    $poster_serie=$p[1];
  else
    $poster_serie="blank.jpg";
  $arr=array();
    if (preg_match_all("/div class\=\"image\"\>(.*?)class\=\"clear\">/ms",$h,$m)) {
        for ($k = 0; $k < count($m[0]); $k++) {
            preg_match("/episodeNumber\" content\=\"(\d+)\"/ms",$m[0][$k],$n);
            if (isset($n[1]))
              $ep       = $n[1];
            else
              $ep       = "N/A";
            preg_match("/itemprop\=\"name\"\>(.*?)\</ms",$m[0][$k],$n);
            if (isset($n[1]))
              $title       = $n[1];
            else
              $title       = "N/A";
            preg_match("/src\=\"(.*?\.jpg)\"/ms",$m[0][$k],$n);
            if (isset($n[1]))
              $poster   = $n[1];
            else
              $poster   = $poster_serie;
            preg_match("/itemprop\=\"description\"\>(.*?)\</ms",$m[0][$k],$n);
            if (isset($n[1]))
              $plot       = trim($n[1]);
            else
              $plot       = "N/A";
            preg_match("/data-const\=\"(.*?)\"/ms",$m[0][$k],$n);
            if (isset($n[1]))
              $imdb       = $n[1];
            else
              $imdb       = "N/A";
            preg_match("/class\=\"ipl-rating-star__rating\"\>(.*?)\</ms",$m[0][$k],$n);
            if (isset($n[1]))
              $rating       = $n[1];
            else
              $rating       = "N/A";
            $arr[$ep] = array(
                'episod' => $ep,
                'title' => $title,
                'poster' => $poster,
                'plot' => $plot,
                'imdb' => $imdb,
                'rating' => $rating
            );
        }
    return $arr;
    } else {
      return false;
    }
}
function getIMDBDetail($tt_imdb)
{
    $l  = "https://www.imdb.com/title/" . $tt_imdb . "/reference";
    $ch = curl_init($l);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
    curl_setopt($ch, CURLOPT_REFERER, $l);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // RETURN THE CONTENTS OF THE CALL
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $h = curl_exec($ch);
    curl_close($ch);
    $arr = array();
    preg_match("/class=\"titlereference-primary-image\".*?src\=\"(.*?)\"/ms", $h, $m);
    if (isset($m[1]))
        $arr["poster"] = $m[1];
    else
        $arr["poster"] = "";
    preg_match('/<title>.*?\(.*?(\d{4}).*?\).*?<\/title>/ms', $h, $m);
    if (isset($m[1]))
        $arr["Year"] = trim($m[1]);
    else
        $arr["Year"] = "N/A";
    preg_match('/<title>(IMDb \- )*(.*?) \(.*?<\/title>/ms', $h, $m);
    if (isset($m[2]))
        $arr['Title'] = trim($m[2]);
    else
        $arr["Title"] = "N/A";
    preg_match('/<\/svg>.*?<\/span>.*?<span class="ipl-rating-star__rating">(.*?)<\/span>/ms', $h, $m);
    if (isset($m[1]))
        $arr["imdbRating"] = $m[1];
    else
        $arr["imdbRating"] = "";
    preg_match('/Runtime<\/td>.*?(\d+ min).*?<\/li>/ms', $h, $m);
    if (isset($m[1]))
        $arr["Runtime"] = trim($m[1]);
    else
        $arr["Runtime"] = "";
    preg_match("/titlereference-section-overview\"\>(.*?)\<div class=\"titlereference-overview-section/ms", $h, $m);
    $p = strip_tags($m[1]);
    preg_match_all("/\d{4}+\n/ms", $p, $x);
    if (isset($x[0]) && count($x[0]) > 0) {
        $t1 = explode($x[0][count($x[0]) - 1], $p);
        $p  = $t1[1];
    }
    $t1 = explode("See all &raquo;", $p);
    if (isset($t1)) {
        $plot = $t1[count($t1) - 1];
    } else {
        $plot = trim($p);
    }
    $t3          = explode("See more &raquo;", $plot);
    $plot        = trim($t3[0]);
    $arr["plot"] = $plot;
    preg_match('/Genres<\/td>.*?<td>(.*?)<\/td>/ms', $h, $m);
    if (isset($m[0])) {
        preg_match_all('/<a.*?\>(.*?)<\/a>/ms', $m[0], $n);
        if (isset($n[1]))
            $arr['Genre'] = implode(", ", $n[1]);
        else
            $arr['Genre'] = "N/A";
    } else {
        $arr['Genre'] = "N/A";
    }
    preg_match_all("/class\=\"primary_photo\"\>.*?itemprop\=\"name\"\>(.*?)\<\/span/ms",$h,$m);
    if (isset($m[1])) {
       if (count($m[1]) > 20)
        $act=array_slice($m[1], 0, 20);
       else
        $act=$m[1];
       $arr['Actors'] = implode(", ", $act);
    } else
            $arr['Actors'] = "N/A";
    return $arr;
}
?>
