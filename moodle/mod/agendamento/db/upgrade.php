<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Database upgrade script for mod_agendamento.
 *
 * @package     mod_agendamento
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_agendamento_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024091601) {
        // Define field completionbooking to be added to agendamento.
        $table = new xmldb_table('agendamento');
        $field = new xmldb_field('completionbooking', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'grade');

        // Conditionally launch add field completionbooking.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Agendamento savepoint reached.
        upgrade_mod_savepoint(true, 2024091601, 'agendamento');
    }
    
    if ($oldversion < 2024091602) {
        // Remove grade field from agendamento table as it's no longer needed.
        $table = new xmldb_table('agendamento');
        $field = new xmldb_field('grade');

        // Conditionally launch drop field grade.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Remove any existing grade items for agendamento activities.
        require_once($CFG->libdir . '/gradelib.php');
        
        // Get all agendamento instances to clean up their grade items.
        $agendamentos = $DB->get_records('agendamento', null, '', 'id, course');
        foreach ($agendamentos as $agendamento) {
            // Delete grade item for this agendamento instance.
            grade_update('mod/agendamento', $agendamento->course, 'mod', 'agendamento',
                        $agendamento->id, 0, null, array('deleted' => 1));
        }

        // Agendamento savepoint reached.
        upgrade_mod_savepoint(true, 2024091602, 'agendamento');
    }
    
    if ($oldversion < 2024091603) {
        // Fix completion states for users who cancelled all their bookings
        // but still have the activity marked as complete
        
        $sql = "SELECT DISTINCT cm.id as cmid, cm.instance as agendamentoid, cmc.userid, cmc.id as completionid
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module AND m.name = 'agendamento'
                JOIN {agendamento} a ON a.id = cm.instance
                JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id
                WHERE a.completionbooking = 1
                AND cmc.completionstate = " . COMPLETION_COMPLETE . "
                AND NOT EXISTS (
                    SELECT 1 
                    FROM {agendamento_bookings} ab
                    JOIN {agendamento_slots} as2 ON ab.slotid = as2.id
                    WHERE as2.agendamento = a.id AND ab.userid = cmc.userid
                )";
        
        $incorrectcompletions = $DB->get_records_sql($sql);
        
        foreach ($incorrectcompletions as $completion) {
            // Reset completion state to incomplete
            $DB->set_field('course_modules_completion', 'completionstate', COMPLETION_INCOMPLETE, 
                          array('id' => $completion->completionid));
            $DB->set_field('course_modules_completion', 'timemodified', time(), 
                          array('id' => $completion->completionid));
        }
        
        // Also fix users who have bookings but are marked as incomplete
        $sql2 = "SELECT DISTINCT cm.id as cmid, cm.instance as agendamentoid, u.id as userid
                 FROM {course_modules} cm
                 JOIN {modules} m ON m.id = cm.module AND m.name = 'agendamento'
                 JOIN {agendamento} a ON a.id = cm.instance
                 JOIN {agendamento_slots} as1 ON as1.agendamento = a.id
                 JOIN {agendamento_bookings} ab ON ab.slotid = as1.id
                 JOIN {user} u ON u.id = ab.userid
                 LEFT JOIN {course_modules_completion} cmc ON cmc.coursemoduleid = cm.id AND cmc.userid = u.id
                 WHERE a.completionbooking = 1
                 AND (cmc.completionstate IS NULL OR cmc.completionstate != " . COMPLETION_COMPLETE . ")";
        
        $missingcompletions = $DB->get_records_sql($sql2);
        
        foreach ($missingcompletions as $missing) {
            // Check if completion record exists
            $params = array('coursemoduleid' => $missing->cmid, 'userid' => $missing->userid);
            if ($existingcompletion = $DB->get_record('course_modules_completion', $params)) {
                // Update existing record
                $existingcompletion->completionstate = COMPLETION_COMPLETE;
                $existingcompletion->timemodified = time();
                $DB->update_record('course_modules_completion', $existingcompletion);
            } else {
                // Create new completion record
                $newcompletion = new stdClass();
                $newcompletion->coursemoduleid = $missing->cmid;
                $newcompletion->userid = $missing->userid;
                $newcompletion->completionstate = COMPLETION_COMPLETE;
                $newcompletion->viewed = 1;
                $newcompletion->timemodified = time();
                $DB->insert_record('course_modules_completion', $newcompletion);
            }
        }
        
        // Agendamento savepoint reached.
        upgrade_mod_savepoint(true, 2024091603, 'agendamento');
    }
    
    return true;
}
