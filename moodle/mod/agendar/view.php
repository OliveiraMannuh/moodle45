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

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/agendar/lib.php');

$id = required_param('id', PARAM_INT);
$action = optional_param('action', 'view', PARAM_ALPHA);

$cm = get_coursemodule_from_id('agendar', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$agendar = $DB->get_record('agendar', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/agendar:view', $context);

// URLs
$pageurl = new moodle_url('/mod/agendar/view.php', array('id' => $cm->id));
$manageurl = new moodle_url('/mod/agendar/manage_slots.php', array('id' => $cm->id));

$PAGE->set_url($pageurl);
$PAGE->set_title(format_string($agendar->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Log da visualização
$event = \mod_agendar\event\course_module_viewed::create(array(
    'objectid' => $agendar->id,
    'context' => $context,
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('agendar', $agendar);
$event->trigger();

echo $OUTPUT->header();

// Cabeçalho
echo $OUTPUT->heading(format_string($agendar->name));

if ($agendar->intro) {
    echo $OUTPUT->box(format_module_intro('agendar', $agendar, $cm->id), 
        'generalbox mod_introbox', 'agendarintro');
}

// Botão para gerenciar slots (apenas para professores)
if (has_capability('mod/agendar:manageslots', $context)) {
    echo html_writer::div(
        $OUTPUT->single_button($manageurl, get_string('manageslots', 'agendar'), 'get', 
            array('class' => 'btn btn-primary')),
        'text-center mb-3'
    );
}

// Ações dos alunos
if ($action === 'book') {
    $slotid = required_param('slotid', PARAM_INT);
    $notes = optional_param('notes', '', PARAM_TEXT);
    
    require_capability('mod/agendar:book', $context);
    
    if (agendar_book_slot($slotid, $USER->id, $notes)) {
        echo $OUTPUT->notification(get_string('bookingsuccess', 'agendar'), 'success');
    } else {
        echo $OUTPUT->notification(get_string('bookingfailed', 'agendar'), 'error');
    }
    
} else if ($action === 'cancel') {
    $bookingid = required_param('bookingid', PARAM_INT);
    
    require_capability('mod/agendar:book', $context);
    
    if (agendar_cancel_booking($bookingid, $USER->id)) {
        echo $OUTPUT->notification(get_string('cancellationsuccess', 'agendar'), 'success');
    } else {
        echo $OUTPUT->notification(get_string('cancellationfailed', 'agendar'), 'error');
    }
}

// Exibir agendamentos do usuário atual
if (has_capability('mod/agendar:book', $context)) {
    $userbookings = agendar_get_user_bookings($agendar->id, $USER->id, 'booked');
    
    if ($userbookings) {
        echo $OUTPUT->heading(get_string('yourbookings', 'agendar'), 3);
        
        $table = new html_table();
        $table->head = array(
            get_string('datetime', 'agendar'),
            get_string('location', 'agendar'),
            get_string('notes', 'agendar'),
            get_string('actions', 'agendar')
        );
        
        foreach ($userbookings as $booking) {
            $datetime = userdate($booking->starttime) . ' - ' . userdate($booking->endtime, get_string('strftimetime'));
            $location = $booking->location ?: '-';
            $notes = $booking->slotnotes ?: '-';
            
            $actions = '';
            if ($agendar->allowcancellation) {
                $timeleft = $booking->starttime - time();
                if ($timeleft > $agendar->cancellationdeadline) {
                    $cancelurl = new moodle_url($pageurl, array('action' => 'cancel', 'bookingid' => $booking->id));
                    $actions = html_writer::link($cancelurl, get_string('cancel'), 
                        array('class' => 'btn btn-sm btn-danger',
                              'onclick' => 'return confirm("' . get_string('confirmcancel', 'agendar') . '")'));
                }
            }
            
            $table->data[] = array($datetime, $location, $notes, $actions);
        }
        
        echo html_writer::table($table);
    }
    
    // Mostrar slots disponíveis se o usuário pode agendar mais
    if (agendar_can_user_book_more($agendar->id, $USER->id)) {
        $availableslots = agendar_get_available_slots($agendar->id, $USER->id);
        
        if ($availableslots) {
            echo $OUTPUT->heading(get_string('availableslots', 'agendar'), 3);
            
            $table = new html_table();
            $table->head = array(
                get_string('datetime', 'agendar'),
                get_string('location', 'agendar'),
                get_string('availability', 'agendar'),
                get_string('notes', 'agendar'),
                get_string('actions', 'agendar')
            );
            
            foreach ($availableslots as $slot) {
                $datetime = userdate($slot->starttime) . ' - ' . userdate($slot->endtime, get_string('strftimetime'));
                $location = $slot->location ?: '-';
                $availability = $slot->currentbookings . '/' . $slot->maxbookings;
                $notes = $slot->notes ?: '-';
                
                $bookurl = new moodle_url($pageurl, array('action' => 'book', 'slotid' => $slot->id));
                $actions = html_writer::link($bookurl, get_string('book', 'agendar'), 
                    array('class' => 'btn btn-sm btn-success'));
                
                $table->data[] = array($datetime, $location, $availability, $notes, $actions);
            }
            
            echo html_writer::table($table);
            
        } else {
            echo $OUTPUT->notification(get_string('noavailableslots', 'agendar'), 'info');
        }
        
    } else {
        echo $OUTPUT->notification(get_string('maxbookingsreached', 'agendar'), 'warning');
    }
}

echo $OUTPUT->footer();
