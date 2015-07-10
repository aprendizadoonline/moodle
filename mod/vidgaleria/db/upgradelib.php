<?php
/**
 * @package    mod
 * @subpackage vidgaleria
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Migrate vidgaleria module data from 1.9 resource_old table to new vidgaleria table
 * @return void
 */
function vidgaleria_20_migrate() {
    global $CFG, $DB;

    require_once($CFG->libdir . "/filelib.php");
    require_once($CFG->libdir . "/resourcelib.php");
    require_once($CFG->dirroot . "/course/lib.php");

    if (!file_exists($CFG->dirroot . "/mod/resource/db/upgradelib.php")) {
        // bad luck, somebody deleted resource module
        return;
    }

    require_once($CFG->dirroot . "/mod/resource/db/upgradelib.php");

    // create resource_old table and copy resource table there if needed
    if (!resource_20_prepare_migration()) {
        // no modules or fresh install
        return;
    }

    $candidates = $DB->get_recordset('resource_old', array('type'=>'file', 'migrated'=>0));
    if (!$candidates->valid()) {
        $candidates->close(); // Not going to iterate (but exit), close rs
        return;
    }

    foreach ($candidates as $candidate) {
        $path = $candidate->reference;
        $siteid = get_site()->id;

        if (strpos($path, 'LOCALPATH') === 0) {
            // ignore not maintained local files - sorry
            continue;
        } else if (!strpos($path, '://')) {
            // not vidgaleria
            continue;
        } else if (preg_match("|$CFG->wwwroot/file.php(\?file=)?/$siteid(/[^\s'\"&\?#]+)|", $path, $matches)) {
            // handled by resource module
            continue;
        } else if (preg_match("|$CFG->wwwroot/file.php(\?file=)?/$candidate->course(/[^\s'\"&\?#]+)|", $path, $matches)) {
            // handled by resource module
            continue;
        }

        upgrade_set_timeout();

        if ($CFG->texteditors !== 'textarea') {
            $intro       = text_to_html($candidate->intro, false, false, true);
            $introformat = FORMAT_HTML;
        } else {
            $intro       = $candidate->intro;
            $introformat = FORMAT_MOODLE;
        }

        $vidgaleria = new stdClass();
        $vidgaleria->course       = $candidate->course;
        $vidgaleria->name         = $candidate->name;
        $vidgaleria->intro        = $intro;
        $vidgaleria->introformat  = $introformat;
        $vidgaleria->externalvideo  = $path;
        $vidgaleria->timemodified = time();

       

        if (!$vidgaleria = resource_migrate_to_module('vidgaleria', $candidate, $vidgaleria)) {
            continue;
        }
    }

    $candidates->close();

    // clear all course modinfo caches
    rebuild_course_cache(0, true);
}
