<?php
/**
 * @package    mod
 * @subpackage vidgaleria
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */
ob_start ();
ob_clean();

require('../../config.php');
require_once($CFG->libdir . '/completionlib.php');

$id       = optional_param('id', 0, PARAM_INT);        // Course module ID
$u        = optional_param('u', 0, PARAM_INT);         // vidgaleria instance id
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($u) {  // Two ways to specify the module
    $vidgaleria = $DB->get_record('vidgaleria', array('id'=>$u), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('vidgaleria', $vidgaleria->id, $vidgaleria->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('vidgaleria', $id, 0, false, MUST_EXIST);
    $vidgaleria = $DB->get_record('vidgaleria', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/vidgaleria:view', $context);

add_to_log($course->id, 'vidgaleria', 'view', 'view.php?id='.$cm->id, $vidgaleria->id, $cm->id);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$config = get_config('video');

$PAGE->set_url('/mod/vidgaleria/view.php', array('id' => $cm->id));
$PAGE->set_title($course->shortname.': '.$vidgaleria->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($vidgaleria);
echo $OUTPUT->header();

echo $OUTPUT->heading(format_string($vidgaleria->name), 2, 'main', 'vidgaleriaheading');

echo '<div class="vidgaleriaworkaround">';

require_once 'MobileDetect.php';
$detect = new MobileDetect();  

$url = $config->url . "admin/moodle_vidgaleria_videos/?id=".$id."&GALERIA_ID=" . $vidgaleria->externalvideo;
$ch = curl_init ($url);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec ($ch);
echo curl_error($ch);
curl_close ($ch);

if( strpos( $res, "Location" ) === 0 )
{
    ob_clean();
    header( $res );
}

echo $res;

echo '</div>';
echo $OUTPUT->footer();

die;


