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
 * Prints an instance of mod_agendar.
 *
 * @package     mod_agendar
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class agendamento_slot_form extends moodleform {
    
    public function definition() {
        $mform = $this->_form;
        $slot = $this->_customdata['slot'];
        $agendamento = $this->_customdata['agendamento'];
        
        // Start time.
        $mform->addElement('date_time_selector', 'starttime', get_string('starttime', 'agendamento'));
        $mform->addRule('starttime', null, 'required', null, 'client');
        
        // End time.
        $mform->addElement('date_time_selector', 'endtime', get_string('endtime', 'agendamento'));
        $mform->addRule('endtime', null, 'required', null, 'client');
        
        // Max participants.
        $mform->addElement('text', 'maxparticipants', get_string('maxparticipants', 'agendamento'));
        $mform->setType('maxparticipants', PARAM_INT);
        $mform->addRule('maxparticipants', null, 'required', null, 'client');
        $mform->addRule('maxparticipants', null, 'numeric', null, 'client');
        $mform->setDefault('maxparticipants', 1);
        
        // Description.
        $mform->addElement('textarea', 'description', get_string('description', 'agendamento'), 
                          array('rows' => 3, 'cols' => 50));
        $mform->setType('description', PARAM_TEXT);
        
        // Set defaults if editing.
        if ($slot) {
            $mform->setDefault('starttime', $slot->starttime);
            $mform->setDefault('endtime', $slot->endtime);
            $mform->setDefault('maxparticipants', $slot->maxparticipants);
            $mform->setDefault('description', $slot->description);
        }
        
        $this->add_action_buttons();
    }
    
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        if ($data['endtime'] <= $data['starttime']) {
            $errors['endtime'] = get_string('endtimebeforestart', 'agendamento');
        }
        
        if ($data['maxparticipants'] < 1) {
            $errors['maxparticipants'] = get_string('invalidmaxparticipants', 'agendamento');
        }
        
        return $errors;
    }
}
