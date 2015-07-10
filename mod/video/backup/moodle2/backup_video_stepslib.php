<?php
/**
 * @package    mod
 * @subpackage video
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die;

 /**
 * Define the complete video structure for backup, with file and id annotations
 */
class backup_video_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        //the video module stores no user info

        // Define each element separated
        $video = new backup_nested_element('video', array('id'), array(
            'name', 'intro', 'introformat', 'externalvideo',
            'timemodified'));


        // Build the tree
        //nothing here for videos

        // Define sources
        $video->set_source_table('video', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        //module has no id annotations

        // Define file annotations
        $video->annotate_files('mod_video', 'intro', null); // This file area hasn't itemid

        // Return the root element (video), wrapped into standard activity structure
        return $this->prepare_activity_structure($video);

    }
}
