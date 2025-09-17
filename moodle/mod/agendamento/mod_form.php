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
 * The main module configuration form.
 *
 * @package     mod_agendamento
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_agendamento_mod_form extends moodleform_mod {
    
    public function definition() {
        global $CFG;
        
        $mform = $this->_form;
        
        // Adding the "general" fieldset.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        
        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('agendamentoname', 'agendamento'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        
        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();
        
        // Grade settings.
        $this->standard_grading_coursemodule_elements();
        
        // Standard coursemodule elements.
        $this->standard_coursemodule_elements();
        
        // Add standard buttons.
        $this->add_action_buttons();
    }
    
    /**
     * Add completion rules to the form.
     *
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules() {
        $mform =& $this->_form;

        $group = array();
        $group[] = $mform->createElement('checkbox', 'completionbooking', '', get_string('completionbooking', 'agendamento'));
        $mform->addGroup($group, 'completionbookinggroup', get_string('completionbookinggroup', 'agendamento'), array(' '), false);
        $mform->addHelpButton('completionbookinggroup', 'completionbookinggroup', 'agendamento');

        return array('completionbookinggroup');
    }

    /**
     * Called during validation to see whether some module-specific completion rules are selected.
     *
     * @param array $data Input data not yet validated.
     * @return bool True if one or more rules is enabled, false if none are.
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completionbooking']);
    }

    /**
     * Get data from the form.
     * This method is called to retrieve form data.
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }

        // Turn off completion settings if the checkboxes aren't ticked.
        if (!empty($data->completionunlocked)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completionbooking) || !$autocompletion) {
                $data->completionbooking = 0;
            }
        }

        return $data;
    }
}
/**
 * This function is called when the instance is deleted.
 * If it returns true, the instance has been deleted properly.
 * If it returns false, deletion has failed and an error message will be displayed.
 */