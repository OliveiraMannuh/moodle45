<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The main mod_agendar configuration form.
 *
 * @package     mod_agendar
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_agendar_mod_form extends moodleform_mod {

    function definition() {
        $mform = $this->_form;

        // Campos padrão
        $mform->addElement('header', 'general', get_string('general', 'form'));
        
        $mform->addElement('text', 'name', get_string('agendarname', 'agendar'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        // Configurações de agendamento
        $mform->addElement('header', 'bookingsettings', get_string('bookingsettings', 'agendar'));
        
        $mform->addElement('text', 'maxbookingsperuser', get_string('maxbookingsperuser', 'agendar'), 
            array('size' => '3'));
        $mform->setType('maxbookingsperuser', PARAM_INT);
        $mform->setDefault('maxbookingsperuser', 1);
        $mform->addHelpButton('maxbookingsperuser', 'maxbookingsperuser', 'agendar');

        $mform->addElement('selectyesno', 'allowcancellation', get_string('allowcancellation', 'agendar'));
        $mform->setDefault('allowcancellation', 1);
        $mform->addHelpButton('allowcancellation', 'allowcancellation', 'agendar');

        $cancellationoptions = array(
            '0' => get_string('cancellationdeadlinenone', 'agendar'),
            '3600' => get_string('timeduration', 'core', '1 ' . get_string('hour')),
            '7200' => get_string('timeduration', 'core', '2 ' . get_string('hours')),
            '86400' => get_string('timeduration', 'core', '1 ' . get_string('day')),
            '172800' => get_string('timeduration', 'core', '2 ' . get_string('days')),
            '604800' => get_string('timeduration', 'core', '1 ' . get_string('week')),
        );
        $mform->addElement('select', 'cancellationdeadline', get_string('cancellationdeadline', 'agendar'), 
            $cancellationoptions);
        $mform->setDefault('cancellationdeadline', 3600);
        $mform->disabledIf('cancellationdeadline', 'allowcancellation', 'eq', 0);

        $mform->addElement('selectyesno', 'emailnotifications', get_string('emailnotifications', 'agendar'));
        $mform->setDefault('emailnotifications', 1);
        $mform->addHelpButton('emailnotifications', 'emailnotifications', 'agendar');

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}
