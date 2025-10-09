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
 * Global interceptor for quiz access control.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler;

defined('MOODLE_INTERNAL') || die();

/**
 * Global quiz access interceptor.
 */
class quiz_interceptor {

    /**
     * Intercept quiz access and check scheduling.
     *
     * @param int $quizid Quiz ID
     * @param int $userid User ID
     * @param string $action Action being performed
     * @return array [allowed, message, redirect_url]
     */
    public static function check_quiz_access($quizid, $userid, $action = 'view') {
        global $DB;

        // Check if this quiz has a scheduler.
        $scheduler = $DB->get_record('quizscheduler', array('quizid' => $quizid));
        if (!$scheduler) {
            // No scheduler, allow normal access.
            return array(
                'allowed' => true,
                'message' => '',
                'redirect_url' => null
            );
        }

        // Get quiz context to check capabilities.
        $cm = get_coursemodule_from_instance('quiz', $quizid);
        if (!$cm) {
            return array('allowed' => false, 'message' => 'Quiz not found', 'redirect_url' => null);
        }
        
        $context = \context_module::instance($cm->id);

        // Allow teachers and managers to bypass scheduling.
        if (has_capability('mod/quiz:manage', $context) || 
            has_capability('mod/quiz:preview', $context)) {
            return array(
                'allowed' => true,
                'message' => '',
                'redirect_url' => null
            );
        }

        // Check user's booking status.
        $access_check = quiz_access_manager::check_quiz_access($quizid, $userid);

        if (!$access_check['can_access']) {
            // Find the scheduler module instance.
            $scheduler_cm = get_coursemodule_from_instance('quizscheduler', $scheduler->id);
            $redirect_url = new \moodle_url('/mod/quizscheduler/view.php', array('id' => $scheduler_cm->id));
            
            return array(
                'allowed' => false,
                'message' => $access_check['message'],
                'redirect_url' => $redirect_url
            );
        }

        return array(
            'allowed' => true,
            'message' => $access_check['message'],
            'redirect_url' => null
        );
    }

    /**
     * Initialize JavaScript timer for active sessions.
     *
     * @param int $quizid Quiz ID
     * @param int $userid User ID
     */
    public static function init_timer($quizid, $userid) {
        global $PAGE;

        $access_check = quiz_access_manager::check_quiz_access($quizid, $userid);
        
        if ($access_check['can_access'] && $access_check['time_left'] > 0) {
            $PAGE->requires->js_call_amd('mod_quizscheduler/quiz_timer', 'init', array(
                'timeleft' => $access_check['time_left'],
                'warningtime' => 300, // 5 minutes
                'quizid' => $quizid
            ));
        }
    }
}
