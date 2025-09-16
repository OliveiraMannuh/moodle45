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

namespace mod_agendar\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class add_slot_form extends \moodleform {
    
    public function definition() {
        $mform = $this->_form;
        
        // Data e hora de início
        $mform->addElement('date_time_selector', 'starttime', get_string('starttime', 'agendar'));
        $mform->addRule('starttime', get_string('required'), 'required', null, 'client');
        
        // Data e hora de fim
        $mform->addElement('date_time_selector', 'endtime', get_string('endtime', 'agendar'));
        $mform->addRule('endtime', get_string('required'), 'required', null, 'client');
        
        // Número máximo de agendamentos
        $mform->addElement('text', 'maxbookings', get_string('maxbookings', 'agendar'), 
            array('size' => 3));
        $mform->setType('maxbookings', PARAM_INT);
        $mform->setDefault('maxbookings', 1);
        $mform->addRule('maxbookings', get_string('required'), 'required', null, 'client');
        
        // Local
        $mform->addElement('text', 'location', get_string('location', 'agendar'), 
            array('size' => 50));
        $mform->setType('location', PARAM_TEXT);
        
        // Notas
        $mform->addElement('textarea', 'notes', get_string('notes', 'agendar'), 
            array('rows' => 3, 'cols' => 50));
        $mform->setType('notes', PARAM_TEXT);
        
        $this->add_action_buttons(true, get_string('addslot', 'agendar'));
    }
    
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        if ($data['endtime'] <= $data['starttime']) {
            $errors['endtime'] = get_string('endtimebeforestart', 'agendar');
        }
        
        if ($data['starttime'] <= time()) {
            $errors['starttime'] = get_string('starttimepast', 'agendar');
        }
        
        if ($data['maxbookings'] < 1) {
            $errors['maxbookings'] = get_string('maxbookingsmin', 'agendar');
        }
        
        return $errors;
    }
}
