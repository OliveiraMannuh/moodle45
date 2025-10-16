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
 * Quiz scheduler management page.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__ . '/classes/manager.php');

// Course module id.
$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

// Adicionar parâmetros de paginação
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 10, PARAM_INT);

$cm = get_coursemodule_from_id('quizscheduler', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$moduleinstance = $DB->get_record('quizscheduler', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);
require_capability('mod/quizscheduler:manageslots', $modulecontext);

$PAGE->set_url('/mod/quizscheduler/manage.php', array(
    'id' => $cm->id, 
    'page' => $page, 
    'perpage' => $perpage
));
$PAGE->set_title(get_string('manageslots', 'mod_quizscheduler'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

// Handle actions.
if ($action === 'delete') {
    require_sesskey();
    $slotid = required_param('slotid', PARAM_INT);
    
    if ($action == 'delete' && $slotid) {
        $result = quizscheduler_delete_slot($slotid);
        
        if ($result) {
            redirect($PAGE->url, get_string('slotdeleted', 'quizscheduler', ''), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect($PAGE->url, get_string('errordeleting', 'quizscheduler', ''), null, \core\output\notification::NOTIFY_ERROR);
        }
    }
}

// Handle slot generation.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_sesskey();
    
    // Processar campos de data e hora
    $startdate = optional_param('startdate', '', PARAM_TEXT);
    $enddate = optional_param('enddate', '', PARAM_TEXT);
    $starttime = optional_param('starttime', '', PARAM_TEXT);
    $endtime = optional_param('endtime', '', PARAM_TEXT);
    $slot_duration = optional_param('slotduration', 60, PARAM_INT);
    $max_participants = optional_param('maxusers', 1, PARAM_INT);
    $weekdays = optional_param_array('weekdays', [], PARAM_INT);
    
    if ($startdate && $starttime && $enddate && $endtime && !empty($weekdays)) {
        try {
            // Converter strings de data/hora para timestamps
            $start_datetime = $startdate . ' ' . $starttime;
            $end_datetime = $enddate . ' ' . $endtime;
            
            $start_timestamp = strtotime($start_datetime);
            $end_timestamp = strtotime($end_datetime);
            
            if ($start_timestamp === false || $end_timestamp === false) {
                throw new \moodle_exception('error:invalidtimes', 'mod_quizscheduler');
            }
            
            if ($start_timestamp >= $end_timestamp) {
                throw new \moodle_exception('error:invalidtimes', 'mod_quizscheduler');
            }
            
            // Gerar slots baseado nos dias da semana selecionados
            $count = 0;
            $current = $start_timestamp;
            
            while ($current < $end_timestamp) {
                $current_weekday = (int)date('w', $current);
                
                // Verificar se o dia atual está na lista de dias selecionados
                if (in_array($current_weekday, $weekdays)) {
                    // Gerar slots para este dia
                    $day_start = strtotime(date('Y-m-d', $current) . ' ' . $starttime);
                    $day_end = strtotime(date('Y-m-d', $current) . ' ' . $endtime);
                    
                    $slot_start = $day_start;
                    while ($slot_start + ($slot_duration * 60) <= $day_end) {
                        $slot_end = $slot_start + ($slot_duration * 60);
                        
                        // Criar o slot
                        $slot = new stdClass();
                        $slot->quizschedulerid = $moduleinstance->id;
                        $slot->starttime = $slot_start;
                        $slot->endtime = $slot_end;
                        $slot->maxparticipants = $max_participants;
                        $slot->timecreated = time();
                        $slot->timemodified = time();
                        
                        $DB->insert_record('quizscheduler_slots', $slot);
                        $count++;
                        
                        $slot_start = $slot_end;
                    }
                }
                
                // Avançar para o próximo dia
                $current = strtotime('+1 day', $current);
            }
            
            redirect($PAGE->url, get_string('slotsgenerated', 'mod_quizscheduler', $count), null, \core\output\notification::NOTIFY_SUCCESS);
            
        } catch (\Exception $e) {
            \core\notification::add($e->getMessage(), \core\output\notification::NOTIFY_ERROR);
        }
    } else {
        \core\notification::add(get_string('error:missingfields', 'mod_quizscheduler'), \core\output\notification::NOTIFY_ERROR);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageschedules', 'quizscheduler'));

// Adicionar container para o novo formulário
echo html_writer::start_div('schedule-manager-container');

// Formulário de geração de horários aprimorado
echo html_writer::start_div('schedule-generator-form card mb-4');
echo html_writer::tag('h3', get_string('generateschedules', 'quizscheduler'), ['class' => 'card-header']);
echo html_writer::start_div('card-body');

echo html_writer::start_tag('form', [
    'method' => 'post',
    'id' => 'schedule-form',
    'class' => 'schedule-generator'
]);

// ADICIONAR SESSKEY - IMPORTANTE!
echo html_writer::empty_tag('input', [
    'type' => 'hidden',
    'name' => 'sesskey',
    'value' => sesskey()
]);

// Seção de Cronometragem (Período)
echo html_writer::start_div('form-section mb-4');
echo html_writer::tag('h4', get_string('scheduleperiod', 'quizscheduler'), ['class' => 'mb-3']);

echo html_writer::start_div('row');

// Data de início
echo html_writer::start_div('col-md-6 mb-3');
echo html_writer::tag('label', get_string('startdate', 'quizscheduler'), [
    'for' => 'start-date',
    'class' => 'form-label'
]);
echo html_writer::empty_tag('input', [
    'type' => 'date',
    'id' => 'start-date',
    'name' => 'startdate',
    'class' => 'form-control',
    'required' => 'required',
    'min' => date('Y-m-d')
]);
echo html_writer::end_div();

// Data de término
echo html_writer::start_div('col-md-6 mb-3');
echo html_writer::tag('label', get_string('enddate', 'quizscheduler'), [
    'for' => 'end-date',
    'class' => 'form-label'
]);
echo html_writer::empty_tag('input', [
    'type' => 'date',
    'id' => 'end-date',
    'name' => 'enddate',
    'class' => 'form-control',
    'required' => 'required',
    'min' => date('Y-m-d')
]);
echo html_writer::end_div();

echo html_writer::end_div(); // row

// Seleção de dias da semana
echo html_writer::start_div('weekdays-selector mb-3');
echo html_writer::tag('label', get_string('selectweekdays', 'quizscheduler'), ['class' => 'form-label d-block mb-2']);

$weekdays = [
    1 => get_string('monday', 'calendar'),
    2 => get_string('tuesday', 'calendar'),
    3 => get_string('wednesday', 'calendar'),
    4 => get_string('thursday', 'calendar'),
    5 => get_string('friday', 'calendar'),
    6 => get_string('saturday', 'calendar'),
    0 => get_string('sunday', 'calendar')
];

echo html_writer::start_div('weekday-buttons-container d-flex flex-wrap gap-2');
foreach ($weekdays as $day => $name) {
    echo html_writer::start_div('form-check form-check-inline');
    echo html_writer::empty_tag('input', [
        'type' => 'checkbox',
        'class' => 'btn-check weekday-checkbox',
        'id' => 'weekday-' . $day,
        'name' => 'weekdays[]',
        'value' => $day,
        'autocomplete' => 'off'
    ]);
    echo html_writer::tag('label', $name, [
        'class' => 'btn btn-outline-primary btn-weekday',
        'for' => 'weekday-' . $day
    ]);
    echo html_writer::end_div();
}
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // form-section

// Seção de Horários
echo html_writer::start_div('form-section mb-4');
echo html_writer::tag('h4', get_string('timesettings', 'quizscheduler'), ['class' => 'mb-3']);

// Container visual para os campos de horário
echo html_writer::start_div('time-inputs-container');
echo html_writer::start_div('time-inputs-header');
echo html_writer::tag('h5', 'Defina o período de funcionamento');
echo html_writer::end_div();

echo html_writer::start_div('row');

// Horário de início
echo html_writer::start_div('col-md-3 mb-3');
echo html_writer::tag('label', get_string('starthour', 'quizscheduler'), [
    'for' => 'start-time',
    'class' => 'form-label'
]);
echo html_writer::empty_tag('input', [
    'type' => 'time',
    'id' => 'start-time',
    'name' => 'starttime',
    'class' => 'form-control',
    'required' => 'required',
    'step' => '300' // 5 minutos
]);
echo html_writer::end_div();

// Horário de término
echo html_writer::start_div('col-md-3 mb-3');
echo html_writer::tag('label', get_string('endhour', 'quizscheduler'), [
    'for' => 'end-time',
    'class' => 'form-label'
]);
echo html_writer::empty_tag('input', [
    'type' => 'time',
    'id' => 'end-time',
    'name' => 'endtime',
    'class' => 'form-control',
    'required' => 'required',
    'step' => '300' // 5 minutos
]);
echo html_writer::end_div();

// Duração do slot
echo html_writer::start_div('col-md-3 mb-3');
echo html_writer::tag('label', get_string('slotduration', 'quizscheduler'), [
    'for' => 'slot-duration',
    'class' => 'form-label'
]);
echo html_writer::empty_tag('input', [
    'type' => 'number',
    'id' => 'slot-duration',
    'name' => 'slotduration',
    'class' => 'form-control',
    'min' => '5',
    'step' => '5',
    'value' => $moduleinstance->slotduration ?? '60',
    'required' => 'required'
]);
echo html_writer::tag('small', get_string('minutes'), ['class' => 'form-text text-muted']);
echo html_writer::end_div();

// Máximo de usuários por slot
echo html_writer::start_div('col-md-3 mb-3');
echo html_writer::tag('label', get_string('maxusers', 'quizscheduler'), [
    'for' => 'max-users',
    'class' => 'form-label'
]);
echo html_writer::empty_tag('input', [
    'type' => 'number',
    'id' => 'max-users',
    'name' => 'maxusers',
    'class' => 'form-control',
    'min' => '1',
    'value' => '1',
    'required' => 'required'
]);
echo html_writer::end_div();

echo html_writer::end_div(); // row
echo html_writer::end_div(); // time-inputs-container

echo html_writer::end_div(); // form-section

// Preview de slots
echo html_writer::start_div('form-section mb-4');
echo html_writer::tag('h4', get_string('schedulepreview', 'quizscheduler'), ['class' => 'mb-3']);
echo html_writer::div('Preencha os campos acima e clique em "Visualizar Horários" para ver uma prévia.', 'alert alert-info', ['id' => 'schedule-preview']);
echo html_writer::end_div();

// Botões
echo html_writer::start_div('form-actions d-flex gap-2');
echo html_writer::tag('button', get_string('previewschedules', 'quizscheduler'), [
    'type' => 'button',
    'id' => 'preview-button',
    'class' => 'btn btn-secondary'
]);
echo html_writer::tag('button', get_string('generateschedules', 'quizscheduler'), [
    'type' => 'submit',
    'class' => 'btn btn-primary'
]);
echo html_writer::end_div();

echo html_writer::end_tag('form');

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

echo html_writer::end_div(); // container

// Carregar o JavaScript
$PAGE->requires->js_call_amd('mod_quizscheduler/schedule_manager', 'init');

// Current slots - COM PAGINAÇÃO
echo html_writer::tag('h4', get_string('currentslots', 'mod_quizscheduler'));

// Obter todos os slots ORDENADOS por data mais recente primeiro
$allslots = $DB->get_records('quizscheduler_slots', 
    array('quizschedulerid' => $moduleinstance->id), 
    'starttime DESC'  // Ordenar do mais recente para o mais antigo
);

// Adicionar contagem de agendamentos ativos para cada slot
foreach ($allslots as $slot) {
    $slot->active_bookings = $DB->count_records('quizscheduler_bookings', array(
        'slotid' => $slot->id
    ));
}

$totalslots = count($allslots);

// Aplicar paginação
$offset = $page * $perpage;
if ($perpage > 0) {
    $slots = array_slice($allslots, $offset, $perpage);
} else {
    $slots = $allslots; // Mostrar todos
}

if (empty($allslots)) {
    echo $OUTPUT->box(get_string('noslots', 'mod_quizscheduler'), 'generalbox');
} else {
    // Controles de paginação ANTES da tabela
    echo html_writer::start_div('scheduling-controls-wrapper mb-3');
    
    // Controle de itens por página
    echo html_writer::start_div('items-per-page-control');
    echo html_writer::start_tag('form', array(
        'method' => 'get',
        'action' => $PAGE->url->out_omit_querystring(),
        'id' => 'perpage-form',
        'class' => 'd-inline'
    ));
    echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $cm->id));
    
    echo html_writer::tag('label', get_string('show', 'quizscheduler') . ': ', array('for' => 'perpage-select'));
    
    $perpageoptions = array(
        5 => '5',
        10 => '10',
        20 => '20',
        50 => '50',
        100 => '100',
        0 => get_string('all', 'quizscheduler')
    );
    
    echo html_writer::select($perpageoptions, 'perpage', $perpage, false, array(
        'id' => 'perpage-select',
        'class' => 'custom-select',
        'onchange' => 'this.form.submit()'
    ));
    
    echo ' ' . get_string('slotsperpage', 'quizscheduler');
    echo html_writer::end_tag('form');
    echo html_writer::end_div();
    
    // Informação de registros
    if ($totalslots > 0) {
        $start = $offset + 1;
        $end = min($offset + $perpage, $totalslots);
        if ($perpage == 0) {
            $end = $totalslots;
            $start = 1;
        }
        echo html_writer::start_div('slots-info');
        echo get_string('showingslots', 'quizscheduler', array(
            'start' => $start,
            'end' => $end,
            'total' => $totalslots
        ));
        echo html_writer::end_div();
    }
    
    echo html_writer::end_div();
    
    // Tabela de horários
    echo html_writer::start_tag('div', array('class' => 'table-responsive'));
    echo html_writer::start_tag('table', array('class' => 'table table-striped'));
    
    echo html_writer::start_tag('thead');
    echo html_writer::start_tag('tr');
    echo html_writer::tag('th', get_string('starttime', 'mod_quizscheduler'));
    echo html_writer::tag('th', get_string('endtime', 'mod_quizscheduler'));
    echo html_writer::tag('th', get_string('maxusers', 'mod_quizscheduler'));
    echo html_writer::tag('th', get_string('bookings', 'mod_quizscheduler'));
    echo html_writer::tag('th', get_string('actions', 'mod_quizscheduler'));
    echo html_writer::end_tag('tr');
    echo html_writer::end_tag('thead');
    
    echo html_writer::start_tag('tbody');
    foreach ($slots as $slot) {
        echo html_writer::start_tag('tr');
        echo html_writer::tag('td', userdate($slot->starttime, get_string('strftimedaydatetime')));
        echo html_writer::tag('td', userdate($slot->endtime, get_string('strftimetime')));
        echo html_writer::tag('td', $slot->maxparticipants);
        echo html_writer::tag('td', ($slot->active_bookings ?? 0) . '/' . $slot->maxparticipants);
        
        // Actions
        $actions = '';
        if (($slot->active_bookings ?? 0) == 0) {
            $delete_url = new moodle_url($PAGE->url, array(
                'action' => 'delete',
                'slotid' => $slot->id,
                'sesskey' => sesskey()
            ));
            $actions = html_writer::link($delete_url, 
                get_string('delete'), 
                array(
                    'class' => 'btn btn-sm btn-danger',
                    'onclick' => 'return confirm("' . get_string('confirmdeleteSlot', 'mod_quizscheduler') . '")'
                )
            );
        }
        
        echo html_writer::tag('td', $actions);
        echo html_writer::end_tag('tr');
    }
    echo html_writer::end_tag('tbody');
    
    echo html_writer::end_tag('table');
    echo html_writer::end_tag('div');
    
    // Paginação APÓS a tabela
    if ($perpage > 0 && $totalslots > $perpage) {
        $baseurl = new moodle_url('/mod/quizscheduler/manage.php', array(
            'id' => $cm->id,
            'perpage' => $perpage
        ));
        echo $OUTPUT->paging_bar($totalslots, $page, $perpage, $baseurl);
    }
}

echo $OUTPUT->footer();
