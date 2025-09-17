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
 * Library of interface functions and constants.
 *
 * @package     mod_agendamento
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Supported features by agendamento module.
 */
function agendamento_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false; // Removida opção de notas
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        default:
            return null;
    }
}

/**
 * Add agendamento instance.
 */
function agendamento_add_instance($agendamento, $mform = null) {
    global $DB;
    
    $agendamento->timecreated = time();
    $agendamento->timemodified = time();
    
    // Set default completion requirement if not set
    if (!isset($agendamento->completionbooking)) {
        $agendamento->completionbooking = 0;
    }
    
    $agendamento->id = $DB->insert_record('agendamento', $agendamento);
    
    return $agendamento->id;
}

/**
 * Update agendamento instance.
 */
function agendamento_update_instance($agendamento, $mform = null) {
    global $DB;
    
    $agendamento->timemodified = time();
    $agendamento->id = $agendamento->instance;
    
    // Set default completion requirement if not set
    if (!isset($agendamento->completionbooking)) {
        $agendamento->completionbooking = 0;
    }
    
    $DB->update_record('agendamento', $agendamento);
    
    return true;
}

/**
 * Delete agendamento instance.
 */
function agendamento_delete_instance($id) {
    global $DB;
    
    if (!$agendamento = $DB->get_record('agendamento', array('id' => $id))) {
        return false;
    }
    
    // Delete bookings first.
    $slots = $DB->get_records('agendamento_slots', array('agendamento' => $id));
    foreach ($slots as $slot) {
        $DB->delete_records('agendamento_bookings', array('slotid' => $slot->id));
    }
    
    // Delete slots.
    $DB->delete_records('agendamento_slots', array('agendamento' => $id));
    
    // Delete the instance.
    $DB->delete_records('agendamento', array('id' => $id));
    
    return true;
}

/**
 * Get completion state.
 * This function is called to determine if the activity should be marked as complete.
 */
function agendamento_get_completion_state($course, $cm, $userid, $type) {
    global $DB;
    
    // Get agendamento instance.
    $agendamento = $DB->get_record('agendamento', array('id' => $cm->instance), '*', MUST_EXIST);
    
    // If completion booking is not required, return the default type
    if (empty($agendamento->completionbooking)) {
        return $type;
    }
    
    // Check if user has any active bookings for this agendamento
    $sql = "SELECT COUNT(b.id)
            FROM {agendamento_bookings} b
            JOIN {agendamento_slots} s ON b.slotid = s.id
            WHERE s.agendamento = ? AND b.userid = ?";

    $bookingcount = $DB->count_records_sql($sql, array($agendamento->id, $userid));
    
    // Return true if user has at least one booking, false otherwise
    return $bookingcount > 0;
}

/**
 * Obtains the automatic completion state for this module based on any conditions
 * in agendamento settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function agendamento_completion_get_state($course, $cm, $userid, $type) {
    return agendamento_get_completion_state($course, $cm, $userid, $type);
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $agendamentonode
 * @return void
 */
function agendamento_extend_settings_navigation($settings, $agendamentonode) {
    global $PAGE;

    // We want to add these new nodes after the Edit settings node, and before the
    // Locally assigned roles node. Of course, both of those are controlled by capabilities.
    $keys = $agendamentonode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false and array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }

    $context = $PAGE->cm->context;
    if (has_capability('mod/agendamento:manageslots', $context)) {
        $url = new moodle_url('/mod/agendamento/manageslots.php', array('id' => $PAGE->cm->id));
        $node = navigation_node::create(
            get_string('manageslots', 'agendamento'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'mod_agendamento_manageslots',
            new pix_icon('i/settings', '')
        );
        $agendamentonode->add_node($node, $beforekey);
    }
}
