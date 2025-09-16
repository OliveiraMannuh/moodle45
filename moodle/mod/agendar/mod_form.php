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
 * The main mod_agendar configuration form.
 *
 * @package     mod_agendar
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_agendar
 * @copyright  2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_agendar_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;
        
        $mform = $this->_form;

        // Seção Geral.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Nome do agendamento.
        $mform->addElement('text', 'name', get_string('name', 'mod_agendar'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Descrição.
        $this->standard_intro_elements();

        // Seção de Horários Disponíveis.
        $mform->addElement('header', 'availabletimessection', get_string('availabletimes', 'mod_agendar'));

        // Elementos que serão repetidos para cada horário.
        $repeatarray = array();
        
        // Data e hora de início.
        $repeatarray[] = $mform->createElement('date_time_selector', 'slotstart', 
                        get_string('slotstart', 'mod_agendar'));

        // Data e hora de fim.
        $repeatarray[] = $mform->createElement('date_time_selector', 'slotend', 
                        get_string('slotend', 'mod_agendar'));

        // Número máximo de participantes.
        $participantoptions = array_combine(range(1, 30), range(1, 30));
        $repeatarray[] = $mform->createElement('select', 'maxparticipants', 
                        get_string('maxparticipants', 'mod_agendar'), 
                        $participantoptions);

        // Checkbox para ativar/desativar o horário.
        $repeatarray[] = $mform->createElement('checkbox', 'slotenabled', 
                        get_string('enabled', 'mod_agendar'));

        // Botão para remover este horário.
        $repeatarray[] = $mform->createElement('button', 'removeslot', 
                        get_string('removeslot', 'mod_agendar'));

        // Opções de repetição.
        $repeateloptions = array();
        $repeateloptions['slotstart']['type'] = PARAM_INT;
        $repeateloptions['slotend']['type'] = PARAM_INT;
        $repeateloptions['maxparticipants']['type'] = PARAM_INT;
        $repeateloptions['slotenabled']['type'] = PARAM_BOOL;
        $repeateloptions['maxparticipants']['default'] = 1;
        $repeateloptions['slotenabled']['default'] = 1;

        // Adiciona os elementos repetidos.
        $this->repeat_elements($repeatarray, 1, $repeateloptions, 
                           'slot_repeats', 'slot_add_fields', 1, 
                           get_string('addmoreslots', 'mod_agendar'), true);

        // Elementos padrão do módulo.
        $this->standard_coursemodule_elements();    

        // Botões padrão.
        $this->add_action_buttons();
    }

    /**
     * Add completion rules to the form
     * 
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules() {
        $mform =& $this->_form;
        
        // Adiciona checkbox para condição de conclusão por agendamento
        $group = array();
        $group[] = $mform->createElement('checkbox', 'completionbooking', '', 
                   get_string('completionbooking', 'mod_agendar'));
        $mform->addGroup($group, 'completionbookinggroup', 
                         get_string('completionbookinggroup', 'mod_agendar'), 
                         array(' '), false);
        
        // Define ajuda para o elemento
        $mform->addHelpButton('completionbookinggroup', 'completionbooking', 'mod_agendar');
        
        // Desabilita se completion tracking estiver desabilitado
        $mform->disabledIf('completionbooking', 'completion', 'eq', COMPLETION_TRACKING_NONE);
        
        return array('completionbookinggroup');
    }

    /**
     * Called during validation to see whether some module-specific completion rules are selected.
     *
     * @param array $data Input data not yet validated.
     * @return bool True if one or more rules is enabled, false if none are.
     */
    public function completion_rule_enabled($data) {
        return !empty($data['completionbooking']);
    }

    /**
     * Validação personalizada do formulário
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validar cada slot de horário.
        for ($i = 0; $i < $data['slot_repeats']; $i++) {
            if (!empty($data['slotenabled'][$i])) {
                // Verifica se o horário final é posterior ao inicial.
                if ($data['slotstart'][$i] >= $data['slotend'][$i]) {
                    $errors["slotend[$i]"] = get_string('endtimebeforestart', 'mod_agendar');
                }

                // Verifica se há sobreposição com outros horários.
                for ($j = 0; $j < $data['slot_repeats']; $j++) {
                    if ($i != $j && !empty($data['slotenabled'][$j])) {
                        if (($data['slotstart'][$i] >= $data['slotstart'][$j] && 
                             $data['slotstart'][$i] < $data['slotend'][$j]) ||
                            ($data['slotend'][$i] > $data['slotstart'][$j] && 
                             $data['slotend'][$i] <= $data['slotend'][$j])) {
                            $errors["slotstart[$i]"] = get_string('slotoverlap', 'mod_agendar');
                            break;
                        }
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Get data for this form
     *
     * @return object
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        
        // Ensure completion checkbox data is properly handled
        if (!empty($data->completionunlocked)) {
            // Auto-completion data may not be in the form
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completionbooking) && $autocompletion) {
                // Disable auto-completion if no rules are set
                $data->completion = COMPLETION_TRACKING_MANUAL;
            }
        }
        
        return $data;
    }
}