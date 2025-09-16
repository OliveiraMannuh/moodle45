<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

defined('MOODLE_INTERNAL') || die();
/**
 * Add agendar instance.
 *
 * @param stdClass $agendar An object from the form
 * @param mod_agendar_mod_form $mform The form
 * @return int The instance id of the new agendar
 */
function agendar_add_instance($agendar, $mform = null) {
    global $DB;
    
    // Insert data to database
    $agendar->timecreated = time();
    $agendar->timemodified = time();
        if (isset($agendar->slot_repeats)) {
        agendar_save_slots($agendar);
        }

    return $DB->insert_record('agendar', $agendar);
    }

/**
 * Update agendar instance.
 *
 * @param stdClass $agendar An object from the form
 * @param mod_agendar_mod_form $mform The form
 * @return bool true
 */
function agendar_update_instance($agendar, $mform = null) {
    global $DB;
    
    if (isset($agendar->slot_repeats)) {
        agendar_save_slots($agendar);
    }
    
    $result = $DB->update_record('agendar', $agendar);
    if ($id = $DB->get_record('agendar', array('id' => $agendar->instance))) {
        if (isset($agendar->slot_repeats)) {
            $DB->delete_records('agendar_slots', array('agendarid' => $agendar->id));
            agendar_save_slots($agendar->id, $agendar);
        }
    }

    return $result;
}

/**
 * Salva os slots de horário no banco de dados
 *
 * @param int $agendarid ID da instância do agendar
 * @param stdClass $data Dados do formulário
 */
function agendar_save_slots($agendarid, $data) {
    global $DB;

    foreach ($data->slot_repeats as $i => $value) {
        if (!empty($value->enabled)) {
            $slot = new stdClass();
            $slot->agendarid = $agendarid;
            $slot->starttime = $data->slotstart[$i];
            $slot->endtime = $data->slotend[$i];
            $slot->maxparticipants = $data->maxparticipants[$i];
            $slot->enabled = 1;
            $slot->timecreated = time();
            
            if (!empty($value->description)) {
                $DB->insert_record('agendar_slots_description', array(
                    'content' => $value->description
                ));
    }

            $DB->insert_record('agendar_slots', $slot);
        }
    }
}

/**
 * Delete agendamento instance.
 *
 * @param int $id The ID of the instance to be deleted
 * @return bool true if deletion is successful, false otherwise
 */
function agenda_delete_instance($id) {
    global $DB;
    
    if (!$agendar = $DB->get_record('agendar', array('id' => $id))) {
        return false;
    }
    
    $DB->delete_records('agendar', array('id' => $agendar->id));
    
    return true;
}

/**
 * Check if the module supports a specific feature.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool True if module supports feature, false if not, null if doesn't know
 */
function agendar_supports($feature) {
    switch ($feature) {
        case 'COMPLETION_TRACKS_VIEWS':
            return true;
        case 'COMPLETION_HAS_RULES':
            return true;
        case 'GRADE_HAS_GRADE':
            return false;
        case 'GRADE_OUTCOMES':
            return false;
        case 'MOD_INTRO':
            return true;
        case 'BACKUP_MOODLE2':
            return true;
        case 'SHOW_DESCRIPTION':
            return true;
        default:
            return null;
    }
}

/**
 * Obtains the automatic completion state for this agendamento based on any conditions
 * in agendamento settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not. (If no conditions, then return value depends on comparison type)
 */
function agendar_get_completion_state($course, $cm, $userid, $type) {
    global $DB;

    // Get agendamento details
    $sql = "SELECT COUNT(*) as count 
            FROM {agendar_slots} s 
            JOIN {agendar_bookings} b ON b.slotid = s.id 
            WHERE s.agendarid = ? AND b.userid = ?";

    if (isset($type)) {
        // Group by agendamento type
        $sql .= " GROUP BY s.type";
}

    $hasBooking = $DB->count_records_sql($sql, array($cm->instance, $userid));

    return $hasBooking;


   $DB->set_db('database');

   // Escrever seu código aqui... 

   // Configurar o módulo de conexão
  if  (!($agendar = $DB->get_record('agendar', array('id' => $cm->instance))))   {
    throw new Exception("Can't find agenda {$agendarid}");
   }

   //Configurar os slots de horário
   $slots    =$DB->get_records_select('agendar_slots', 'agendaid =  ?',  array($agendarid));
}

