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
 * Scheduled task to cleanup expired bookings.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task to cleanup expired bookings.
 */
class cleanup_expired_bookings extends \core\task\scheduled_task {

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('cleanupexpiredbookings', 'mod_quizscheduler');
    }

    /**
     * Execute the task.
     */
    public function execute() {
        global $DB;

        $now = time();
        $gracetime = 30 * 60; // 30 minutes grace period after slot end time.

        mtrace('Starting cleanup of expired quiz scheduler bookings...');

        // Find expired bookings that are still marked as 'booked'.
        $sql = "SELECT b.id, b.slotid, s.endtime
                FROM {quizscheduler_bookings} b
                JOIN {quizscheduler_slots} s ON b.slotid = s.id
                WHERE b.status = 'booked'
                  AND (s.endtime + :gracetime) < :now";

        $expiredbookings = $DB->get_records_sql($sql, array(
            'gracetime' => $gracetime,
            'now' => $now
        ));

        $count = 0;
        foreach ($expiredbookings as $booking) {
            // Mark as missed.
            $booking->status = 'missed';
            $DB->update_record('quizscheduler_bookings', $booking);
            $count++;
        }

        if ($count > 0) {
            mtrace("Marked {$count} expired bookings as missed.");
        }

        // Clean up old cancelled bookings (older than 30 days).
        $oldtime = $now - (30 * 24 * 60 * 60);
        $deleted = $DB->delete_records_select('quizscheduler_bookings',
            "status = 'cancelled' AND timebooked < ?", array($oldtime));

        if ($deleted) {
            mtrace("Deleted {$deleted} old cancelled bookings.");
        }

        // Clean up empty slots that are older than 7 days.
        $sql = "DELETE FROM {quizscheduler_slots}
                WHERE endtime < :oldtime
                  AND currentbookings = 0
                  AND id NOT IN (
                      SELECT DISTINCT slotid 
                      FROM {quizscheduler_bookings}
                      WHERE slotid IS NOT NULL
                  )";

        $DB->execute($sql, array('oldtime' => $now - (7 * 24 * 60 * 60)));

        mtrace('Cleanup of expired quiz scheduler bookings completed.');
    }
}
