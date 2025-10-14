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

if (!has_capability('mod/quizscheduler:view', $modulecontext)) {
    throw new moodle_exception('nopermissions', 'error', '', 'view');
}

// Parâmetros de paginação
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);

$PAGE->set_url('/mod/quizscheduler/view.php', array('id' => $cm->id, 'page' => $page, 'perpage' => $perpage));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

// CORREÇÃO CRÍTICA: Adicionar JavaScript inline ANTES do header
echo '<script src="' . new moodle_url('/mod/quizscheduler/remove_duplicates.js') . '"></script>';

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

// CORREÇÃO: Obter slots disponíveis diretamente do banco de dados
$allslots = $DB->get_records('quizscheduler_slots', 
    array('quizschedulerid' => $moduleinstance->id), 
    'starttime DESC'
);

// Contar total de slots
$totalslots = count($allslots);

// Aplicar paginação
$offset = $page * $perpage;
if ($perpage > 0) {
    $slots = array_slice($allslots, $offset, $perpage);
} else {
    $slots = $allslots; // Mostrar todos
}

if (!empty($slots)) {
    echo $OUTPUT->heading(get_string('availableslots', 'quizscheduler'), 4);
    
    // Controles de paginação ANTES da tabela
    echo '<div class="scheduling-controls-wrapper">';
    
    // Controle de itens por página
    echo '<div class="items-per-page-control">';
    echo '<form method="get" action="' . $PAGE->url->out_omit_querystring() . '" id="perpage-form">';
    echo '<input type="hidden" name="id" value="' . $cm->id . '">';
    echo '<label for="perpage-select">Mostrar: </label>';

    $perpageoptions = [
        5 => '5',
        10 => '10',
        20 => '20',
        50 => '50',
        100 => '100',
        0 => 'Todos'
    ];

    echo html_writer::select($perpageoptions, 'perpage', $perpage, false, [
        'id' => 'perpage-select',
        'class' => 'custom-select',
        'onchange' => 'this.form.submit()'
    ]);

    echo ' horários por página';
    echo '</form>';
    echo '</div>';

    // Informação de registros
    if ($totalslots > 0) {
        $start = $offset + 1;
        $end = min($offset + $perpage, $totalslots);
        if ($perpage == 0) {
            $end = $totalslots;
            $start = 1;
        }
        echo '<div class="slots-info">';
        echo "Mostrando {$start} a {$end} de {$totalslots} horários";
        echo '</div>';
    }

    echo '</div>'; // fim scheduling-controls-wrapper
    
    // Tabela de horários
    $table = new html_table();
    $table->attributes['class'] = 'timeslots-table generaltable';
    $table->head = array(
        get_string('datetime', 'quizscheduler'),
        get_string('availability', 'quizscheduler'),
        get_string('actions', 'quizscheduler')
    );
    
    foreach ($slots as $slot) {
        $row = new html_table_row();
        
        // Data e hora
        $datetime = userdate($slot->starttime, '%A, %d %b %Y, %H:%M') . 
                   ' - ' . userdate($slot->endtime, '%H:%M') . ' PM';
        $row->cells[] = $datetime;
        
        // Disponibilidade
        $bookingCount = $DB->count_records('quizscheduler_bookings', array('slotid' => $slot->id));
        $maxUsers = isset($slot->maxusers) ? $slot->maxusers : (isset($slot->maxparticipants) ? $slot->maxparticipants : 1);
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
                $actioncell = html_writer::span('Agendado (Ativo)', 'badge badge-success');
            } else {
                $actioncell = html_writer::span('Concluído', 'badge badge-secondary');
            }
        } else {
            // Usuário não tem este slot
            if ($hasActiveBooking) {
                // Tem agendamento ativo em outro slot
                $actioncell = html_writer::span('Você possui um agendamento ativo', 'badge badge-warning');
            } else if ($slot->starttime <= $currentTime) {
                // Slot já passou
                $actioncell = html_writer::span('Horário passou', 'badge badge-secondary');
            } else if ($bookingCount >= $maxUsers) {
                // Slot lotado
                $actioncell = html_writer::span('Lotado', 'badge badge-danger');
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
    
    // Paginação APÓS a tabela
    if ($perpage > 0 && $totalslots > $perpage) {
        $baseurl = new moodle_url('/mod/quizscheduler/view.php', [
            'id' => $cm->id,
            'perpage' => $perpage
        ]);
        echo $OUTPUT->paging_bar($totalslots, $page, $perpage, $baseurl);
    }
    
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

echo $OUTPUT->footer();
