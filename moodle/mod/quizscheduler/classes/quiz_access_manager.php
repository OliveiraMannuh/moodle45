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
 * Quiz access manager with strict scheduling control.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler;

defined('MOODLE_INTERNAL') || die();

/**
 * Strict quiz access manager.
 */
class quiz_access_manager {

    /**
     * Check if user can access quiz - STRICT MODE.
     *
     * @param int $quizid Quiz ID
     * @param int $userid User ID
     * @return array [can_access, message, time_left]
     */
    public static function check_quiz_access($quizid, $userid) {
        global $DB;

        // Get quiz scheduler instance
        $scheduler = $DB->get_record('quizscheduler', array('quizid' => $quizid));
        if (!$scheduler) {
            return array(
                'can_access' => true,
                'message' => '',
                'time_left' => 0
            );
        }

        $now = time();
        $grace_time = 5 * 60; // Only 5 minutes grace period

        // Find EXACT booking for current time - be very strict
        $sql = "SELECT b.*, s.starttime, s.endtime, s.id as slotid
                FROM {quizscheduler_bookings} b
                JOIN {quizscheduler_slots} s ON b.slotid = s.id
                WHERE s.quizschedulerid = :schedulerid
                  AND b.userid = :userid
                  AND b.status IN ('booked', 'active')
                  AND :now >= s.starttime
                  AND :now2 <= (s.endtime + :gracetime)
                ORDER BY s.starttime DESC
                LIMIT 1";

        $params = array(
            'schedulerid' => $scheduler->id,
            'userid' => $userid,
            'now' => $now,
            'now2' => $now,
            'gracetime' => $grace_time
        );

        $booking = $DB->get_record_sql($sql, $params);

        if ($booking) {
            // User has valid booking within time window
            $time_left = ($booking->endtime + $grace_time) - $now;
            
            // Update status if needed
            if ($booking->status === 'booked') {
                $booking->status = 'active';
                $booking->timestarted = $now;
                $DB->update_record('quizscheduler_bookings', $booking);
                
                // Log the start
                error_log("Quiz Scheduler: User {$userid} started quiz {$quizid} at " . date('Y-m-d H:i:s', $now));
            }

            return array(
                'can_access' => true,
                'message' => get_string('accessgranted', 'mod_quizscheduler', 
                    userdate($booking->endtime + $grace_time, '%d/%m/%Y às %H:%M')),
                'time_left' => $time_left
            );
        }

        // No valid booking found - check for future bookings
        $next_booking = $DB->get_record_sql(
            "SELECT b.*, s.starttime, s.endtime
             FROM {quizscheduler_bookings} b
             JOIN {quizscheduler_slots} s ON b.slotid = s.id
             WHERE s.quizschedulerid = :schedulerid
               AND b.userid = :userid
               AND b.status = 'booked'
               AND s.starttime > :now
             ORDER BY s.starttime ASC
             LIMIT 1",
            array('schedulerid' => $scheduler->id, 'userid' => $userid, 'now' => $now)
        );

        if ($next_booking) {
            $time_until = $next_booking->starttime - $now;
            $time_until_formatted = format_time($time_until);
            
            $message = "Acesso negado. Você tem um agendamento para " . 
                userdate($next_booking->starttime, '%d/%m/%Y às %H:%M') . 
                " (em {$time_until_formatted}). Aguarde até o horário marcado.";
        } else {
            // Check if user has any bookings at all
            $any_booking = $DB->record_exists_sql(
                "SELECT 1 FROM {quizscheduler_bookings} b
                 JOIN {quizscheduler_slots} s ON b.slotid = s.id
                 WHERE s.quizschedulerid = ? AND b.userid = ?",
                array($scheduler->id, $userid)
            );
            
            if ($any_booking) {
                $message = "Acesso negado. Você não possui agendamentos válidos para este horário. " .
                    "Verifique seus agendamentos ou faça um novo agendamento.";
            } else {
                $message = "Acesso negado. Você precisa agendar um horário antes de acessar este questionário. " .
                    "Clique em 'Ir para Agendamento' para selecionar um horário disponível.";
            }
        }

        // Log denied access
        error_log("Quiz Scheduler: Access DENIED for user {$userid} to quiz {$quizid} at " . date('Y-m-d H:i:s', $now));

        return array(
            'can_access' => false,
            'message' => $message,
            'time_left' => 0
        );
    }

    /**
     * Mark quiz as completed.
     */
    public static function mark_quiz_completed($quizid, $userid, $attemptid) {
        global $DB;

        $scheduler = $DB->get_record('quizscheduler', array('quizid' => $quizid));
        if (!$scheduler) {
            return;
        }

        // Find active booking
        $sql = "SELECT b.*
                FROM {quizscheduler_bookings} b
                JOIN {quizscheduler_slots} s ON b.slotid = s.id
                WHERE s.quizschedulerid = :schedulerid
                  AND b.userid = :userid
                  AND b.status IN ('booked', 'active')
                ORDER BY b.timestarted DESC
                LIMIT 1";

        $booking = $DB->get_record_sql($sql, array(
            'schedulerid' => $scheduler->id,
            'userid' => $userid
        ));

        if ($booking) {
            $booking->status = 'completed';
            $booking->timefinished = time();
            $DB->update_record('quizscheduler_bookings', $booking);
            
            error_log("Quiz Scheduler: User {$userid} completed quiz {$quizid} at " . date('Y-m-d H:i:s'));
        }
    }

    /**
     * Mark missed bookings more aggressively.
     */
    public static function mark_missed_bookings() {
        global $DB;

        $now = time();
        $grace_time = 5 * 60; // 5 minutes grace

        // Mark as missed - be more aggressive
        $sql = "UPDATE {quizscheduler_bookings} 
                SET status = 'missed'
                WHERE id IN (
                    SELECT b.id 
                    FROM {quizscheduler_bookings} b
                    JOIN {quizscheduler_slots} s ON b.slotid = s.id
                    WHERE b.status = 'booked'
                      AND (s.endtime + :gracetime) < :now
                )";

        $result = $DB->execute($sql, array('gracetime' => $grace_time, 'now' => $now));
        
        if ($result) {
            error_log("Quiz Scheduler: Marked missed bookings at " . date('Y-m-d H:i:s'));
        }
    }
}
