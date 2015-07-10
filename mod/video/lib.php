<?php
/**
 * @package    mod
 * @subpackage video
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in video module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function video_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Returns all other caps used in module
 * @return array
 */
function video_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function video_reset_userdata($data) {
    return array();
}

/**
 * List of view style log actions
 * @return array
 */
function video_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List of update style log actions
 * @return array
 */
function video_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add video instance.
 * @param object $data
 * @param object $mform
 * @return int new video instance id
 */
function video_add_instance($data, $mform) {
    global $CFG, $DB;

    $data->timemodified = time();
    $data->id = $DB->insert_record('video', $data);

    return $data->id;
}

/**
 * Update video instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function video_update_instance($data, $mform) {
    global $CFG, $DB;

    $data->timemodified = time();
    $data->id           = $data->instance;

    $DB->update_record('video', $data);

    return true;
}

/**
 * Delete video instance.
 * @param int $id
 * @return bool true
 */
function video_delete_instance($id) {
    global $DB;

    if (!$video = $DB->get_record('video', array('id'=>$id))) {
        return false;
    }

    // note: all context files are deleted automatically

    $DB->delete_records('video', array('id'=>$video->id));

    return true;
}

/**
 * Return use outline
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $video
 * @return object|null
 */
function video_user_outline($course, $user, $mod, $video) {
    global $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'video',
                                              'action'=>'view', 'info'=>$video->id), 'time ASC')) {

        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $result = new stdClass();
        $result->info = get_string('numviews', '', $numviews);
        $result->time = $lastlog->time;

        return $result;
    }
    return NULL;
}

/**
 * Return use complete
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $video
 */
function video_user_complete($course, $user, $mod, $video) {
    global $CFG, $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'video',
                                              'action'=>'view', 'info'=>$video->id), 'time ASC')) {
        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $strmostrecently = get_string('mostrecently');
        $strnumviews = get_string('numviews', '', $numviews);

        echo "$strnumviews - $strmostrecently ".userdate($lastlog->time);

    } else {
        print_string('neverseen', 'video');
    }
}

/**
 * Returns the users with data in one video
 *
 * @todo: deprecated - to be deleted in 2.2
 *
 * @param int $videoid
 * @return bool false
 */
function video_get_participants($videoid) {
    return false;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param object $coursemodule
 * @return object info
 */
function video_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;

    if (!$video = $DB->get_record('video', array('id'=>$coursemodule->instance),
            'id, name, externalvideo, intro, introformat')) {
        return NULL;
    }

    $info = new cached_cm_info();
    $info->name = $video->name;

    $config = get_config('video');
    if( $config->popup )
    {
        $fullurl = "$CFG->wwwroot/mod/video/view.php?id=$coursemodule->id&amp;popup=1";
        $width  = 620;
        $height = 480;
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $info->onclick = "window.open('$fullurl', '', '$wh'); return false;";
    }
    return $info;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function video_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-video-*'=>get_string('page-mod-video-x', 'video'));
    return $module_pagetype;
}

/**
 * Export video resource contents
 *
 * @return array of file content
 */
function video_export_contents($cm, $basevideo) {
    global $CFG, $DB;
    $contents = array();
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $video = $DB->get_record('video', array('id'=>$cm->instance), '*', MUST_EXIST);

    $fullvideo = $video->externalvideo;
    $isvideo = clean_param($fullvideo, PARAM_video);
    if (empty($isvideo)) {
        return null;
    }

    $video = array();
    $video['type'] = 'video';
    $video['filename']     = $video->name;
    $video['filepath']     = null;
    $video['filesize']     = 0;
    $video['filevideo']      = $fullvideo;
    $video['timecreated']  = null;
    $video['timemodified'] = $video->timemodified;
    $video['sortorder']    = null;
    $video['userid']       = null;
    $video['author']       = null;
    $video['license']      = null;
    $contents[] = $video;

    return $contents;
}
