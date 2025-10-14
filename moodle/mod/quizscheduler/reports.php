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
 * Reports for quiz scheduler.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

$id = required_param('id', PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);
$slotfilter = optional_param('slotfilter', 0, PARAM_INT);
$statusfilter = optional_param('statusfilter', '', PARAM_ALPHA);

$cm = get_coursemodule_from_id('quizscheduler', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$moduleinstance = $DB->get_record('quizscheduler', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);
require_capability('mod/quizscheduler:viewreports', $modulecontext);

$PAGE->set_url('/mod/quizscheduler/reports.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

// Construir consulta com filtros
$sqlwhere = "s.quizschedulerid = ?";
$params = array($moduleinstance->id);

if ($slotfilter > 0) {
    $sqlwhere .= " AND s.id = ?";
    $params[] = $slotfilter;
}

// Filtro por status de agendamento
if (!empty($statusfilter)) {
    $currentTime = time();
    switch ($statusfilter) {
        case 'active':
            $sqlwhere .= " AND s.endtime > ?";
            $params[] = $currentTime;
            break;
        case 'completed':
            $sqlwhere .= " AND s.endtime <= ?";
            $params[] = $currentTime;
            break;
        case 'upcoming':
            $sqlwhere .= " AND s.starttime > ?";
            $params[] = $currentTime;
            break;
    }
}

// Verificar se é uma requisição de download antes de gerar qualquer HTML
if ($download === 'excel') {
    // Limpar qualquer output anterior
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Headers corretos para CSV
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="relatorio_agendamentos_' . date('Y-m-d_H-i-s') . '.csv"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    
    // BOM para UTF-8 (para abrir corretamente no Excel)
    echo "\xEF\xBB\xBF";
    
    // Cabeçalhos do CSV
    $headers = array(
        'Nome Completo',
        'Email',
        'Data/Hora Início',
        'Data/Hora Fim',
        'Status Agendamento',
        'Data do Agendamento',
        'Status do Quiz',
        'Nota do Quiz',
        'Tentativas'
    );
    
    // Escrever cabeçalhos
    echo implode(';', $headers) . "\n";
    
    // Get report data
    $sql = "SELECT b.id, b.timebooked,
                   s.starttime, s.endtime,
                   u.id as userid, u.firstname, u.lastname, u.email,
                   qa.sumgrades, qa.state, qa.timestart, qa.timefinish, qa.attempt,
                   q.grade as maxgrade,
                   CASE 
                       WHEN s.starttime > ? THEN 'upcoming'
                       WHEN s.endtime > ? THEN 'active' 
                       ELSE 'completed'
                   END as booking_status
            FROM {quizscheduler_bookings} b
            JOIN {quizscheduler_slots} s ON b.slotid = s.id
            JOIN {user} u ON b.userid = u.id
            LEFT JOIN {quiz} q ON q.id = ?
            LEFT JOIN {quiz_attempts} qa ON qa.quiz = ? AND qa.userid = u.id 
                AND qa.id = (SELECT MAX(id) FROM {quiz_attempts} qa2 WHERE qa2.quiz = qa.quiz AND qa2.userid = qa.userid)
            WHERE $sqlwhere
            ORDER BY s.starttime ASC, u.lastname ASC, u.firstname ASC";

    $allparams = array_merge(array(time(), time(), $moduleinstance->quizid, $moduleinstance->quizid), $params);
    $bookings = $DB->get_records_sql($sql, $allparams);
    
    // Processar cada linha de dados
    foreach ($bookings as $booking) {
        // Nome completo
        $fullname = trim($booking->firstname . ' ' . $booking->lastname);
        
        // Datas formatadas
        $starttime = userdate($booking->starttime, '%d/%m/%Y %H:%M');
        $endtime = userdate($booking->endtime, '%d/%m/%Y %H:%M');
        $timebooked = userdate($booking->timebooked, '%d/%m/%Y %H:%M');
        
        // Status do agendamento (traduzido)
        $bookingstatus = '';
        switch ($booking->booking_status) {
            case 'upcoming':
                $bookingstatus = 'Próximo';
                break;
            case 'active':
                $bookingstatus = 'Ativo';
                break;
            case 'completed':
                $bookingstatus = 'Concluído';
                break;
            default:
                $bookingstatus = ucfirst($booking->booking_status);
        }
        
        // Status do quiz
        $quizstatus = '';
        if ($booking->state === 'finished') {
            $quizstatus = 'Concluído';
        } elseif ($booking->state === 'inprogress') {
            $quizstatus = 'Em Progresso';
        } elseif (!empty($booking->timestart) && empty($booking->timefinish)) {
            $quizstatus = 'Em Progresso';
        } else {
            $quizstatus = 'Não Iniciado';
        }
        
        // Nota do quiz
        $grade = '';
        if (!empty($booking->sumgrades) && !empty($booking->maxgrade)) {
            $finalgrade = round(($booking->sumgrades / $booking->maxgrade) * 100, 2);
            $grade = $finalgrade . '%';
        } else if ($booking->state === 'finished') {
            $grade = '0%';
        } else {
            $grade = '-';
        }
        
        // Número da tentativa
        $attempt = !empty($booking->attempt) ? $booking->attempt : '-';
        
        // Montar linha do CSV (usando ponto e vírgula como separador)
        $row = array(
            '"' . str_replace('"', '""', $fullname) . '"',
            '"' . str_replace('"', '""', $booking->email) . '"',
            '"' . $starttime . '"',
            '"' . $endtime . '"',
            '"' . $bookingstatus . '"',
            '"' . $timebooked . '"',
            '"' . $quizstatus . '"',
            '"' . $grade . '"',
            '"' . $attempt . '"'
        );
        
        echo implode(';', $row) . "\n";
    }
    
    exit();
}

// Get report data para exibição
$sql = "SELECT b.id, b.timebooked,
               s.starttime, s.endtime,
               u.id as userid, u.firstname, u.lastname, u.email,
               qa.sumgrades, qa.state, qa.timestart, qa.timefinish, qa.attempt,
               q.grade as maxgrade,
               CASE 
                   WHEN s.starttime > ? THEN 'upcoming'
                   WHEN s.endtime > ? THEN 'active' 
                   ELSE 'completed'
               END as booking_status
        FROM {quizscheduler_bookings} b
        JOIN {quizscheduler_slots} s ON b.slotid = s.id
        JOIN {user} u ON b.userid = u.id
        LEFT JOIN {quiz} q ON q.id = ?
        LEFT JOIN {quiz_attempts} qa ON qa.quiz = ? AND qa.userid = u.id 
            AND qa.id = (SELECT MAX(id) FROM {quiz_attempts} qa2 WHERE qa2.quiz = qa.quiz AND qa2.userid = qa.userid)
        WHERE $sqlwhere
        ORDER BY s.starttime DESC, b.timebooked DESC, u.lastname ASC, u.firstname ASC";

$allparams = array_merge(array(time(), time(), $moduleinstance->quizid, $moduleinstance->quizid), $params);
$bookings = $DB->get_records_sql($sql, $allparams);

// Obter slots para filtro
$slots = $DB->get_records('quizscheduler_slots', 
    array('quizschedulerid' => $moduleinstance->id), 
    'starttime ASC');

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($moduleinstance->name));

// Navigation.
$viewurl = new moodle_url('/mod/quizscheduler/view.php', array('id' => $cm->id));
echo html_writer::div(
    html_writer::link($viewurl, get_string('back'), array('class' => 'btn btn-secondary')),
    'mb-3'
);

echo $OUTPUT->heading(get_string('bookingreport', 'mod_quizscheduler'), 3);

// Formulário de filtros
echo html_writer::start_tag('form', array('method' => 'get', 'class' => 'mb-3'));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $cm->id));

echo html_writer::start_div('row');

// Filtro por slot
echo html_writer::start_div('col-md-4');
echo html_writer::tag('label', get_string('filterbyslot', 'quizscheduler'), array('for' => 'slotfilter'));
$slotoptions = array(0 => get_string('allslots', 'quizscheduler'));
foreach ($slots as $slot) {
    $slotoptions[$slot->id] = userdate($slot->starttime, get_string('strftimedaydatetime')) . 
                             ' - ' . userdate($slot->endtime, get_string('strftimetime'));
}
echo html_writer::select($slotoptions, 'slotfilter', $slotfilter, false, array('class' => 'form-control'));
echo html_writer::end_div();

// Filtro por status
echo html_writer::start_div('col-md-4');
echo html_writer::tag('label', get_string('filterbystatus', 'quizscheduler'), array('for' => 'statusfilter'));
$statusoptions = array(
    '' => get_string('allstatuses', 'quizscheduler'),
    'upcoming' => get_string('upcoming', 'quizscheduler'),
    'active' => get_string('active', 'quizscheduler'),
    'completed' => get_string('completed', 'quizscheduler')
);
echo html_writer::select($statusoptions, 'statusfilter', $statusfilter, false, array('class' => 'form-control'));
echo html_writer::end_div();

// Botões
echo html_writer::start_div('col-md-4');
echo html_writer::tag('label', '&nbsp;', array('class' => 'd-block'));
echo html_writer::tag('button', get_string('filter'), array('type' => 'submit', 'class' => 'btn btn-primary mr-2'));
echo html_writer::link(
    new moodle_url('/mod/quizscheduler/reports.php', array(
        'id' => $cm->id, 
        'download' => 'excel', 
        'slotfilter' => $slotfilter, 
        'statusfilter' => $statusfilter
    )),
    get_string('downloadcsv', 'quizscheduler'),
    array('class' => 'btn btn-success')
);
echo html_writer::end_div();

echo html_writer::end_div();
echo html_writer::end_tag('form');

// Summary statistics.
if (!empty($bookings)) {
    $stats = array(
        'total' => count($bookings),
        'upcoming' => 0,
        'active' => 0,
        'completed' => 0,
        'quizcompleted' => 0,
        'quizinprogress' => 0
    );
    
    foreach ($bookings as $booking) {
        $stats[$booking->booking_status]++;
        if ($booking->state === 'finished') {
            $stats['quizcompleted']++;
        } elseif ($booking->state === 'inprogress') {
            $stats['quizinprogress']++;
        }
    }
    
    echo html_writer::start_div('row mb-4');
    
    foreach ($stats as $status => $count) {
        $label = '';
        switch ($status) {
            case 'total': $label = get_string('total'); break;
            case 'upcoming': $label = get_string('upcoming', 'quizscheduler'); break;
            case 'active': $label = get_string('active', 'quizscheduler'); break;
            case 'completed': $label = get_string('completed', 'quizscheduler'); break;
            case 'quizcompleted': $label = get_string('quizcompleted', 'quizscheduler'); break;
            case 'quizinprogress': $label = get_string('quizinprogress', 'quizscheduler'); break;
        }
        
        echo html_writer::div(
            html_writer::div(
                html_writer::tag('h4', $count, array('class' => 'mb-1')) .
                html_writer::tag('small', $label, array('class' => 'text-muted')),
                'card-body text-center'
            ),
            'col-md-2 mb-2'
        );
    }
    
    echo html_writer::end_div();
    
    // Bookings table.
    $table = new html_table();
    $table->head = array(
        get_string('user'),
        get_string('email'),
        get_string('datetime', 'quizscheduler'),
        get_string('status', 'quizscheduler'),
        get_string('timebooked', 'quizscheduler'),
        get_string('quizstatus', 'quizscheduler')
    );
    
    foreach ($bookings as $booking) {
        $userurl = new moodle_url('/user/profile.php', array('id' => $booking->userid));
        $username = html_writer::link($userurl, fullname($booking));
        
        $datetime = userdate($booking->starttime, get_string('strftimedaydatetime')) . 
                   ' - ' . userdate($booking->endtime, get_string('strftimetime'));
        $timebooked = userdate($booking->timebooked, get_string('strftimedatetimeshort', 'langconfig'));
        
        // Status do agendamento com badge
        $statusclass = '';
        $statustext = '';
        switch ($booking->booking_status) {
            case 'upcoming':
                $statusclass = 'badge badge-info';
                $statustext = get_string('upcoming', 'quizscheduler');
                break;
            case 'active':
                $statusclass = 'badge badge-success';
                $statustext = get_string('active', 'quizscheduler');
                break;
            case 'completed':
                $statusclass = 'badge badge-secondary';
                $statustext = get_string('completed', 'quizscheduler');
                break;
        }
        
        $status = html_writer::span($statustext, $statusclass);
        
        // Status do quiz
        $quizstatus = '';
        $quizstatusclass = '';

        if ($booking->state === 'finished') {
            $quizstatus = get_string('completed', 'quizscheduler');
            $quizstatusclass = 'badge badge-success';
        } elseif ($booking->state === 'inprogress') {
            $quizstatus = get_string('inprogress', 'quizscheduler');
            $quizstatusclass = 'badge badge-warning';
        } elseif (!empty($booking->timestart) && empty($booking->timefinish)) {
            $quizstatus = get_string('inprogress', 'quizscheduler');
            $quizstatusclass = 'badge badge-warning';
        } else {
            $quizstatus = get_string('notstarted', 'quizscheduler');
            $quizstatusclass = 'badge badge-secondary';
        }

        $quizstatusformatted = html_writer::span($quizstatus, $quizstatusclass);

        $table->data[] = array(
            $username,
            $booking->email,
            $datetime,
            $status,
            $timebooked,
            $quizstatusformatted
        );
    }
    
    echo html_writer::table($table);
    
} else {
    echo $OUTPUT->notification(get_string('nobookings', 'quizscheduler'), \core\output\notification::NOTIFY_INFO);
}

echo $OUTPUT->footer();
