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
 * Restore steps for mod_agendamento.
 *
 * @package     mod_agendamento
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class restore_agendamento_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {
        $paths = [];
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('agendamento', '/activity/agendamento');
        $paths[] = new restore_path_element('agendamento_slot', '/activity/agendamento/slots/slot');
        if ($userinfo) {
            $paths[] = new restore_path_element('agendamento_booking', '/activity/agendamento/slots/slot/bookings/booking');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    protected function process_agendamento($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Handle completion booking field for older backups
        if (!isset($data->completionbooking)) {
            $data->completionbooking = 0;
        }

        // Insert the agendamento record.
        $newitemid = $DB->insert_record('agendamento', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    protected function process_agendamento_slot($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->agendamento = $this->get_new_parentid('agendamento');
        $data->starttime = $this->apply_date_offset($data->starttime);
        $data->endtime = $this->apply_date_offset($data->endtime);
        $data->timecreated = $this->apply_date_offset($data->timecreated);

        $newitemid = $DB->insert_record('agendamento_slots', $data);
        $this->set_mapping('agendamento_slot', $oldid, $newitemid);
    }

    protected function process_agendamento_booking($data) {
        global $DB;

        $data = (object)$data;

        $data->slotid = $this->get_new_parentid('agendamento_slot');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timecreated = $this->apply_date_offset($data->timecreated);

        $newitemid = $DB->insert_record('agendamento_bookings', $data);
    }

    protected function after_execute() {
        // Add agendamento related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_agendamento', 'intro', null);
    }
}
