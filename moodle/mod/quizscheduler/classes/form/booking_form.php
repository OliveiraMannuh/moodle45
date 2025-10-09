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
 * Booking form for quiz scheduler.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for booking a time slot.
 */
class booking_form extends \moodleform {

    /**
     * Define the form.
     */
    protected function definition() {
        $mform = $this->_form;
        $slots = $this->_customdata['slots'];
        $manager = $this->_customdata['manager'];

        $mform->addElement('header', 'bookingheader', get_string('availableslots', 'mod_quizscheduler'));

        if (empty($slots)) {
            $mform->addElement('static', 'noslots', '', get_string('noslots', 'mod_quizscheduler'));
            return;
        }

        $slotoptions = array();
        foreach ($slots as $slot) {
            $starttime = userdate($slot->starttime, get_string('strftimetime24', 'langconfig'));
            $endtime = userdate($slot->endtime, get_string('strftimetime24', 'langconfig'));
            $date = userdate($slot->starttime, get_string('strftimedate', 'langconfig'));
            
            $label = $date . ' - ' . $starttime . ' a ' . $endtime;
            $label .= ' (' . $slot->available_spaces . ' ' . get_string('available', 'core') . ')';
            
            $slotoptions[$slot->id] = $label;
        }

        $mform->addElement('select', 'slotid', get_string('selectslot', 'mod_quizscheduler'), $slotoptions);
        $mform->addRule('slotid', null, 'required', null, 'client');

        $this->add_action_buttons(true, get_string('bookslot', 'mod_quizscheduler'));
    }

    /**
     * Validate the form data.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        $manager = $this->_customdata['manager'];
        $userid = $this->_customdata['userid'];

        if (!empty($data['slotid'])) {
            if (!$manager->can_book_slot($data['slotid'], $userid)) {
                $errors['slotid'] = get_string('error:cannotbook', 'mod_quizscheduler');
            }
        }

        return $errors;
    }
}
