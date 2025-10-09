<?php
require_once('../../config.php');
require_once('lib.php');

$quizid = required_param('id', PARAM_INT);
$quiz = $DB->get_record('quiz', array('id' => $quizid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $quiz->course), '*', MUST_EXIST);

require_login($course, true);

$PAGE->set_url('/mod/quizscheduler/schedule.php', array('id' => $quizid));
$PAGE->set_title('Agendar Questionário');
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

// Formulário de agendamento
$mform = new \mod_quizscheduler\forms\schedule_form();

if ($data = $mform->get_data()) {
    // Processar agendamento
    try {
        \mod_quizscheduler\scheduler::schedule_quiz($quizid, $USER->id, $data->scheduled_time);
        echo $OUTPUT->notification('Questionário agendado com sucesso!', 'success');
    } catch (Exception $e) {
        echo $OUTPUT->notification($e->getMessage(), 'error');
    }
}

$mform->display();
echo $OUTPUT->footer();