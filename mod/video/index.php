<?php
/**
 * @package    mod
 * @subpackage video
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

require('../../config.php');

$id = required_param('id', PARAM_INT); // course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

add_to_log($course->id, 'video', 'view all', "index.php?id=$course->id", '');

$strvideo       = get_string('modulename', 'video');
$strvideos      = get_string('modulenameplural', 'video');
$strsectionname  = get_string('sectionname', 'format_'.$course->format);
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_video('/mod/video/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strvideos);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strvideos);
echo $OUTPUT->header();

if (!$videos = get_all_instances_in_course('video', $course)) {
    notice(get_string('thereareno', 'moodle', $strvideos), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);
if ($usesections) {
    $sections = get_all_sections($course->id);
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($videos as $video) {
    $cm = $modinfo->cms[$video->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($video->section !== $currentsection) {
            if ($video->section) {
                $printsection = get_section_name($course, $sections[$video->section]);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $video->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($video->timemodified)."</span>";
    }

    $extra = empty($cm->extra) ? '' : $cm->extra;
    $icon = '';
    if (!empty($cm->icon)) {
        // each video has an icon in 2.0
        $icon = '<img src="'.$OUTPUT->pix_video($cm->icon).'" class="activityicon" alt="'.get_string('modulename', $cm->modname).'" /> ';
    }

    $class = $video->visible ? '' : 'class="dimmed"'; // hidden modules are dimmed
    $table->data[] = array (
        $printsection,
        "<a $class $extra href=\"view.php?id=$cm->id\">".$icon.format_string($video->name)."</a>",
        format_module_intro('video', $video, $cm->id));
}

echo html_writer::table($table);

echo $OUTPUT->footer();
