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
 * Booking management for quiz scheduler
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/quizscheduler/lib.php');

$id = required_param('id', PARAM_INT); // Course module ID
$slotid = required_param('slotid', PARAM_INT);
$action = required_param('action', PARAM_ALPHA); // book or cancel

$cm = get_coursemodule_from_id('quizscheduler', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$quizscheduler = $DB->get_record('quizscheduler', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$viewurl = new moodle_url('/mod/quizscheduler/view.php', array('id' => $cm->id));

if ($action === 'book') {
    // Verificar se pode fazer nova reserva
    if (!quizscheduler_can_user_book_new_slot($USER->id, $quizscheduler->id)) {
        redirect($viewurl, get_string('youhaveactivebooking', 'quizscheduler'), null, 'error');
    }
    
    // Verificar se o slot existe e está disponível
    $slot = $DB->get_record('quizscheduler_slots', array('id' => $slotid), '*', MUST_EXIST);
    
    if ($slot->starttime <= time()) {
        redirect($viewurl, get_string('slotnotavailable', 'quizscheduler'), null, 'error');
    }
    
    // Criar a reserva
    $booking = new stdClass();
    $booking->slotid = $slotid;
    $booking->userid = $USER->id;
    $booking->timebooked = time();
    
    try {
        $bookingid = $DB->insert_record('quizscheduler_bookings', $booking);
        
        // Enviar email de confirmação após booking bem-sucedido
        if ($bookingid) {
            // Buscar dados para o email
            $slot = $DB->get_record('quizscheduler_slots', array('id' => $slotid));
            $student = $DB->get_record('user', array('id' => $USER->id));
            $course = $DB->get_record('course', array('id' => $cm->course));
            
            // Preparar e enviar email
            //$subject = 'Confirmação de Agendamento: ' . $quizscheduler->name;
            
            // Teste título do questionário
            $quiz = $DB->get_record('quiz', array('id' => $quizscheduler->quizid), '*', MUST_EXIST);
            $cm_quiz = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);
            $subject = "Confirmação de Agendamento: " . $quiz->name;
            $message = "Olá " . fullname($student) . ",\n\n";
            $message .= "Você se inscreveu com sucesso na avaliação:\n\n";
            //$message .= "Questinário: " . $quizscheduler->name . "\n";
            $message .= "Questinário: " . $quiz->name . "\n";
            $message .= "Data: " . userdate($slot->starttime, '%d/%m/%Y') . "\n";
            $message .= "Horário: " . userdate($slot->starttime, '%H:%M') . " - " . userdate($slot->endtime, '%H:%M') . "\n";
            $message .= "Curso: " . $course->fullname . "\n\n";
            
            // Buscar o quiz associado ao agendamento
            $quiz = $DB->get_record('quiz', array('id' => $quizscheduler->quizid), '*', MUST_EXIST);
            $cm_quiz = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);
            $quizurl = new moodle_url('/mod/quiz/view.php', array('id' => $cm_quiz->id));
            
            
            $message .= "Link do questionário: " . $quizurl . "\n";
            //$message .= "Link para visualizar agendamentos: " . $viewurl . "\n\n";
            $supportname = get_config('moodle', 'supportname');
            $message .= "Atenciosamente,\n" . $supportname . ".";
            //$message .= "Atenciosamente,\n" . $SITE->fullname;
            
            // Enviar email
            $result = email_to_user($student, core_user::get_noreply_user(), $subject, $message);
            
            // Log para debug
            if (!$result) {
                error_log('Falha ao enviar email de confirmação para: ' . $student->email);
            } else {
                error_log('Email enviado com sucesso para: ' . $student->email);
            }
        }
        
        redirect($viewurl, get_string('bookingsuccess', 'quizscheduler'), null, 'success');
    } catch (Exception $e) {
        redirect($viewurl, get_string('bookingerror', 'quizscheduler'), null, 'error');
    }
} else if ($action === 'cancel') {
    // Verificar se existe a reserva do usuário para este slot
    $booking = $DB->get_record('quizscheduler_bookings', 
        array('slotid' => $slotid, 'userid' => $USER->id));
    
    if (!$booking) {
        redirect($viewurl, get_string('nobooking', 'quizscheduler'), null, 'error');
    }
    
    // Obter dados do slot para verificar cancelamento
    $slot = $DB->get_record('quizscheduler_slots', array('id' => $slotid), '*', MUST_EXIST);
    
    // Verificar se ainda é possível cancelar
    $cancellationdeadline = $slot->starttime - ($quizscheduler->cancellationtime ?? 3600);
    if (time() > $cancellationdeadline) {
        redirect($viewurl, get_string('cancellationtoolate', 'quizscheduler'), null, 'error');
    }
    
    try {
        $DB->delete_records('quizscheduler_bookings', array('id' => $booking->id));
        redirect($viewurl, get_string('cancellationsuccess', 'quizscheduler'), null, 'success');
    } catch (Exception $e) {
        redirect($viewurl, get_string('cancellationerror', 'quizscheduler'), null, 'error');
    }
    
} else {
    throw new moodle_exception('invalidaction', 'quizscheduler');
}

