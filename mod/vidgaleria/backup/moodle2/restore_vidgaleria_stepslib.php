<?php
/**
 * @package    mod
 * @subpackage vidgaleria
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

/**
 * Define all the restore steps that will be used by the restore_vidgaleria_activity_task
 */

/**
 * Structure step to restore one vidgaleria activity
 */
class restore_vidgaleria_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('vidgaleria', '/activity/vidgaleria');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_vidgaleria($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // insert the vidgaleria record
        $newitemid = $DB->insert_record('vidgaleria', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // Add vidgaleria related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_vidgaleria', 'intro', null);
    }
}
