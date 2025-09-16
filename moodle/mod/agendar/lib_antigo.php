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
 * Library of interface functions and constants.
 *
 * @package     mod_agendar
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add agendar instance.
 *
 * @param stdClass $agendar An object from the form
 * @param mod_agendar_mod_form $mform The form
 * @return int The instance id of the new agendar
 */
function agendar_add_instance($agendar, $mform = null) {
    global $DB;
    
    $agendar->timecreated = time();
    $agendar->timemodified = time();
    
    $id = $DB->insert_record('agendar', $agendar);

    // Salvar os slots de horário se existirem
    if (isset($agendar->slot_repeats)) {
        agendar_save_slots($id, $agendar);
    }
    
    return $id;
}

/**
 * Update agendar instance.
 *
 * @param stdClass $agendar An object from the form
 * @param mod_agendar_mod_form $mform The form
 * @return bool true
 */
function agendar_update_instance($agendar, $mform = null) {
    global $DB;
    
    $agendar->timemodified = time();
    $agendar->id = $agendar->instance;
    
    $result = $DB->update_record('agendar', $agendar);

    // Atualizar os slots de horário
    if (isset($agendar->slot_repeats)) {
        // Remover slots antigos
        $DB->delete_records('agendar_slots', array('agendarid' => $agendar->id));
        // Salvar novos slots
        agendar_save_slots($agendar->id, $agendar);
    }
    
    return $result;
}

/**
 * Delete agendar instance.
 *
 * @param int $id The instance id
 * @return bool true
 */
function agendar_delete_instance($id) {
    global $DB;
    
    if (!$agendar = $DB->get_record('agendar', array('id' => $id))) {
        return false;
    }

    // Remover slots relacionados
    $DB->delete_records('agendar_slots', array('agendarid' => $id));
    
    // Remover agendamentos relacionados
    $DB->delete_records('agendar_bookings', array('agendarid' => $id));
    
    $DB->delete_records('agendar', array('id' => $agendar->id));
    
    return true;
}

/**
 * Salva os slots de horário no banco de dados
 *
 * @param int $agendarid ID da instância do agendar
 * @param stdClass $data Dados do formulário
 */
function agendar_save_slots($agendarid, $data) {
    global $DB;

    for ($i = 0; $i < $data->slot_repeats; $i++) {
        if (!empty($data->slotenabled[$i])) {
            $slot = new stdClass();
            $slot->agendarid = $agendarid;
            $slot->starttime = $data->slotstart[$i];
            $slot->endtime = $data->slotend[$i];
            $slot->maxparticipants = $data->maxparticipants[$i];
            $slot->enabled = 1;
            $slot->timecreated = time();
            
            $DB->insert_record('agendar_slots', $slot);
        }
    }
}

/**
 * Checks if the module supports a specific feature.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function agendar_supports($feature) {
    switch($feature) {
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return false; // Não rastreia visualizações
        case FEATURE_COMPLETION_HAS_RULES:
            return true; // Suporte a regras de completion customizadas
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_COMMUNICATION;
        default:
            return null;
    }
}

/**
 * Obtains the automatic completion state for this agendar based on any conditions
 * in agendar settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not. (If no conditions, then return value depends on comparison type)
 */
function agendar_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    // Get agendar details
    if (!($agendar = $DB->get_record('agendar', array('id' => $cm->instance)))) {
        throw new Exception("Can't find agendar {$cm->instance}");
    }

    // Verificar se a completion por agendamento está habilitada
    if (!$agendar->completionbooking) {
        return $type; // Retorna o tipo se a condição não está ativa
    }

    // Verificar se o usuário fez um agendamento
    $booking = $DB->get_record('agendar_bookings', array(
        'agendarid' => $agendar->id,
        'userid' => $userid
    ));

    return (bool)$booking;
}

/**
 * Função para marcar completion quando um agendamento é feito
 * Chame esta função quando um usuário fizer um agendamento
 *
 * @param int $agendarid ID da instância do agendar
 * @param int $userid ID do usuário
 */
function agendar_update_completion_on_booking($agendarid, $userid) {
    global $DB;

    // Obter o course module
    $cm = get_coursemodule_from_instance('agendar', $agendarid);
    if (!$cm) {
        return;
    }

    // Obter o curso
    $course = $DB->get_record('course', array('id' => $cm->course));
    if (!$course) {
        return;
    }

    // Verificar se completion está habilitado
    $completion = new completion_info($course);
    if (!$completion->is_enabled($cm)) {
        return;
    }

    // Atualizar o status de completion
    $completion->update_state($cm, COMPLETION_COMPLETE, $userid);
}

/**
 * Função para fazer um agendamento
 *
 * @param int $slotid ID do slot
 * @param int $userid ID do usuário
 * @return bool|int True ou ID do agendamento se bem-sucedido, false caso contrário
 */
function agendar_make_booking($slotid, $userid) {
    global $DB;

    $slot = $DB->get_record('agendar_slots', array('id' => $slotid));
    if (!$slot) {
        return false;
    }

    // Verificar se já existe um agendamento para este usuário neste agendar
    $existingbooking = $DB->get_record('agendar_bookings', array(
        'agendarid' => $slot->agendarid,
        'userid' => $userid
    ));

    if ($existingbooking) {
        return false; // Usuário já tem agendamento
    }

    // Verificar se o slot ainda tem vagas
    $currentbookings = $DB->count_records('agendar_bookings', array('slotid' => $slotid));
    if ($currentbookings >= $slot->maxparticipants) {
        return false; // Slot lotado
    }

    // Criar novo agendamento
    $booking = new stdClass();
    $booking->agendarid = $slot->agendarid;
    $booking->slotid = $slotid;
    $booking->userid = $userid;
    $booking->timecreated = time();

    $bookingid = $DB->insert_record('agendar_bookings', $booking);

    if ($bookingid) {
        // Atualizar completion
        agendar_update_completion_on_booking($slot->agendarid, $userid);
        return $bookingid;
    }

    return false;
}

/**
 * Função para cancelar um agendamento
 *
 * @param int $agendarid ID da instância do agendar
 * @param int $userid ID do usuário
 * @return bool True se bem-sucedido
 */
function agendar_cancel_booking($agendarid, $userid) {
    global $DB;

    $booking = $DB->get_record('agendar_bookings', array(
        'agendarid' => $agendarid,
        'userid' => $userid
    ));

    if (!$booking) {
        return false;
    }

    $result = $DB->delete_records('agendamento_bookings', array('id' => $booking->id));

    if ($result) {
        // Atualizar completion - marcar como incompleto
        $cm = get_coursemodule_from_instance('agendar', $agendarid);
        if ($cm) {
            $course = $DB->get_record('course', array('id' => $cm->course));
            if ($course) {
                $completion = new completion_info($course);
                if ($completion->is_enabled($cm)) {
                    $completion->update_state($cm, COMPLETION_INCOMPLETE, $userid);
                }
            }
        }
    }

    return $result;
}

/**
 * Obter o agendamento de um usuário
 *
 * @param int $agendarid ID da instância do agendar
 * @param int $userid ID do usuário
 * @return object|false Dados do agendamento ou false se não encontrado
 */
function agendar_get_user_booking($agendarid, $userid) {
    global $DB;

    $sql = "SELECT b.*, s.starttime, s.endtime, s.maxparticipants
            FROM {agendamento_bookings} b
            JOIN {agendar_slots} s ON b.slotid = s.id
            WHERE b.agendarid = ? AND b.userid = ?";

    return $DB->get_record_sql($sql, array($agendarid, $userid));
}

/**
 * Obter todos os slots de um agendar
 *
 * @param int $agendarid ID da instância do agendar
 * @return array Array de slots
 */
function agendar_get_slots($agendarid) {
    global $DB;

    $sql = "SELECT s.*, 
                   COUNT(b.id) as current_bookings
            FROM {agendar_slots} s
            LEFT JOIN {agendamento_bookings} b ON s.id = b.slotid
            WHERE s.agendarid = ? AND s.enabled = 1
            GROUP BY s.id, s.agendarid, s.starttime, s.endtime, s.maxparticipants, s.enabled, s.timecreated
            ORDER BY s.starttime";

    return $DB->get_records_sql($sql, array($agendarid));
}