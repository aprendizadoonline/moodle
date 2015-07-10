<?php
require('../../config.php');
require_once($CFG->libdir . '/completionlib.php');

$config = get_config('video');

$url = $config->url . "admin/moodle_video_listar/?VIDEOPASTAS_ID=" . $_POST['VIDEOPASTAS_ID'] . '&VIDEO_ID=' . $_POST['VIDEO_ID'];
$ch = curl_init ($url) ;
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1) ;
$res = curl_exec ($ch) ;
curl_close ($ch) ;
echo $res;
