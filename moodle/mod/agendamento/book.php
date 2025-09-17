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
 * Book or cancel booking for a time slot.
 *
 * @package     mod_agendamento
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = required_param('id', PARAM_INT);
$slotid = required_param('slotid', PARAM_INT);
$action = required_param('action', PARAM_ALPHA);

// Validate sesskey
if (!confirm_sesskey()) {
    print_error('invalidsesskey');
}

$cm = get_coursemodule_from_id('agendamento', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$agendamento = $DB->get_record('agendamento', array('id' => $cm->instance), '*', MUST_EXIST);
$slot = $DB->get_record('agendamento_slots', array('id' => $slotid), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

// Verify that the slot belongs to this agendamento
if ($slot->agendamento != $agendamento->id) {
    print_error('invalidslot', 'agendamento');
}

if ($action === 'book') {
    // Check if slot is full.
    $bookingcount = $DB->count_records('agendamento_bookings', array('slotid' => $slot->id));
    if ($bookingcount >= $slot->maxparticipants) {
        redirect(new moodle_url('/mod/agendamento/view.php', array('id' => $cm->id)), 
                 get_string('slotfull', 'agendamento'), null, \core\output\notification::NOTIFY_ERROR);
    }
    
    // Check if user already booked.
    if ($DB->record_exists('agendamento_bookings', array('slotid' => $slot->id, 'userid' => $USER->id))) {
        redirect(new moodle_url('/mod/agendamento/view.php', array('id' => $cm->id)), 
                 get_string('alreadybooked', 'agendamento'), null, \core\output\notification::NOTIFY_ERROR);
    }
    
    // Create booking.
    $booking = new stdClass();
    $booking->slotid = $slot->id;
    $booking->userid = $USER->id;
    $booking->timecreated = time();
    
    $DB->insert_record('agendamento_bookings', $booking);
    
    // Update completion - This is the key part for automatic completion
    $completion = new completion_info($course);
    if ($completion->is_enabled($cm) && !empty($agendamento->completionbooking)) {
        $completion->update_state($cm, COMPLETION_COMPLETE, $USER->id);
    }
    
    redirect(new moodle_url('/mod/agendamento/view.php', array('id' => $cm->id)), 
             get_string('bookingsuccess', 'agendamento'), null, \core\output\notification::NOTIFY_SUCCESS);
             
} else if ($action === 'cancel') {
    // Check if user has booking.
    if (!$DB->record_exists('agendamento_bookings', array('slotid' => $slot->id, 'userid' => $USER->id))) {
        redirect(new moodle_url('/mod/agendamento/view.php', array('id' => $cm->id)), 
                 get_string('nobooking', 'agendamento'), null, \core\output\notification::NOTIFY_ERROR);
    }
    
    // Cancel booking.
    $DB->delete_records('agendamento_bookings', array('slotid' => $slot->id, 'userid' => $USER->id));
    
    // Update completion state - Check if user still has other bookings
    $completion = new completion_info($course);
    if ($completion->is_enabled($cm) && !empty($agendamento->completionbooking)) {
        // Check if user has other bookings for this agendamento activity
        $sql = "SELECT COUNT(b.id)
                FROM {agendamento_bookings} b
                JOIN {agendamento_slots} s ON b.slotid = s.id
                WHERE s.agendamento = ? AND b.userid = ?";

        $remainingbookings = $DB->count_records_sql($sql, array($agendamento->id, $USER->id));

        // If no more bookings and completion requires booking, mark as incomplete
        if ($remainingbookings == 0) {
            // Force update completion state to incomplete
            $completion->update_state($cm, COMPLETION_INCOMPLETE, $USER->id);
            
            // Also update the completion cache to ensure it's properly reset
            $completion->internal_get_state($cm, $USER->id, null);
            
            // Alternative approach: Reset completion entirely and let it be recalculated
            $params = array(
                'coursemoduleid' => $cm->id,
                'userid' => $USER->id
            );
            
            // Delete existing completion record to force recalculation
            if ($completionrecord = $DB->get_record('course_modules_completion', $params)) {
                // Reset completion viewed and completionstate
                $completionrecord->completionstate = COMPLETION_INCOMPLETE;
                $completionrecord->timemodified = time();
                $DB->update_record('course_modules_completion', $completionrecord);
            }
        }
    }
    
    redirect(new moodle_url('/mod/agendamento/view.php', array('id' => $cm->id)), 
             get_string('cancelsuccess', 'agendamento'), null, \core\output\notification::NOTIFY_SUCCESS);
             
} else {
    print_error('invalidaction');
}
