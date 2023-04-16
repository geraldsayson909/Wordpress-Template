<?php
if(!file_exists("../../../../../wp-load.php")){
    require_once("../../../../../wp-systcon/wp-load.php");
} else {
    require_once("../../../../../wp-load.php");
}

$_POST = json_decode(file_get_contents("php://input"),true);
if($_POST['whitelist']){
    $data_to_write = $_POST['whitelist'];
    $res = '';
    $path = dirname(dirname(__FILE__)) . '/whitelist.txt';    
    $file_permmission = substr(sprintf('%o', fileperms($path)), -4);
    if($file_permmission == '0555') {
        $res = 'not writable';
    } else {        
        $fp = fopen($path, 'a');    
        fwrite($fp, $data_to_write);
        fclose($fp);
        $res = 'success';     
    }   
    echo $res;
}
?>