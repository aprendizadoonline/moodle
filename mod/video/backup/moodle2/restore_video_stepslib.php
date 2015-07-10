<?php
/**
 * @package    mod
 * @subpackage video
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

/**
 * Define all the restore steps that will be used by the restore_video_activity_task
 */

/**
 * Structure step to restore one video activity
 */
class restore_video_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('video', '/activity/video');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_video($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // insert the video record
        $newitemid = $DB->insert_record('video', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // Add video related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_video', 'intro', null);
    }
}
