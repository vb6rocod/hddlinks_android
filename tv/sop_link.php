<html>
<?php
include ("../common.php");
error_reporting(0);
set_time_limit(0);
$id = $_GET["file"];
$title = $_GET["title"];
//$t1 = dirname($_SERVER['SCRIPT_FILENAME']);
$stopsop = dirname($_SERVER['SCRIPT_FILENAME']) .'/sop/sopk.vbs'; shell_exec($stopsop);
$sopvbs = 'Set WshShell = WScript.CreateObject("WScript.Shell") 
Return = WshShell.Run("sop\sop.exe '.$id.'", 0)
Set ws=CreateObject("WScript.Shell")';
file_put_contents("./sop/sop.vbs",$sopvbs);
$tvsop = dirname($_SERVER['SCRIPT_FILENAME']) .'/sop/sop.vbs'; shell_exec($tvsop);
  $mpc=trim(file_get_contents($base_pass."mpc.txt"));
  $c='"'.$mpc.'" "http://127.0.0.1:8902"';
  pclose(popen($c,"r"));
  shell_exec($stopsop);
  echo '<script type="text/javascript">window.close();</script>';
  die();
?>
<title><?php echo $title;?></title>
<link rel="stylesheet" type="text/css" href="http://hd4all.co.nf/vlc/xtr.css" />
<body>
<div class='PlayDbox' id='PlayDbox'><div align='left' style='position:relative;'> 
<embed classid="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921" id="vlc" events="True" height="0" width="0">
                <param name="MRL" value="http://127.0.0.1:8902">
                <param name="ShowDisplay" value="True">
                <param name="AutoLoop" value="False">
                <param name="AutoPlay" value="True">
                <param name="Volume" value="50">
                <param name="toolbar" value="False">
                <param name="StartTime" value="0">
                <embed pluginspage="http://www.videolan.org" type="application/x-vlc-plugin" version="VideoLAN.VLCPlugin.2" target="http://127.0.0.1:8902" autoplay="True" toolbar="False" loop="true" text="Waiting for video" name="vlc" height="100%" width="100%" /></object>
<script language='Javascript'>
var vlc=document.getElementById('vlc');var volume_number = 10;var apratio43 = '4:3';var apratio169 = '16:9';
function volume_up()   {vlc.audio.volume += volume_number;}
function volume_down() { vlc.audio.volume -= volume_number;}
function onStop() {vlc.playlist.stop();}
function onPlay() {vlc.playlist.play();} 
function onFullscreen() {vlc.video.toggleFullscreen();} 
function onMute() {vlc.audio.toggleMute();}
function ratio43() {vlc.video.aspectRatio = apratio43;}
function ratio169() {vlc.video.aspectRatio = apratio169;}
function myFunction() {
    location.reload();
}
</script>
</div>
<div class=dbox id=dbox>
<input id=Playb title='Play' class=dboximgl type=image src='http://hd4all.co.nf/vlc/play3.svg' href='javascript:;' onclick='onPlay()' value='Play' />
<input id=Stop title='Stop' class=dboximgl type=image src='http://hd4all.co.nf/vlc/stop2.svg' href='javascript:;' onclick='onStop()' value='Stop' >
<input id=Plat title='4:3' class=dboximgl type=image src='http://hd4all.co.nf/vlc/table2.svg' href='javascript:;' onclick='ratio43()' value=' 4:3 ' />
<input id=Wide title='16:9' class=dboximgl type=image src='http://hd4all.co.nf/vlc/table.svg'  href='javascript:;' onclick='ratio169()' value='16:9' />
<input id=Plat title='Reload' class=dboximgl type=image src='https://openclipart.org/image/800px/svg_to_png/189377/refresh.png'  href='javascript:;' onclick='myFunction()' value='16:9' />  
<input id=Fullscreen title='Fullscreen' class=dboximgr type=image src='http://hd4all.co.nf/vlc/enlarge.svg' href='javascript:;' onclick='onFullscreen()' value='Fullscreen' />
<input id=Volumep title='Volume+' class=dboximgr type=image src='http://hd4all.co.nf/vlc/volume-increase.svg' href='javascript:;' onclick='volume_up()' value='Vol +' />
<input id=Volumem title='Volume-' class=dboximgr type=image src='http://hd4all.co.nf/vlc/volume-decrease.svg' href='javascript:;' onclick='volume_down()' value='Vol -' />
<input id=Mute title='Mute' class=dboximgr type=image src='http://hd4all.co.nf/vlc/volume-mute2.svg' href='javascript:;' onclick='onMute()' value='Mute' />
<iframe class=dboxfr frameborder=0 ></iframe>
<script type="text/javascript">
        function getVLC(name)
        {
            if (window.document[name])
            {
                return window.document[name];
            }
            if (navigator.appName.indexOf("Microsoft Internet")==-1)
            {
                if (document.embeds && document.embeds[name])
                    return document.embeds[name];
            }
            else // if (navigator.appName.indexOf("Microsoft Internet")!=-1)
            {
                return document.getElementById(name);
            }
        }

        function AspectRatio(value, el)
        {
            var vlc = getVLC("vlc");
            if( vlc )
                vlc.video.aspectRatio = value;
				
			$('#footer ul li a').removeClass('select');
            $(el).addClass('select');
        }
		
		setTimeout(function() {
            $("#vlc").hide().show();
        }, 100);
		
		var vlc = getVLC("vlc");
		vlc.video.aspectRatio = '16:9';
    </script>
</div></div></body>

</html>
