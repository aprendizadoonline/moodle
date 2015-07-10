<?php
/**
 * @package    mod
 * @subpackage video
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */
ob_start ();
ob_clean();

require('../../config.php');
require_once($CFG->libdir . '/completionlib.php');

$id       = optional_param('id', 0, PARAM_INT);        // Course module ID
$u        = optional_param('u', 0, PARAM_INT);         // video instance id
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($u) {  // Two ways to specify the module
    $video = $DB->get_record('video', array('id'=>$u), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('video', $video->id, $video->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('video', $id, 0, false, MUST_EXIST);
    $video = $DB->get_record('video', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/video:view', $context);

add_to_log($course->id, 'video', 'view', 'view.php?id='.$cm->id, $video->id, $cm->id);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$config = get_config('video');

$popup = 'false';
if( !isset( $_GET['popup'] ) && !$config->popup )
{
	$PAGE->set_url('/mod/video/view.php', array('id' => $cm->id));
    $PAGE->set_title($course->shortname.': '.$video->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($video);
    echo $OUTPUT->header();

    echo $OUTPUT->heading(format_string($video->name), 2, 'main', 'videoheading');

    echo '<div class="videoworkaround">';
}
else
	$popup = 'true';






$admins = get_admins();
$isadmin = false;
foreach($admins as $admin) {
    if ($USER->id == $admin->id) {
        $isadmin = true;
        break;
    }
}
if( !$isadmin )
{
    $context = get_context_instance(CONTEXT_COURSE,$course->id);
    $roles = get_user_roles($context, $USER->id, true);

    foreach( $roles as $role )
    {
        if($role->shortname == 'editingteacher')
            $isadmin = true;
    }
}






require_once 'MobileDetect.php';
$detect = new MobileDetect();

$campo = $config->seguranca;

$url = $config->url . "admin/moodle_video_player/?VIDEO_ID=" . $video->externalvideo .
	'&isiOS=' .          ($detect->isiOS()?'true':'false') .
	'&isAndroidOS=' .    ($detect->isAndroidOS()?'true':'false') .
	'&isBlackBerryOS=' . ($detect->isBlackBerryOS()?'true':'false') .
	'&popup=' .           $popup .
	'&user_id=' .         $USER->id.
    '&user_cpf=' .        $USER->$campo.
    '&admin=' . ($isadmin?'admin':'aluno');
$ch = curl_init ($url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec ($ch);
curl_close ($ch);

if( strpos( $res, "Location" ) === 0 )
{
    ob_clean();
    header( $res );
}

echo $res;
    
if( !isset( $_GET['popup'] ) && !$config->popup )
{
	echo '</div>';
	echo $OUTPUT->footer();
}
die;


