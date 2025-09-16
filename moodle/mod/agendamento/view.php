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

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID.
$a  = optional_param('a', 0, PARAM_INT);  // Agendamento instance ID.

if ($id) {
    $cm         = get_coursemodule_from_id('agendamento', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $agendamento  = $DB->get_record('agendamento', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($a) {
    $agendamento  = $DB->get_record('agendamento', array('id' => $a), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $agendamento->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('agendamento', $agendamento->id, $course->id, false, MUST_EXIST);
} else {
    print_error('missingparameter');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

// Trigger course_module_viewed event only if classes exist
if (class_exists('mod_agendamento\event\course_module_viewed')) {
    $event = \mod_agendamento\event\course_module_viewed::create(array(
        'objectid' => $agendamento->id,
        'context' => $context,
    ));
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot($cm->modname, $agendamento);
    $event->trigger();
}

// Completion.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/agendamento/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($agendamento->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

// Show intro.
if ($agendamento->intro) {
    echo $OUTPUT->box(format_module_intro('agendamento', $agendamento, $cm->id), 'generalbox mod_introbox', 'agendamentointro');
}

// Check capabilities.
$canmanage = has_capability('mod/agendamento:manageslots', $context);

if ($canmanage) {
    echo '<p><a href="' . new moodle_url('/mod/agendamento/manageslots.php', array('id' => $cm->id)) . '" class="btn btn-primary">' . 
         get_string('manageslots', 'agendamento') . '</a></p>';
}

// Show available slots.
$slots = $DB->get_records('agendamento_slots', array('agendamento' => $agendamento->id), 'starttime ASC');

if (empty($slots)) {
    echo '<p>' . get_string('noslots', 'agendamento') . '</p>';
} else {
    echo '<h3>' . get_string('availableslots', 'agendamento') . '</h3>';
    
    foreach ($slots as $slot) {
        $bookingcount = $DB->count_records('agendamento_bookings', array('slotid' => $slot->id));
        $userbooked = $DB->record_exists('agendamento_bookings', array('slotid' => $slot->id, 'userid' => $USER->id));
        
        echo '<div class="slot-item card mb-2">';
        echo '<div class="card-body">';
        echo '<h5>' . userdate($slot->starttime) . ' - ' . userdate($slot->endtime) . '</h5>';
        
        if (!empty($slot->description)) {
            echo '<p>' . format_text($slot->description) . '</p>';
        }
        
        echo '<p>' . get_string('participants', 'agendamento') . ': ' . $bookingcount . '/' . $slot->maxparticipants . '</p>';
        
        if ($userbooked) {
            echo '<span class="badge badge-success">' . get_string('booked', 'agendamento') . '</span>';
            echo ' <a href="' . new moodle_url('/mod/agendamento/book.php', array(
                'id' => $cm->id, 
                'slotid' => $slot->id, 
                'action' => 'cancel',
                'sesskey' => sesskey()
            )) . '" class="btn btn-sm btn-outline-danger">' . get_string('cancel', 'agendamento') . '</a>';
        } else if ($bookingcount >= $slot->maxparticipants) {
            echo '<span class="badge badge-warning">' . get_string('full', 'agendamento') . '</span>';
        } else {
            echo '<a href="' . new moodle_url('/mod/agendamento/book.php', array(
                'id' => $cm->id, 
                'slotid' => $slot->id, 
                'action' => 'book',
                'sesskey' => sesskey()
            )) . '" class="btn btn-sm btn-primary">' . get_string('book', 'agendamento') . '</a>';
        }
        
        echo '</div>';
        echo '</div>';
    }
}

echo $OUTPUT->footer();
