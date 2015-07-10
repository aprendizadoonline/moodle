<?php
/**
 * @package    mod
 * @subpackage vidgaleria
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in vidgaleria module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function vidgaleria_supports($feature) {
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
function vidgaleria_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function vidgaleria_reset_userdata($data) {
    return array();
}

/**
 * List of view style log actions
 * @return array
 */
function vidgaleria_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List of update style log actions
 * @return array
 */
function vidgaleria_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add vidgaleria instance.
 * @param object $data
 * @param object $mform
 * @return int new vidgaleria instance id
 */
function vidgaleria_add_instance($data, $mform) {
    global $CFG, $DB;

    $data->timemodified = time();
    $data->id = $DB->insert_record('vidgaleria', $data);

    return $data->id;
}

/**
 * Update vidgaleria instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function vidgaleria_update_instance($data, $mform) {
    global $CFG, $DB;

    $data->timemodified = time();
    $data->id           = $data->instance;

    $DB->update_record('vidgaleria', $data);

    return true;
}

/**
 * Delete vidgaleria instance.
 * @param int $id
 * @return bool true
 */
function vidgaleria_delete_instance($id) {
    global $DB;

    if (!$vidgaleria = $DB->get_record('vidgaleria', array('id'=>$id))) {
        return false;
    }

    // note: all context files are deleted automatically

    $DB->delete_records('vidgaleria', array('id'=>$vidgaleria->id));

    return true;
}

/**
 * Return use outline
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $vidgaleria
 * @return object|null
 */
function vidgaleria_user_outline($course, $user, $mod, $vidgaleria) {
    global $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'vidgaleria',
                                              'action'=>'view', 'info'=>$vidgaleria->id), 'time ASC')) {

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
 * @param object $vidgaleria
 */
function vidgaleria_user_complete($course, $user, $mod, $vidgaleria) {
    global $CFG, $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'vidgaleria',
                                              'action'=>'view', 'info'=>$vidgaleria->id), 'time ASC')) {
        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $strmostrecently = get_string('mostrecently');
        $strnumviews = get_string('numviews', '', $numviews);

        echo "$strnumviews - $strmostrecently ".userdate($lastlog->time);

    } else {
        print_string('neverseen', 'vidgaleria');
    }
}

/**
 * Returns the users with data in one vidgaleria
 *
 * @todo: deprecated - to be deleted in 2.2
 *
 * @param int $vidgaleriaid
 * @return bool false
 */
function vidgaleria_get_participants($vidgaleriaid) {
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
function vidgaleria_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;

    if (!$vidgaleria = $DB->get_record('vidgaleria', array('id'=>$coursemodule->instance),
            'id, name, externalvideo, intro, introformat')) {
        return NULL;
    }

    $info = new cached_cm_info();
    $info->name = $vidgaleria->name;

    $config = get_config('vidgaleria');
    return $info;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function vidgaleria_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-vidgaleria-*'=>get_string('page-mod-vidgaleria-x', 'vidgaleria'));
    return $module_pagetype;
}

/**
 * Export vidgaleria resource contents
 *
 * @return array of file content
 */
function vidgaleria_export_contents($cm, $basevidgaleria) {
    global $CFG, $DB;
    $contents = array();
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $vidgaleria = $DB->get_record('vidgaleria', array('id'=>$cm->instance), '*', MUST_EXIST);

    $fullvidgaleria = $vidgaleria->externalvidgaleria;
    $isvidgaleria = clean_param($fullvidgaleria, PARAM_vidgaleria);
    if (empty($isvidgaleria)) {
        return null;
    }

    $vidgaleria = array();
    $vidgaleria['type'] = 'vidgaleria';
    $vidgaleria['filename']     = $vidgaleria->name;
    $vidgaleria['filepath']     = null;
    $vidgaleria['filesize']     = 0;
    $vidgaleria['filevidgaleria']      = $fullvidgaleria;
    $vidgaleria['timecreated']  = null;
    $vidgaleria['timemodified'] = $vidgaleria->timemodified;
    $vidgaleria['sortorder']    = null;
    $vidgaleria['userid']       = null;
    $vidgaleria['author']       = null;
    $vidgaleria['license']      = null;
    $contents[] = $vidgaleria;

    return $contents;
}
