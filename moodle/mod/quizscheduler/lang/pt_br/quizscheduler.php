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
 * Brazilian Portuguese strings for mod_quizscheduler.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Agendamento de Questionários';
$string['modulename'] = 'Agendamento de Questionários';
$string['modulenameplural'] = 'Agendamentos de Questionários';
$string['pluginadministration'] = 'Administração do Agendamento de Questionários';

// Capabilities.
$string['quizscheduler:addinstance'] = 'Adicionar uma nova instância de agendamento de questionário';
$string['quizscheduler:view'] = 'Ver agendamento de questionário';
$string['quizscheduler:book'] = 'Agendar horário para questionário';
$string['quizscheduler:manageslots'] = 'Gerenciar horários disponíveis';
$string['quizscheduler:viewreports'] = 'Ver relatórios de agendamento';
$string['quizscheduler:managebookings'] = 'Gerenciar agendamentos de usuários';

// Form fields.
$string['name'] = 'Nome';
$string['intro'] = 'Descrição';
$string['quizid'] = 'Questionário';
$string['quizid_help'] = 'Selecione o questionário que será agendado pelos usuários.';
$string['timeopen'] = 'Agendamentos abrem';
$string['timeopen_help'] = 'Data e hora de abertura dos agendamentos.';
$string['timeclose'] = 'Agendamentos fecham';
$string['timeclose_help'] = 'Data e hora de fechamento dos agendamentos.';
$string['maxbookings'] = 'Máximo de agendamentos por usuário';
$string['maxbookings_help'] = 'Número máximo de agendamentos que um usuário pode fazer.';
$string['slotduration'] = 'Duração do slot (minutos)';
$string['slotduration_help'] = 'Duração de cada slot de agendamento em minutos.';
$string['maxusersperslot'] = 'Máximo de usuários por slot';
$string['maxusersperslot_help'] = 'Número máximo de usuários que podem agendar o mesmo horário.';
$string['schedulestarttime'] = 'Horário de início do agendamento';
$string['scheduleendtime'] = 'Horário de fim do agendamento';

// Schedule settings
$string['schedulesettings'] = 'Configurações de Agendamento';
$string['schedulestarttime_help'] = 'Data e hora de início do período de agendamento (opcional).';
$string['scheduleendtime_help'] = 'Data e hora de fim do período de agendamento (opcional).';

// Settings strings
$string['configurationsettings'] = 'Configurações';

// General strings.
$string['availableslots'] = 'Horários disponíveis';
$string['bookedslots'] = 'Meus agendamentos';
$string['bookslot'] = 'Agendar horário';
$string['cancelbooking'] = 'Cancelar agendamento';
$string['confirmcancel'] = 'Tem certeza de que deseja cancelar este agendamento?';
$string['bookingsuccess'] = 'Horário agendado com sucesso!';
$string['cancelsuccess'] = 'Agendamento cancelado com sucesso!';
$string['noslots'] = 'Não há horários disponíveis no momento.';
$string['nobookings'] = 'Você não possui agendamentos.';
$string['slotfull'] = 'Este horário está lotado.';
$string['alreadybooked'] = 'Você já possui um agendamento para este questionário.';
$string['bookingclosed'] = 'Os agendamentos estão fechados.';
$string['starttime'] = 'Horário de início';
$string['endtime'] = 'Horário de término';
$string['status'] = 'Status';
$string['booked'] = 'Agendado';
$string['completed'] = 'Concluído';
$string['missed'] = 'Perdido';
$string['cancelled'] = 'Cancelado';

// Reports.
$string['reports'] = 'Relatórios';
$string['bookingsreport'] = 'Relatório de agendamentos';
$string['bookingreport'] = 'Relatório de Agendamentos';
$string['viewreports'] = 'Ver Relatórios';
$string['filterbyslot'] = 'Filtrar por Horário';
$string['filterbystatus'] = 'Filtrar por Status';
$string['allslots'] = 'Todos os horários';
$string['allstatuses'] = 'Todos os status';
$string['downloadcsv'] = 'Baixar CSV';
$string['user'] = 'Usuário';
$string['timebooked'] = 'Data do Agendamento';
$string['actions'] = 'Ações';
$string['manageslots'] = 'Gerenciar horários';
$string['addslot'] = 'Adicionar horário';
$string['editslot'] = 'Editar horário';
$string['deleteslot'] = 'Excluir horário';
$string['quizstatus'] = 'Status do Quiz';
$string['quizcompleted'] = 'Quiz Concluído';
$string['quizinprogress'] = 'Quiz em Progresso';
$string['notstarted'] = 'Não iniciado';
$string['inprogress'] = 'Em Progresso';

// Management strings.
$string['generateslots'] = 'Gerar horários';
$string['currentslots'] = 'Horários atuais';
$string['maxusers'] = 'Máximo de usuários';
$string['bookings'] = 'Agendamentos';
$string['expired'] = 'Expirado';
$string['confirmdeleteSlot'] = 'Tem certeza de que deseja excluir este horário?';
$string['slotdeleted'] = 'Horário excluído com sucesso';
$string['slotsgenerated'] = '{$a} horários foram gerados';
$string['selectslot'] = 'Selecionar horário';

// Events.
$string['eventbookingcreated'] = 'Agendamento criado';
$string['eventbookingcancelled'] = 'Agendamento cancelado';
$string['eventcoursemoduleviewed'] = 'Módulo do curso visualizado';
$string['eventquizcompleted'] = 'Questionário agendado concluído';
$string['eventscheduledquizstarted'] = 'Questionário agendado iniciado';
$string['eventslotbooked'] = 'Horário agendado';

// Task strings.
$string['cleanupexpiredbookings'] = 'Limpeza de agendamentos expirados';

// Additional strings for pages.
$string['back'] = 'Voltar';
$string['timing'] = 'Cronometragem';
$string['settings'] = 'Configurações';
$string['minutes'] = 'minutos';
$string['hour'] = 'hora';
$string['hours'] = 'horas';
$string['available'] = 'disponível';
$string['yes'] = 'Sim';
$string['no'] = 'Não';
$string['download'] = 'Baixar';
$string['total'] = 'Total';
$string['delete'] = 'Excluir';
$string['date'] = 'Data';
$string['administration'] = 'Administração';
$string['information'] = 'Informações';

// Interface strings.
$string['adminpaneldesc'] = 'Use as opções abaixo para gerenciar horários e visualizar relatórios.';
$string['admininfo'] = 'Use as opções abaixo para gerenciar horários e visualizar relatórios.';
$string['bookinginfo'] = 'Informações de Agendamento';
$string['schedulinginfo'] = 'Informações de Agendamento';
$string['bookingperiod'] = 'Período de agendamento: {$a->open} até {$a->close}';
$string['scheduleperiod'] = 'Período do Agendamento';
$string['selectweekdays'] = 'Selecione os dias da semana';
$string['timesettings'] = 'Configurações de Horário';
$string['starthour'] = 'Horário de Início';
$string['endhour'] = 'Horário de Término';
$string['schedulepreview'] = 'Prévia dos Horários';
$string['previewschedules'] = 'Visualizar Horários';
$string['generateschedules'] = 'Gerar Horários';
$string['manageschedules'] = 'Gerenciar Horários';
$string['startdate'] = 'Data de Início';
$string['enddate'] = 'Data de Término';
$string['selectdaterange'] = 'Selecione o Período';
$string['selectdaterange_placeholder'] = 'Clique para selecionar o intervalo de datas';

// ...existing code...

$string['until'] = 'até';
$string['slotsinfo'] = '{$a->available} de {$a->total} horários disponíveis';
$string['bookingsleft'] = 'Agendamentos: {$a->current} de {$a->max} utilizados';
$string['noslotscreated'] = 'Ainda não foram criados horários. Entre em contato com seu professor.';
$string['contactteacher'] = 'Entre em contato com seu professor para mais informações sobre agendamento de horários.';
$string['cannotbook'] = 'Não é possível agendar';
$string['totalslots'] = 'Total de horários';
$string['totalbookings'] = 'Total de agendamentos';
$string['createslotshelp'] = 'Use o formulário acima para gerar horários para os alunos agendarem.';
$string['full'] = 'Lotado';
$string['disabled'] = 'Desabilitado';

// View page strings.
$string['yourbookings'] = 'Seus Agendamentos';
$string['noavailableslots'] = 'Não há horários disponíveis no momento.';
$string['datetime'] = 'Data e Hora';
$string['availability'] = 'Disponibilidade';
$string['book'] = 'Agendar';
$string['cancel'] = 'Cancelar';
$string['bookingsstats'] = 'Agendamentos';
$string['slotsused'] = 'utilizados';
$string['of'] = 'de';
$string['bookingcancelled'] = 'Agendamento cancelado com sucesso!';
$string['slotcreated'] = 'Horário criado com sucesso!';

// Status strings.
$string['status_booked'] = 'Agendado';
$string['status_active'] = 'Ativo';
$string['status_completed'] = 'Concluído';
$string['status_missed'] = 'Perdido';
$string['upcoming'] = 'Próximo';
$string['active'] = 'Ativo';
$string['reserved'] = 'reservado(s)';
$string['hasactivebooking'] = 'Você possui um agendamento ativo';
$string['slotpassed'] = 'Horário já passou';

// Rebooking functionality
$string['activebookingexists'] = 'Você já possui um agendamento ativo. Só é possível agendar um novo horário após o término do atual.';
$string['youhaveactivebooking'] = 'Você já possui um agendamento ativo. Aguarde a conclusão para fazer um novo agendamento.';
$string['youcanrebooknow'] = 'Seu agendamento anterior foi concluído. Você pode fazer um novo agendamento.';
$string['bookingexpired'] = 'Agendamento anterior finalizado';
$string['activebooking'] = 'Agendamento ativo';
$string['nomorebookings'] = 'Você já possui um agendamento ativo para este questionário';
$string['canbook'] = 'Disponível para agendamento';
$string['bookingerror'] = 'Erro ao agendar horário';
$string['cancellationsuccess'] = 'Agendamento cancelado com sucesso';
$string['cancellationerror'] = 'Erro ao cancelar agendamento';
$string['nobooking'] = 'Nenhum agendamento encontrado para cancelar';
$string['slotnotavailable'] = 'Este horário não está mais disponível';
$string['invalidslot'] = 'Horário inválido';
$string['invalidaction'] = 'Ação inválida';

// Errors.
$string['error:noquiz'] = 'Nenhum questionário encontrado neste curso.';
$string['error:invalidslot'] = 'Horário inválido.';
$string['error:cannotbook'] = 'Não é possível agendar este horário.';
$string['error:cannotcancel'] = 'Não é possível cancelar este agendamento.';
$string['error:slothasbookings'] = 'Não é possível excluir um horário que possui agendamentos';
$string['error:invalidvalue'] = 'Valor inválido';
$string['error:invalidtimes'] = 'Horários de início e fim inválidos';
$string['error:cannotgenerateslots'] = 'Não foi possível gerar os horários';
$string['error:cannotdelete'] = 'Não foi possível excluir o horário';
$string['error:overlappingslots'] = 'Horários não podem se sobrepor';
$string['error:missingfields'] = 'Campos obrigatórios não preenchidos';
$string['closebeforeopen'] = 'A data de fechamento deve ser posterior à data de abertura.';
$string['nopermissions'] = 'Você não tem permissão para acessar este conteúdo.';
$string['bookingnotfound'] = 'Agendamento não encontrado';
$string['slotnotfound'] = 'Horário não encontrado';
$string['nopermissiontocancelbooking'] = 'Você não tem permissão para cancelar este agendamento';
$string['cancellationtoolate'] = 'Não é possível cancelar. O prazo limite é 1 hora antes do início';

// Privacy.
$string['privacy:metadata'] = 'O plugin Quiz Scheduler armazena informações sobre agendamentos de usuários.';
$string['privacy:metadata:quizscheduler_bookings'] = 'Informações sobre agendamentos de usuários para slots de questionários.';
$string['privacy:metadata:quizscheduler_bookings:userid'] = 'O ID do usuário que fez o agendamento.';
$string['privacy:metadata:quizscheduler_bookings:timebooked'] = 'Quando o agendamento foi feito.';
$string['privacy:metadata:quizscheduler_bookings:status'] = 'O status do agendamento.';
$string['privacy:metadata:quizscheduler_bookings:timestarted'] = 'Quando o questionário foi iniciado.';
$string['privacy:metadata:quizscheduler_bookings:timefinished'] = 'Quando o questionário foi finalizado.';

// Access control strings.
$string['accessgranted'] = 'Acesso liberado até {$a}';
$string['accessdenied_future'] = 'Acesso ao questionário negado. Seu próximo horário agendado é: {$a}';
$string['accessdenied_nobooking'] = 'Acesso ao questionário negado. Você precisa agendar um horário primeiro.';
$string['teachercanbypass'] = 'Professores podem acessar este questionário sem restrições de agendamento.';
$string['timeleftwarning'] = 'Atenção: Você tem {$a} restantes no seu horário agendado.';
$string['timeexpired'] = 'Seu tempo agendado expirou';
$string['quizforcedsubmit'] = 'Questionário foi enviado automaticamente devido ao tempo expirado';
$string['gotoschedule'] = 'Ir para Agendamento';
$string['schedulefirst'] = 'Você deve agendar um horário antes de fazer este questionário.';
$string['quizaccessdenied'] = 'Acesso ao Questionário Negado';
$string['schedulerequired'] = 'Agendamento Obrigatório';
$string['accessdenied'] = 'Acesso Negado';
$string['backtocourse'] = 'Voltar ao Curso';

// Email notifications - CORRIGIDAS
$string['message_provider:slot_booking_confirmation'] = 'Confirmação de agendamento de horário';
$string['email_booking_subject'] = 'Confirmação de agendamento: {$a->quizname}';
$string['email_booking_body'] = 'Olá {$a->studentname},

Seu agendamento para o questionário "{$a->quizname}" foi confirmado com sucesso.

Curso: {$a->coursename}
Data: {$a->date}
Horário: {$a->starttime} - {$a->endtime}

Por favor, esteja presente no horário agendado.

{$a->sitename}';
$string['email_booking_small'] = 'Agendamento confirmado: {$a->quizname}';
$string['add_to_google_calendar'] = 'Adicionar ao Google Calendar';
$string['ical_attachment_note'] = 'Um arquivo iCalendar (.ics) está anexado a este email. Você pode importá-lo para seu aplicativo de calendário (Google Calendar, Outlook, Apple Calendar, etc.).';
$string['quiz_scheduled_details'] = 'Questionário agendado: {$a}';

// Pagination
$string['show'] = 'Mostrar';
$string['all'] = 'Todos';
$string['slotsperpage'] = 'horários por página';
$string['showingslots'] = 'Mostrando {$a->start} a {$a->end} de {$a->total} horários';
