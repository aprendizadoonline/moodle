<?php
/**
 * @package    mod
 * @subpackage vidgaleria
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_vidgaleria_install() {
    global $CFG;

    // migrate settings if present
    if (!empty($CFG->resource_secretphrase)) {
        set_config('secretphrase', $CFG->resource_secretphrase, 'vidgaleria');
    }
    unset_config('resource_secretphrase');

    // Upgrade from old resource module type if needed
    require_once($CFG->dirroot . "/mod/vidgaleria/db/upgradelib.php");
    vidgaleria_20_migrate();
}

function xmldb_vidgaleria_install_recovery() {
    global $CFG;

    // Upgrade from old resource module type if needed
    require_once($CFG->dirroot . "/mod/vidgaleria/db/upgradelib.php");
    vidgaleria_20_migrate();
}
