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
            
            // Buscar o quiz associado ao agendamento
            $quiz = $DB->get_record('quiz', array('id' => $quizscheduler->quizid), '*', MUST_EXIST);
            $cm_quiz = get_coursemodule_from_instance('quiz', $quiz->id, $course->id);
            $quizurl = new moodle_url('/mod/quiz/view.php', array('id' => $cm_quiz->id));
            
            // ============================================
            // GERAR LINK DO GOOGLE CALENDAR - CORRIGIDO
            // ============================================
            
            // Formatar datas no formato correto: YYYYMMDDTHHMMSSZ (UTC)
            $startdate = gmdate('Ymd\THis\Z', $slot->starttime);
            $enddate = gmdate('Ymd\THis\Z', $slot->endtime);
            
            // Preparar textos (SEM urlencode aqui - será feito no http_build_query)
            //$event_title = $quiz->name . ' - ' . $course->fullname;
            $event_title = $quiz->name;
            $event_details = 'Questionário: ' . $quiz->name . "\n" . 
                            'Curso: ' . $course->fullname . "\n" .
                            'Link do quiz: ' . $quizurl->out(false);
            $event_location = $course->fullname;
            
            // Montar URL do Google Calendar
            // Usar calendar.google.com/calendar/render com os parâmetros corretos
            $google_calendar_url = 'https://calendar.google.com/calendar/render?' . http_build_query([
                'action' => 'TEMPLATE',
                'text' => $event_title,
                'dates' => $startdate . '/' . $enddate,
                'details' => $event_details,
                //'location' => $event_location,
                'trp' => 'false',
                'sprop' => 'name:' . $SITE->fullname
            ], '', '&', PHP_QUERY_RFC3986);
            
            // Debug: Log da URL gerada
            error_log('Google Calendar URL: ' . $google_calendar_url);
            error_log('Start time: ' . $slot->starttime . ' -> ' . $startdate);
            error_log('End time: ' . $slot->endtime . ' -> ' . $enddate);
            
            // ============================================
            // MENSAGEM DE EMAIL - VERSÃO TEXTO
            // ============================================
            
            $subject = "Confirmação de Agendamento: " . $quiz->name;
            
            $messagetext = "Olá " . fullname($student) . ",\n\n";
            $messagetext .= "Você se inscreveu com sucesso na avaliação:\n\n";
            $messagetext .= "Atividade: " . $quiz->name . "\n";
            $messagetext .= "Data: " . userdate($slot->starttime, '%d/%m/%Y') . "\n";
            $messagetext .= "Horário: " . userdate($slot->starttime, '%H:%M') . " - " . userdate($slot->endtime, '%H:%M') . "\n";
            $messagetext .= "Curso: " . $course->fullname . "\n\n";
            $messagetext .= "Link do questionário: " . $quizurl->out(false) . "\n\n";
            
            // Adicionar link do Google Calendar na versão texto
            $messagetext .= "═══════════════════════════════════════\n";
            $messagetext .= "📅 ADICIONAR AO GOOGLE CALENDAR\n";
            $messagetext .= "═══════════════════════════════════════\n\n";
            $messagetext .= "Clique no link abaixo para adicionar ao seu Google Calendar:\n\n";
            $messagetext .= $google_calendar_url . "\n\n";
            $messagetext .= "Ou copie e cole o link acima no seu navegador.\n";
            $messagetext .= "═══════════════════════════════════════\n\n";
            
            $supportname = get_config('moodle', 'supportname');
            if (empty($supportname)) {
                $supportname = $SITE->fullname;
            }
            $messagetext .= "Atenciosamente,\n" . $supportname . ".";
            
            // ============================================
            // MENSAGEM DE EMAIL - VERSÃO HTML
            // ============================================
            
            $messagehtml = '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px;">
                            <h2 style="margin: 0; color: #942037; font-size: 24px;">📋 Confirmação de Agendamento</h2>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 0 30px 20px 30px;">
                            <p>Olá <strong>' . htmlspecialchars(fullname($student)) . '</strong>,</p>
                            <p>Você se inscreveu com sucesso na avaliação:</p>
                            
                            <div style="background-color: #ffeff2; padding: 20px; border-left: 4px solid #942037; margin: 20px 0;">
                                <p style="margin: 5px 0;"><strong>Atividade:</strong> ' . htmlspecialchars($quiz->name) . '</p>
                                <p style="margin: 5px 0;"><strong>Data:</strong> ' . userdate($slot->starttime, '%d/%m/%Y') . '</p>
                                <p style="margin: 5px 0;"><strong>Horário:</strong> ' . userdate($slot->starttime, '%H:%M') . ' - ' . userdate($slot->endtime, '%H:%M') . '</p>
                                <p style="margin: 5px 0;"><strong>Curso:</strong> ' . htmlspecialchars($course->fullname) . '</p>
                            </div>
                            
                            <p style="margin-top: 20px;">
                                <a href="' . htmlspecialchars($quizurl->out(false)) . '" 
                                   style="background-color: #942037; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                                    🔗 Acessar Questionário
                                </a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Google Calendar Button -->
                    <tr>
                        <td align="center" style="padding: 30px; background-color: #f9f9f9; border-top: 2px solid #e0e0e0;">
                            <p style="margin: 0 0 15px 0; font-size: 16px; color: #333;">
                                <strong>📅 Adicione este agendamento ao seu calendário:</strong>
                            </p>
                            <a href="' . htmlspecialchars($google_calendar_url) . '" 
                               target="_blank"
                               style="background-color: #942037; color: white; padding: 14px 28px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; font-size: 16px;">
                                📅 Adicionar ao Google Calendar
                            </a>
                            <p style="margin-top: 15px; font-size: 12px; color: #666;">
                                Clique no botão acima para adicionar o agendamento ao Google Calendar.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #f9f9f9; border-top: 1px solid #e0e0e0; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; font-size: 12px; color: #999;">
                                Atenciosamente,<br>' . htmlspecialchars($supportname) . '
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
            
            // ============================================
            // ENVIAR EMAIL
            // ============================================
            
            // Enviar email com versão HTML e texto
            $result = email_to_user(
                $student, 
                core_user::get_noreply_user(), 
                $subject, 
                $messagetext,  // Versão texto
                $messagehtml   // Versão HTML
            );
            
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

