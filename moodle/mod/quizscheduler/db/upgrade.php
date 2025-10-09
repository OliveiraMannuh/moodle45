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
 * Quiz scheduler upgrade script.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_quizscheduler_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2025100110) {

        // Define fields to be added to quizscheduler.
        $table = new xmldb_table('quizscheduler');
        
        $field1 = new xmldb_field('schedulestarttime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'quizid');
        $field2 = new xmldb_field('scheduleendtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'schedulestarttime');

        // Conditionally launch add fields.
        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }
        
        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        // Quizscheduler savepoint reached.
        upgrade_mod_savepoint(true, 2025100110, 'quizscheduler');
    }
    
    if ($oldversion < 2025100119) {
        // Define field maxparticipants to be added to quizscheduler_slots.
        $table = new xmldb_table('quizscheduler_slots');
        $field = new xmldb_field('maxparticipants', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1');

        // Conditionally launch add field maxparticipants.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quizscheduler savepoint reached.
        upgrade_mod_savepoint(true, 2025100119, 'quizscheduler');
    }

    return true;
}
