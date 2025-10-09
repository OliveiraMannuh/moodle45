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
 * Quiz access rule for scheduler control.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');

/**
 * Quiz access rule for scheduler control.
 */
class quiz_access_rule extends \quiz_access_rule_base {

    /**
     * Return an appropriately configured instance of this rule, if it applies
     * to the given quiz, or null if it does not.
     *
     * @param quiz $quizobj information about the quiz in question.
     * @param int $timenow the time that should be considered as 'now'.
     * @param bool $canignoretimelimits whether the current user is exempt from
     *      time limits by the mod/quiz:ignoretimelimits capability.
     * @return quiz_access_rule_base|null the rule, if applicable, else null.
     */
    public static function make(\quiz $quizobj, $timenow, $canignoretimelimits) {
        global $DB;

        // Verificar se existe um scheduler para este quiz
        $scheduler = $DB->get_record('quizscheduler', array('quizid' => $quizobj->get_quiz()->id));
        if (!$scheduler) {
            return null;
        }

        return new self($quizobj, $timenow);
    }

    /**
     * Whether the user should be blocked from starting a new attempt or continuing
     * an attempt now.
     *
     * @return string false if access should be allowed, a message explaining the
     *      reason if access should be prevented.
     */
    public function prevent_access() {
        global $USER;

        // Permitir que professores e gerentes contornem o agendamento
        $context = $this->quizobj->get_context();
        if (has_capability('mod/quiz:manage', $context) || 
            has_capability('mod/quiz:preview', $context)) {
            return false;
        }

        $access_check = quiz_access_manager::check_quiz_access(
            $this->quizobj->get_quiz()->id, 
            $USER->id
        );

        if (!$access_check['can_access']) {
            return $access_check['message'];
        }

        return false;
    }

    /**
     * Information, such as might be shown on the quiz view page, relating to this restriction.
     * There is no obligation to return anything. If it is not appropriate to tell students
     * about this rule, then just return ''.
     *
     * @return mixed a message, or array of messages, explaining the restriction
     *         (may be '' if no message is appropriate).
     */
    public function description() {
        global $USER, $DB;

        $context = $this->quizobj->get_context();
        if (has_capability('mod/quiz:manage', $context) || 
            has_capability('mod/quiz:preview', $context)) {
            return get_string('teachercanbypass', 'mod_quizscheduler');
        }

        // Buscar o agendamento relacionado
        $scheduler = $DB->get_record('quizscheduler', array('quizid' => $this->quizobj->get_quiz()->id));
        if (!$scheduler) {
            return '';
        }

        $scheduler_url = new \moodle_url('/mod/quizscheduler/view.php', 
            array('q' => $scheduler->id));
        
        $link = \html_writer::link($scheduler_url, 
            get_string('gotoschedule', 'mod_quizscheduler'),
            array('class' => 'btn btn-primary'));

        $access_check = quiz_access_manager::check_quiz_access(
            $this->quizobj->get_quiz()->id, 
            $USER->id
        );

        if ($access_check['can_access'] && $access_check['time_left'] > 0) {
            $time_left_formatted = format_time($access_check['time_left']);
            return get_string('timeleftwarning', 'mod_quizscheduler', $time_left_formatted);
        }

        return $access_check['message'] . '<br><br>' . $link;
    }

    /**
     * Sets up the attempt (review or summary) page with any special extra
     * properties required by this rule.
     *
     * @param moodle_page $page the page object to initialise.
     */
    public function setup_attempt_page($page) {
        global $USER;

        $access_check = quiz_access_manager::check_quiz_access(
            $this->quizobj->get_quiz()->id, 
            $USER->id
        );

        if ($access_check['can_access'] && $access_check['time_left'] > 0) {
            // Add JavaScript countdown timer.
            $page->requires->js_call_amd('mod_quizscheduler/timer', 'init', array(
                'timeleft' => $access_check['time_left'],
                'warningtime' => 300 // 5 minutes warning
            ));
        }
    }
}
