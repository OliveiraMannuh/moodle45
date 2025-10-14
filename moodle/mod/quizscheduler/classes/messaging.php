<?php
defined('MOODLE_INTERNAL') || die();

class quizscheduler_messaging {

    public static function send_booking_confirmation($student, $slot, $quiz, $course) {
        global $CFG, $SITE;

        // Verificações básicas
        if (!$student || !$student->email) {
            return false;
        }

        // Preparar dados da mensagem
        $a = new stdClass();
        $a->studentname = fullname($student);
        $a->quizname = format_string($quiz->name);
        $a->coursename = format_string($course->fullname);
        $a->date = userdate($slot->starttime, get_string('strftimedaydate'));
        $a->starttime = userdate($slot->starttime, get_string('strftimetime'));
        $a->endtime = userdate($slot->endtime, get_string('strftimetime'));
        $a->sitename = format_string($SITE->fullname);
        
        // Adicionar link do Google Calendar
        $a->googlecalendarlink = self::generate_google_calendar_link($slot, $quiz, $course);

        // Criar mensagem de texto com link destacado
        $messagetext = get_string('email_booking_body', 'mod_quizscheduler', $a);
        $messagetext .= "\n\n";
        $messagetext .= "═══════════════════════════════════════\n";
        $messagetext .= "📅 ADICIONAR AO GOOGLE CALENDAR\n";
        $messagetext .= "═══════════════════════════════════════\n\n";
        $messagetext .= "Clique no link abaixo para adicionar este agendamento ao seu Google Calendar:\n\n";
        $messagetext .= $a->googlecalendarlink;
        $messagetext .= "\n\n";
        $messagetext .= "Ou copie e cole o link acima no seu navegador.\n";
        $messagetext .= "═══════════════════════════════════════\n";

        // Criar mensagem HTML
        $subject = get_string('email_booking_subject', 'mod_quizscheduler', $a);
        $messagehtml = self::generate_html_message($a);

        // Gerar arquivo iCalendar
        $icalfile = self::create_ical_file($slot, $quiz, $course);

        // Tentar enviar com PHPMailer primeiro
        try {
            $mail = get_mailer();
            
            // Limpar destinatários anteriores
            $mail->clearAddresses();
            $mail->clearAttachments();
            
            // Configurar remetente
            $fromaddress = !empty($CFG->noreplyaddress) ? $CFG->noreplyaddress : 'noreply@' . parse_url($CFG->wwwroot, PHP_URL_HOST);
            $mail->setFrom($fromaddress, $SITE->fullname);
            
            // Configurar destinatário
            $mail->addAddress($student->email, fullname($student));
            
            // Configurar assunto
            $mail->Subject = $subject;
            
            // Configurar corpo do email
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Body = $messagehtml;
            $mail->AltBody = $messagetext;
            
            // Anexar arquivo iCalendar
            if ($icalfile && file_exists($icalfile)) {
                $mail->addAttachment($icalfile, 'quiz-booking.ics', 'base64', 'text/calendar; charset=utf-8; method=REQUEST');
            }
            
            // Enviar email
            $mailresult = $mail->send();
            
            // Limpar arquivo temporário
            if ($icalfile && file_exists($icalfile)) {
                @unlink($icalfile);
            }
            
            // Enviar notificação interna
            self::send_internal_notification($student, $subject, $messagetext, $messagehtml);
            
            return $mailresult;
            
        } catch (Exception $e) {
            debugging('Erro ao enviar email via PHPMailer: ' . $e->getMessage(), DEBUG_DEVELOPER);
            
            // Fallback: usar email_to_user
            try {
                $from = \core_user::get_noreply_user();
                
                $result = email_to_user(
                    $student,
                    $from,
                    $subject,
                    $messagetext,
                    $messagehtml,
                    $icalfile && file_exists($icalfile) ? $icalfile : '',
                    $icalfile && file_exists($icalfile) ? 'quiz-booking.ics' : ''
                );
                
                // Limpar arquivo temporário
                if ($icalfile && file_exists($icalfile)) {
                    @unlink($icalfile);
                }
                
                // Enviar notificação interna
                self::send_internal_notification($student, $subject, $messagetext, $messagehtml);
                
                return $result;
                
            } catch (Exception $e2) {
                debugging('Erro ao enviar email via email_to_user: ' . $e2->getMessage(), DEBUG_DEVELOPER);
                
                // Limpar arquivo temporário
                if ($icalfile && file_exists($icalfile)) {
                    @unlink($icalfile);
                }
                
                return false;
            }
        }
    }

    /**
     * Envia notificação interna do Moodle
     */
    private static function send_internal_notification($student, $subject, $messagetext, $messagehtml) {
        try {
            $eventdata = new \core\message\message();
            $eventdata->component = 'mod_quizscheduler';
            $eventdata->name = 'slot_booking_confirmation';
            $eventdata->userfrom = \core_user::get_noreply_user();
            $eventdata->userto = $student;
            $eventdata->subject = $subject;
            $eventdata->fullmessage = $messagetext;
            $eventdata->fullmessageformat = FORMAT_HTML;
            $eventdata->fullmessagehtml = $messagehtml;
            $eventdata->smallmessage = substr($messagetext, 0, 100);
            $eventdata->notification = 1;
            
            message_send($eventdata);
        } catch (Exception $e) {
            debugging('Erro ao enviar notificação interna: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }

    /**
     * Gera link para adicionar evento ao Google Calendar
     */
    private static function generate_google_calendar_link($slot, $quiz, $course) {
        // Formato de data do Google Calendar: YYYYMMDDTHHmmssZ
        $startdate = gmdate('Ymd\THis\Z', $slot->starttime);
        $enddate = gmdate('Ymd\THis\Z', $slot->endtime);
        
        $title = $quiz->name . ' - ' . $course->fullname;
        $details = 'Quiz: ' . $quiz->name . "\n" . 'Curso: ' . $course->fullname;
        
        $params = [
            'action' => 'TEMPLATE',
            'text' => $title,
            'dates' => $startdate . '/' . $enddate,
            'details' => $details,
            'trp' => 'false'
        ];
        
        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    /**
     * Gera mensagem HTML com link do Google Calendar
     */
    private static function generate_html_message($a) {
        $body = get_string('email_booking_body', 'mod_quizscheduler', $a);
        $body_html = nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));
        
        $gcal_text = htmlspecialchars(get_string('add_to_google_calendar', 'mod_quizscheduler'), ENT_QUOTES, 'UTF-8');
        $gcal_link = htmlspecialchars($a->googlecalendarlink, ENT_QUOTES, 'UTF-8');
        $ical_note = htmlspecialchars(get_string('ical_attachment_note', 'mod_quizscheduler'), ENT_QUOTES, 'UTF-8');
        $sitename = htmlspecialchars($a->sitename, ENT_QUOTES, 'UTF-8');
        
        return '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Confirmação de Agendamento</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px;">
                            <h2 style="margin: 0; color: #0066cc; font-size: 24px;">📋 Confirmação de Agendamento</h2>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 0 30px 20px 30px;">
                            <div style="background-color: #f0f7ff; padding: 20px; border-left: 4px solid #0066cc; margin-bottom: 20px;">
                                ' . $body_html . '
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Google Calendar Button -->
                    <tr>
                        <td align="center" style="padding: 0 30px 30px 30px;">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" style="background-color: #4285f4; border-radius: 5px;">
                                        <a href="' . $gcal_link . '" 
                                           target="_blank"
                                           style="display: inline-block; padding: 16px 32px; color: #ffffff; text-decoration: none; font-weight: bold; font-size: 16px;">
                                            📅 ' . $gcal_text . '
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin-top: 15px; font-size: 12px; color: #666;">
                                Clique no botão acima para adicionar ao Google Calendar
                            </p>
                        </td>
                    </tr>
                    
                    <!-- iCal Note -->
                    <tr>
                        <td style="padding: 20px 30px 30px 30px; border-top: 2px solid #e0e0e0;">
                            <p style="margin: 0; font-size: 13px; color: #666; line-height: 1.5;">
                                <strong>💡 Dica:</strong> ' . $ical_note . '
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #f9f9f9; border-top: 1px solid #e0e0e0; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; font-size: 12px; color: #999;">' . $sitename . '</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * Cria arquivo iCalendar (.ics) com o evento
     * @return string|false Caminho do arquivo criado ou false em caso de erro
     */
    private static function create_ical_file($slot, $quiz, $course) {
        global $CFG;

        try {
            // Criar diretório temporário se não existir
            $tempdir = $CFG->tempdir . '/quizscheduler';
            if (!file_exists($tempdir)) {
                mkdir($tempdir, 0777, true);
            }

            // Nome único para o arquivo
            $filename = $tempdir . '/quiz_' . $slot->id . '_' . time() . '.ics';

            // Gerar conteúdo iCalendar
            $ical = self::generate_ical_content($slot, $quiz, $course);

            // Escrever arquivo
            if (file_put_contents($filename, $ical) !== false) {
                return $filename;
            }

            return false;

        } catch (Exception $e) {
            debugging('Erro ao criar arquivo iCalendar: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return false;
        }
    }

    /**
     * Gera conteúdo do arquivo iCalendar
     * @return string Conteúdo do arquivo .ics
     */
    private static function generate_ical_content($slot, $quiz, $course) {
        global $CFG;

        // Gerar UID único para o evento
        $uid = 'quizscheduler-' . $slot->id . '-' . time() . '@' . parse_url($CFG->wwwroot, PHP_URL_HOST);

        // Formatar datas no formato iCalendar
        $dtstart = gmdate('Ymd\THis\Z', $slot->starttime);
        $dtend = gmdate('Ymd\THis\Z', $slot->endtime);
        $dtstamp = gmdate('Ymd\THis\Z');

        // Escapar caracteres especiais
        $summary = self::ical_escape($quiz->name);
        $description = self::ical_escape(
            get_string('quiz_scheduled_details', 'mod_quizscheduler', $quiz->name) . "\n" .
            get_string('course', 'core') . ': ' . $course->fullname
        );
        $location = self::ical_escape($course->fullname);

        // URL do quiz (se disponível)
        $url = '';
        if (isset($CFG->wwwroot) && isset($quiz->cmid)) {
            $url = $CFG->wwwroot . '/mod/quiz/view.php?id=' . $quiz->cmid;
        }

        // Montar conteúdo iCalendar
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Moodle//Quiz Scheduler//EN\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:REQUEST\r\n";
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:{$uid}\r\n";
        $ical .= "DTSTAMP:{$dtstamp}\r\n";
        $ical .= "DTSTART:{$dtstart}\r\n";
        $ical .= "DTEND:{$dtend}\r\n";
        $ical .= "SUMMARY:{$summary}\r\n";
        $ical .= "DESCRIPTION:{$description}\r\n";
        $ical .= "LOCATION:{$location}\r\n";
        
        if (!empty($url)) {
            $ical .= "URL:{$url}\r\n";
        }
        
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "SEQUENCE:0\r\n";
        $ical .= "PRIORITY:5\r\n";
        
        // Adicionar alarme (lembrete 1 hora antes)
        $ical .= "BEGIN:VALARM\r\n";
        $ical .= "TRIGGER:-PT1H\r\n";
        $ical .= "ACTION:DISPLAY\r\n";
        $ical .= "DESCRIPTION:Lembrete: {$summary}\r\n";
        $ical .= "END:VALARM\r\n";
        
        $ical .= "END:VEVENT\r\n";
        $ical .= "END:VCALENDAR\r\n";

        return $ical;
    }

    /**
     * Escapa caracteres especiais para formato iCalendar
     * @param string $text Texto a ser escapado
     * @return string Texto escapado
     */
    private static function ical_escape($text) {
        // Remover tags HTML
        $text = strip_tags($text);
        
        // Escapar caracteres especiais
        $text = str_replace(['\\', ',', ';', "\n"], ['\\\\', '\\,', '\\;', '\\n'], $text);
        
        // Limitar tamanho da linha (75 caracteres)
        $text = wordwrap($text, 73, "\r\n ", true);
        
        return $text;
    }
}
