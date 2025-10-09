<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The main mod_quizscheduler configuration form.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 */
class mod_quizscheduler_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('name', 'mod_quizscheduler'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Quiz selection.
        $quizzes = $DB->get_records_menu('quiz', array('course' => $this->current->course), 'name', 'id, name');
        if (empty($quizzes)) {
            $mform->addElement('static', 'noquizzes', get_string('quizid', 'mod_quizscheduler'), 
                get_string('error:noquiz', 'mod_quizscheduler'));
        } else {
            $mform->addElement('select', 'quizid', get_string('quizid', 'mod_quizscheduler'), $quizzes);
            $mform->addHelpButton('quizid', 'quizid', 'mod_quizscheduler');
            $mform->addRule('quizid', null, 'required', null, 'client');
        }

        // Timing section.
        $mform->addElement('header', 'timing', get_string('timing', 'mod_quizscheduler'));

        $mform->addElement('date_time_selector', 'timeopen', get_string('timeopen', 'mod_quizscheduler'), 
            array('optional' => true));
        $mform->addHelpButton('timeopen', 'timeopen', 'mod_quizscheduler');

        $mform->addElement('date_time_selector', 'timeclose', get_string('timeclose', 'mod_quizscheduler'), 
            array('optional' => true));
        $mform->addHelpButton('timeclose', 'timeclose', 'mod_quizscheduler');

        // Schedule period section.
        $mform->addElement('header', 'schedulesettings', get_string('schedulesettings', 'mod_quizscheduler'));

        $mform->addElement('date_time_selector', 'schedulestarttime', 
            get_string('schedulestarttime', 'mod_quizscheduler'), 
            array('optional' => true));
        $mform->addHelpButton('schedulestarttime', 'schedulestarttime', 'mod_quizscheduler');

        $mform->addElement('date_time_selector', 'scheduleendtime', 
            get_string('scheduleendtime', 'mod_quizscheduler'), 
            array('optional' => true));
        $mform->addHelpButton('scheduleendtime', 'scheduleendtime', 'mod_quizscheduler');

        // Configuration section.
        $mform->addElement('header', 'configurationsettings', get_string('settings', 'mod_quizscheduler'));

        $mform->addElement('text', 'maxbookings', get_string('maxbookings', 'mod_quizscheduler'));
        $mform->setType('maxbookings', PARAM_INT);
        $mform->setDefault('maxbookings', 1);
        $mform->addHelpButton('maxbookings', 'maxbookings', 'mod_quizscheduler');

        // Duration options.
        $duration_options = array(
            5 => '5 ' . get_string('minutes', 'mod_quizscheduler'),
            15 => '15 ' . get_string('minutes', 'mod_quizscheduler'),
            30 => '30 ' . get_string('minutes', 'mod_quizscheduler'),
            45 => '45 ' . get_string('minutes', 'mod_quizscheduler'),
            60 => '1 ' . get_string('hour', 'mod_quizscheduler'),
            90 => '1.5 ' . get_string('hours', 'mod_quizscheduler'),
            120 => '2 ' . get_string('hours', 'mod_quizscheduler'),
        );

        $mform->addElement('select', 'slotduration', get_string('slotduration', 'mod_quizscheduler'), $duration_options);
        $mform->setDefault('slotduration', 60);
        $mform->addHelpButton('slotduration', 'slotduration', 'mod_quizscheduler');

        $mform->addElement('text', 'maxusersperslot', get_string('maxusersperslot', 'mod_quizscheduler'));
        $mform->setType('maxusersperslot', PARAM_INT);
        $mform->setDefault('maxusersperslot', 1);
        $mform->addHelpButton('maxusersperslot', 'maxusersperslot', 'mod_quizscheduler');

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }

    /**
     * Enforce validation rules here
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *               or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['timeopen']) && !empty($data['timeclose'])) {
            if ($data['timeopen'] >= $data['timeclose']) {
                $errors['timeclose'] = get_string('closebeforeopen', 'mod_quizscheduler');
            }
        }

        if (!empty($data['schedulestarttime']) && !empty($data['scheduleendtime'])) {
            if ($data['schedulestarttime'] >= $data['scheduleendtime']) {
                $errors['scheduleendtime'] = get_string('closebeforeopen', 'mod_quizscheduler');
            }
        }

        return $errors;
    }
}