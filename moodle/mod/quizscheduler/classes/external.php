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
 * External API for quiz scheduler.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;

/**
 * External API for quiz scheduler.
 */
class external extends external_api {

    /**
     * Parameters for check_access function.
     */
    public static function check_access_parameters() {
        return new external_function_parameters(
            array(
                'quizid' => new external_value(PARAM_INT, 'Quiz ID')
            )
        );
    }

    /**
     * Check quiz access for current user.
     */
    public static function check_access($quizid) {
        global $USER;

        $params = self::validate_parameters(self::check_access_parameters(), array(
            'quizid' => $quizid
        ));

        // Verify user is logged in.
        require_login();

        $result = quiz_interceptor::check_quiz_access($params['quizid'], $USER->id);
        
        // Add time left information.
        $access_info = quiz_access_manager::check_quiz_access($params['quizid'], $USER->id);
        
        return array(
            'allowed' => $result['allowed'],
            'message' => $result['message'],
            'redirect_url' => $result['redirect_url'] ? $result['redirect_url']->out() : '',
            'timeleft' => $access_info['time_left'] ?? 0
        );
    }

    /**
     * Return structure for check_access function.
     */
    public static function check_access_returns() {
        return new external_single_structure(
            array(
                'allowed' => new external_value(PARAM_BOOL, 'Whether access is allowed'),
                'message' => new external_value(PARAM_TEXT, 'Access message'),
                'redirect_url' => new external_value(PARAM_URL, 'Redirect URL if access denied'),
                'timeleft' => new external_value(PARAM_INT, 'Time left in seconds')
            )
        );
    }

    /**
     * Parameters for get_quiz_id function.
     */
    public static function get_quiz_id_parameters() {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course Module ID')
            )
        );
    }

    /**
     * Get quiz ID from course module ID.
     */
    public static function get_quiz_id($cmid) {
        global $DB;

        $params = self::validate_parameters(self::get_quiz_id_parameters(), array(
            'cmid' => $cmid
        ));

        require_login();

        $cm = get_coursemodule_from_id('quiz', $params['cmid'], 0, false, IGNORE_MISSING);
        
        return array(
            'quizid' => $cm ? $cm->instance : 0
        );
    }

    /**
     * Return structure for get_quiz_id function.
     */
    public static function get_quiz_id_returns() {
        return new external_single_structure(
            array(
                'quizid' => new external_value(PARAM_INT, 'Quiz ID')
            )
        );
    }
}
