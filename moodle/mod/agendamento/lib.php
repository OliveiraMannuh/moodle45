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
            return false; // Não mostrar nota na atividade.
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
    
    agendamento_grade_item_update($agendamento);
    
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
    
    agendamento_grade_item_update($agendamento);
    
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
    
    agendamento_grade_item_delete($agendamento);
    
    return true;
}

/**
 * Create/update grade item.
 */
function agendamento_grade_item_update($agendamento, $grades = null) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');
    
    if ($agendamento->grade == 0) {
        return grade_update('mod/agendamento', $agendamento->course, 'mod', 'agendamento',
                           $agendamento->id, 0, null, array('deleted' => 1));
    }
    
    $item = array();
    $item['itemname'] = clean_param($agendamento->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax'] = $agendamento->grade;
    $item['grademin'] = 0;
    
    return grade_update('mod/agendamento', $agendamento->course, 'mod', 'agendamento',
                       $agendamento->id, 0, $grades, $item);
}

/**
 * Delete grade item.
 */
function agendamento_grade_item_delete($agendamento) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');
    
    return grade_update('mod/agendamento', $agendamento->course, 'mod', 'agendamento',
                       $agendamento->id, 0, null, array('deleted' => 1));
}

/**
 * Get completion state.
 * This function is called to determine if the activity should be marked as complete.
 */
function agendamento_get_completion_state($course, $cm, $userid, $type) {
    global $DB;
    
    // Get agendamento instance.
    $agendamento = $DB->get_record('agendamento', array('id' => $cm->instance), '*', MUST_EXIST);
    
    // If completion booking is not required, return true (completed by view or other conditions)
    if (empty($agendamento->completionbooking)) {
        return $type; // Return the type as completion state
    }
    
    // Check if user has booked any slot.
    $sql = "SELECT COUNT(b.id)
            FROM {agendamento_bookings} b
            JOIN {agendamento_slots} s ON b.slotid = s.id
            WHERE s.agendamento = ? AND b.userid = ?"; // Ensure the booking is not cancelled.

    $bookingcount = $DB->count_records_sql($sql, array($agendamento->id, $userid));
    
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
 * Return grade for given user or all users.
 */
function agendamento_get_user_grades($agendamento, $userid = 0) {
    global $DB;

    if ($agendamento->grade == 0) {
        return array();
    }

    $params = array($agendamento->id);
    $usersql = '';
    if ($userid) {
        $usersql = ' AND b.userid = ?';
        $params[] = $userid;
    }

    $sql = "SELECT b.userid, {$agendamento->grade} as rawgrade, b.timecreated as dategraded
            FROM {agendamento_bookings} b
            JOIN {agendamento_slots} s ON b.slotid = s.id
            WHERE s.agendamento = ? $usersql
            GROUP BY b.userid, b.timecreated";

    return $DB->get_records_sql($sql, $params);
}

/**
 * Update grades in central gradebook
 */
function agendamento_update_grades($agendamento, $userid = 0, $nullifnone = true) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    if ($agendamento->grade == 0) {
        agendamento_grade_item_update($agendamento);
    } else if ($grades = agendamento_get_user_grades($agendamento, $userid)) {
        agendamento_grade_item_update($agendamento, $grades);
    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid = $userid;
        $grade->rawgrade = null;
        agendamento_grade_item_update($agendamento, $grade);
    } else {
        agendamento_grade_item_update($agendamento);
    }
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
