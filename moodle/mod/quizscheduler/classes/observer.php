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
 * Event observers for quiz scheduler.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler;

defined('MOODLE_INTERNAL') || die();

/**
 * Event observers for quiz scheduler.
 */
class observer {

    /**
     * Observer for quiz attempt submitted event.
     */
    public static function quiz_attempt_submitted(\mod_quiz\event\attempt_submitted $event) {
        quiz_access_manager::mark_quiz_completed(
            $event->other['quizid'],
            $event->userid,
            $event->objectid
        );
    }

    /**
     * Observer for quiz attempt started event.
     */
    public static function quiz_attempt_started(\mod_quiz\event\attempt_started $event) {
        // Verify access one more time when attempt starts
        global $USER;
        
        $access_check = quiz_access_manager::check_quiz_access($event->other['quizid'], $USER->id);
        
        if (!$access_check['can_access']) {
            // Force redirect if somehow they got through
            global $DB;
            $scheduler = $DB->get_record('quizscheduler', array('quizid' => $event->other['quizid']));
            if ($scheduler) {
                $scheduler_cm = get_coursemodule_from_instance('quizscheduler', $scheduler->id);
                $redirect_url = new \moodle_url('/mod/quizscheduler/view.php', array('id' => $scheduler_cm->id));
                redirect($redirect_url, $access_check['message'], null, \core\output\notification::NOTIFY_ERROR);
                exit;
            }
        }

        // Log successful start
        $context = $event->get_context();
        $custom_event = \mod_quizscheduler\event\scheduled_quiz_started::create(array(
            'objectid' => $event->objectid,
            'context' => $context,
            'userid' => $event->userid,
            'other' => array(
                'quizid' => $event->other['quizid']
            )
        ));
        $custom_event->trigger();
    }

    /**
     * Observer for course module viewed - FORCE INTERCEPT.
     */
    public static function course_module_viewed(\core\event\course_module_viewed $event) {
        global $USER;
        
        // Only process quiz views
        if ($event->other['modulename'] !== 'quiz') {
            return;
        }

        // Skip if user is not logged in
        if (!$USER->id || isguestuser()) {
            return;
        }

        self::force_quiz_access_check($event->other['instanceid'], $USER->id);
    }

    /**
     * Force quiz access check and redirect if needed.
     */
    private static function force_quiz_access_check($quizid, $userid) {
        global $DB;

        // Check if quiz has scheduler
        $scheduler = $DB->get_record('quizscheduler', array('quizid' => $quizid));
        if (!$scheduler) {
            return;
        }

        // Get quiz CM for capability check
        $cm = get_coursemodule_from_instance('quiz', $quizid);
        if (!$cm) {
            return;
        }

        $context = \context_module::instance($cm->id);

        // Allow teachers to bypass
        if (has_capability('mod/quiz:manage', $context) || 
            has_capability('mod/quiz:preview', $context)) {
            return;
        }

        // Check access
        $access_check = quiz_access_manager::check_quiz_access($quizid, $userid);
        
        if (!$access_check['can_access']) {
            // Find scheduler CM and FORCE redirect
            $scheduler_cm = get_coursemodule_from_instance('quizscheduler', $scheduler->id);
            $redirect_url = new \moodle_url('/mod/quizscheduler/view.php', array('id' => $scheduler_cm->id));
            
            // Force immediate redirect with exit
            redirect($redirect_url, $access_check['message'], 0, \core\output\notification::NOTIFY_ERROR);
            exit; // Force stop execution
        }
    }
}
