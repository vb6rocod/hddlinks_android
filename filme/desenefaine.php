<!DOCTYPE html>
<?php
include ("../common.php");
$query = $_GET["page"];
if($query) {
   $queryArr = explode(',', $query);
   $page = $queryArr[0];
   $search = $queryArr[1];
   $page_title=urldecode($queryArr[2]);
   $search=str_replace("|","&",$search);
}
//https://filmeseriale.online/seriale/
//https://desenefaine.ro/filme-animate-online-
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />

<script type="text/javascript">
// create the XMLHttpRequest object, according browser
function get_XmlHttp() {
  // create the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;
  if(window.XMLHttpRequest) {		// for Forefox, IE7+, Opera, Safari, ...
    xmlHttp = new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {	// for Internet Explorer 5 or 6
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlHttp;
}

// sends data to a php file, via POST, and displays the received answer
function ajaxrequest(title, link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  var the_data = 'mod=add&title='+ title +'&link='+link;
  var php_file='filmeseriale_add.php';
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      alert (request.responseText);
    }
  }
}
</script>
</head>
<body><div id="mainnav">
<H2></H2>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$n=0;
echo '<H2>'.$page_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a class="nav" href="desenefaine.php?page='.($page-1).','.$search.','.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="desenefaine.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="desenefaine.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
$l =$search."-".$page."-date.html";
//https://filmeseriale.online/seriale/page/2/
//https://drive.google.com/file/d/1uSKiFvNhewneYKrOMCR2cMCXXVK_ZXep/preview
//https://lh5.googleusercontent.com/6U6mswDg_D_8eiK5T6dqFPHm6SUMaF7YUL8Ea1a-CQkQR3G0f0R0qgscgmBG_QLd7HnWApbJcpqMdkObXlOw=w640-h360-n-k
//https://drive.google.com/get_video_info?docid=1uSKiFvNhewneYKrOMCR2cMCXXVK_ZXep
//https://r4---sn-4g5e6nez.c.drive.google.com/videoplayback?id=9deda3fbf9ace69a&itag=22&source=webdrive&requiressl=yes&mm=30&mn=sn-4g5e6nez&ms=nxu&mv=u&pl=23&ttl=transient&ei=c6QSW7jQL9fSqQWF6JCwAQ&susc=dr&driveid=1uSKiFvNhewneYKrOMCR2cMCXXVK_ZXep&app=explorer&mime=video/mp4&lmt=1527889357510475&mt=1527947838&ip=82.210.178.129&ipbits=0&expire=1527952003&cp=QVNHWUpfUVBORFhOOlhhQ2Ewb1hyc29m&sparams=ip,ipbits,expire,id,itag,source,requiressl,mm,mn,ms,mv,pl,ttl,ei,susc,driveid,app,mime,lmt,cp&signature=870F14B3FD2408FF8FDA685E40A3D96EF4253058.22E27479F2B24839002EDFEB35A8119808729258&key=ck2&cpn=R3VRi6b03wLprVKI&c=WEB_EMBEDDED_PLAYER&cver=20180525
//https://r4---sn-4g5e6nez.c.drive.google.com/videoplayback?id=9deda3fbf9ace69a&itag=18&source=webdrive&requiressl=yes&mm=30&mn=sn-4g5e6nez&ms=nxu&mv=u&pl=23&sc=yes&ttl=transient&ei=iecSW_f_JNLUqAWxopKYCA&susc=dr&driveid=1uSKiFvNhewneYKrOMCR2cMCXXVK_ZXep&app=texmex&mime=video/mp4&lmt=1527889333164027&mt=1527964983&ip=82.210.178.129&ipbits=0&expire=1527979977&cp=QVNHWUpfWFlVSFhOOmN1WmRiR09UZVhC&sparams=ip%2Cipbits%2Cexpire%2Cid%2Citag%2Csource%2Crequiressl%2Cmm%2Cmn%2Cms%2Cmv%2Cpl%2Csc%2Cttl%2Cei%2Csusc%2Cdriveid%2Capp%2Cmime%2Clmt%2Ccp&signature=3201FCB01E4982FBA98EE7C0B7DD5DEC026B0D51.0B2B27B8B5A2B5D815DB60D456475B91D89492B8&key=ck2&cpn=R3VRi6b03wLprVKI
//https://r4---sn-4g5ednll.c.drive.google.com/videoplayback?id=9deda3fbf9ace69a&itag=18&source=webdrive&requiressl=yes&mm=30&mn=sn-4g5ednll&ms=nxu&mv=u&pl=23&ttl=transient&ei=wOMSW-_HOsGHqwWAxZO4Bw&susc=dr&driveid=1uSKiFvNhewneYKrOMCR2cMCXXVK_ZXep&app=explorer&mime=video/mp4&lmt=1527889333164027&mt=1527964278&ip=82.210.178.129&ipbits=0&expire=1527968208&cp=QVNHWUpfV1JOSVhOOkpTT05peWVyVWNQ&sparams=ip,ipbits,expire,id,itag,source,requiressl,mm,mn,ms,mv,pl,ttl,ei,susc,driveid,app,mime,lmt,cp&signature=0716716318BC86C1586FF281CBCE25F4F9C7794E.7C90C14BFBA918F924C0C3E8C7AF46A09075DF31&key=ck2
//https://r4---sn-4g5e6nez.c.drive.google.com/videoplayback?id=9deda3fbf9ace69a&itag=22&source=webdrive&requiressl=yes&mm=30&mn=sn-4g5e6nez&ms=nxu&mv=u&pl=23&ttl=transient&ei=1OISW7OvJYr_qQW7oZuoCQ&susc=dr&driveid=1uSKiFvNhewneYKrOMCR2cMCXXVK_ZXep&app=explorer&mime=video/mp4&lmt=1527889357510475&mt=1527963597&ip=82.210.178.129&ipbits=0&expire=1527967972&cp=QVNHWUpfVllVQ1hOOlBWdzlrTllxV2tM&sparams=ip%2Cipbits%2Cexpire%2Cid%2Citag%2Csource%2Crequiressl%2Cmm%2Cmn%2Cms%2Cmv%2Cpl%2Cttl%2Cei%2Csusc%2Cdriveid%2Capp%2Cmime%2Clmt%2Ccp&signature=64F1E88C3D0296BA5FD8E8C7D3777D1743189855.12FA3AA97B08FA5CF04E93EE6288F9AC37B87B5C&key=ck2

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://desenefaine.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
$videos = explode('div class="thumbnail"', $html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t1 = explode('a href="', $video);
    $t2 = explode('"', $t1[1]);
    $link = $t2[0];
	$link1 = $link;


    $t3 = explode('title="',$video);
    $t4 = explode('"',$t3[2]);
    $title = trim($t4[0]);

    $t1 = explode('data-echo="', $video);
    $t2 = explode('"', $t1[1]);
    $image=$t2[0];
    //$link="fs.php?link=".$link1."&title=".urlencode($title)."&tip=movie";
    $link = 'filme_link.php?file='.urlencode($link1).",".urlencode(fix_t($title));
  if ($n==0) echo '<TR>';
  echo '<td class="mp" align="center" width="25%"><a class="imdb" href="'.$link.'" target="_blank"><img src="'.$image.'" width="200px" height="150px"><BR>'.$title.'</a>
  </TD>';
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a class="nav" href="desenefaine.php?page='.($page-1).','.$search.','.urlencode($page_title).'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="desenefaine.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="desenefaine.php?page='.($page+1).','.$search.','.urlencode($page_title).'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo "</table>";
?>
<br></div></body>
</html>
