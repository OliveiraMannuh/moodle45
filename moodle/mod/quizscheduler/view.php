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
 * Prints an instance of mod_quizscheduler.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

// Activity instance id.
$q = optional_param('q', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('quizscheduler', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('quizscheduler', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $moduleinstance = $DB->get_record('quizscheduler', array('id' => $q), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('quizscheduler', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

// CORREÇÃO: Verificar se usuário pode visualizar o módulo
if (!has_capability('mod/quizscheduler:view', $modulecontext)) {
    throw new \moodle_exception('nopermissions', 'mod_quizscheduler');
}

$PAGE->set_url('/mod/quizscheduler/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

echo $OUTPUT->header();

// Display admin section for teachers.
if (has_capability('mod/quizscheduler:manageslots', $modulecontext)) {
    echo $OUTPUT->box_start('generalbox boxaligncenter');
    echo $OUTPUT->heading(get_string('administration', 'mod_quizscheduler'), 3);
    
    echo html_writer::start_div('btn-group mb-3');
    echo html_writer::link(
        new moodle_url('/mod/quizscheduler/manage.php', array('id' => $cm->id)),
        get_string('manageslots', 'mod_quizscheduler'),
        array('class' => 'btn btn-primary')
    );
    
    // Adicionar link para relatórios
    if (has_capability('mod/quizscheduler:viewreports', $modulecontext)) {
        echo html_writer::link(
            new moodle_url('/mod/quizscheduler/reports.php', array('id' => $cm->id)),
            get_string('viewreports', 'mod_quizscheduler'),
            array('class' => 'btn btn-info ml-2')
        );
    }
    
    echo html_writer::end_div();
    echo $OUTPUT->box_end();
}

// Display scheduler information.
echo $OUTPUT->heading(get_string('schedulinginfo', 'mod_quizscheduler'), 3);

// Get schedule period
$schedule_start = isset($moduleinstance->schedulestarttime) ? $moduleinstance->schedulestarttime : null;
$schedule_end = isset($moduleinstance->scheduleendtime) ? $moduleinstance->scheduleendtime : null;

if ($schedule_start && $schedule_end) {
    echo html_writer::tag('p', 
        get_string('scheduleperiod', 'mod_quizscheduler') . ': ' . 
        userdate($schedule_start, get_string('strftimedaydatetime')) . ' ' . 
        get_string('until', 'mod_quizscheduler') . ' ' . 
        userdate($schedule_end, get_string('strftimedaydatetime'))
    );
}

// CORREÇÃO PRINCIPAL: Usar as funções do lib.php diretamente
$currentTime = time();

// Obter agendamentos do usuário usando as funções corretas
$allUserBookings = quizscheduler_get_user_bookings($USER->id, $moduleinstance->id, false);
$activeUserBookings = quizscheduler_get_user_bookings($USER->id, $moduleinstance->id, true);
$hasActiveBooking = quizscheduler_user_has_active_booking($USER->id, $moduleinstance->id);

// Obter todos os slots disponíveis
$availableSlots = $DB->get_records('quizscheduler_slots', 
    array('quizschedulerid' => $moduleinstance->id), 
    'starttime ASC'
);

// Exibir agendamentos do usuário
if (!empty($allUserBookings)) {
    echo $OUTPUT->heading(get_string('yourbookings', 'quizscheduler'), 4);
    
    $table = new html_table();
    $table->head = array(
        get_string('datetime', 'quizscheduler'),
        get_string('status', 'quizscheduler'),
        get_string('actions', 'quizscheduler')
    );
    
    foreach ($allUserBookings as $booking) {
        $row = new html_table_row();
        
        // Data e hora
        $datetime = userdate($booking->starttime, '%A, %d %b %Y, %H:%M') . 
                   ' - ' . userdate($booking->endtime, '%H:%M') . ' PM';
        $row->cells[] = $datetime;
        
        // Status
        if ($booking->endtime > $currentTime) {
            $status = html_writer::span('Ativo', 'badge badge-success');
        } else {
            $status = html_writer::span('Concluído', 'badge badge-secondary');
        }
        $row->cells[] = $status;
        
        // Ações
        $actions = '';
        if ($booking->endtime > $currentTime) {
            $cancelurl = new moodle_url('/mod/quizscheduler/book.php', array(
                'id' => $cm->id,
                'slotid' => $booking->slotid,
                'action' => 'cancel'
            ));
            $actions = html_writer::link($cancelurl, 'Cancelar', 
                array('class' => 'btn btn-sm btn-danger'));
        }
        $row->cells[] = $actions;
        
        $table->data[] = $row;
    }
    
    echo html_writer::table($table);
}

// Exibir horários disponíveis
if (!empty($availableSlots)) {
    echo $OUTPUT->heading(get_string('availableslots', 'quizscheduler'), 4);
    
    $table = new html_table();
    $table->head = array(
        get_string('datetime', 'quizscheduler'),
        get_string('availability', 'quizscheduler'),
        get_string('actions', 'quizscheduler')
    );

    // Ordenar slots por data/hora (mais recente primeiro)
    usort($availableSlots, function($a, $b) {
        return $b->starttime - $a->starttime;
    });
    
    foreach ($availableSlots as $slot) {
        $row = new html_table_row();
        
        // Data e hora
        $datetime = userdate($slot->starttime, '%A, %d %b %Y, %H:%M') . 
                   ' - ' . userdate($slot->endtime, '%H:%M') . ' PM';
        $row->cells[] = $datetime;
        
        // Disponibilidade
        $bookingCount = $DB->count_records('quizscheduler_bookings', array('slotid' => $slot->id));
        $maxUsers = isset($slot->maxusers) ? $slot->maxusers : $slot->maxparticipants;
        $availability = $bookingCount . '/' . $maxUsers . ' reservado(s)';
        $row->cells[] = $availability;
        
        // Ações - LÓGICA CORRIGIDA
        $actioncell = '';
        
        // Verificar se usuário já tem este slot específico
        $userHasThisSlot = false;
        foreach ($allUserBookings as $booking) {
            if ($booking->slotid == $slot->id) {
                $userHasThisSlot = true;
                break;
            }
        }
        
        if ($userHasThisSlot) {
            // Usuário já tem este slot
            if ($slot->endtime > $currentTime) {
                $actioncell = 'Agendado (Ativo)';
            } else {
                $actioncell = 'Concluído';
            }
        } else {
            // Usuário não tem este slot
            if ($hasActiveBooking) {
                // Tem agendamento ativo em outro slot
                $actioncell = 'Você possui um agendamento ativo';
            } else if ($slot->starttime <= $currentTime) {
                // Slot já passou
                $actioncell = 'Horário passou';
            } else if ($bookingCount >= $maxUsers) {
                // Slot lotado
                $actioncell = 'Lotado';
            } else {
                // Pode agendar - BOTÃO DE AÇÃO
                $bookurl = new moodle_url('/mod/quizscheduler/book.php', array(
                    'id' => $cm->id,
                    'slotid' => $slot->id,
                    'action' => 'book'
                ));
                $actioncell = html_writer::link($bookurl, 'Agendar', 
                    array('class' => 'btn btn-primary btn-sm'));
            }
        }
        
        $row->cells[] = $actioncell;
        $table->data[] = $row;
    }
    
    echo html_writer::table($table);
} else {
    echo $OUTPUT->box(
        'Nenhum horário disponível no momento.',
        'generalbox boxaligncenter alert alert-info'
    );
}

// Mostrar estatísticas
$totalBookings = count($allUserBookings);
$maxBookings = isset($moduleinstance->maxbookings) ? $moduleinstance->maxbookings : 1;

echo html_writer::tag('p', 
    'Agendamentos: ' . $totalBookings . ' de ' . $maxBookings . ' utilizados'
);

// Procurar pela seção onde o POST do booking é processado
if ($action == 'bookslot') {
    // ...existing booking logic...
    
    // Após a linha onde o booking é salvo (geralmente após um $DB->insert_record)
    // Adicionar imediatamente:
    
    // Enviar email de confirmação
    $slot = $DB->get_record('quizscheduler_slots', array('id' => $slotid));
    $student = $USER;
    $course = $DB->get_record('course', array('id' => $cm->course));
    
    // Preparar email
    $subject = 'Agendamento confirmado: ' . $scheduler->name;
    $message = "Olá " . fullname($student) . ",\n\n";
    $message .= "Seu agendamento foi confirmado:\n\n";
    $message .= "Quiz: " . $scheduler->name . "\n";
    $message .= "Data: " . userdate($slot->starttime, '%d/%m/%Y') . "\n";
    $message .= "Horário: " . userdate($slot->starttime, '%H:%M') . " - " . userdate($slot->endtime, '%H:%M') . "\n";
    $message .= "Curso: " . $course->fullname . "\n\n";
    $message .= "Compareça no horário agendado.\n\n";
    $message .= "Atenciosamente";
    
    // Enviar email
    email_to_user($student, core_user::get_noreply_user(), $subject, $message);
}

echo $OUTPUT->footer();
