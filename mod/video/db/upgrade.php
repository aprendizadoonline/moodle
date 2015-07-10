<?php
/**
 * @package    mod
 * @subpackage video
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_video_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this
    if ($oldversion < 2011092800) {

        // Changing nullability of field externalvideo on table videos to not-null
        $table = new xmldb_table('video');
        $field = new xmldb_field('externalvideo', XMLDB_TYPE_TEXT, 'small', null,
                XMLDB_NOTNULL, null, null, 'introformat');

        $DB->set_field_select('video', 'externalvideo', $DB->sql_empty(), 'externalvideo IS NULL');
        // Launch change of nullability for field =externalvideo
        $dbman->change_field_notnull($table, $field);

        // video savepoint reached
        upgrade_mod_savepoint(true, 2011092800, 'video');
    }

    // Moodle v2.2.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
