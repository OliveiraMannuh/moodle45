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
 * Global interceptor that runs before any quiz processing.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler;

defined('MOODLE_INTERNAL') || die();

/**
 * Global interceptor for quiz access.
 */
class global_interceptor {

    /**
     * Check if current request is for a quiz and intercept if needed.
     */
    public static function intercept_quiz_requests() {
        global $USER, $DB, $CFG;

        // Skip if not logged in or if we're already in scheduler
        if (!$USER->id || isguestuser() || strpos($_SERVER['REQUEST_URI'], '/mod/quizscheduler/') !== false) {
            return;
        }

        // Check if this is a quiz-related request
        $quiz_id = self::extract_quiz_id_from_request();
        if (!$quiz_id) {
            return;
        }

        // Check if this quiz has a scheduler
        $scheduler = $DB->get_record('quizscheduler', array('quizid' => $quiz_id));
        if (!$scheduler) {
            return;
        }

        // Get quiz CM for capability check
        $cm = get_coursemodule_from_instance('quiz', $quiz_id);
        if (!$cm) {
            return;
        }

        $context = \context_module::instance($cm->id);

        // Allow teachers and managers to bypass
        if (has_capability('mod/quiz:manage', $context) || 
            has_capability('mod/quiz:preview', $context)) {
            return;
        }

        // Check user's access
        $access_check = quiz_access_manager::check_quiz_access($quiz_id, $USER->id);
        
        if (!$access_check['can_access']) {
            // Find scheduler CM for redirect
            $scheduler_cm = get_coursemodule_from_instance('quizscheduler', $scheduler->id);
            $redirect_url = new \moodle_url('/mod/quizscheduler/view.php', array('id' => $scheduler_cm->id));
            
            // Store message and redirect
            \core\notification::add($access_check['message'], \core\output\notification::NOTIFY_ERROR);
            redirect($redirect_url, $access_check['message'], null, \core\output\notification::NOTIFY_ERROR);
            exit;
        }
    }

    /**
     * Extract quiz ID from current request.
     *
     * @return int|null Quiz ID if found, null otherwise
     */
    private static function extract_quiz_id_from_request() {
        global $DB;

        // Direct quiz ID parameter
        $quizid = optional_param('quizid', 0, PARAM_INT);
        if ($quizid) {
            return $quizid;
        }

        // Course module ID parameter
        $id = optional_param('id', 0, PARAM_INT);
        if ($id) {
            $cm = get_coursemodule_from_id('quiz', $id, 0, false, IGNORE_MISSING);
            if ($cm) {
                return $cm->instance;
            }
        }

        // Attempt ID parameter
        $attemptid = optional_param('attempt', 0, PARAM_INT);
        if ($attemptid) {
            $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid), 'quiz');
            if ($attempt) {
                return $attempt->quiz;
            }
        }

        // Check URL path for quiz module
        if (strpos($_SERVER['REQUEST_URI'], '/mod/quiz/') !== false) {
            // Try to extract from URL patterns
            if (preg_match('/[?&]id=(\d+)/', $_SERVER['REQUEST_URI'], $matches)) {
                $cm = get_coursemodule_from_id('quiz', $matches[1], 0, false, IGNORE_MISSING);
                if ($cm) {
                    return $cm->instance;
                }
            }
        }

        return null;
    }
}
