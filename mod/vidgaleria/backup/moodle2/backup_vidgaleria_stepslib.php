<?php
/**
 * @package    mod
 * @subpackage vidgaleria
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die;

 /**
 * Define the complete vidgaleria structure for backup, with file and id annotations
 */
class backup_vidgaleria_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        //the vidgaleria module stores no user info

        // Define each element separated
        $vidgaleria = new backup_nested_element('vidgaleria', array('id'), array(
            'name', 'intro', 'introformat', 'externalvideo',
            'timemodified'));


        // Build the tree
        //nothing here for vidgalerias

        // Define sources
        $vidgaleria->set_source_table('vidgaleria', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        //module has no id annotations

        // Define file annotations
        $vidgaleria->annotate_files('mod_vidgaleria', 'intro', null); // This file area hasn't itemid

        // Return the root element (vidgaleria), wrapped into standard activity structure
        return $this->prepare_activity_structure($vidgaleria);

    }
}
