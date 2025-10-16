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
    'starttime ASC'  // Ordenar por data crescente para o calendário
);

// NOVO: Organizar slots por data para o calendário
$slots_by_date = [];
foreach ($allslots as $slot) {
    // Calcular duração em minutos
    $duration = round(($slot->endtime - $slot->starttime) / 60);
    
    $date = date('Y-m-d', $slot->starttime);
    if (!isset($slots_by_date[$date])) {
        $slots_by_date[$date] = [];
    }
    
    // Verificar disponibilidade
    $bookingCount = $DB->count_records('quizscheduler_bookings', array('slotid' => $slot->id));
    $maxUsers = isset($slot->maxusers) ? $slot->maxusers : 1;
    $isAvailable = ($bookingCount < $maxUsers) && ($slot->starttime > $currentTime);
    
    // Verificar se usuário já tem este slot
    $userHasThisSlot = false;
    foreach ($allUserBookings as $booking) {
        if ($booking->slotid == $slot->id) {
            $userHasThisSlot = true;
            break;
        }
    }
    
    $slots_by_date[$date][] = [
        'id' => $slot->id,
        'time' => date('H:i', $slot->starttime),
        'starttime' => $slot->starttime,
        'endtime' => $slot->endtime,
        'duration' => $duration,
        'available' => $isAvailable,
        'booked' => $bookingCount,
        'max' => $maxUsers,
        'userBooked' => $userHasThisSlot
    ];
}

// ==================== NOVA INTERFACE DE CALENDÁRIO ====================
echo '<div class="quizscheduler-booking-wrapper">
    <div class="booking-hero">
        <h2>Agendar Horário</h2>
        <p>Selecione uma data e horário disponível</p>
    </div>
    
    <div class="booking-grid">
        <div class="booking-calendar">
            <div class="calendar-header">Escolha a data</div>
            
            <div class="calendar-nav">
                <button onclick="changeMonth(-1)">← Anterior</button>
                <span class="calendar-month-label" id="current-month"></span>
                <button onclick="changeMonth(1)">Próximo →</button>
            </div>
            
            <table class="calendar-table">
                <thead>
                    <tr>
                        <th>Dom</th>
                        <th>Seg</th>
                        <th>Ter</th>
                        <th>Qua</th>
                        <th>Qui</th>
                        <th>Sex</th>
                        <th>Sáb</th>
                    </tr>
                </thead>
                <tbody id="calendar-body"></tbody>
            </table>
        </div>
        
        <div class="booking-slots">
            <div class="slots-header">Horários disponíveis</div>
            <div class="slots-list" id="slots-list">
                <div class="slots-empty">Selecione uma data no calendário</div>
            </div>
        </div>
    </div>
    
    <div class="booking-footer">
        <div class="booking-selection" id="selection-status">Nenhum horário selecionado</div>
        <button class="booking-confirm-btn" id="confirm-btn" disabled>Confirmar Agendamento</button>
    </div>
</div>

<script>
const slotsData = ' . json_encode($slots_by_date) . ';
const hasActiveBooking = ' . ($hasActiveBooking ? 'true' : 'false') . ';
const cmId = ' . $cm->id . ';
let currentDate = new Date();
let selectedDate = null;
let selectedSlot = null;

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    const monthNames = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
    document.getElementById("current-month").textContent = monthNames[month] + " " + year;
    
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    let html = "<tr>";
    
    for (let i = 0; i < firstDay; i++) {
        html += "<td></td>";
    }
    
    for (let day = 1; day <= daysInMonth; day++) {
        if ((firstDay + day - 1) % 7 === 0 && day !== 1) {
            html += "</tr><tr>";
        }
        
        const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
        const hasSlots = slotsData[dateStr] && slotsData[dateStr].some(s => s.available || s.userBooked);
        const checkDate = new Date(year, month, day);
        const isToday = checkDate.getTime() === today.getTime();
        const isSelected = selectedDate === dateStr;
        
        let classes = "calendar-day-btn";
        if (isToday) classes += " today";
        if (isSelected) classes += " selected";
        
        html += `<td><button class="${classes}" ${!hasSlots ? "disabled" : ""} 
                 onclick="selectDate(\'${dateStr}\')">${day}</button></td>`;
    }
    
    html += "</tr>";
    document.getElementById("calendar-body").innerHTML = html;
}

function changeMonth(delta) {
    currentDate.setMonth(currentDate.getMonth() + delta);
    renderCalendar();
}

function selectDate(date) {
    selectedDate = date;
    selectedSlot = null;
    renderCalendar();
    renderSlots(date);
    updateStatus();
}

function renderSlots(date) {
    const slots = slotsData[date] || [];
    const container = document.getElementById("slots-list");
    
    if (slots.length === 0) {
        container.innerHTML = \'<div class="slots-empty">Nenhum horário disponível</div>\';
        return;
    }
    
    let html = "";
    slots.forEach(slot => {
        let slotClass = "slot-item";
        let clickable = true;
        let slotInfo = `${slot.booked}/${slot.max} reservados`;
        
        if (slot.userBooked) {
            slotClass += " selected";
            slotInfo = "Você agendou este horário";
            clickable = false;
        } else if (!slot.available) {
            slotClass += " disabled";
            clickable = false;
            if (slot.booked >= slot.max) {
                slotInfo = "Lotado";
            } else {
                slotInfo = "Horário passou";
            }
        } else if (hasActiveBooking) {
            slotClass += " disabled";
            clickable = false;
            slotInfo = "Você já tem um agendamento ativo";
        }
        
        const onclick = clickable ? `onclick="selectSlot(${slot.id}, \'${slot.time}\', ${slot.starttime})"` : "";
        const style = !clickable ? \'style="cursor: not-allowed; opacity: 0.6;"\' : "";
        
        html += `<div class="${slotClass}" ${onclick} ${style}>
            <div>
                <div class="slot-time">${slot.time}</div>
                <small style="color: #666;">${slotInfo}</small>
            </div>
            <span class="slot-duration">${slot.duration} min</span>
        </div>`;
    });
    
    container.innerHTML = html;
}

function selectSlot(id, time, starttime) {
    if (hasActiveBooking) {
        alert("Você já possui um agendamento ativo.");
        return;
    }
    
    selectedSlot = {id, time, starttime};
    document.querySelectorAll(".slot-item:not(.disabled)").forEach(el => el.classList.remove("selected"));
    event.target.closest(".slot-item").classList.add("selected");
    updateStatus();
}

function updateStatus() {
    const status = document.getElementById("selection-status");
    const btn = document.getElementById("confirm-btn");
    
    if (selectedDate && selectedSlot) {
        const [y, m, d] = selectedDate.split("-");
        status.textContent = `Selecionado: ${d}/${m}/${y} às ${selectedSlot.time}`;
        status.classList.add("has-selection");
        btn.disabled = false;
        btn.onclick = () => {
            if (confirm("Confirmar agendamento para " + d + "/" + m + "/" + y + " às " + selectedSlot.time + "?")) {
                window.location.href = "book.php?id=" + cmId + "&slotid=" + selectedSlot.id + "&action=book";
            }
        };
    } else {
        status.textContent = "Nenhum horário selecionado";
        status.classList.remove("has-selection");
        btn.disabled = true;
    }
}

// Inicializar calendário
renderCalendar();

// Se há uma data com slots, selecionar automaticamente
const today = new Date();
const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, "0")}-${String(today.getDate()).padStart(2, "0")}`;
if (slotsData[todayStr] && slotsData[todayStr].length > 0) {
    selectDate(todayStr);
}
</script>';

// ==================== FIM DA NOVA INTERFACE ====================

// Mostrar estatísticas
$totalBookings = count($allUserBookings);
$maxBookings = isset($moduleinstance->maxbookings) ? $moduleinstance->maxbookings : 1;

echo html_writer::tag('p', 
    'Agendamentos: ' . $totalBookings . ' de ' . $maxBookings . ' utilizados',
    array('class' => 'mt-4')
);

echo $OUTPUT->footer();
