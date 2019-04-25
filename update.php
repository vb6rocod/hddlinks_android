<?php
error_reporting(0);
function htmldecode($str){
    if (is_string($str)){
       if (get_magic_quotes_gpc()) return stripslashes(html_entity_decode_for_php4_compatibility($str));
       else return html_entity_decode($str);
    } else return $str;
}
$url=$_POST['url'];
$cmd_arg="scripts_t.zip";
//$base_url=$_SERVER['SCRIPT_FILENAME'];
$base_url=dirname($_SERVER['SCRIPT_FILENAME']);
$dir_atual = substr($base_url, 0, strrpos($base_url, '/'))."/";
$dir_antes="";
$dir_dest="";
//$url= 'http://hddlinks.netai.net/scripts_t.zip';
$path = $dir_atual.'scripts_t.zip';
if (file_exists($path)) unlink ($path);
//frame=3&action=72&dir_dest=&chmod_arg=&cmd_arg=scripts_t.zip&dir_atual=%2Fmnt%2Fsdcard%2Fhtdocs%2F&dir_antes=&selected_dir_list=&selected_file_list=
    //foreach ($_GET as $key => $val) $$key=htmldecode($val);
    //foreach ($_POST as $key => $val) $$key=htmldecode($val);
    //foreach ($_COOKIE as $key => $val) $$key=htmldecode($val);
    if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) $ip = $_SERVER["REMOTE_ADDR"]; //nao usa proxy
    else $ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; //usa proxy
    $islinux = !(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    $url_info = parse_url($_SERVER["HTTP_REFERER"]);
    $doc_root = ($islinux) ? $_SERVER["DOCUMENT_ROOT"] : ucfirst($_SERVER["DOCUMENT_ROOT"]);
    $script_filename = $doc_root.$_SERVER["PHP_SELF"];
    $path_info = pathinfo($script_filename);
function zip_extract(){
  global $cmd_arg,$dir_atual,$islinux;
  $zip = zip_open($dir_atual.$cmd_arg);
  if ($zip) {
    while ($zip_entry = zip_read($zip)) {
        if (zip_entry_filesize($zip_entry)) {
            $complete_path = $path.dirname(zip_entry_name($zip_entry));
            $complete_name = $path.zip_entry_name($zip_entry);
            if(!file_exists($complete_path)) {
                $tmp = '';
                foreach(explode('/',$complete_path) AS $k) {
                    $tmp .= $k.'/';
                    if(!file_exists($tmp)) {
                        @mkdir($dir_atual.$tmp, 0777);
                    }
                }
            }
            if (zip_entry_open($zip, $zip_entry, "r")) {
                if ($fd = fopen($dir_atual.$complete_name, 'w')){
                    fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
                    fclose($fd);
                } else echo "fopen($dir_atual.$complete_name) error<br>";
                zip_entry_close($zip_entry);
            } else echo "zip_entry_open($zip,$zip_entry) error<br>";
        }
    }
    zip_close($zip);
  }
}


$fp = fopen($path, 'w');

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_FILE, $fp);

$data = curl_exec($ch);

curl_close($ch);
fclose($fp);

zip_extract();
if (file_exists($path)) unlink ($path);

?>
