<?php
/**
 * @package    mod
 * @subpackage video
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_video_install() {
    global $CFG;

    // migrate settings if present
    if (!empty($CFG->resource_secretphrase)) {
        set_config('secretphrase', $CFG->resource_secretphrase, 'video');
    }
    unset_config('resource_secretphrase');

    // Upgrade from old resource module type if needed
    require_once($CFG->dirroot . "/mod/video/db/upgradelib.php");
    video_20_migrate();
}

function xmldb_video_install_recovery() {
    global $CFG;

    // Upgrade from old resource module type if needed
    require_once($CFG->dirroot . "/mod/video/db/upgradelib.php");
    video_20_migrate();
}
