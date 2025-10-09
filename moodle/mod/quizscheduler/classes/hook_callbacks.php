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
 * Hook callbacks for quiz scheduler.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler;

defined('MOODLE_INTERNAL') || die();

/**
 * Hook callbacks for quiz access control.
 */
class hook_callbacks {

    /**
     * Intercept quiz access via output processing.
     */
    public static function intercept_quiz_access() {
        global $PAGE, $USER, $DB, $OUTPUT;

        // Only process quiz pages.
        if (!$PAGE->cm || $PAGE->cm->modname !== 'quiz') {
            return;
        }

        // Skip if user is not logged in.
        if (!$USER->id || isguestuser()) {
            return;
        }

        $quizid = $PAGE->cm->instance;
        
        // Check access through scheduler.
        $access_result = quiz_interceptor::check_quiz_access($quizid, $USER->id);
        
        if (!$access_result['allowed'] && $access_result['redirect_url']) {
            // Display access denied page instead of redirecting.
            self::display_access_denied_page($access_result);
            die();
        }

        // Initialize timer and interceptor if access is allowed.
        if ($access_result['allowed']) {
            quiz_interceptor::init_timer($quizid, $USER->id);
        }
        
        // Always load the interceptor JavaScript.
        $PAGE->requires->js_call_amd('mod_quizscheduler/interceptor', 'init');
    }

    /**
     * Display custom access denied page with better messaging.
     */
    private static function display_access_denied_page($access_result) {
        global $PAGE, $OUTPUT, $COURSE, $USER, $DB;

        $PAGE->set_title(get_string('accessdenied', 'mod_quizscheduler'));
        $PAGE->set_heading(get_string('accessdenied', 'mod_quizscheduler'));
        
        // Get quiz info.
        $quiz = $DB->get_record('quiz', array('id' => $PAGE->cm->instance));
        $scheduler = $DB->get_record('quizscheduler', array('quizid' => $quiz->id));
        
        echo $OUTPUT->header();
        
        // Custom access denied content.
        echo $OUTPUT->box_start('generalbox boxaligncenter');
        
        echo html_writer::tag('h2', 
            get_string('quizaccessdenied', 'mod_quizscheduler'), 
            array('class' => 'text-danger')
        );
        
        echo html_writer::tag('div', 
            html_writer::tag('i', '', array('class' => 'fa fa-exclamation-triangle fa-3x text-warning')),
            array('class' => 'text-center mb-3')
        );
        
        echo html_writer::tag('p', $access_result['message'], array('class' => 'lead text-center'));
        
        // Show user's booking status.
        $user_bookings = self::get_user_booking_info($scheduler->id, $USER->id);
        
        if (!empty($user_bookings)) {
            echo html_writer::tag('h4', get_string('yourbookings', 'mod_quizscheduler'));
            echo html_writer::start_tag('ul', array('class' => 'list-group mb-3'));
            
            foreach ($user_bookings as $booking) {
                $status_class = '';
                switch ($booking->status) {
                    case 'booked':
                        $status_class = 'list-group-item-info';
                        break;
                    case 'active':
                        $status_class = 'list-group-item-success';
                        break;
                    case 'completed':
                        $status_class = 'list-group-item-success';
                        break;
                    case 'missed':
                        $status_class = 'list-group-item-danger';
                        break;
                }
                
                echo html_writer::tag('li',
                    html_writer::tag('strong', userdate($booking->starttime, get_string('strftimedaydatetime'))) .
                    ' - ' . userdate($booking->endtime, get_string('strftimetime')) .
                    html_writer::tag('span', 
                        get_string('status_' . $booking->status, 'mod_quizscheduler'),
                        array('class' => 'badge badge-secondary float-right')
                    ),
                    array('class' => 'list-group-item ' . $status_class)
                );
            }
            
            echo html_writer::end_tag('ul');
        }
        
        // Action buttons.
        echo html_writer::start_div('text-center');
        
        if ($access_result['redirect_url']) {
            echo html_writer::link(
                $access_result['redirect_url'],
                get_string('gotoschedule', 'mod_quizscheduler'),
                array('class' => 'btn btn-primary btn-lg mr-2')
            );
        }
        
        echo html_writer::link(
            new moodle_url('/course/view.php', array('id' => $COURSE->id)),
            get_string('backtocourse', 'core'),
            array('class' => 'btn btn-secondary btn-lg')
        );
        
        echo html_writer::end_div();
        
        echo $OUTPUT->box_end();
        
        echo $OUTPUT->footer();
    }

    /**
     * Get user booking information.
     */
    private static function get_user_booking_info($schedulerid, $userid) {
        global $DB;
        
        $sql = "SELECT b.*, s.starttime, s.endtime
                FROM {quizscheduler_bookings} b
                JOIN {quizscheduler_slots} s ON b.slotid = s.id
                WHERE s.quizschedulerid = :schedulerid
                  AND b.userid = :userid
                ORDER BY s.starttime DESC";
                
        return $DB->get_records_sql($sql, array(
            'schedulerid' => $schedulerid,
            'userid' => $userid
        ));
    }
}
