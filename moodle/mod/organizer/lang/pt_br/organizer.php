<?php
// This file is part of mod_organizer for Moodle - http://moodle.org/
//
// It is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// It is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * lang/pt-br/organizer.php
 *
 * @package   mod_organizer
 * @author    Andreas Hruska (andreas.hruska@tuwien.ac.at)
 * @author    Katarzyna Potocka (katarzyna.potocka@tuwien.ac.at)
 * @author    Andreas Windbichler
 * @author    Ivan Šakić
 * @copyright 2014 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['absolutedeadline'] = 'Fim da inscrição';
$string['absolutedeadline_help'] = 'Marque esta opção para definir o tempo após o qual nenhuma outra ação do aluno será possível.';
$string['actionlink_delete'] = 'excluir';
$string['actionlink_edit'] = 'editar';
$string['actionlink_eval'] = 'grade';
$string['actionlink_print'] = 'imprimir';
$string['actions'] = 'Ação';
$string['actions_help'] = 'Ação a ser tomada.';
$string['addappointment'] = 'Adicionar agendamento';
$string['addslots_placesinfo'] = 'Esta ação irá criar {$a->numplaces} novos lugares possíveis, totalizando {$a->totalplaces} lugares possíveis para {$a->numstudents} estudantes.';
$string['addslots_placesinfo_group'] = 'Esta ação irá criar {$a->numplaces} novos lugares possíveis, totalizando {$a->totalplaces} lugares possíveis para {$a->numgroups} grupos.';
$string['allowcreationofpasttimeslots'] = 'Criação de horários passados';
$string['allowedprofilefieldsprint'] = 'Campos de perfil de usuário permitidos';
$string['allowedprofilefieldsprint2'] = 'Campos de perfil de usuário permitidos para impressão de horários de um único organizador';
$string['allowsubmissionsanddescriptionfromdatesummary'] = 'Os detalhes do organizador e o formulário de inscrição estarão disponíveis a partir de <strong>{$a}</strong>';
$string['allowsubmissionsfromdate'] = 'Início das inscrições';
$string['allowsubmissionsfromdate_help'] = 'Marque esta opção se você quiser que este organizador esteja disponível para os estudantes a partir de um determinado momento.';
$string['allowsubmissionsfromdatesummary'] = 'Este organizador aceitará inscrições a partir de <strong>{$a}</strong>';
$string['allowsubmissionstodate'] = 'Fim das inscrições';
$string['alwaysshowdescription'] = 'Sempre mostrar a descrição';
$string['alwaysshowdescription_help'] = 'Se desativado, a Descrição da Tarefa acima só ficará visível para os estudantes na data de \'Início das inscrições\'.';
$string['applicant'] = 'Esta é a pessoa que registrou o grupo';
$string['appointment_reminder_student:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, você tem um agendamento com {$a->sendername} em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['appointment_reminder_student:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, você tem um agendamento em grupo com {$a->sendername} em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['appointment_reminder_student:group:smallmessage'] = 'Você tem um agendamento em grupo com {$a->sendername} em {$a->date} às {$a->time} em {$a->location}.';
$string['appointment_reminder_student:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Lembrete de agendamento em grupo';
$string['appointment_reminder_student:smallmessage'] = 'Você tem um agendamento com {$a->sendername} em {$a->date} às {$a->time} em {$a->location}.';
$string['appointment_reminder_student:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Lembrete de agendamento';
$string['appointment_reminder_teacher:digest:fullmessage'] = 'Olá, {$a->receivername}!

Amanhã você tem os seguintes agendamentos:

{$a->digest}

Sistema de Mensagens do Moodle';
$string['appointment_reminder_teacher:digest:smallmessage'] = 'Você recebeu um resumo de seus agendamentos para amanhã.';
$string['appointment_reminder_teacher:digest:subject'] = 'Resumo de agendamentos';
$string['appointment_reminder_teacher:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, você tem um agendamento com estudantes em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['appointment_reminder_teacher:group:digest:fullmessage'] = 'Olá, {$a->receivername}!

Amanhã você tem os seguintes agendamentos:

{$a->digest}

Sistema de Mensagens do Moodle';
$string['appointment_reminder_teacher:group:digest:smallmessage'] = 'Você recebeu um resumo de seus agendamentos para amanhã.';
$string['appointment_reminder_teacher:group:digest:subject'] = 'Resumo de agendamentos';
$string['appointment_reminder_teacher:smallmessage'] = 'Você tem um agendamento com estudantes em {$a->date} às {$a->time} em {$a->location}.';
$string['appointment_reminder_teacher:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Lembrete de agendamento';
$string['appointmentcomments'] = 'Comentários';
$string['appointmentcomments_help'] = 'Informações adicionais sobre os agendamentos podem ser adicionadas aqui.';
$string['appointmentdatetime'] = 'Data e hora';
$string['appointmentdeleted_notify_student:fullmessage'] = 'Olá, {$a->receivername}!

Seu agendamento no curso {$a->courseshortname} em {$a->date} às {$a->time} em {$a->location} foi excluído.';
$string['appointmentdeleted_notify_student:group:fullmessage'] = 'Olá, {$a->receivername}!

Seu agendamento no curso {$a->courseshortname} em {$a->date} às {$a->time} em {$a->location} foi excluído.';
$string['appointmentdeleted_notify_student:group:smallmessage'] = 'Seu agendamento em {$a->date} às {$a->time} no organizador \'{$a->organizername}\' foi excluído.';
$string['appointmentdeleted_notify_student:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento excluído';
$string['appointmentdeleted_notify_student:smallmessage'] = 'Seu agendamento em {$a->date} às {$a->time} no organizador \'{$a->organizername}\' foi excluído.';
$string['appointmentdeleted_notify_student:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento excluído';
$string['assign'] = 'Atribuir';
$string['assign_notify_student:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, um agendamento com {$a->slot_teacher} em {$a->date} às {$a->time} foi atribuído a você por {$a->sendername}.

Professor: {$a->slot_teacher}
Local: {$a->slot_location}
Data: {$a->date} às {$a->time}

Sistema de Mensagens do Moodle';
$string['assign_notify_student:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, um agendamento com {$a->slot_teacher} em {$a->date} às {$a->time} foi atribuído ao seu grupo {$a->groupname} por {$a->sendername}.

Professor: {$a->slot_teacher}
Local: {$a->slot_location}
Data: {$a->date} às {$a->time}

Sistema de Mensagens do Moodle';
$string['assign_notify_student:group:smallmessage'] = 'Um agendamento com {$a->slot_teacher} em {$a->date} às {$a->time} foi atribuído ao seu grupo {$a->groupname} por {$a->sendername}.';
$string['assign_notify_student:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento atribuído pelo professor';
$string['assign_notify_student:smallmessage'] = 'Um agendamento com {$a->slot_teacher} em {$a->date} às {$a->time} foi atribuído a você por {$a->sendername}.';
$string['assign_notify_student:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento atribuído pelo professor';
$string['assign_notify_teacher:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, um agendamento com {$a->participantname} em {$a->date} às {$a->time} foi atribuído a você por {$a->sendername}.

Participante: {$a->participantname}
Local: {$a->slot_location}
Data: {$a->date} às {$a->time}

Sistema de Mensagens do Moodle';
$string['assign_notify_teacher:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, um agendamento com o grupo {$a->groupname} em {$a->date} às {$a->time} foi atribuído a você por {$a->sendername}.

Grupo: {$a->groupname}
Local: {$a->slot_location}
Data: {$a->date} às {$a->time}

Sistema de Mensagens do Moodle';
$string['assign_notify_teacher:group:smallmessage'] = 'Um agendamento com o grupo {$a->groupname} em {$a->date} às {$a->time} foi atribuído a você por {$a->sendername}.';
$string['assign_notify_teacher:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento atribuído';
$string['assign_notify_teacher:smallmessage'] = 'Um agendamento com {$a->sendername} em {$a->date} às {$a->time} foi atribuído a você por {$a->sendername}';
$string['assign_notify_teacher:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento atribuído';
$string['assign_title'] = 'Atribuir agendamento';
$string['assignsuccess'] = 'O horário foi atribuído com sucesso e o(s) participante(s) foi(ram) notificado(s).';
$string['assignsuccessnotsent'] = 'O horário foi atribuído com sucesso, MAS o(s) participante(s) NÃO foi(ram) notificado(s).';
$string['atlocation'] = 'em';
$string['attended'] = 'compareceu';
$string['auth'] = 'Método de autenticação';
$string['availability'] = 'Disponibilidade';
$string['availablefrom'] = 'Inscrições possíveis a partir de';
$string['availablefrom_help'] = 'Defina o período de tempo em que os estudantes poderão se registrar para esses horários. Alternativamente, marque \'A partir de agora\' para habilitar o registro imediatamente.';
$string['availablegrouplist'] = 'Grupos disponíveis';
$string['availableslotsfor'] = 'Horários disponíveis para';
$string['back'] = 'Voltar';
$string['btn_add'] = 'Adicionar novos horários';
$string['btn_assign'] = 'Atribuir horário';
$string['btn_comment'] = 'Editar seu comentário';
$string['btn_delete'] = 'Remover horários selecionados';
$string['btn_deleteappointment'] = 'Excluir agendamento';
$string['btn_deletesingle'] = 'Remover horário selecionado';
$string['btn_edit'] = 'Editar horários selecionados';
$string['btn_editsingle'] = 'Editar horário selecionado';
$string['btn_eval'] = 'Avaliar horários selecionados';
$string['btn_eval_short'] = 'Avaliar';
$string['btn_evalsingle'] = 'Avaliar horário selecionado';
$string['btn_exportics'] = 'Exportar horário selecionado como um arquivo ICS';
$string['btn_print'] = 'Imprimir horários selecionados';
$string['btn_printsingle'] = 'Imprimir horário selecionado';
$string['btn_queue'] = 'Entrar na fila';
$string['btn_reeval'] = 'Reavaliar';
$string['btn_register'] = 'Registrar';
$string['btn_remind'] = 'Enviar lembrete';
$string['btn_reregister'] = 'Registrar-se novamente';
$string['btn_save'] = 'Salvar comentário';
$string['btn_send'] = 'Enviar';
$string['btn_sendall'] = 'Enviar lembretes a todos os participantes sem agendamentos suficientes:';
$string['btn_start'] = 'Iniciar';
$string['btn_unqueue'] = 'Remover da fila';
$string['btn_unregister'] = 'Cancelar registro';
$string['calendarsettings'] = 'Configurações do calendário';
$string['can_reregister'] = 'Você pode se registrar novamente para outro agendamento.';
$string['cannot_eval'] = 'Não é possível avaliar, o estudante tem um ';
$string['cfg_dontshowidentity'] = 'Ocultar identidade';
$string['cfg_dontshowidentity_desc'] = 'Ocultar a identidade do participante na lista de horários';
$string['cfg_limitedwidth'] = 'Área de conteúdo menor';
$string['cfg_limitedwidth_desc'] = 'Usar área de conteúdo menor no estilo do Moodle 4.x no organizador. O padrão do Moodle é usado, mas pode ser esticado por entradas de tabela longas.';
$string['changegradewarning'] = 'Este organizador tem agendamentos avaliados e a alteração das configurações de avaliação não recalculará automaticamente as notas existentes. Você deve reavaliar todos os agendamentos existentes, se desejar alterar a nota.';
$string['collision'] = 'Aviso! Colisão detectada com o(s) seguinte(s) evento(s) e/ou horário(s):';
$string['configabsolutedeadline'] = 'O deslocamento padrão do seletor de data e hora em relação à data e hora atuais.';
$string['configahead'] = 'antes';
$string['configallowcreationofpasttimeslots'] = 'É permitido criar horários passados?';
$string['configday'] = 'dia';
$string['configdays'] = 'dias';
$string['configdigest'] = 'Enviar resumo dos agendamentos do dia seguinte para o professor.';
$string['configdigest_label'] = 'Enviar resumo de agendamentos para professores';
$string['configdontsend'] = 'Não enviar';
$string['configemailteachers'] = 'Enviar notificações por e-mail para os professores sobre mudanças no status de registro.';
$string['configemailteachers_label'] = 'Enviar notificações por e-mail para professores';
$string['confighour'] = 'hora';
$string['confighours'] = 'horas';
$string['configintro'] = 'Os valores que você definir aqui definem os valores padrão que são usados no formulário de configurações quando você cria um novo organizador.';
$string['configlocationlink'] = 'O link para um mecanismo de busca usado para mostrar o caminho para o local. Coloque $searchstring na URL onde a consulta vai.';
$string['configlocationslist'] = 'Locais para campo de autocompletar';
$string['configlocationslist_desc'] = 'Cada local deve ser inserido em uma linha separada!';
$string['configmaximumgrade'] = 'Define o valor padrão selecionado no campo de nota ao criar um novo organizador. Esta é a nota máxima atribuível a um estudante para seu agendamento.';
$string['configminute'] = 'minuto';
$string['configminutes'] = 'minutos';
$string['configmonth'] = 'mês';
$string['configmonths'] = 'meses';
$string['confignever'] = 'Nunca';
$string['configrelativedeadline'] = 'O tempo padrão antes de um agendamento quando os participantes devem ser notificados sobre ele.';
$string['configrequiremodintro'] = 'Desative esta opção se você não quiser forçar os usuários a inserir a descrição de cada atividade.';
$string['configsingleslotprintfield'] = 'campo de usuário a ser impresso quando um único horário é impresso';
$string['configweek'] = 'semana';
$string['configweeks'] = 'semanas';
$string['configyear'] = 'ano';
$string['confirm_conflicts'] = 'Tem certeza de que deseja ignorar as colisões e criar os horários?';
$string['confirm_delete'] = 'Excluir';
$string['confirm_organizer_remind_all'] = 'Enviar';
$string['create'] = 'Criar';
$string['created'] = 'Criado';
$string['createsubmit'] = 'Criar horários';
$string['crontaskname'] = 'Tarefa cron do organizador';
$string['datapreviewtitle'] = 'Visualização de dados';
$string['datapreviewtitle_help'] = 'Clique em [+] ou [-] para mostrar ou ocultar colunas.';
$string['datetemplate'] = '%d.%m.%Y';
$string['datetime'] = 'Data e hora';
$string['datetime_help'] = 'Data e hora do horário.';
$string['day'] = 'dia';
$string['day_0'] = 'Segunda-feira';
$string['day_1'] = 'Terça-feira';
$string['day_2'] = 'Quarta-feira';
$string['day_3'] = 'Quinta-feira';
$string['day_4'] = 'Sexta-feira';
$string['day_5'] = 'Sábado';
$string['day_6'] = 'Domingo';
$string['day_pl'] = 'dias';
$string['dbid'] = 'ID do BD';
$string['defaultsingleslotprintfields'] = 'Campos de perfil de usuário padrão para impressão de horário único';
$string['delete_organizer_grades'] = 'Excluir notas de todos os organizadores';
$string['deleteappointmentheader'] = 'Excluir este agendamento';
$string['deleteheader'] = 'Excluindo os seguintes horários:';
$string['deletekeep'] = 'Os seguintes agendamentos serão cancelados. Os estudantes registrados serão notificados e os horários serão excluídos:';
$string['deletenoslots'] = 'Nenhum horário excluível selecionado';
$string['deleteorganizergrades'] = 'Excluir notas do livro de notas';
$string['details'] = 'Detalhes do status';
$string['details_help'] = 'Status atual deste horário.';
$string['downloadfile'] = 'Baixar arquivo';
$string['duedate'] = 'Data de vencimento';
$string['duedateerror'] = 'A data limite absoluta não pode ser definida antes da data de disponibilidade!';
$string['duration'] = 'Duração';
$string['duration_help'] = 'Define a duração dos agendamentos. Todos os períodos de tempo definidos serão divididos em horários da duração definida aqui. Qualquer tempo restante permanecerá sem uso (ou seja, se o período for de 40 min e a duração for definida para 15 min, haverá 2 horários no total com 10 minutos extras não utilizados).';
$string['edit_notify_student:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, os detalhes do agendamento {$a->sendername} em {$a->date} às {$a->time} foram alterados.

Professor: {$a->slot_teacher}
Local: {$a->slot_location}
Máx. de participantes: {$a->slot_maxparticipants}
Comentários:
{$a->slot_comments}

Sistema de Mensagens do Moodle';
$string['edit_notify_student:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, os detalhes do agendamento em grupo {$a->sendername} em {$a->date} às {$a->time} foram alterados.

Professor: {$a->slot_teacher}
Local: {$a->slot_location}
Máx. de participantes: {$a->slot_maxparticipants}
Comentários:
{$a->slot_comments}

Sistema de Mensagens do Moodle';
$string['edit_notify_student:group:smallmessage'] = 'Os detalhes do agendamento em grupo {$a->sendername} em {$a->date} às {$a->time} foram alterados.';
$string['edit_notify_student:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Detalhes do agendamento alterados';
$string['edit_notify_student:smallmessage'] = 'Os detalhes do agendamento {$a->sendername} em {$a->date} às {$a->time} foram alterados.';
$string['edit_notify_student:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Detalhes do agendamento alterados';
$string['edit_notify_teacher:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, os detalhes do horário em {$a->date} às {$a->time} foram alterados por {$a->sendername}.

Professor(es): {$a->slot_teacher}
Local: {$a->slot_location}
Máx. de participantes: {$a->slot_maxparticipants}
Comentários: {$a->slot_comments}

Sistema de Mensagens do Moodle';
$string['edit_notify_teacher:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, os detalhes do horário em {$a->date} às {$a->time} foram alterados por {$a->sendername}.

Professor(es): {$a->slot_teacher}
Local: {$a->slot_location}
Máx. de participantes: {$a->slot_maxparticipants}
Comentários: {$a->slot_comments}

Sistema de Mensagens do Moodle';
$string['edit_notify_teacher:group:smallmessage'] = 'Os detalhes do horário em {$a->date} às {$a->time} foram alterados por {$a->sendername}.';
$string['edit_notify_teacher:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Detalhes do agendamento alterados';
$string['edit_notify_teacher:smallmessage'] = 'Os detalhes do horário em {$a->date} às {$a->time} foram alterados por {$a->sendername}.';
$string['edit_notify_teacher:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Detalhes do agendamento alterados';
$string['edit_submit'] = 'Confirmar alterações';
$string['emailteachers'] = 'Enviar notificações por e-mail para os professores';
$string['emailteachers_help'] = 'As notificações para professores quando um estudante se registra pela primeira vez em um horário são normalmente suprimidas para evitar spam. Marque esta opção para permitir que o organizador envie essas notificações por e-mail para os professores. Observe que as notificações para cancelamento de registro e alteração de horários são sempre enviadas.';
$string['enableprintslotuserfields'] = 'Permitir alteração nos campos de perfil do usuário';
$string['enableprintslotuserfieldsdesc'] = 'Controla se os professores podem alterar os campos de perfil de usuário padrão selecionados abaixo';
$string['err_availablefromearly'] = 'Esta data não pode ser definida após a data de início!';
$string['err_availablefromlate'] = 'Esta data não pode ser definida após a data de término!';
$string['err_availablepastdeadline'] = 'Este horário não pode ser disponibilizado após a data limite do agendador ({$a->deadline})!';
$string['err_collision'] = 'Este período colide com outros períodos:';
$string['err_comments'] = 'Você deve inserir uma descrição!';
$string['err_enddate'] = 'A data de término não pode ser definida antes da data de início!';
$string['err_fromto'] = 'A hora de término não pode ser definida antes da hora de início!';
$string['err_fullminute'] = 'A duração deve ser um minuto completo.';
$string['err_fullminutegap'] = 'O intervalo deve ser um minuto completo.';
$string['err_isgrouporganizer_app'] = 'Não é possível alterar o modo de grupo, pois já existem agendamentos programados neste organizador!';
$string['err_location'] = 'Você deve inserir um local!';
$string['err_norecipients'] = 'Nenhum destinatário foi selecionado!';
$string['err_noslots'] = 'Nenhum horário foi selecionado!';
$string['err_posint'] = 'Você deve inserir um número inteiro positivo!';
$string['err_startdate'] = 'A data de início não pode ser definida antes da data de hoje ({$a->now})!';
$string['eval_attended'] = 'Compareceu';
$string['eval_feedback'] = 'Feedback';
$string['eval_grade'] = 'Nota';
$string['eval_header'] = 'Horários selecionados';
$string['eval_link'] = 'novo agendamento';
$string['eval_no_participants'] = 'Este horário não teve participantes';
$string['eval_not_occured'] = 'Este horário ainda não ocorreu';
$string['eval_notify_newappointment:student:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, seu agendamento {$a->sendername} em {$a->date} às {$a->time} em {$a->location} foi avaliado.

Os professores do curso permitem que você se registre novamente para qualquer horário disponível no organizador {$a->organizername}.

Sistema de Mensagens do Moodle';
$string['eval_notify_newappointment:student:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, seu agendamento em grupo {$a->sendername} em {$a->date} às {$a->time} em {$a->location} foi avaliado.

Os professores do curso permitem que você se registre novamente para qualquer horário disponível no organizador {$a->coursefullname}.

Sistema de Mensagens do Moodle';
$string['eval_notify_newappointment:student:group:smallmessage'] = 'Seu agendamento em grupo em {$a->date} às {$a->time} em {$a->location} foi avaliado.';
$string['eval_notify_newappointment:student:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento avaliado';
$string['eval_notify_newappointment:student:smallmessage'] = 'Seu agendamento em {$a->date} às {$a->time} em {$a->location} foi avaliado.';
$string['eval_notify_newappointment:student:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento avaliado';
$string['eval_notify_student:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, seu agendamento {$a->sendername} em {$a->date} às {$a->time} em {$a->location} foi avaliado.

Sistema de Mensagens do Moodle';
$string['eval_notify_student:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, seu agendamento em grupo {$a->sendername} em {$a->date} às {$a->time} em {$a->location} foi avaliado.

Sistema de Mensagens do Moodle';
$string['eval_notify_student:group:smallmessage'] = 'Seu agendamento em grupo em {$a->date} às {$a->time} em {$a->location} foi avaliado.';
$string['eval_notify_student:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento avaliado';
$string['eval_notify_student:smallmessage'] = 'Seu agendamento em {$a->date} às {$a->time} em {$a->location} foi avaliado.';
$string['eval_notify_student:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento avaliado';
$string['evaluate'] = 'Avaliar';
$string['event'] = 'Evento de calendário';
$string['eventappointmentadded'] = 'Estudante registrado em um horário.';
$string['eventappointmentassigned'] = 'Agendamento foi atribuído pelo professor.';
$string['eventappointmentcommented'] = 'Agendamento foi comentado.';
$string['eventappointmentdeleted'] = 'Agendamento foi excluído pelo professor.';
$string['eventappointmentevaluated'] = 'Agendamento foi avaliado.';
$string['eventappointmentlistprinted'] = 'Lista de agendamentos foi impressa.';
$string['eventappointmentremindersent'] = 'Lembrete de agendamento enviado.';
$string['eventappointmentremoved'] = 'Estudante cancelou o registro de um horário.';
$string['eventappwith:group'] = 'Agendamento em grupo';
$string['eventappwith:single'] = 'Agendamento';
$string['eventnoparticipants'] = 'Sem participantes';
$string['eventqueueadded'] = 'Adicionado à lista de espera';
$string['eventqueueremoved'] = 'Removido da lista de espera';
$string['eventregistrationsviewed'] = 'Aba de registros visualizada.';
$string['eventslotcreated'] = 'Novos horários criados.';
$string['eventslotdeleted'] = 'Horário excluído.';
$string['eventslotupdated'] = 'Horário atualizado.';
$string['eventslotviewed'] = 'Horários visualizados.';
$string['eventteacheranonymous'] = 'um professor anônimo';
$string['eventtemplate'] = '{$a->courselink} / {$a->organizerlink}: {$a->appwith} {$a->with} {$a->participants}<br />Local: {$a->location}<br />';
$string['eventtemplatecomment'] = 'Comentário:<br />{$a}<br />';
$string['eventtemplatewithoutlinks'] = '{$a->coursename} / {$a->organizername}: {$a->appwith} {$a->with} {$a->participants}<br />Local: {$a->location}<br />';
$string['eventtitle'] = '{$a->coursename} / {$a->organizername}: {$a->appwith}';
$string['eventwith'] = 'com';
$string['eventwithout'] = 'com';
$string['exportics'] = 'Exportar ICS';
$string['exporticsaction'] = 'exportar ICS';
$string['exportsettings'] = 'Configurações de exportação';
$string['filtertable'] = '\'Filtrando esta tabela\'';
$string['filtertable_help'] = 'Pesquise por strings mútuas nestes horários aqui.';
$string['finalgrade'] = 'Este valor foi definido no livro de notas e não pode ser alterado com o organizador.';
$string['font_large'] = 'grande';
$string['font_medium'] = 'médio';
$string['font_small'] = 'pequeno';
$string['format'] = 'Formato';
$string['format_csv_comma'] = 'CSV (;)';
$string['format_csv_tab'] = 'CSV (tab)';
$string['format_ods'] = 'ODS';
$string['format_pdf'] = 'PDF';
$string['format_xls'] = 'XLS';
$string['format_xlsx'] = 'XLSX';
$string['fulldatelongtemplate'] = '%A %d. %B %Y';
$string['fulldatetemplate'] = '%a %d.%m.%Y';
$string['fulldatetimelongtemplate'] = '%A %d. %B %Y %H:%M';
$string['fulldatetimetemplate'] = '%a %d.%m.%Y %H:%M';
$string['fullname_template'] = '{$a->firstname} {$a->lastname}';
$string['gap'] = 'Intervalo';
$string['gap_help'] = 'Define o intervalo entre dois agendamentos.';
$string['grade'] = 'Nota máxima';
$string['grade_help'] = 'Define a quantidade máxima de pontos que pode ser concedida para qualquer agendamento que possa ser avaliado.';
$string['gradeaggregationmethod'] = 'Método de agregação';
$string['gradeaggregationmethod_help'] = 'A agregação determina como as notas em uma categoria são combinadas, como:

* Média das notas - A soma de todas as notas dividida pelo número total de notas
* Nota mais baixa
* Nota mais alta
* Natural - A soma de todos os valores de nota';
$string['grading_desc_grade'] = 'A avaliação está ativa.';
$string['grading_desc_nograde'] = 'A avaliação não está ativa.';
$string['group_registration_notify:student:queue:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, {$a->sendername} adicionou seu grupo {$a->groupname} à lista de espera do horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['group_registration_notify:student:queue:group:smallmessage'] = '{$a->sendername} adicionou seu grupo {$a->groupname} à lista de espera do horário em {$a->date} às {$a->time}.';
$string['group_registration_notify:student:queue:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo adicionado à lista de espera';
$string['group_registration_notify:student:register:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, {$a->sendername} registrou seu grupo {$a->groupname} para o horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['group_registration_notify:student:register:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, {$a->sendername} registrou seu grupo {$a->groupname} para o horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['group_registration_notify:student:register:group:smallmessage'] = '{$a->sendername} registrou seu grupo {$a->groupname} para o horário em {$a->date} às {$a->time}.';
$string['group_registration_notify:student:register:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo registrado';
$string['group_registration_notify:student:register:smallmessage'] = '{$a->sendername} registrou seu grupo {$a->groupname} para o horário em {$a->date} às {$a->time}.';
$string['group_registration_notify:student:register:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo registrado';
$string['group_registration_notify:student:reregister:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, {$a->sendername} registrou novamente seu grupo {$a->groupname} para um novo horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['group_registration_notify:student:reregister:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, {$a->sendername} registrou novamente seu grupo {$a->groupname} para um novo horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['group_registration_notify:student:reregister:group:smallmessage'] = '{$a->sendername} registrou novamente seu grupo {$a->groupname} para um novo horário em {$a->date} às {$a->time}.';
$string['group_registration_notify:student:reregister:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo registrado novamente';
$string['group_registration_notify:student:reregister:smallmessage'] = '{$a->sendername} registrou novamente seu grupo {$a->groupname} para um novo horário em {$a->date} às {$a->time}.';
$string['group_registration_notify:student:reregister:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo registrado novamente';
$string['group_registration_notify:student:unqueue:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, {$a->sendername} removeu seu grupo {$a->groupname} da lista de espera do horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['group_registration_notify:student:unqueue:group:smallmessage'] = '{$a->sendername} removeu seu grupo {$a->groupname} da lista de espera do horário em {$a->date} às {$a->time}.';
$string['group_registration_notify:student:unqueue:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo removido da lista de espera';
$string['group_registration_notify:student:unregister:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, {$a->sendername} cancelou o registro de seu grupo {$a->groupname} do horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['group_registration_notify:student:unregister:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, {$a->sendername} cancelou o registro de seu grupo {$a->groupname} do horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['group_registration_notify:student:unregister:group:smallmessage'] = '{$a->sendername} cancelou o registro de seu grupo {$a->groupname} do horário em {$a->date} às {$a->time}.';
$string['group_registration_notify:student:unregister:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo com registro cancelado';
$string['group_registration_notify:student:unregister:smallmessage'] = '{$a->sendername} cancelou o registro de seu grupo {$a->groupname} do horário em {$a->date} às {$a->time}.';
$string['group_registration_notify:student:unregister:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo com registro cancelado';
$string['group_slot_available'] = 'Horário disponível';
$string['group_slot_full'] = 'Horário preenchido';
$string['groupmodeexistingcoursegroups'] = 'Usar grupos de curso existentes';
$string['groupmodenogroups'] = 'Sem agendamentos em grupo';
$string['groupmodeslotgroups'] = 'Criação de grupo por horário vazio';
$string['groupmodeslotgroupsappointment'] = 'Criação de grupo por horário reservado';
$string['groupoptions'] = 'Configurações de grupo';
$string['grouporganizer_desc'] = 'Este é um organizador de grupo.';
$string['grouporganizer_desc_novalidgroup'] = 'Este é um organizador de grupo. Você não é membro de um grupo incluído nesta instância do organizador!';
$string['grouporganizer_desc_participant'] = 'Este é um organizador de grupo. Clicar no botão registrar irá registrar você e todos os membros do seu grupo {$a->groupname} neste horário. Todos os membros do grupo podem alterar e comentar o registro.';
$string['grouppicker'] = 'Seletor de grupo';
$string['groupwarning'] = 'Verifique as opções de grupo abaixo!';
$string['headerfooter'] = 'Imprimir cabeçalho/rodapé';
$string['headerfooter_help'] = 'Imprimir cabeçalho/rodapé se marcado';
$string['hidecalendar'] = 'Ocultar calendário';
$string['hidecalendar_help'] = 'Marque para ocultar o calendário neste organizador';
$string['hour'] = 'h';
$string['hour_pl'] = 'h';
$string['id'] = 'ID';
$string['img_title_due'] = 'O horário é agendável';
$string['img_title_evaluated'] = 'O horário foi avaliado';
$string['img_title_full'] = 'O horário está cheio';
$string['img_title_no_participants'] = 'O horário não teve participantes';
$string['img_title_past_deadline'] = 'O horário passou da sua data limite';
$string['img_title_pending'] = 'O horário está pendente de avaliação';
$string['includetraineringroups'] = 'Incluir treinador em grupos';
$string['includetraineringroups_help'] = 'Se você marcar a caixa, não apenas os participantes do horário, mas também seus treinadores, serão incluídos nos grupos.';
$string['infobox_app_countdown'] = 'Tempo restante para o agendamento: {$a->days} dias, {$a->hours} horas, {$a->minutes} minutos, {$a->seconds} segundos';
$string['infobox_app_inprogress'] = 'O agendamento está em andamento.';
$string['infobox_app_occured'] = 'O agendamento já ocorreu.';
$string['infobox_appointmentsstatus_pl'] = '{$a->tooless} agendamento(s) está(ão) em falta. Existem {$a->places} lugares livres em {$a->slots} horário(s) futuro(s).';
$string['infobox_appointmentsstatus_sg'] = '{$a->tooless} agendamento(s) está(ão) em falta. Existe {$a->places} lugar livre em {$a->slots} horário(s) futuro(s).';
$string['infobox_counter_slotrows'] = 'horários exibidos.';
$string['infobox_deadline_countdown'] = 'Tempo restante para a data limite: {$a->days} dias, {$a->hours} horas, {$a->minutes} minutos, {$a->seconds} segundos';
$string['infobox_deadline_passed'] = 'A data limite de registro passou. Você não pode mais alterar os registros.';
$string['infobox_deadline_passed_slot'] = 'xxx horário(s) não pôde(puderam) ser criado(s) porque a data limite de registro passou.';
$string['infobox_deadline_passed_slotphp'] = '{$a->slots} horário(s) não pôde(puderam) ser criado(s) porque a data limite de registro passou.';
$string['infobox_deadlines_title'] = 'Datas limite';
$string['infobox_description_title'] = 'Descrição do organizador';
$string['infobox_feedback_title'] = 'Feedback';
$string['infobox_group'] = 'Meu grupo: {$a->groupname}';
$string['infobox_link'] = 'Mostrar/Ocultar';
$string['infobox_messages_title'] = 'Mensagens do sistema';
$string['infobox_messaging_title'] = '';
$string['infobox_minmax'] = 'Agendamentos por usuário: Mínimo {$a->min} - Máximo {$a->max}.';
$string['infobox_mycomments_title'] = 'Meus comentários';
$string['infobox_myslot_noslot'] = 'Você não está registrado em nenhum horário no momento.';
$string['infobox_myslot_title'] = 'Meus horários';
$string['infobox_myslot_userslots_left'] = 'Você tem {$a->left} agendamento(s) restante(s).';
$string['infobox_myslot_userslots_left_group'] = 'Seu grupo tem {$a->left} agendamento(s) restante(s).';
$string['infobox_myslot_userslots_max_reached'] = 'Você agendou a quantidade máxima de {$a->max} horário(s).';
$string['infobox_myslot_userslots_max_reached_group'] = 'Seu grupo agendou a quantidade máxima de {$a->max} horário(s).';
$string['infobox_myslot_userslots_min_not_reached'] = 'Você ainda não agendou a quantidade mínima necessária de {$a->min} horário(s).';
$string['infobox_myslot_userslots_min_not_reached_group'] = 'Seu grupo ainda não agendou a quantidade mínima necessária de {$a->min} horário(s).';
$string['infobox_myslot_userslots_min_reached'] = 'Você agendou com sucesso a quantidade mínima necessária de {$a->min} horário(s).';
$string['infobox_myslot_userslots_min_reached_group'] = 'Seu grupo agendou a quantidade mínima necessária de {$a->min} agendamentos.';
$string['infobox_myslot_userslots_status'] = '{$a->booked} de {$a->max} horários agendados.';
$string['infobox_organizer_expired'] = 'Este organizador expirou em {$a->date} às {$a->time}';
$string['infobox_organizer_expires'] = 'Este organizador irá expirar em {$a->date} às {$a->time}.';
$string['infobox_organizer_never_expires'] = 'Este organizador não expira.';
$string['infobox_registrationstatistic_title'] = 'Resumo';
$string['infobox_showallparticipants'] = 'Mostrar todos os participantes';
$string['infobox_showfreeslots'] = 'Apenas horários livres';
$string['infobox_showhiddenslots'] = 'Horários ocultos';
$string['infobox_showmyslotsonly'] = 'Apenas meus horários';
$string['infobox_showregistrationsonly'] = 'Apenas horários agendados';
$string['infobox_showslots'] = 'Também horários passados';
$string['infobox_slotoverview_title'] = 'Visão geral do horário';
$string['infobox_slotsviewoptions'] = 'Opções de filtro especiais';
$string['infobox_slotsviewoptions_help'] = 'Estas opções de filtro são combinadas por conjunções AND!';
$string['infobox_statistic_maxreached'] = '{$a->maxreached} de {$a->entries} participantes agendaram o máximo de {$a->max} horário(s).';
$string['infobox_statistic_maxreached_group'] = '{$a->maxreached} de {$a->entries} grupos agendaram o máximo de {$a->max} horário(s).';
$string['infobox_statistic_minreached'] = '{$a->minreached} de {$a->entries} participantes agendaram a quantidade necessária de {$a->min} horário(s).';
$string['infobox_statistic_minreached_group'] = '{$a->minreached} de {$a->entries} grupos atingiram a quantidade necessária de {$a->min} horário(s).';
$string['infobox_title'] = 'Caixa de informações';
$string['introeditor_error'] = 'A descrição do organizador deve ser fornecida!';
$string['invalidgrouping'] = 'Você deve selecionar um agrupamento válido!';
$string['inwaitingqueue'] = 'Lista de espera';
$string['isgrouporganizer'] = 'Agendamentos em grupo';
$string['isgrouporganizer_help'] = 'Marque esta opção se você quiser que este organizador lide com grupos em vez de usuários individuais.
\'Usar grupos existentes\': Um único membro do grupo agenda um horário para o grupo.
\'Criação de grupo por horário vazio\': Um grupo do curso é criado para cada novo horário.
\'Criação de grupo por horário agendado\': Um grupo do curso é criado para cada horário agendado.';
$string['location'] = 'Local';
$string['location_help'] = 'O local onde o horário acontece.';
$string['locationlink'] = 'URL do link do local';
$string['locationlink_help'] = 'Digite aqui o endereço completo do site para o qual você quer que o link do local se refira. Este site deve pelo menos conter informações sobre como chegar ao local. Por favor, digite o endereço completo (incluindo http://)';
$string['locationlinkenable'] = 'autolink de informações de local';
$string['locationmandatory'] = 'A entrada do local do horário é obrigatória';
$string['locationsettings'] = 'Configurações de local do horário';
$string['maillink'] = 'O organizador está disponível <a href=\'{$a}\'>aqui</a>.';
$string['maxparticipants'] = 'Máx. de participantes';
$string['maxparticipants_help'] = 'Define o número máximo de estudantes que podem se registrar para estes horários. No caso de um organizador de grupo, este número é sempre limitado a um.';
$string['message_autogenerated2'] = 'Mensagem gerada automaticamente';
$string['message_custommessage'] = 'Mensagem personalizada';
$string['message_custommessage_help'] = 'Use este campo para inserir um texto pessoal na mensagem gerada automaticamente.';
$string['message_error_action_notallowed'] = 'Esta ação não é mais possível. Por favor, volte e atualize seu navegador!';
$string['message_error_groupsynchronization'] = 'A sincronização do grupo de horários falhou!';
$string['message_error_noactionchosen'] = 'Por favor, escolha uma ação antes de pressionar o botão Iniciar.';
$string['message_error_slot_full_group'] = 'Este horário já está ocupado!';
$string['message_error_slot_full_single'] = 'Este horário não tem mais lugares livres!';
$string['message_error_unknown_unqueue'] = 'Sua entrada na lista de espera não pôde ser removida! Erro desconhecido.';
$string['message_error_unknown_unregister'] = 'Seu registro não pôde ser removido! Erro desconhecido.';
$string['message_info_appointment_deleted'] = 'O agendamento foi excluído. O participante foi notificado.';
$string['message_info_appointment_deleted_group'] = 'Os agendamentos de um grupo foram excluídos. Os participantes foram notificados.';
$string['message_info_appointment_not_deleted'] = 'Ocorreu um problema ao excluir o(s) agendamento(s).';
$string['message_info_queued'] = 'Você foi adicionado à lista de espera de um horário.';
$string['message_info_queued_group'] = 'Seu grupo foi adicionado à lista de espera de um horário';
$string['message_info_registered'] = 'Você se registrou com sucesso para um horário.';
$string['message_info_registered_group'] = 'Seu grupo se registrou com sucesso para um horário.';
$string['message_info_reminders_sent_pl'] = '{$a->count} lembretes foram enviados.';
$string['message_info_reminders_sent_sg'] = '{$a->count} lembrete foi enviado.';
$string['message_info_reregistered'] = 'Você se registrou novamente com sucesso para um horário.';
$string['message_info_reregistered_group'] = 'Seu grupo se registrou novamente com sucesso para um horário.';
$string['message_info_slots_added_pl'] = '{$a->count} novos horários foram adicionados.';
$string['message_info_slots_added_sg'] = '{$a->count} novo horário foi adicionado.';
$string['message_info_slots_deleted_pl'] = '{$a->deleted} horários foram excluídos. {$a->notified} participante(s) foi(ram) notificado(s).';
$string['message_info_slots_deleted_sg'] = 'Um horário foi excluído. {$a->notified} participante(s) foi(ram) notificado(s).';
$string['message_info_slots_edited_pl'] = '{$a->count} horários foram editados.';
$string['message_info_slots_edited_sg'] = '{$a->count} horário foi editado.';
$string['message_info_slots_evaluated_pl'] = '{$a->count} participantes foram avaliados.';
$string['message_info_slots_evaluated_sg'] = '{$a->count} participante foi avaliado.';
$string['message_info_unqueued'] = 'Você foi removido da lista de espera de um horário.';
$string['message_info_unqueued_group'] = 'Seu grupo foi removido da lista de espera de um horário.';
$string['message_info_unregistered'] = 'Você cancelou o registro de um horário com sucesso.';
$string['message_info_unregistered_group'] = 'Seu grupo cancelou o registro de um horário com sucesso.';
$string['message_warning_no_slots_added'] = 'Nenhum novo horário foi adicionado!';
$string['message_warning_no_slots_selected'] = 'Você deve selecionar pelo menos um horário primeiro!';
$string['message_warning_no_visible_slots_selected'] = 'Você deve selecionar pelo menos um horário VISÍVEL primeiro!';
$string['messageprovider:appointment_reminder_student'] = 'Lembrete de agendamento do organizador';
$string['messageprovider:appointment_reminder_teacher'] = 'Lembrete de agendamento do organizador (Professor)';
$string['messageprovider:appointmentdeleted_notify_student'] = 'Agendamento do organizador excluído';
$string['messageprovider:assign_notify_student'] = 'Atribuição do organizador pelo professor';
$string['messageprovider:assign_notify_teacher'] = 'Atribuição do organizador';
$string['messageprovider:edit_notify_student'] = 'Mudanças no organizador';
$string['messageprovider:edit_notify_teacher'] = 'Mudanças no organizador (Professor)';
$string['messageprovider:eval_notify_student'] = 'Notificação de avaliação do organizador';
$string['messageprovider:group_registration_notify_student'] = 'Notificação de registro em grupo do organizador';
$string['messageprovider:manual_reminder_student'] = 'Lembrete manual de agendamento do organizador';
$string['messageprovider:register_notify_teacher'] = 'Notificação de registro do organizador';
$string['messageprovider:register_notify_teacher_queue'] = 'Notificação de entrada na fila do organizador';
$string['messageprovider:register_notify_teacher_register'] = 'Notificação de registro do organizador';
$string['messageprovider:register_notify_teacher_reregister'] = 'Notificação de novo registro do organizador';
$string['messageprovider:register_notify_teacher_unqueue'] = 'Notificação de saída da fila do organizador';
$string['messageprovider:register_notify_teacher_unregister'] = 'Notificação de cancelamento de inscrição do organizador';
$string['messageprovider:register_promotion_student'] = 'Notificação de promoção da fila do organizador';
$string['messageprovider:register_reminder_student'] = 'Lembrete de registro do organizador';
$string['messageprovider:slotdeleted_notify_student'] = 'Horários do organizador cancelados';
$string['messageprovider:test'] = 'Mensagem de Teste do Organizador';
$string['messages_all'] = 'Todos os registros, novos registros e cancelamentos de registro';
$string['messages_none'] = 'Nenhuma notificação de registro';
$string['messages_re_unreg'] = 'Apenas novos registros e cancelamentos de registro';
$string['min'] = 'min';
$string['min_pl'] = 'mins';
$string['modformwarningplural'] = 'Estes campos não podem ser editados, pois já existem agendamentos feitos neste organizador!';
$string['modformwarningsingular'] = 'Este campo não pode ser editado, pois já existem agendamentos feitos neste organizador!';
$string['modulename'] = 'Organizador';
$string['modulename_help'] = 'Organizadores permitem que os professores agendem horários com os estudantes, criando horários para os quais os estudantes podem se registrar.';
$string['modulenameplural'] = 'Organizadores';
$string['monthlyview'] = 'Visualização mensal';
$string['multimember'] = 'Usuários não podem pertencer a vários grupos de curso!';
$string['multimemberspecific'] = 'O usuário {$a->username} {$a->idnumber} está registrado em mais de um grupo! ({$a->groups})';
$string['multipleappointmentenddate'] = 'Data de término';
$string['multipleappointmentstartdate'] = 'Data de início';
$string['mymoodle_app_slot'] = 'Agendamento em {$a->date} às {$a->time}';
$string['mymoodle_attended'] = '{$a->attended}/{$a->total} estudantes completaram um agendamento';
$string['mymoodle_attended_group'] = '{$a->attended}/{$a->total} grupos completaram um agendamento';
$string['mymoodle_attended_group_short'] = '{$a->attended} de {$a->total} grupos compareceram a pelo menos um agendamento';
$string['mymoodle_attended_short'] = '{$a->attended} de {$a->total} participantes compareceram a pelo menos um agendamento';
$string['mymoodle_completed_app'] = 'Você completou seu agendamento em {$a->date} às {$a->time}';
$string['mymoodle_completed_app_group'] = 'Seu grupo {$a->groupname} compareceu ao agendamento em {$a->date} às {$a->time}';
$string['mymoodle_missed_app'] = 'Você não compareceu ao agendamento em {$a->date} às {$a->time}';
$string['mymoodle_missed_app_group'] = 'Seu grupo {$a->groupname} não compareceu ao agendamento em {$a->date} às {$a->time}';
$string['mymoodle_next_slot'] = 'Próximo horário em {$a->date} às {$a->time}';
$string['mymoodle_no_reg_slot'] = 'Você agendou {$a->booked} horários e ainda não atingiu o mínimo de {$a->slotsmin} horários.';
$string['mymoodle_no_reg_slot_group'] = 'Seu grupo {$a->groupname} agendou {$a->booked} horários e ainda não atingiu o mínimo de {$a->slotsmin} horários.';
$string['mymoodle_no_slots'] = 'Nenhum horário futuro';
$string['mymoodle_organizer_expired'] = 'Este organizador expirou em {$a->date} às {$a->time}. Você não pode mais usá-lo';
$string['mymoodle_organizer_expires'] = 'Este organizador expira em {$a->date} às {$a->time}';
$string['mymoodle_pending_app'] = 'Seu agendamento está pendente de avaliação';
$string['mymoodle_pending_app_group'] = 'O agendamento do seu grupo {$a->groupname} está pendente de avaliação';
$string['mymoodle_reg_slot'] = 'Você agendou {$a->booked} horários e, portanto, atingiu o mínimo de {$a->slotsmin} agendamentos.';
$string['mymoodle_reg_slot_group'] = 'Seu grupo {$a->groupname} agendou {$a->booked} horários e, portanto, atingiu o mínimo de {$a->slotsmin} agendamentos.';
$string['mymoodle_registered'] = '{$a->registered}/{$a->total} estudantes se registraram para um agendamento';
$string['mymoodle_registered_group'] = '{$a->registered}/{$a->total} grupos se registraram para um agendamento';
$string['mymoodle_registered_group_short'] = '{$a->registered} de {$a->total} grupos agendaram o mínimo de {$a->slotsmin} horários';
$string['mymoodle_registered_short'] = '{$a->registered} de {$a->total} participantes agendaram o mínimo de {$a->slotsmin} horários';
$string['mymoodle_upcoming_app'] = 'Seu agendamento ocorrerá em {$a->date} às {$a->time} em {$a->location}';
$string['mymoodle_upcoming_app_group'] = 'O agendamento do seu grupo, {$a->groupname}, ocorrerá em {$a->date} às {$a->time} em {$a->location}';
$string['newslot'] = 'Adicionar mais horários';
$string['no_due_my_slots'] = 'Todos os seus horários neste organizador expiraram e/ou estão ocultos';
$string['no_due_slots'] = 'Todos os horários criados neste organizador expiraram';
$string['no_my_slots'] = 'Você não tem horários criados neste organizador';
$string['no_slots'] = 'Não há horários criados neste organizador';
$string['no_slots_defined'] = 'Não há horários de agendamento disponíveis no momento.';
$string['no_slots_defined_teacher'] = 'Não há horários de agendamento presentes no momento. Clique <a href=\'{$a->link}\'>aqui</a> para criar alguns agora.';
$string['nocalendareventslotcreation'] = 'Sem eventos de calendário para horários vazios';
$string['nocalendareventslotcreation_help'] = 'Se você marcar esta opção, nenhum evento de calendário será criado ao criar horários. Apenas agendamentos criarão eventos de calendário de horário.';
$string['nofreeslots'] = 'Nenhum horário livre disponível.';
$string['nogroup'] = 'Nenhum grupo';
$string['nolocationplaceholder'] = '[A ser definido]';
$string['noparticipants'] = 'Nenhum participante';
$string['noreregistrations'] = 'Sem novos registros após a data limite';
$string['noreregistrations_help'] = 'Se um horário agendado atingiu a data limite, ele não pode mais ser a fonte de um novo registro.';
$string['norightpage'] = 'Você não tem permissão para acessar esta página.';
$string['nosingleslotprintfields'] = 'A impressão não é possível. Nenhum campo de usuário foi definido. Veja as configurações do organizador.';
$string['noslots'] = 'Nenhum horário para ';
$string['noslotsselected'] = 'Nenhum horário selecionado!';
$string['notificationtime'] = 'Lembrete de agendamento relativo';
$string['notificationtime_help'] = 'Define com que antecedência do agendamento registrado o estudante deve ser lembrado dele.';
$string['novalidparticipants'] = 'Nenhum participante válido';
$string['numentries'] = 'Entradas mostradas por página';
$string['numentries_help'] = 'Escolha \'ótimo\' para otimizar a distribuição das entradas da lista de acordo com o tamanho do texto e a orientação da página escolhidos, se houver muitos participantes registrados em seu curso';
$string['organizer'] = 'Organizador';
$string['organizer:addinstance'] = 'Adicionar um novo organizador';
$string['organizer:addslots'] = 'Adicionar novos horários';
$string['organizer:assignslots'] = 'Atribuir horário a um estudante';
$string['organizer:comment'] = 'Adicionar comentários';
$string['organizer:deleteappointments'] = 'Excluir agendamentos';
$string['organizer:deleteslots'] = 'Excluir horários existentes';
$string['organizer:editslots'] = 'Editar horários existentes';
$string['organizer:evalslots'] = 'Avaliar horários concluídos';
$string['organizer:leadslots'] = 'Liderar horários';
$string['organizer:printslots'] = 'Imprimir horários existentes';
$string['organizer:receivemessagesstudent'] = 'Receber mensagens enviadas para estudantes';
$string['organizer:receivemessagesteacher'] = 'Receber mensagens enviadas para professores';
$string['organizer:register'] = 'Registrar-se para um horário';
$string['organizer:sendreminders'] = 'Enviar lembretes de registro para estudantes';
$string['organizer:unregister'] = 'Cancelar registro de um horário';
$string['organizer:viewallslots'] = 'Ver todos os horários como professor';
$string['organizer:viewmyslots'] = 'Ver meus próprios horários como professor';
$string['organizer:viewregistrations'] = 'Ver status dos registros de estudantes';
$string['organizer:viewstudentview'] = 'Ver todos os horários como estudante';
$string['organizer_remind_all_no_recepients'] = 'Não há destinatários válidos.';
$string['organizer_remind_all_recepients_pl'] = 'Um total de {$a->count} mensagens será enviado para os seguintes destinatários:';
$string['organizer_remind_all_recepients_sg'] = 'Um total de {$a->count} mensagem será enviado para o seguinte destinatário:';
$string['organizer_remind_all_title'] = 'Enviar lembretes';
$string['organizercommon'] = 'Configurações do organizador';
$string['organizername'] = 'Nome do organizador';
$string['orientationlandscape'] = 'paisagem';
$string['orientationportrait'] = 'retrato';
$string['otherheader'] = 'Outro';
$string['pageorientation'] = 'Orientação da página';
$string['participants'] = 'Participante(s)';
$string['participants_help'] = 'Lista de participante(s) que agendaram este horário.';
$string['pasttimeslotstring'] = 'xxx horários não puderam ser criados porque a criação de horários passados não é permitida.';
$string['pasttimeslotstringphp'] = '{$a->slots} horários não puderam ser criados porque a criação de horários passados não é permitida.';
$string['pdf_notactive'] = 'não ativo';
$string['pdfsettings'] = 'Configurações de PDF';
$string['places_inqueue'] = '{$a->inqueue} na lista de espera';
$string['places_inqueue_withposition'] = 'Você é o {$a->queueposition}. na lista de espera';
$string['places_taken_pl'] = '{$a->numtakenplaces}/{$a->totalplaces} lugares ocupados';
$string['places_taken_sg'] = '{$a->numtakenplaces}/{$a->totalplaces} lugar ocupado';
$string['pluginadministration'] = 'Administração do organizador';
$string['pluginname'] = 'Organizador';
$string['position'] = 'Posição na fila';
$string['print_return'] = 'Voltar para a visão geral do horário';
$string['printout'] = 'Impressão';
$string['printpreview'] = 'Visualização de impressão (primeiras 10 entradas)';
$string['printslotuserfieldsnotenabled'] = 'A função de Campos de Usuário de Horário de Impressão não está ativada pelo administrador.';
$string['printsubmit'] = 'Exibir tabela para impressão';
$string['privacy:metadata:applicantidappointment'] = 'Identificador do usuário que agendou este horário para o grupo.';
$string['privacy:metadata:applicantidqueue'] = 'Identificador do usuário que fez esta entrada na fila de espera para o grupo.';
$string['privacy:metadata:attended'] = 'Se o usuário ou grupo compareceu ou não ao horário.';
$string['privacy:metadata:comments'] = 'Os comentários dos treinadores para este horário.';
$string['privacy:metadata:feedback'] = 'O feedback dos treinadores ao avaliar o horário.';
$string['privacy:metadata:grade'] = 'A nota que o usuário ou grupo recebeu para este horário.';
$string['privacy:metadata:groupidappointment'] = 'Identificador do grupo de usuários que agendou este horário.';
$string['privacy:metadata:groupidqueue'] = 'Identificador do grupo que fez esta entrada na fila de espera para um horário.';
$string['privacy:metadata:organizerslotappointments'] = 'Tabela na qual os agendamentos de horários são armazenados.';
$string['privacy:metadata:organizerslotqueues'] = 'Tabela na qual as entradas da fila de espera para horários são armazenadas.';
$string['privacy:metadata:organizerslottrainer'] = 'Tabela na qual os treinadores de um horário são armazenados.';
$string['privacy:metadata:showfreeslotsonly'] = 'Preferência do usuário: A tabela de horários deve exibir apenas horários livres.';
$string['privacy:metadata:showhiddenslots'] = 'Preferência do usuário: A tabela de horários deve exibir horários ocultos.';
$string['privacy:metadata:showmyslotsonly'] = 'Preferência do usuário: A tabela de horários deve exibir apenas meus horários.';
$string['privacy:metadata:showpasttimeslots'] = 'Preferência do usuário: A tabela de horários deve exibir também horários passados.';
$string['privacy:metadata:showregistrationsonly'] = 'Preferência do usuário: A tabela de horários deve exibir apenas registros.';
$string['privacy:metadata:teacherapplicantid'] = 'Identificador do treinador que atribuiu este horário a um participante ou grupo.';
$string['privacy:metadata:teacherapplicanttimemodified'] = 'Hora em que um treinador atribuiu este horário a um participante ou grupo.';
$string['privacy:metadata:trainerid'] = 'Identificador de um treinador de um horário.';
$string['privacy:metadata:useridappointment'] = 'Identificador do usuário que agendou este horário.';
$string['privacy:metadata:useridqueue'] = 'Identificador do usuário que fez esta entrada na fila de espera para um horário.';
$string['queue'] = 'Filas de espera';
$string['queue_help'] = 'Filas de espera permitem que os usuários se registrem para um horário mesmo que o número máximo de participantes já tenha sido atingido. Os usuários são adicionados a uma fila de espera e atribuídos ao horário (em ordem) assim que um lugar se torna disponível.';
$string['recipientname'] = '_nome do destinatário_';
$string['reg_not_occured'] = 'Este horário ainda não ocorreu';
$string['reg_status'] = 'Status do registro';
$string['reg_status_not_registered'] = 'Não registrado';
$string['reg_status_organizer_expired'] = 'Organizador expirado';
$string['reg_status_registered'] = 'Registrado';
$string['reg_status_slot_attended'] = 'Compareceu';
$string['reg_status_slot_available'] = 'Horário disponível';
$string['reg_status_slot_expired'] = 'Horário expirado';
$string['reg_status_slot_full'] = 'Horário cheio';
$string['reg_status_slot_not_attended'] = 'Não compareceu';
$string['reg_status_slot_past_deadline'] = 'Horário passou da data limite';
$string['reg_status_slot_pending'] = 'Horário pendente de avaliação';
$string['register_notify_teacher:queue:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, o estudante {$a->sendername} entrou na fila de espera para o horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['register_notify_teacher:queue:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, o estudante {$a->sendername} adicionou o grupo {$a->groupname} à lista de espera para o horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['register_notify_teacher:queue:group:smallmessage'] = 'O estudante {$a->sendername} adicionou o grupo {$a->groupname} à lista de espera para o horário em {$a->date} às {$a->time} em {$a->location}.';
$string['register_notify_teacher:queue:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo adicionado à lista de espera';
$string['register_notify_teacher:queue:smallmessage'] = 'O estudante {$a->sendername} entrou na fila de espera para o horário em {$a->date} às {$a->time} em {$a->location}.';
$string['register_notify_teacher:queue:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Estudante entrou na fila de espera';
$string['register_notify_teacher:register:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, o estudante {$a->sendername} se registrou para o horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['register_notify_teacher:register:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, o estudante {$a->sendername} registrou o grupo {$a->groupname} para o horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['register_notify_teacher:register:group:smallmessage'] = 'O estudante {$a->sendername} registrou o grupo {$a->groupname} para o horário em {$a->date} às {$a->time} em {$a->location}.';
$string['register_notify_teacher:register:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo registrado';
$string['register_notify_teacher:register:smallmessage'] = 'O estudante {$a->sendername} se registrou para o horário em {$a->date} às {$a->time} em {$a->location}.';
$string['register_notify_teacher:register:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Estudante registrado';
$string['register_notify_teacher:reregister:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, o estudante {$a->sendername} se registrou novamente para o novo horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['register_notify_teacher:reregister:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, o estudante {$a->sendername} registrou novamente o grupo {$a->groupname} para o novo horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['register_notify_teacher:reregister:group:smallmessage'] = 'O estudante {$a->sendername} registrou novamente o grupo {$a->groupname} para o horário em {$a->date} às {$a->time} em {$a->location}.';
$string['register_notify_teacher:reregister:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo registrado novamente';
$string['register_notify_teacher:reregister:smallmessage'] = 'O estudante {$a->sendername} se registrou novamente para o horário em {$a->date} às {$a->time} em {$a->location}.';
$string['register_notify_teacher:reregister:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Estudante registrado novamente';
$string['register_notify_teacher:unqueue:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, o estudante {$a->sendername} saiu da lista de espera do horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['register_notify_teacher:unqueue:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, o estudante {$a->sendername} removeu o grupo {$a->groupname} da lista de espera do horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['register_notify_teacher:unqueue:group:smallmessage'] = 'O estudante {$a->sendername} removeu o grupo {$a->groupname} da lista de espera do horário em {$a->date} às {$a->time} em {$a->location}.';
$string['register_notify_teacher:unqueue:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo removido da lista de espera';
$string['register_notify_teacher:unqueue:smallmessage'] = 'O estudante {$a->sendername} saiu da lista de espera do horário em {$a->date} às {$a->time} em {$a->location}.';
$string['register_notify_teacher:unqueue:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Estudante removido da lista de espera';
$string['register_notify_teacher:unregister:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, o estudante {$a->sendername} cancelou o registro do horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['register_notify_teacher:unregister:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, o estudante {$a->sendername} cancelou o registro do grupo {$a->groupname} do horário em {$a->date} às {$a->time} em {$a->location}.

Sistema de Mensagens do Moodle';
$string['register_notify_teacher:unregister:group:smallmessage'] = 'O estudante {$a->sendername} cancelou o registro do grupo {$a->groupname} do horário em {$a->date} às {$a->time} em {$a->location}.';
$string['register_notify_teacher:unregister:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Grupo com registro cancelado';
$string['register_notify_teacher:unregister:smallmessage'] = 'O estudante {$a->sendername} cancelou o registro do horário em {$a->date} às {$a->time} em {$a->location}.';
$string['register_notify_teacher:unregister:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Estudante com registro cancelado';
$string['register_promotion_student:fullmessage'] = 'Seu registro para um horário foi promovido do status \'lista de espera\' para o status \'agendado\'.';
$string['register_promotion_student:group:fullmessage'] = 'O registro do seu grupo para um horário foi promovido do status \'lista de espera\' para o status \'agendado\'.';
$string['register_promotion_student:group:smallmessage'] = 'O registro do seu grupo para um horário foi promovido do status \'lista de espera\' para o status \'agendado\'.';
$string['register_promotion_student:group:subject'] = 'Organizador do Moodle: Grupo promovido da fila';
$string['register_promotion_student:smallmessage'] = 'Seu registro para um horário foi promovido do status \'lista de espera\' para o status \'agendado\'.';
$string['register_promotion_student:subject'] = 'Organizador do Moodle: Promovido da fila';
$string['register_reminder_student:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, você ainda não se registrou para a quantidade solicitada de horários.

{$a->custommessage}

Sistema de Mensagens do Moodle';
$string['register_reminder_student:group:fullmessage'] = 'Olá, {$a->receivername}!

Como parte do curso {$a->courseid} {$a->coursefullname}, seu grupo {$a->groupname} ainda não se registrou para a quantidade solicitada de horários.

{$a->custommessage}

Sistema de Mensagens do Moodle';
$string['register_reminder_student:group:smallmessage'] = 'Por favor, registre seu grupo para a quantidade solicitada de horários.';
$string['register_reminder_student:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Lembrete de registro';
$string['register_reminder_student:smallmessage'] = 'Por favor, registre-se para a quantidade solicitada de horários.';
$string['register_reminder_student:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Lembrete de registro';
$string['relative_deadline_before'] = 'antes do agendamento';
$string['relative_deadline_now'] = 'A partir de agora';
$string['relativedeadline'] = 'Data limite relativa';
$string['relativedeadline_help'] = 'Define a data limite para a aplicação para um horário específico com antecedência. Os estudantes não poderão mais se registrar ou cancelar o registro depois que a data limite expirar.';
$string['remindall_desc'] = 'Enviar lembretes a todos os participantes sem agendamento';
$string['remindallmultiple_desc'] = 'Enviar lembretes a todos os participantes sem agendamentos suficientes';
$string['requiremodintro'] = 'Exigir descrição da atividade';
$string['reset_organizer_all'] = 'Excluindo horários, agendamentos e eventos relacionados';
$string['resetorganizerall'] = 'Limpar todos os dados do organizador (horários e agendamentos)';
$string['reviewsubmit'] = 'Revisar horários';
$string['rewievslotsheader'] = 'Revisar horários';
$string['search:activity'] = 'Organizador - informações da atividade';
$string['searchfilter'] = 'Buscar / Filtrar';
$string['sec'] = 'seg';
$string['sec_pl'] = 'segs';
$string['select'] = 'Selecionar horários';
$string['select_all_entries'] = 'Selecionar todas as entradas';
$string['select_all_slots'] = 'Selecionar todos os horários visíveis';
$string['select_help'] = 'Selecione um ou mais horários com os quais você deseja trabalhar.';
$string['selectedgrouplist'] = 'Grupos selecionados';
$string['selectedslots'] = 'Horários selecionados';
$string['showmore'] = 'Mostrar mais';
$string['signature'] = 'Assinatura';
$string['singleslotcommands'] = 'Ação de horário único';
$string['singleslotcommands_help'] = 'Clique em um botão de ação para trabalhar diretamente em um horário.';
$string['singleslotprintfield'] = 'Imprimir campo de usuário do horário';
$string['singleslotprintfield0'] = 'Imprimir campo de usuário do horário';
$string['singleslotprintfield0_help'] = 'Estes campos de usuário são usados para cada participante quando um único horário é impresso.';
$string['singleslotprintfields'] = 'Campos de perfil de usuário de horário de impressão única';
$string['singleslotprintfields_help'] = 'Nesta seção, você define campos pessoais adicionais a serem impressos para cada participante quando um único horário é impresso.';
$string['slot'] = 'Agendamento';
$string['slot_anonymous'] = 'Horário anônimo';
$string['slot_slotvisible'] = 'Membros visíveis apenas se for seu próprio horário';
$string['slot_visible'] = 'Membros do horário sempre visíveis';
$string['slotassignedby'] = 'Horário atribuído por';
$string['slotdeleted_notify_student:fullmessage'] = 'Olá, {$a->receivername}!

Seu agendamento no curso {$a->courseshortname} em {$a->date} às {$a->time} em {$a->location} foi cancelado.';
$string['slotdeleted_notify_student:group:fullmessage'] = 'Olá, {$a->receivername}!

Seu agendamento no curso {$a->courseshortname} em {$a->date} às {$a->time} em {$a->location} foi cancelado.';
$string['slotdeleted_notify_student:group:smallmessage'] = 'Seu agendamento em {$a->date} às {$a->time} no organizador \'{$a->organizername}\' foi cancelado.';
$string['slotdeleted_notify_student:group:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento cancelado';
$string['slotdeleted_notify_student:smallmessage'] = 'Seu agendamento em {$a->date} às {$a->time} no organizador \'{$a->organizername}\' foi cancelado.';
$string['slotdeleted_notify_student:subject'] = '[{$a->courseid}{$a->courseshortname} / {$a->organizername}] - Agendamento cancelado';
$string['slotdetails'] = 'Detalhes do horário';
$string['slotfrom'] = 'de';
$string['slotlistempty'] = 'Nenhum horário foi encontrado.';
$string['slotoptionstable'] = '\'Estendendo esta tabela\'';
$string['slotoptionstable_help'] = 'Mostrar também horários passados ou ocultos.';
$string['slotperiodendtime'] = 'Data de término';
$string['slotperiodheader'] = 'Gerar horários para o intervalo de datas';
$string['slotperiodheader_help'] = 'Especifique a data de início e de término para as quais os intervalos de tempo diários (seção abaixo) se aplicarão. Especifique aqui também se o horário deve ser visível para os estudantes.';
$string['slotperiodstarttime'] = 'Data de início';
$string['slottimeframesheader'] = 'Intervalos de tempo específicos';
$string['slottimeframesheader_help'] = 'Esta seção permite a definição, baseada no dia da semana, de intervalos de tempo que serão preenchidos com horários de agendamento com as propriedades especificadas acima. Pode haver mais de um intervalo de tempo por dia. Se um intervalo de tempo na segunda-feira for selecionado, ele irá gerar horários para cada segunda-feira entre a data de início e de término (inclusive).';
$string['slotto'] = 'para';
$string['status'] = 'Detalhes do status';
$string['status_help'] = 'Status atual deste horário.';
$string['status_no_entries'] = 'Este organizador não tem estudantes registrados.';
$string['stroptimal'] = 'ótimo';
$string['studentcomment_title'] = 'Comentários do estudante';
$string['synchronizegroupmembers'] = 'Sincronizar membros do grupo';
$string['synchronizegroupmembers_help'] = 'Se os membros do grupo do Moodle mudarem, as mudanças serão aplicadas aos horários agendados.';
$string['taballapp'] = 'Agendamentos';
$string['tabstatus'] = 'Status do registro';
$string['tabstud'] = 'Visão do estudante';
$string['teacher'] = 'Professor';
$string['teacher_help'] = 'Lista de treinadores deste horário.';
$string['teacher_unchanged'] = '-- inalterado --';
$string['teachercomment_title'] = 'Comentários do professor';
$string['teacherfeedback_title'] = 'Feedback do professor';
$string['teacherid'] = 'Treinador';
$string['teacherid_help'] = 'Selecione o treinador que você quer que lidere os agendamentos';
$string['teacherinvisible'] = 'Professor invisível';
$string['teachervisible'] = 'Professor visível';
$string['teachervisible_help'] = 'Marque esta opção se você quiser permitir que os estudantes vejam o professor associado ao horário.';
$string['textsize'] = 'Tamanho do texto';
$string['th_actions'] = 'Ação';
$string['th_appdetails'] = 'Detalhes';
$string['th_attended'] = 'Comp.';
$string['th_bookings'] = 'Total de Agendamentos';
$string['th_comments'] = 'Comentário do Participante';
$string['th_datetime'] = 'Data e hora';
$string['th_datetimedeadline'] = 'Data e hora';
$string['th_details'] = 'Status';
$string['th_duration'] = 'Duração';
$string['th_email'] = 'E-mail';
$string['th_evaluated'] = 'Aval.';
$string['th_feedback'] = 'Feedback';
$string['th_firstname'] = 'Primeiro nome';
$string['th_grade'] = 'Nota';
$string['th_group'] = 'Grupo';
$string['th_groupname'] = 'Grupo';
$string['th_idnumber'] = 'Número de identificação';
$string['th_lastname'] = 'Sobrenome';
$string['th_location'] = 'Local';
$string['th_participant'] = 'Participante';
$string['th_participants'] = 'Participantes';
$string['th_status'] = 'Status';
$string['th_teacher'] = 'Professor';
$string['th_teachercomments'] = 'Comentário do professor';
$string['timeshift'] = 'Deslocando data limite absoluta';
$string['timeslot'] = 'Horário do Organizador';
$string['timetemplate'] = '%H:%M';
$string['title_add'] = 'Adicionar novos horários de agendamento';
$string['title_comment'] = 'Editar seus comentários';
$string['title_delete'] = 'Excluir horários selecionados';
$string['title_delete_appointment'] = 'Excluir agendamento atribuído';
$string['title_edit'] = 'Editar horários selecionados';
$string['title_eval'] = 'Avaliar horários selecionados';
$string['title_print'] = 'Imprimir horários';
$string['totalday'] = 'xxx horários para yyy pessoas';
$string['totalday_groups'] = 'xxx horários para yyy grupos';
$string['totalslots'] = 'de {$a->starttime} a {$a->endtime}, {$a->duration} {$a->unit} cada, {$a->totalslots} horário(s) no total';
$string['totaltotal'] = 'Total: xxx horários para yyy pessoas';
$string['totaltotal_groups'] = 'Total: xxx horários para yyy grupos';
$string['trainer'] = 'Treinador';
$string['trainerid'] = 'Professor';
$string['trainerid_help'] = 'Selecione o professor que você quer que lidere os agendamentos';
$string['unavailableslot'] = 'Este horário está disponível a partir de';
$string['unknown'] = 'Desconhecido';
$string['userslots_mingreatermax'] = 'O mínimo de horários do usuário é maior que o máximo.';
$string['userslotsdailymax'] = 'Máximo de horários por participante ou grupo por dia';
$string['userslotsdailymax_help'] = 'Quantidade de horários que um participante ou grupo pode agendar por dia. \'0\' significa que não há limite diário.';
$string['userslotsmax'] = 'Máximo de horários por participante ou grupo';
$string['userslotsmax_help'] = 'Quantidade de horários que um participante ou grupo pode agendar.';
$string['userslotsmin'] = 'Mínimo de horários por participante ou grupo';
$string['userslotsmin_help'] = 'Mínimo de horários que um participante ou grupo precisa agendar.';
$string['visibility'] = 'Visibilidade dos membros - predefinição';
$string['visibility_all'] = 'Visível';
$string['visibility_anonymous'] = 'Anônimo';
$string['visibility_help'] = 'Definição da opção de visibilidade padrão com a qual um novo horário será criado.<br/><b>Anônimo:</b> Os membros deste horário são sempre invisíveis para todos.<br/><b>Visível:</b> Todos os membros deste horário são sempre visíveis para todos.<br/><b>Apenas visível para os membros do horário:</b> Apenas os membros do horário podem se ver.';
$string['visibility_slot'] = 'Apenas visível para os membros do horário';
$string['visible'] = 'Horário visível';
$string['waitinglists_desc_active'] = 'Listas de espera estão ativadas.';
$string['waitinglists_desc_notactive'] = 'Listas de espera não estão ativadas.';
$string['warning_groupingid'] = 'Modo de grupo ativado. Você deve selecionar um agrupamento válido.';
$string['warninggroupmode'] = 'Você deve ativar o modo de grupo e selecionar um agrupamento para criar um organizador de grupo!';
$string['warningtext1'] = 'Os horários selecionados contêm valores diferentes neste campo!';
$string['warningtext2'] = 'AVISO! O conteúdo deste campo foi alterado!';
$string['weekdaylabel'] = 'Horário do dia da semana';
$string['with'] = 'com';
