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
require_once($CFG->dirroot.'/mod/agendar/classes/form/add_slot_form.php');

$id = required_param('id', PARAM_INT);
$action = optional_param('action', 'list', PARAM_ALPHA);
$slotid = optional_param('slotid', 0, PARAM_INT);

$cm = get_coursemodule_from_id('agendar', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$agendar = $DB->get_record('agendar', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/agendar:manageslots', $context);

$pageurl = new moodle_url('/mod/agendar/manage_slots.php', array('id' => $cm->id));
$viewurl = new moodle_url('/mod/agendar/view.php', array('id' => $cm->id));

$PAGE->set_url($pageurl);
$PAGE->set_title(get_string('manageslots', 'agendar'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Processar ações
if ($action === 'add') {
    $form = new \mod_agendar\form\add_slot_form($pageurl->out(false, array('action' => 'add')));
    
    if ($form->is_cancelled()) {
        redirect($pageurl);
    } else if ($data = $form->get_data()) {
        $slot = new stdClass();
        $slot->agendarid = $agendar->id;
        $slot->starttime = $data->starttime;
        $slot->endtime = $data->endtime;
        $slot->maxbookings = $data->maxbookings;
        $slot->location = $data->location;
        $slot->notes = $data->notes;
        $slot->visible = 1;
        $slot->timecreated = time();
        $slot->timemodified = time();
        
        if ($DB->insert_record('agendar_slots', $slot)) {
            redirect($pageurl, get_string('slotadded', 'agendar'), null, \core\output\notification::NOTIFY_SUCCESS);
        }
    }
    
} else if ($action === 'delete' && $slotid) {
    require_sesskey();
    
    // Verificar se há agendamentos
    $bookings = $DB->count_records('agendar_bookings', array('slotid' => $slotid, 'status' => 'booked'));
    if ($bookings > 0) {
        redirect($pageurl, get_string('slothasbookings', 'agendar'), null, \core\output\notification::NOTIFY_ERROR);
    }
    
    $DB->delete_records('agendar_bookings', array('slotid' => $slotid));
    $DB->delete_records('agendar_slots', array('id' => $slotid));
    
    redirect($pageurl, get_string('slotdeleted', 'agendar'), null, \core\output\notification::NOTIFY_SUCCESS);
    
} else if ($action === 'toggle' && $slotid) {
    require_sesskey();
    
    $slot = $DB->get_record('agendar_slots', array('id' => $slotid));
    if ($slot) {
        $slot->visible = $slot->visible ? 0 : 1;
        $slot->timemodified = time();
        $DB->update_record('agendar_slots', $slot);
    }
    
    redirect($pageurl);
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('manageslots', 'agendar'));

// Breadcrumb
$breadcrumb = array(
    html_writer::link($viewurl, format_string($agendar->name)),
    get_string('manageslots', 'agendar')
);
echo html_writer::div(implode(' / ', $breadcrumb), 'breadcrumb mb-3');

if ($action === 'add') {
    echo $OUTPUT->heading(get_string('addslot', 'agendar'), 3);
    $form->display();
    
} else {
    // Botão para adicionar novo slot
    $addurl = new moodle_url($pageurl, array('action' => 'add'));
    echo html_writer::div(
        $OUTPUT->single_button($addurl, get_string('addslot', 'agendar'), 'get', 
            array('class' => 'btn btn-primary')),
        'text-center mb-3'
    );
    
    // Listar slots existentes
    $slots = $DB->get_records('agendar_slots', array('agendarid' => $agendar->id), 'starttime');
    
    if ($slots) {
        $table = new html_table();
        $table->head = array(
            get_string('datetime', 'agendar'),
            get_string('location', 'agendar'),
            get_string('maxbookings', 'agendar'),
            get_string('currentbookings', 'agendar'),
            get_string('visible', 'agendar'),
            get_string('actions', 'agendar')
        );
        
        foreach ($slots as $slot) {
            $datetime = userdate($slot->starttime) . '<br>' . 
                       userdate($slot->endtime, get_string('strftimetime'));
            
            $location = $slot->location ?: '-';
            
            $currentbookings = $DB->count_records('agendar_bookings', 
                array('slotid' => $slot->id, 'status' => 'booked'));
            
            $bookingsstatus = $currentbookings . '/' . $slot->maxbookings;
            if ($currentbookings >= $slot->maxbookings) {
                $bookingsstatus = html_writer::span($bookingsstatus, 'badge badge-danger');
            } else if ($currentbookings > 0) {
                $bookingsstatus = html_writer::span($bookingsstatus, 'badge badge-warning');
            } else {
                $bookingsstatus = html_writer::span($bookingsstatus, 'badge badge-success');
            }
            
            $visible = $slot->visible ? 
                html_writer::span(get_string('yes'), 'badge badge-success') :
                html_writer::span(get_string('no'), 'badge badge-secondary');
            
            // Ações
            $actions = array();
            
            // Ver agendamentos
            if ($currentbookings > 0) {
                $viewbookingsurl = new moodle_url($pageurl, 
                    array('action' => 'viewbookings', 'slotid' => $slot->id));
                $actions[] = html_writer::link($viewbookingsurl, 
                    get_string('viewbookings', 'agendar'), 
                    array('class' => 'btn btn-sm btn-info'));
            }
            
            // Toggle visibility
            $toggleurl = new moodle_url($pageurl, 
                array('action' => 'toggle', 'slotid' => $slot->id, 'sesskey' => sesskey()));
            $toggletext = $slot->visible ? get_string('hide') : get_string('show');
            $actions[] = html_writer::link($toggleurl, $toggletext, 
                array('class' => 'btn btn-sm btn-secondary'));
            
            // Excluir (apenas se não houver agendamentos)
            if ($currentbookings == 0) {
                $deleteurl = new moodle_url($pageurl, 
                    array('action' => 'delete', 'slotid' => $slot->id, 'sesskey' => sesskey()));
                $actions[] = html_writer::link($deleteurl, get_string('delete'), 
                    array('class' => 'btn btn-sm btn-danger',
                          'onclick' => 'return confirm("' . get_string('confirmdelete', 'agendar') . '")'));
            }
            
            $table->data[] = array(
                $datetime,
                $location,
                $slot->maxbookings,
                $bookingsstatus,
                $visible,
                implode(' ', $actions)
            );
        }
        
        echo html_writer::table($table);
        
    } else {
        echo $OUTPUT->notification(get_string('noslots', 'agendar'), 'info');
    }
    
    // Exibir agendamentos se solicitado
    if ($action === 'viewbookings' && $slotid) {
        $slot = $DB->get_record('agendar_slots', array('id' => $slotid));
        $bookings = agendar_get_slot_bookings($slotid);
        
        if ($slot && $bookings) {
            echo $OUTPUT->heading(get_string('bookingsfor', 'agendar') . ': ' . 
                userdate($slot->starttime), 3);
            
            $table = new html_table();
            $table->head = array(
                get_string('student'),
                get_string('email'),
                get_string('bookingtime', 'agendar'),
                get_string('notes', 'agendar')
            );
            
            foreach ($bookings as $booking) {
                $student = fullname($booking);
                $email = $booking->email;
                $bookingtime = userdate($booking->timecreated);
                $notes = $booking->bookingnotes ?: '-';
                
                $table->data[] = array($student, $email, $bookingtime, $notes);
            }
            
            echo html_writer::table($table);
        }
    }
}

echo $OUTPUT->footer();
