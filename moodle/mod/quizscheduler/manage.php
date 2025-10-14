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
    
    // CORREÇÃO: Processar campos de data e hora corretamente
    $generate_startdate = optional_param('generate_startdate', '', PARAM_TEXT);
    $generate_starttime = optional_param('generate_starttime', '', PARAM_TEXT);
    $generate_enddate = optional_param('generate_enddate', '', PARAM_TEXT);
    $generate_endtime = optional_param('generate_endtime', '', PARAM_TEXT);
    $slot_duration = optional_param('slot_duration', 60, PARAM_INT);
    $max_participants = optional_param('max_participants', 1, PARAM_INT);
    
    if ($generate_startdate && $generate_starttime && $generate_enddate && $generate_endtime) {
        try {
            // Converter strings de data/hora para timestamps
            $start_datetime = $generate_startdate . ' ' . $generate_starttime;
            $end_datetime = $generate_enddate . ' ' . $generate_endtime;
            
            $start_timestamp = strtotime($start_datetime);
            $end_timestamp = strtotime($end_datetime);
            
            if ($start_timestamp === false || $end_timestamp === false) {
                throw new \moodle_exception('error:invalidtimes', 'mod_quizscheduler');
            }
            
            if ($start_timestamp >= $end_timestamp) {
                throw new \moodle_exception('error:invalidtimes', 'mod_quizscheduler');
            }
            
            $count = mod_quizscheduler\manager::generate_slots(
                $moduleinstance->id,
                $start_timestamp,
                $end_timestamp,
                $slot_duration,
                $max_participants
            );
            
            redirect($PAGE->url, get_string('slotsgenerated', 'mod_quizscheduler', $count), null, \core\output\notification::NOTIFY_SUCCESS);
            
        } catch (\Exception $e) {
            \core\notification::add($e->getMessage(), \core\output\notification::NOTIFY_ERROR);
        }
    } else {
        \core\notification::add(get_string('error:missingfields', 'mod_quizscheduler'), \core\output\notification::NOTIFY_ERROR);
    }
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('manageslots', 'mod_quizscheduler'));

// Navigation.
$viewurl = new moodle_url('/mod/quizscheduler/view.php', array('id' => $cm->id));
echo html_writer::div(
    html_writer::link($viewurl, get_string('back'), array('class' => 'btn btn-secondary')),
    'mb-3'
);

// Slot generation form.
echo $OUTPUT->box_start('generalbox');
echo html_writer::tag('h4', get_string('generateslots', 'mod_quizscheduler'));

echo html_writer::start_tag('form', array('method' => 'post', 'action' => $PAGE->url));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));

echo html_writer::start_div('row');

// Start date/time
echo html_writer::start_div('col-md-6');
echo html_writer::tag('label', get_string('starttime', 'mod_quizscheduler'));
echo html_writer::empty_tag('input', array(
    'type' => 'date',
    'name' => 'generate_startdate',
    'class' => 'form-control mb-2',
    'required' => 'required'
));
echo html_writer::empty_tag('input', array(
    'type' => 'time',
    'name' => 'generate_starttime',
    'class' => 'form-control mb-2',
    'required' => 'required'
));
echo html_writer::end_div();

// End date/time
echo html_writer::start_div('col-md-6');
echo html_writer::tag('label', get_string('endtime', 'mod_quizscheduler'));
echo html_writer::empty_tag('input', array(
    'type' => 'date',
    'name' => 'generate_enddate',
    'class' => 'form-control mb-2',
    'required' => 'required'
));
echo html_writer::empty_tag('input', array(
    'type' => 'time',
    'name' => 'generate_endtime',
    'class' => 'form-control mb-2',
    'required' => 'required'
));
echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::start_div('row');

// Duration and participants
echo html_writer::start_div('col-md-6');
echo html_writer::tag('label', get_string('slotduration', 'mod_quizscheduler'));
echo html_writer::empty_tag('input', array(
    'type' => 'number',
    'name' => 'slot_duration',
    'value' => '60',
    'min' => '5',
    'max' => '300',
    'class' => 'form-control mb-2',
    'required' => 'required'
));
echo html_writer::end_div();

echo html_writer::start_div('col-md-6');
echo html_writer::tag('label', get_string('maxusersperslot', 'mod_quizscheduler'));
echo html_writer::empty_tag('input', array(
    'type' => 'number',
    'name' => 'max_participants',
    'value' => '1',
    'min' => '1',
    'max' => '50',
    'class' => 'form-control mb-2',
    'required' => 'required'
));
echo html_writer::end_div();

echo html_writer::end_div();

echo html_writer::empty_tag('input', array(
    'type' => 'submit',
    'value' => get_string('generateslots', 'mod_quizscheduler'),
    'class' => 'btn btn-primary'
));

echo html_writer::end_tag('form');
echo $OUTPUT->box_end();

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
