<?php
namespace mod_quizscheduler;

class scheduler {
    
    /**
     * Agenda um questionário para um usuário
     */
    public static function schedule_quiz($quizid, $userid, $scheduled_time) {
        global $DB;
        
        // Validar se o horário está disponível
        if (!self::is_time_available($quizid, $scheduled_time)) {
            throw new \moodle_exception('timeslot_unavailable', 'mod_quizscheduler');
        }
        
        // Criar agendamento
        $record = new \stdClass();
        $record->quizid = $quizid;
        $record->userid = $userid;
        $record->scheduled_time = $scheduled_time;
        $record->status = 'scheduled';
        $record->created_at = time();
        
        return $DB->insert_record('quizscheduler_schedules', $record);
    }
    
    /**
     * Verifica se o usuário pode acessar o quiz no momento
     */
    public static function can_access_quiz($quizid, $userid) {
        global $DB;
        
        $schedule = $DB->get_record('quizscheduler_schedules', [
            'quizid' => $quizid,
            'userid' => $userid,
            'status' => 'scheduled'
        ]);
        
        if (!$schedule) {
            return false;
        }
        
        $current_time = time();
        $grace_period = 15 * 60; // 15 minutos de tolerância
        
        return ($current_time >= $schedule->scheduled_time && 
                $current_time <= ($schedule->scheduled_time + $grace_period));
    }
}