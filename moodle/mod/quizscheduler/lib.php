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
 * Library of interface functions and constants.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Load our auto-loader immediately
require_once(__DIR__ . '/classes/autoloader.php');
require_once($CFG->dirroot . '/mod/quizscheduler/classes/messaging.php');

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function quizscheduler_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_quizscheduler into the database.
 *
 * @param stdClass $moduleinstance An object from the form.
 * @param mod_quizscheduler_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function quizscheduler_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('quizscheduler', $moduleinstance);

    return $id;
}

/**
 * Updates an instance of the mod_quizscheduler in the database.
 *
 * @param stdClass $moduleinstance An object from the form in mod_form.php.
 * @param mod_quizscheduler_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function quizscheduler_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('quizscheduler', $moduleinstance);
}

/**
 * Removes an instance of the mod_quizscheduler from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function quizscheduler_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('quizscheduler', array('id' => $id));
    if (!$exists) {
        return false;
    }

    // Delete related records.
    $DB->delete_records('quizscheduler_slots', array('quizschedulerid' => $id));
    $DB->delete_records_select('quizscheduler_bookings', 
        'slotid IN (SELECT id FROM {quizscheduler_slots} WHERE quizschedulerid = ?)', array($id));
    
    $DB->delete_records('quizscheduler', array('id' => $id));

    return true;
}

/**
 * Deleta um slot de agendamento
 * 
 * @param int $slotid ID do slot a ser deletado
 * @return bool True se deletado com sucesso
 */
function quizscheduler_delete_slot($slotid) {
    global $DB;
    
    try {
        // Verifica se o slot existe
        if (!$DB->record_exists('quizscheduler_slots', array('id' => $slotid))) {
            return false;
        }
        
        // Remove reservas/bookings associadas ao slot
        $DB->delete_records('quizscheduler_bookings', array('slotid' => $slotid));
        
        // Remove o slot
        $result = $DB->delete_records('quizscheduler_slots', array('id' => $slotid));
        
        return $result;
        
    } catch (Exception $e) {
        debugging('Erro ao deletar slot: ' . $e->getMessage(), DEBUG_DEVELOPER);
        return false;
    }
}

/**
 * Extend navigation settings.
 */
function quizscheduler_extend_settings_navigation(settings_navigation $settings, navigation_node $quizschedulernode) {
    global $PAGE;

    if ($PAGE->cm && $PAGE->cm->modname === 'quizscheduler') {
        $context = $PAGE->cm->context;
        
        if (has_capability('mod/quizscheduler:manageslots', $context)) {
            $quizschedulernode->add(
                get_string('manageslots', 'mod_quizscheduler'),
                new moodle_url('/mod/quizscheduler/manage.php', array('id' => $PAGE->cm->id)),
                navigation_node::TYPE_SETTING
            );
        }
    }
}

/**
 * Verifica se um slot já foi encerrado
 */
function quizscheduler_slot_is_finished($slotid) {
    global $DB;
    
    $slot = $DB->get_record('quizscheduler_slots', ['id' => $slotid]);
    return $slot && $slot->endtime < time();
}

/**
 * Verifica se o usuário tem agendamentos ativos (não finalizados)
 */
function quizscheduler_user_has_active_booking($userid, $quizschedulerid) {
    global $DB;
    
    return $DB->record_exists_sql(
        "SELECT 1 FROM {quizscheduler_bookings} qb 
         JOIN {quizscheduler_slots} qs ON qb.slotid = qs.id 
         WHERE qb.userid = ? AND qs.quizschedulerid = ? AND qs.endtime > ?",
        [$userid, $quizschedulerid, time()]
    );
}

/**
 * Obtém o agendamento ativo do usuário
 */
function quizscheduler_get_user_active_booking($userid, $quizschedulerid) {
    global $DB;
    
    return $DB->get_record_sql(
        "SELECT qb.*, qs.starttime, qs.endtime, qs.maxusers
         FROM {quizscheduler_bookings} qb 
         JOIN {quizscheduler_slots} qs ON qb.slotid = qs.id 
         WHERE qb.userid = ? AND qs.quizschedulerid = ? AND qs.endtime > ?
         ORDER BY qs.starttime DESC LIMIT 1",
        [$userid, $quizschedulerid, time()]
    );
}

/**
 * Obtém todos os agendamentos do usuário
 */
function quizscheduler_get_user_bookings($userid, $quizschedulerid, $activeonly = false) {
    global $DB;
    
    $timecondition = $activeonly ? 'AND qs.endtime > ?' : '';
    $params = [$userid, $quizschedulerid];
    if ($activeonly) {
        $params[] = time();
    }
    
    return $DB->get_records_sql(
        "SELECT qb.*, qs.starttime, qs.endtime, qs.maxusers, qs.currentbookings
         FROM {quizscheduler_bookings} qb 
         JOIN {quizscheduler_slots} qs ON qb.slotid = qs.id 
         WHERE qb.userid = ? AND qs.quizschedulerid = ? $timecondition
         ORDER BY qs.starttime DESC",
        $params
    );
}

/**
 * Verifica se o usuário pode fazer uma nova reserva
 */
function quizscheduler_can_user_book_new_slot($userid, $quizschedulerid) {
    return !quizscheduler_user_has_active_booking($userid, $quizschedulerid);
}

/**
 * Function to book a student into a quiz slot
 * (Esta função provavelmente já existe - modificar para adicionar o envio de e-mail)
 */
function quizscheduler_book_student_slot($slotid, $userid) {
    global $DB;
    
    // ...existing booking logic...
    
    // Get required data for email
    $slot = $DB->get_record('quizscheduler_slots', ['id' => $slotid], '*', MUST_EXIST);
    $quiz = $DB->get_record('quiz', ['id' => $slot->quizid], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $slot->courseid], '*', MUST_EXIST);
    $student = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    
    // Send booking confirmation email
    quizscheduler_messaging::send_booking_confirmation($student, $slot, $quiz, $course);
    
    // ...existing code...
    
    return $booking_result;
}

/**
 * Adiciona uma nova reserva (booking) para um usuário em um slot específico.
 * 
 * @param int $slotid ID do slot
 * @param int $userid ID do usuário
 * @return bool True se a reserva foi adicionada com sucesso, false caso contrário.
 */
function quizscheduler_add_booking($slotid, $userid) {
    global $DB;
    
    // ...existing booking logic...
    
    // APÓS o booking ser salvo com sucesso, adicionar:
    
    // Carregar classe de mensagens
    require_once(__DIR__ . '/classes/messaging.php');
    
    // Buscar dados necessários
    $slot = $DB->get_record('quizscheduler_slots', ['id' => $slotid], '*', MUST_EXIST);
    $student = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    $cm = get_coursemodule_from_id('quizscheduler', $slot->quizscheduler);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $quizscheduler = $DB->get_record('quizscheduler', ['id' => $slot->quizscheduler], '*', MUST_EXIST);
    
    // Enviar e-mail de confirmação
    quizscheduler_messaging::send_booking_confirmation($student, $slot, $quizscheduler, $course);
    
    // ...existing code...
    
    return $result;
}
