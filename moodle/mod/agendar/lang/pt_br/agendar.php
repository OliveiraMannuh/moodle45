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
 * Plugin strings are defined here.
 *
 * @package     mod_agendar
 * @category    string
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Agendar';
$string['modulenameplural'] = 'Agendar';
$string['modulename_help'] = 'A atividade agendar permite que professores criem slots de horários disponíveis para que os alunos possam agendar encontros, consultas, apresentações ou outras atividades.';
$string['pluginname'] = 'Agendar';
$string['pluginadministration'] = 'Administração do agendar';

// Capabilities
$string['agendar:addinstance'] = 'Adicionar nova atividade agendar';
$string['agendar:view'] = 'Ver atividade agendar';
$string['agendar:book'] = 'Fazer agendamentos';
$string['agendar:manageslots'] = 'Gerenciar horários disponíveis';
$string['agendar:viewallbookings'] = 'Ver todos os agendamentos';

// Form strings
$string['agendarname'] = 'Nome do agendamento';
$string['bookingsettings'] = 'Configurações de agendamento';
$string['maxbookingsperuser'] = 'Máximo de agendamentos por usuário';
$string['maxbookingsperuser_help'] = 'Número máximo de slots que cada usuário pode agendar';
$string['allowcancellation'] = 'Permitir cancelamentos';
$string['allowcancellation_help'] = 'Se habilitado, usuários poderão cancelar seus agendamentos';
$string['cancellationdeadline'] = 'Prazo para cancelamento';
$string['cancellationdeadlinenone'] = 'Sem prazo limite';
$string['emailnotifications'] = 'Notificações por email';
$string['emailnotifications_help'] = 'Enviar emails de confirmação e lembretes';

// Slot management
$string['manageslots'] = 'Gerenciar horários';
$string['addslot'] = 'Adicionar horário';
$string['starttime'] = 'Hora de início';
$string['endtime'] = 'Hora de fim';
$string['maxbookings'] = 'Máximo de agendamentos';
$string['currentbookings'] = 'Agendamentos atuais';
$string['location'] = 'Local';
$string['notes'] = 'Observações';
$string['visible'] = 'Visível';
$string['datetime'] = 'Data e hora';
$string['availability'] = 'Disponibilidade';
$string['actions'] = 'Ações';

// Booking strings
$string['book'] = 'Agendar';
$string['cancel'] = 'Cancelar';
$string['yourbookings'] = 'Seus agendamentos';
$string['availableslots'] = 'Horários disponíveis';
$string['noavailableslots'] = 'Não há horários disponíveis no momento';
$string['maxbookingsreached'] = 'Você já atingiu o limite máximo de agendamentos';
$string['bookingsuccess'] = 'Agendamento realizado com sucesso!';
$string['bookingfailed'] = 'Falha ao realizar agendamento';
$string['cancellationsuccess'] = 'Agendamento cancelado com sucesso!';
$string['cancellationfailed'] = 'Falha ao cancelar agendamento';
$string['confirmcancel'] = 'Tem certeza que deseja cancelar este agendamento?';
$string['bookingtime'] = 'Data do agendamento';

// Slot management messages
$string['slotadded'] = 'Horário adicionado com sucesso';
$string['slotdeleted'] = 'Horário excluído com sucesso';
$string['slothasbookings'] = 'Não é possível excluir um horário que possui agendamentos';
$string['noslots'] = 'Nenhum horário foi criado ainda';
$string['confirmdelete'] = 'Tem certeza que deseja excluir este horário?';
$string['viewbookings'] = 'Ver agendamentos';
$string['bookingsfor'] = 'Agendamentos para';

// Validation messages
$string['endtimebeforestart'] = 'A hora de fim deve ser posterior à hora de início';
$string['starttimepast'] = 'A hora de início deve ser no futuro';
$string['maxbookingsmin'] = 'O número máximo de agendamentos deve ser pelo menos 1';

// Events
$string['eventcoursemoduleviewed'] = 'Módulo de agendamento visualizado';
$string['eventslotcreated'] = 'Horário criado';
$string['eventslotbooked'] = 'Horário agendado';
$string['eventslotcancelled'] = 'Agendamento cancelado';
