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
 * Manager for quiz scheduler operations.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler;

defined('MOODLE_INTERNAL') || die();

/**
 * Manager class for quiz scheduler operations.
 */
class manager {

    /**
     * Cancel a booking.
     *
     * @param int $bookingid Booking ID
     * @param object $context Context object
     * @return bool Success status
     */
    public static function cancel_booking($bookingid, $context) {
        global $DB, $USER;

        try {
            // Get booking details
            $booking = $DB->get_record('quizscheduler_bookings', array('id' => $bookingid));
            if (!$booking) {
                throw new \moodle_exception('bookingnotfound', 'mod_quizscheduler');
            }

            // Get slot details for validation
            $slot = $DB->get_record('quizscheduler_slots', array('id' => $booking->slotid));
            if (!$slot) {
                throw new \moodle_exception('slotnotfound', 'mod_quizscheduler');
            }

            // Check if user can cancel this booking
            if ($booking->userid != $USER->id && !has_capability('mod/quizscheduler:manageslots', $context)) {
                throw new \moodle_exception('nopermissiontocancelbooking', 'mod_quizscheduler');
            }

            // Check if booking can still be cancelled (not too late)
            $now = time();
            $cancel_deadline = $slot->starttime - (30 * 60); // 30 minutes before start
            
            if ($now > $cancel_deadline && !has_capability('mod/quizscheduler:manageslots', $context)) {
                throw new \moodle_exception('cancellationtoolate', 'mod_quizscheduler');
            }

            // Delete the booking
            $DB->delete_records('quizscheduler_bookings', array('id' => $bookingid));

            // Trigger booking cancelled event
            $event = \mod_quizscheduler\event\booking_cancelled::create(array(
                'objectid' => $bookingid,
                'context' => $context,
                'userid' => $USER->id,
                'other' => array(
                    'slotid' => $booking->slotid,
                    'originaluserid' => $booking->userid
                )
            ));
            $event->trigger();

            return true;

        } catch (\Exception $e) {
            // Log the error for debugging
            error_log('Quiz Scheduler - Cancel booking error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a new booking.
     *
     * @param int $slotid Slot ID
     * @param int $userid User ID
     * @param object $context Context object
     * @return int Booking ID
     */
    public static function create_booking($slotid, $userid, $context) {
        global $DB;

        try {
            // Verificar se o slot existe
            $slot = $DB->get_record('quizscheduler_slots', array('id' => $slotid));
            if (!$slot) {
                throw new \moodle_exception('slotnotfound', 'mod_quizscheduler');
            }

            // Verificar se usuário já tem booking ATIVO para este agendador
            $existing_booking = $DB->get_record_sql(
                "SELECT b.* FROM {quizscheduler_bookings} b
                 JOIN {quizscheduler_slots} s ON b.slotid = s.id
                 WHERE s.quizschedulerid = ? AND b.userid = ? AND b.status = 'booked'",
                array($slot->quizschedulerid, $userid)
            );

            if ($existing_booking) {
                throw new \moodle_exception('alreadybooked', 'mod_quizscheduler');
            }

            // Contar bookings ativos no slot
            $active_bookings = $DB->count_records('quizscheduler_bookings', 
                array('slotid' => $slotid, 'status' => 'booked')
            );

            if ($active_bookings >= $slot->maxparticipants) {
                throw new \moodle_exception('slotfull', 'mod_quizscheduler');
            }

            // Criar booking
            $booking = new \stdClass();
            $booking->slotid = $slotid;
            $booking->userid = $userid;
            $booking->status = 'booked';
            $booking->timebooked = time();
            $booking->timestarted = null;
            $booking->timefinished = null;

            $bookingid = $DB->insert_record('quizscheduler_bookings', $booking);

            // Trigger event
            $event = \mod_quizscheduler\event\booking_created::create(array(
                'objectid' => $bookingid,
                'context' => $context,
                'userid' => $userid,
                'other' => array(
                    'slotid' => $slotid
                )
            ));
            $event->trigger();

            return $bookingid;

        } catch (\Exception $e) {
            error_log('Quiz Scheduler - Create booking error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get user's bookings for a scheduler - CORREÇÃO: Usar nomes corretos das colunas.
     *
     * @param int $schedulerid Scheduler ID
     * @param int $userid User ID
     * @return array Bookings
     */
    public static function get_user_bookings($schedulerid, $userid) {
        global $DB;

        $sql = "SELECT b.id, b.slotid, b.userid, b.status, b.timebooked, b.timestarted, b.timefinished,
                   s.starttime, s.endtime
            FROM {quizscheduler_bookings} b
            JOIN {quizscheduler_slots} s ON b.slotid = s.id
            WHERE s.quizschedulerid = :schedulerid
              AND b.userid = :userid
              AND b.status != 'cancelled'
            ORDER BY s.starttime DESC";

        return $DB->get_records_sql($sql, array(
            'schedulerid' => $schedulerid,
            'userid' => $userid
        ));
    }

    /**
     * Get available slots for booking - VERSÃO SIMPLIFICADA.
     *
     * @param int $schedulerid Scheduler ID
     * @return array Available slots
     */
    public static function get_available_slots($schedulerid) {
        global $DB;

        $now = time();
        
        // Buscar TODOS os slots futuros (sem filtro de disponibilidade)
        $slots = $DB->get_records_select('quizscheduler_slots', 
            'quizschedulerid = ? AND starttime > ?',
            array($schedulerid, $now),
            'starttime ASC'
        );

        if (empty($slots)) {
            return array();
        }

        // Para cada slot, adicionar contagem de bookings
        foreach ($slots as $slot) {
            // Contar bookings não cancelados
            $current_bookings = $DB->count_records_select(
                'quizscheduler_bookings',
                'slotid = ? AND status NOT IN (?)',
                array($slot->id, 'cancelled')
            );
            
            $slot->current_bookings = $current_bookings;
        }

        return $slots; // Retornar TODOS os slots, não filtrar aqui
    }

    /**
     * Get all slots for a scheduler with booking counts.
     *
     * @param int $schedulerid Scheduler ID
     * @return array All slots with booking information
     */
    public static function get_all_slots($schedulerid) {
        global $DB;

        $slots = $DB->get_records('quizscheduler_slots', 
            array('quizschedulerid' => $schedulerid), 
            'starttime ASC'
        );

        if (empty($slots)) {
            return array();
        }

        foreach ($slots as $slot) {
            // Count total bookings for each slot
            $slot->total_bookings = $DB->count_records('quizscheduler_bookings', array(
                'slotid' => $slot->id
            ));
            
            // Count active bookings (booked + active status)
            $booked_count = $DB->count_records('quizscheduler_bookings', array(
                'slotid' => $slot->id,
                'status' => 'booked'
            ));
            
            $active_count = $DB->count_records('quizscheduler_bookings', array(
                'slotid' => $slot->id,
                'status' => 'active'
            ));
            
            $slot->active_bookings = $booked_count + $active_count;
        }

        return $slots;
    }

    /**
     * Generate time slots automatically.
     *
     * @param int $schedulerid Scheduler ID
     * @param int $starttime Start timestamp
     * @param int $endtime End timestamp
     * @param int $duration Duration in minutes
     * @param int $maxparticipants Maximum participants per slot
     * @return int Number of slots created
     */
    public static function generate_slots($schedulerid, $starttime, $endtime, $duration, $maxparticipants = 1) {
        global $DB;

        $count = 0;
        $duration_seconds = $duration * 60; // Convert minutes to seconds
        $current_time = $starttime;

        try {
            while ($current_time + $duration_seconds <= $endtime) {
                // Check if slot already exists
                $existing = $DB->get_record('quizscheduler_slots', array(
                    'quizschedulerid' => $schedulerid,
                    'starttime' => $current_time
                ));

                if (!$existing) {
                    $slot = new \stdClass();
                    $slot->quizschedulerid = $schedulerid;
                    $slot->starttime = $current_time;
                    $slot->endtime = $current_time + $duration_seconds;
                    $slot->maxparticipants = $maxparticipants;

                    $DB->insert_record('quizscheduler_slots', $slot);
                    $count++;
                }

                // Move to next slot time
                $current_time += $duration_seconds;
            }

            return $count;

        } catch (\Exception $e) {
            error_log('Quiz Scheduler - Generate slots error: ' . $e->getMessage());
            throw new \moodle_exception('error:cannotgenerateslots', 'mod_quizscheduler');
        }
    }

    /**
     * Create a single time slot.
     *
     * @param int $schedulerid Scheduler ID
     * @param int $starttime Start timestamp
     * @param int $endtime End timestamp
     * @param int $maxparticipants Maximum participants
     * @return int Slot ID
     */
    public static function create_slot($schedulerid, $starttime, $endtime, $maxparticipants = 1) {
        global $DB;

        try {
            // Validate times
            if ($starttime >= $endtime) {
                throw new \moodle_exception('error:invalidtimes', 'mod_quizscheduler');
            }

            // Check for overlapping slots
            $overlapping = $DB->get_records_sql(
                "SELECT id FROM {quizscheduler_slots} 
                 WHERE quizschedulerid = ? 
                 AND ((starttime <= ? AND endtime > ?) OR (starttime < ? AND endtime >= ?))",
                array($schedulerid, $starttime, $starttime, $endtime, $endtime)
            );

            if (!empty($overlapping)) {
                throw new \moodle_exception('error:overlappingslots', 'mod_quizscheduler');
            }

            $slot = new \stdClass();
            $slot->quizschedulerid = $schedulerid;
            $slot->starttime = $starttime;
            $slot->endtime = $endtime;
            $slot->maxparticipants = $maxparticipants;

            return $DB->insert_record('quizscheduler_slots', $slot);

        } catch (\Exception $e) {
            error_log('Quiz Scheduler - Create slot error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Deleta um slot de agendamento
     * 
     * @param int $slotid ID do slot a ser deletado
     * @return bool True se deletado com sucesso, false caso contrário
     */
    public static function delete_slot($slotid) {
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
}
