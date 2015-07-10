<?php

/**
 * @package    mod
 * @subpackage vidgaleria
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die();

/**
 * vidgaleria conversion handler. This resource handler is called by moodle1_mod_resource_handler
 */
class moodle1_mod_vidgaleria_handler extends moodle1_resource_successor_handler {

    /** @var moodle1_file_manager instance */
    protected $fileman = null;

    /**
     * Converts /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE data
     * Called by moodle1_mod_resource_handler::process_resource()
     */
    public function process_legacy_resource($data) {

        // get the course module id and context id
        $instanceid = $data['id'];
        $cminfo     = $this->get_cminfo($instanceid, 'resource');
        $moduleid   = $cminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // prepare the new vidgaleria instance record
        $vidgaleria                 = array();
        $vidgaleria['id']           = $data['id'];
        $vidgaleria['name']         = $data['name'];
        $vidgaleria['intro']        = $data['intro'];
        $vidgaleria['introformat']  = $data['introformat'];
        $vidgaleria['externalvideo']  = $data['reference'];
        $vidgaleria['timemodified'] = $data['timemodified'];


        // convert course files embedded into the intro
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_vidgaleria', 'intro');
        $vidgaleria['intro'] = moodle1_converter::migrate_referenced_files($vidgaleria['intro'], $this->fileman);

        // write vidgaleria.xml
        $this->open_xml_writer("activities/vidgaleria_{$moduleid}/vidgaleria.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'vidgaleria', 'contextid' => $contextid));
        $this->write_xml('vidgaleria', $vidgaleria, array('/vidgaleria/id'));
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml
        $this->open_xml_writer("activities/vidgaleria_{$moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }
}
