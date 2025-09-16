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


defined('MOODLE_INTERNAL') || die();

/**
 * Lista de funcionalidades suportadas pelo módulo agendar
 */
function agendar_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        default:
            return null;
    }
}

/**
 * Adiciona uma instância da atividade agendar
 */
function agendar_add_instance($agendar) {
    global $DB;
    
    $agendar->timecreated = time();
    $agendar->timemodified = time();
    
    return $DB->insert_record('agendar', $agendar);
}

/**
 * Atualiza uma instância da atividade agendar
 */
function agendar_update_instance($agendar) {
    global $DB;
    
    $agendar->timemodified = time();
    $agendar->id = $agendar->instance;
    
    return $DB->update_record('agendar', $agendar);
}

/**
 * Remove uma instância da atividade agendar
 */
function agendar_delete_instance($id) {
    global $DB;
    
    if (!$agendar = $DB->get_record('agendar', array('id' => $id))) {
        return false;
    }
    
    // Remove agendamentos
    $slots = $DB->get_records('agendar_slots', array('agendarid' => $id));
    foreach ($slots as $slot) {
        $DB->delete_records('agendar_bookings', array('slotid' => $slot->id));
    }
    
    // Remove slots
    $DB->delete_records('agendar_slots', array('agendarid' => $id));
    
    // Remove a instância
    $DB->delete_records('agendar', array('id' => $id));
    
    return true;
}

/**
 * Retorna informações sobre completion
 */
function agendar_get_completion_state($course, $cm, $userid, $type) {
    global $DB;
    
    $agendar = $DB->get_record('agendar', array('id' => $cm->instance), '*', MUST_EXIST);
    
    // Verifica se o usuário tem pelo menos um agendamento ativo
    $sql = "SELECT COUNT(ab.id) 
            FROM {agendar_bookings} ab 
            JOIN {agendar_slots} as ON as.id = ab.slotid 
            WHERE as.agendarid = ? AND ab.userid = ? AND ab.status = 'booked'";
    
    $bookings = $DB->count_records_sql($sql, array($agendar->id, $userid));
    
    return $bookings > 0;
}

/**
 * Obtém slots disponíveis para agendamento
 */
function agendar_get_available_slots($agendarid, $userid = null) {
    global $DB;
    
    $params = array($agendarid, time());
    $usersql = '';
    
    if ($userid) {
        $usersql = " AND s.id NOT IN (
            SELECT slotid FROM {agendar_bookings} 
            WHERE userid = ? AND status = 'booked'
        )";
        $params[] = $userid;
    }
    
    $sql = "SELECT s.*, 
                   COUNT(b.id) as currentbookings
            FROM {agendar_slots} s
            LEFT JOIN {agendar_bookings} b ON s.id = b.slotid AND b.status = 'booked'
            WHERE s.agendarid = ? 
              AND s.starttime > ? 
              AND s.visible = 1
              $usersql
            GROUP BY s.id
            HAVING COUNT(b.id) < s.maxbookings
            ORDER BY s.starttime";
    
    return $DB->get_records_sql($sql, $params);
}

/**
 * Verifica se o usuário pode agendar mais slots
 */
function agendar_can_user_book_more($agendarid, $userid) {
    global $DB;
    
    $agendar = $DB->get_record('agendar', array('id' => $agendarid));
    if (!$agendar) {
        return false;
    }
    
    $sql = "SELECT COUNT(ab.id) 
            FROM {agendar_bookings} ab 
            JOIN {agendar_slots} as ON as.id = ab.slotid 
            WHERE as.agendarid = ? AND ab.userid = ? AND ab.status = 'booked'";
    
    $currentbookings = $DB->count_records_sql($sql, array($agendarid, $userid));
    
    return $currentbookings < $agendar->maxbookingsperuser;
}

/**
 * Agenda um slot para um usuário
 */
function agendar_book_slot($slotid, $userid, $notes = '') {
    global $DB;
    
    // Verifica se o slot está disponível
    $slot = $DB->get_record('agendar_slots', array('id' => $slotid));
    if (!$slot || $slot->starttime <= time()) {
        return false;
    }
    
    // Verifica se o usuário já tem este slot agendado
    $existingbooking = $DB->get_record('agendar_bookings', 
        array('slotid' => $slotid, 'userid' => $userid, 'status' => 'booked'));
    if ($existingbooking) {
        return false;
    }
    
    // Verifica limite de agendamentos do slot
    $currentbookings = $DB->count_records('agendar_bookings', 
        array('slotid' => $slotid, 'status' => 'booked'));
    if ($currentbookings >= $slot->maxbookings) {
        return false;
    }
    
    // Verifica limite de agendamentos do usuário
    if (!agendar_can_user_book_more($slot->agendarid, $userid)) {
        return false;
    }
    
    // Cria o agendamento
    $booking = new stdClass();
    $booking->slotid = $slotid;
    $booking->userid = $userid;
    $booking->status = 'booked';
    $booking->bookingnotes = $notes;
    $booking->timecreated = time();
    $booking->timemodified = time();
    
    return $DB->insert_record('agendar_bookings', $booking);
}

/**
 * Cancela um agendamento
 */
function agendar_cancel_booking($bookingid, $userid) {
    global $DB;
    
    $booking = $DB->get_record('agendar_bookings', array('id' => $bookingid, 'userid' => $userid));
    if (!$booking || $booking->status !== 'booked') {
        return false;
    }
    
    $slot = $DB->get_record('agendar_slots', array('id' => $booking->slotid));
    $agendar = $DB->get_record('agendar', array('id' => $slot->agendarid));
    
    // Verifica se o cancelamento é permitido
    if (!$agendar->allowcancellation) {
        return false;
    }
    
    // Verifica deadline para cancelamento
    $timeleft = $slot->starttime - time();
    if ($timeleft < $agendar->cancellationdeadline) {
        return false;
    }
    
    // Atualiza o status
    $booking->status = 'cancelled';
    $booking->timemodified = time();
    
    return $DB->update_record('agendar_bookings', $booking);
}

/**
 * Obtém agendamentos de um usuário
 */
function agendar_get_user_bookings($agendarid, $userid, $status = null) {
    global $DB;
    
    $params = array($agendarid, $userid);
    $statuswhere = '';
    
    if ($status) {
        $statuswhere = " AND b.status = ?";
        $params[] = $status;
    }
    
    $sql = "SELECT b.*, s.starttime, s.endtime, s.location, s.notes as slotnotes
            FROM {agendar_bookings} b
            JOIN {agendar_slots} s ON b.slotid = s.id
            WHERE s.agendarid = ? AND b.userid = ? $statuswhere
            ORDER BY s.starttime";
    
    return $DB->get_records_sql($sql, $params);
}

/**
 * Obtém todos os agendamentos de um slot
 */
function agendar_get_slot_bookings($slotid) {
    global $DB;
    
    $sql = "SELECT b.*, u.firstname, u.lastname, u.email
            FROM {agendar_bookings} b
            JOIN {user} u ON b.userid = u.id
            WHERE b.slotid = ? AND b.status = 'booked'
            ORDER BY b.timecreated";
    
    return $DB->get_records_sql($sql, array($slotid));
}
