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
 * Privacy provider for mod_agendamento.
 *
 * @package     mod_agendamento
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_agendamento\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\transform;

class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'agendamento_bookings',
            [
                'slotid' => 'privacy:metadata:agendamento_bookings:slotid',
                'userid' => 'privacy:metadata:agendamento_bookings:userid',
                'timecreated' => 'privacy:metadata:agendamento_bookings:timecreated',
            ],
            'privacy:metadata:agendamento_bookings'
        );

        return $collection;
    }

    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        $sql = "SELECT c.id
                FROM {context} c
                INNER JOIN {course_modules} cm ON cm.id = c.instanceid AND c.contextlevel = :contextlevel
                INNER JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                INNER JOIN {agendamento} a ON a.id = cm.instance
                INNER JOIN {agendamento_slots} s ON s.agendamento = a.id
                INNER JOIN {agendamento_bookings} b ON b.slotid = s.id
                WHERE b.userid = :userid";

        $params = [
            'modname' => 'agendamento',
            'contextlevel' => CONTEXT_MODULE,
            'userid' => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $params = [
            'cmid' => $context->instanceid,
            'modname' => 'agendamento',
        ];

        $sql = "SELECT b.userid
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                JOIN {agendamento} a ON a.id = cm.instance
                JOIN {agendamento_slots} s ON s.agendamento = a.id
                JOIN {agendamento_bookings} b ON b.slotid = s.id
                WHERE cm.id = :cmid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_MODULE) {
                continue;
            }

            $cm = get_coursemodule_from_id('agendamento', $context->instanceid);
            if (!$cm) {
                continue;
            }

            $agendamento = $DB->get_record('agendamento', ['id' => $cm->instance]);
            if (!$agendamento) {
                continue;
            }

            $sql = "SELECT b.*, s.starttime, s.endtime
                    FROM {agendamento_bookings} b
                    JOIN {agendamento_slots} s ON s.id = b.slotid
                    WHERE s.agendamento = :agendamento AND b.userid = :userid
                    ORDER BY s.starttime";

            $params = ['agendamento' => $agendamento->id, 'userid' => $user->id];
            $bookings = $DB->get_records_sql($sql, $params);

            if (!empty($bookings)) {
                $data = [];
                foreach ($bookings as $booking) {
                    $data[] = [
                        'starttime' => transform::datetime($booking->starttime),
                        'endtime' => transform::datetime($booking->endtime),
                        'timecreated' => transform::datetime($booking->timecreated),
                    ];
                }

                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'mod_agendamento')],
                    (object) ['bookings' => $data]
                );
            }
        }
    }

    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id('agendamento', $context->instanceid);
        if (!$cm) {
            return;
        }

        $agendamento = $DB->get_record('agendamento', ['id' => $cm->instance]);
        if (!$agendamento) {
            return;
        }

        $slotids = $DB->get_fieldset_select('agendamento_slots', 'id', 'agendamento = :agendamento', 
                                           ['agendamento' => $agendamento->id]);

        if (!empty($slotids)) {
            list($insql, $params) = $DB->get_in_or_equal($slotids, SQL_PARAMS_NAMED);
            $DB->delete_records_select('agendamento_bookings', "slotid $insql", $params);
        }
    }

    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_MODULE) {
                continue;
            }

            $cm = get_coursemodule_from_id('agendamento', $context->instanceid);
            if (!$cm) {
                continue;
            }

            $agendamento = $DB->get_record('agendamento', ['id' => $cm->instance]);
            if (!$agendamento) {
                continue;
            }

            $slotids = $DB->get_fieldset_select('agendamento_slots', 'id', 'agendamento = :agendamento', 
                                               ['agendamento' => $agendamento->id]);

            if (!empty($slotids)) {
                list($insql, $params) = $DB->get_in_or_equal($slotids, SQL_PARAMS_NAMED);
                $params['userid'] = $userid;
                $DB->delete_records_select('agendamento_bookings', "slotid $insql AND userid = :userid", $params);
            }
        }
    }

    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id('agendamento', $context->instanceid);
        if (!$cm) {
            return;
        }

        $agendamento = $DB->get_record('agendamento', ['id' => $cm->instance]);
        if (!$agendamento) {
            return;
        }

        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        $slotids = $DB->get_fieldset_select('agendamento_slots', 'id', 'agendamento = :agendamento', 
                                           ['agendamento' => $agendamento->id]);

        if (!empty($slotids)) {
            list($slotinsql, $slotparams) = $DB->get_in_or_equal($slotids, SQL_PARAMS_NAMED, 'slot');
            list($userinsql, $userparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'user');
            
            $params = array_merge($slotparams, $userparams);
            $DB->delete_records_select('agendamento_bookings', 
                                     "slotid $slotinsql AND userid $userinsql", $params);
        }
    }
}
