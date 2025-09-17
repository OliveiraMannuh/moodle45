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
 * Backup steps for mod_agendamento.
 *
 * @package     mod_agendamento
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class backup_agendamento_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {
        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $agendamento = new backup_nested_element('agendamento', ['id'], [
            'name', 'intro', 'introformat', 'timecreated', 'timemodified', 'grade', 'completionbooking'
        ]);

        $slots = new backup_nested_element('slots');

        $slot = new backup_nested_element('slot', ['id'], [
            'starttime', 'endtime', 'maxparticipants', 'description', 'timecreated'
        ]);

        $bookings = new backup_nested_element('bookings');

        $booking = new backup_nested_element('booking', ['id'], [
            'userid', 'timecreated'
        ]);

        // Build the tree.
        $agendamento->add_child($slots);
        $slots->add_child($slot);
        $slot->add_child($bookings);
        $bookings->add_child($booking);

        // Define sources.
        $agendamento->set_source_table('agendamento', ['id' => backup::VAR_ACTIVITYID]);

        $slot->set_source_table('agendamento_slots', ['agendamento' => backup::VAR_PARENTID]);

        // All the rest of elements only happen if we are including user info.
        if ($userinfo) {
            $booking->set_source_table('agendamento_bookings', ['slotid' => backup::VAR_PARENTID]);
        }

        // Define id annotations.
        $booking->annotate_ids('user', 'userid');

        // Define file annotations.
        $agendamento->annotate_files('mod_agendamento', 'intro', null);

        // Return the root element (agendamento), wrapped into standard activity structure.
        return $this->prepare_activity_structure($agendamento);
    }
}
