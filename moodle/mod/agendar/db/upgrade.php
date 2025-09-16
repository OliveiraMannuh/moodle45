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
 * Prints an instance of mod_agendar.
 *
 * @package     mod_agendar
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_agendar_upgrade($oldversion) {  // Era: xmldb_agendamento_upgrade
    global $CFG, $DB;
    
    $dbman = $DB->get_manager();
    
    // Migração da versão antiga "agendamento" para "agendar"
    if ($oldversion < 2024091600) {
        
        // Renomear tabelas se existirem da versão anterior
        if ($dbman->table_exists('agendamento')) {
            $table = new xmldb_table('agendamento');
            $dbman->rename_table($table, 'agendar');
        }
        
        if ($dbman->table_exists('agendamento_submissions')) {
            $table = new xmldb_table('agendamento_submissions');
            $dbman->rename_table($table, 'agendar_submissions');
            
            // Atualizar campo de referência
            $field = new xmldb_field('agendamentoid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            if ($dbman->field_exists($table, $field)) {
                $dbman->rename_field($table, $field, 'agendarid');
            }
        }
        
        // Atualizar registros do módulo no sistema
        $DB->execute("UPDATE {modules} SET name = 'agendar' WHERE name = 'agendamento'");
        $DB->execute("UPDATE {course_modules} cm 
                      SET cm.module = (SELECT id FROM {modules} WHERE name = 'agendar') 
                      WHERE cm.module = (SELECT id FROM {modules} WHERE name = 'agendamento')");
        
        upgrade_mod_savepoint(true, 2024091600, 'agendar');
    }
    
    return true;
}