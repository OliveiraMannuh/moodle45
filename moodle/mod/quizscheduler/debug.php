<?php
require(__DIR__.'/../../config.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('quizscheduler', $id, 0, false, MUST_EXIST);
$moduleinstance = $DB->get_record('quizscheduler', array('id' => $cm->instance), '*', MUST_EXIST);

require_login();

echo "<h1>Debug QuizScheduler</h1>";

echo "<h2>Module Instance:</h2>";
echo "<pre>" . print_r($moduleinstance, true) . "</pre>";

echo "<h2>All Slots:</h2>";
$all_slots = $DB->get_records('quizscheduler_slots', array('quizschedulerid' => $moduleinstance->id));
echo "<pre>" . print_r($all_slots, true) . "</pre>";

echo "<h2>Available Slots:</h2>";
$available_slots = mod_quizscheduler\manager::get_available_slots($moduleinstance->id);
echo "<pre>" . print_r($available_slots, true) . "</pre>";

echo "<h2>User Bookings:</h2>";
$user_bookings = mod_quizscheduler\manager::get_user_bookings($moduleinstance->id, $USER->id);
echo "<pre>" . print_r($user_bookings, true) . "</pre>";

echo "<h2>Current Time:</h2>";
echo "Now: " . time() . " (" . date('Y-m-d H:i:s') . ")<br>";

echo "<h2>User Capabilities:</h2>";
$context = context_module::instance($cm->id);
echo "view: " . (has_capability('mod/quizscheduler:view', $context) ? 'YES' : 'NO') . "<br>";
echo "book: " . (has_capability('mod/quizscheduler:book', $context) ? 'YES' : 'NO') . "<br>";
echo "manage: " . (has_capability('mod/quizscheduler:manageslots', $context) ? 'YES' : 'NO') . "<br>";