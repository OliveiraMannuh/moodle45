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

        // Criar objeto de mensagem
        $eventdata = new \core\message\message();
        $eventdata->component = 'mod_quizscheduler';
        $eventdata->name = 'slot_booking_confirmation';
        $eventdata->userfrom = \core_user::get_noreply_user();
        $eventdata->userto = $student;
        $eventdata->subject = get_string('email_booking_subject', 'mod_quizscheduler', $a);
        $eventdata->fullmessage = get_string('email_booking_body', 'mod_quizscheduler', $a);
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml = '';
        $eventdata->smallmessage = get_string('email_booking_small', 'mod_quizscheduler', $a);
        $eventdata->notification = 1;

        // Enviar mensagem
        return message_send($eventdata);
    }
}
