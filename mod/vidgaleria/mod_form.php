<?php
/**
 * @package    mod
 * @subpackage vidgaleria
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_vidgaleria_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;
        $mform = $this->_form;

        $config = get_config('video');
        

        //-------------------------------------------------------
        // Headers
        //-------------------------------------------------------
        $mform->addElement('html', '<script src="' . $config->url . 'js/jquery.min.js" type="text/javascript"></script>');
		$mform->addElement('html', '<script src="' . $config->url . 'js/vidgaleriateca.plugin.js" type="text/javascript"></script>');
		$mform->addElement('html', '<link href="'  . $config->url . 'css/moodle-v2.css"   rel="stylesheet" type="text/css" media="all" />');
        
		
        //-------------------------------------------------------
        // Formulário
        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'), array(), array('class'=>'48'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'), array());
        $mform->addElement('text', 'externalvideo', get_string('externalvideo', 'vidgaleria'), array('size'=>'60'), array('usefilepicker'=>true));

        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values) {

    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validating Entered vidgaleria, we are looking for obvious problems only,
        // teachers are responsible for testing if it actually works.

        // This is not a security validation!! Teachers are allowed to enter "javascript:alert(666)" for example.

        // NOTE: do not try to explain the difference between vidgaleria and URI, people would be only confused...

        if (empty($data['externalvideo'])) {
            $errors['externalvideo'] = get_string('required');
        }
        return $errors;
    }

}
