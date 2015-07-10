<?php
require('../../config.php');
require_once($CFG->libdir . '/completionlib.php');

$config = get_config('video');

$url = $config->url . "admin/moodle_vidgaleria_listar/?VIDEO_CURSO=" . $_POST['VIDEO_CURSO'] . "&VIDEO_ID=" . $_POST['VIDEO_ID'];
$ch = curl_init ($url) ;
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1) ;
$res = curl_exec ($ch) ;
curl_close ($ch) ; 
echo $res;
