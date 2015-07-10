<?php
/**
 * @package    mod
 * @subpackage video
 * @copyright  2012 Eduardo Kraus  {@link http://eduardokraus.com}
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->libdir . "/resourcelib.php");

    $settings->add(new admin_setting_configtext('video/url',
        'URL', 'Url completa da Videoteca. Ex: http://videoteca.seudominio.com/', ''));
    
    $settings->add(new admin_setting_configcheckbox('video/popup', 
        'Pop-up','Abrir os vídeos em Pop-up? (Se alterar este valor, limpe o cache para aplicar a todos os vídeos)', 0));


    $settings->add(new admin_setting_configselect('video/seguranca', 'Segurança',
        'No player mostrar CPF ou ID?', 'id',
            array (
                'id' => 'ID do Aluno',
                'cpf' => 'CPF'
            )
    ));
}
