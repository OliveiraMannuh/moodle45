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

// manageslots.php - Versão Simplificada
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = required_param('id', PARAM_INT); // Course module ID.
$action = optional_param('action', 'list', PARAM_ALPHA);
$slotid = optional_param('slotid', 0, PARAM_INT);

$cm = get_coursemodule_from_id('agendamento', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$agendamento = $DB->get_record('agendamento', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/agendamento:manageslots', $context);

$PAGE->set_url('/mod/agendamento/manageslots.php', array('id' => $cm->id));
$PAGE->set_title(format_string($agendamento->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Handle form submission for adding/editing slots
if ($action === 'save') {
    require_sesskey();
    
    $starttime = required_param('starttime', PARAM_INT);
    $endtime = required_param('endtime', PARAM_INT);
    $maxparticipants = required_param('maxparticipants', PARAM_INT);
    $description = optional_param('description', '', PARAM_TEXT);
    
    // Validation
    $errors = array();
    if ($endtime <= $starttime) {
        $errors[] = get_string('endtimebeforestart', 'agendamento');
    }
    if ($maxparticipants < 1) {
        $errors[] = get_string('invalidmaxparticipants', 'agendamento');
    }
    
    if (empty($errors)) {
        $slot = new stdClass();
        $slot->agendamento = $agendamento->id;
        $slot->starttime = $starttime;
        $slot->endtime = $endtime;
        $slot->maxparticipants = $maxparticipants;
        $slot->description = $description;
        $slot->timecreated = time();
        
        if ($slotid) {
            $slot->id = $slotid;
            $DB->update_record('agendamento_slots', $slot);
        } else {
            $DB->insert_record('agendamento_slots', $slot);
        }
        
        redirect(new moodle_url('/mod/agendamento/manageslots.php', array('id' => $cm->id)),
                 get_string('slotsaved', 'agendamento'));
    }
}

// Handle delete action
if ($action === 'delete' && $slotid) {
    require_sesskey();
    
    // Delete bookings first.
    $DB->delete_records('agendamento_bookings', array('slotid' => $slotid));
    // Delete slot.
    $DB->delete_records('agendamento_slots', array('id' => $slotid));
    
    redirect(new moodle_url('/mod/agendamento/manageslots.php', array('id' => $cm->id)),
             get_string('slotdeleted', 'agendamento'));
}

echo $OUTPUT->header();

// Show add/edit form
if ($action === 'add' || $action === 'edit') {
    $slot = null;
    if ($slotid && $action === 'edit') {
        $slot = $DB->get_record('agendamento_slots', array('id' => $slotid));
    }
    
    $title = $slot ? get_string('editslot', 'agendamento') : get_string('addslot', 'agendamento');
    echo $OUTPUT->heading($title);
    
    // Display validation errors if any
    if (isset($errors) && !empty($errors)) {
        foreach ($errors as $error) {
            echo $OUTPUT->notification($error, 'notifyproblem');
        }
    }
    
    // Simple HTML form
    echo '<form method="post" action="' . new moodle_url('/mod/agendamento/manageslots.php') . '">';
    echo '<input type="hidden" name="id" value="' . $cm->id . '">';
    echo '<input type="hidden" name="action" value="save">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
    if ($slot) {
        echo '<input type="hidden" name="slotid" value="' . $slot->id . '">';
    }
    
    echo '<div class="form-group">';
    echo '<label for="starttime">' . get_string('starttime', 'agendamento') . '</label>';
    $starttime = $slot ? $slot->starttime : time();
    echo '<input type="datetime-local" class="form-control" name="starttime_local" id="starttime" required onchange="updateStartTime()">';
    echo '<input type="hidden" name="starttime" id="starttime_hidden" value="' . $starttime . '">';
    echo '</div>';
    
    echo '<div class="form-group">';
    echo '<label for="endtime">' . get_string('endtime', 'agendamento') . '</label>';
    $endtime = $slot ? $slot->endtime : (time() + 3600);
    echo '<input type="datetime-local" class="form-control" name="endtime_local" id="endtime" required onchange="updateEndTime()">';
    echo '<input type="hidden" name="endtime" id="endtime_hidden" value="' . $endtime . '">';
    echo '</div>';
    
    echo '<div class="form-group">';
    echo '<label for="maxparticipants">' . get_string('maxparticipants', 'agendamento') . '</label>';
    $maxparticipants = $slot ? $slot->maxparticipants : 1;
    echo '<input type="number" class="form-control" name="maxparticipants" id="maxparticipants" value="' . $maxparticipants . '" min="1" required>';
    echo '</div>';
    
    echo '<div class="form-group">';
    echo '<label for="description">' . get_string('description', 'agendamento') . '</label>';
    $description = $slot ? $slot->description : '';
    echo '<textarea class="form-control" name="description" id="description" rows="3">' . htmlspecialchars($description) . '</textarea>';
    echo '</div>';
    
    echo '<div class="form-group">';
    echo '<input type="submit" class="btn btn-primary" value="' . get_string('save') . '">';
    echo ' <a href="' . new moodle_url('/mod/agendamento/manageslots.php', array('id' => $cm->id)) . '" class="btn btn-secondary">' . get_string('cancel') . '</a>';
    echo '</div>';
    
    echo '</form>';
    
    // JavaScript to handle datetime conversion
    echo '<script>
    function formatDateForInput(timestamp) {
        var date = new Date(timestamp * 1000);
        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, "0");
        var day = String(date.getDate()).padStart(2, "0");
        var hours = String(date.getHours()).padStart(2, "0");
        var minutes = String(date.getMinutes()).padStart(2, "0");
        return year + "-" + month + "-" + day + "T" + hours + ":" + minutes;
    }
    
    function updateStartTime() {
        var input = document.getElementById("starttime");
        var hidden = document.getElementById("starttime_hidden");
        var date = new Date(input.value);
        hidden.value = Math.floor(date.getTime() / 1000);
    }
    
    function updateEndTime() {
        var input = document.getElementById("endtime");
        var hidden = document.getElementById("endtime_hidden");
        var date = new Date(input.value);
        hidden.value = Math.floor(date.getTime() / 1000);
    }
    
    // Initialize datetime inputs
    document.getElementById("starttime").value = formatDateForInput(' . $starttime . ');
    document.getElementById("endtime").value = formatDateForInput(' . $endtime . ');
    </script>';
    
} else {
    // Default action: list slots.
    echo $OUTPUT->heading(get_string('manageslots', 'agendamento'));
    
    echo '<p><a href="' . new moodle_url('/mod/agendamento/manageslots.php', array('id' => $cm->id, 'action' => 'add')) . 
         '" class="btn btn-primary">' . get_string('addslot', 'agendamento') . '</a></p>';
    
    $slots = $DB->get_records('agendamento_slots', array('agendamento' => $agendamento->id), 'starttime ASC');
    
    if (empty($slots)) {
        echo '<p>' . get_string('noslots', 'agendamento') . '</p>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>' . get_string('starttime', 'agendamento') . '</th>';
        echo '<th>' . get_string('endtime', 'agendamento') . '</th>';
        echo '<th>' . get_string('maxparticipants', 'agendamento') . '</th>';
        echo '<th>' . get_string('participants', 'agendamento') . '</th>';
        echo '<th>' . get_string('description', 'agendamento') . '</th>';
        echo '<th>' . get_string('actions') . '</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($slots as $slot) {
            $bookingcount = $DB->count_records('agendamento_bookings', array('slotid' => $slot->id));
            
            echo '<tr>';
            echo '<td>' . userdate($slot->starttime, get_string('strftimedatetimeshort')) . '</td>';
            echo '<td>' . userdate($slot->endtime, get_string('strftimedatetimeshort')) . '</td>';
            echo '<td>' . $slot->maxparticipants . '</td>';
            echo '<td>' . $bookingcount . '</td>';
            echo '<td>' . format_text($slot->description, FORMAT_PLAIN, array('para' => false)) . '</td>';
            echo '<td>';
            echo '<a href="' . new moodle_url('/mod/agendamento/manageslots.php', 
                    array('id' => $cm->id, 'action' => 'edit', 'slotid' => $slot->id)) . 
                 '" class="btn btn-sm btn-secondary">' . get_string('edit') . '</a> ';
            echo '<a href="' . new moodle_url('/mod/agendamento/manageslots.php', 
                    array('id' => $cm->id, 'action' => 'delete', 'slotid' => $slot->id, 'sesskey' => sesskey())) . 
                 '" class="btn btn-sm btn-danger" onclick="return confirm(\'' . 
                 get_string('confirmdelete', 'agendamento') . '\')">' . get_string('delete') . '</a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
    
    echo '<p><a href="' . new moodle_url('/mod/agendamento/view.php', array('id' => $cm->id)) . 
         '" class="btn btn-secondary">' . get_string('back') . '</a></p>';
}

echo $OUTPUT->footer();
