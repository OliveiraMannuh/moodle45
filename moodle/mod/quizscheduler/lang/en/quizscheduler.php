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
 * English strings for mod_quizscheduler.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Quiz Scheduler';
$string['modulename'] = 'Quiz Scheduler';
$string['modulenameplural'] = 'Quiz Schedulers';
$string['pluginadministration'] = 'Quiz Scheduler administration';

// Capabilities.
$string['quizscheduler:addinstance'] = 'Add a new quiz scheduler instance';
$string['quizscheduler:view'] = 'View quiz scheduler';
$string['quizscheduler:book'] = 'Book quiz time slot';
$string['quizscheduler:manageslots'] = 'Manage available time slots';
$string['quizscheduler:viewreports'] = 'View booking reports';
$string['quizscheduler:managebookings'] = 'Manage user bookings';

// Form fields.
$string['name'] = 'Name';
$string['intro'] = 'Description';
$string['quizid'] = 'Quiz';
$string['quizid_help'] = 'Select the quiz that will be scheduled by users.';
$string['timeopen'] = 'Bookings open';
$string['timeopen_help'] = 'Date and time when bookings open.';
$string['timeclose'] = 'Bookings close';
$string['timeclose_help'] = 'Date and time when bookings close.';
$string['maxbookings'] = 'Maximum bookings per user';
$string['maxbookings_help'] = 'Maximum number of bookings a user can make.';
$string['slotduration'] = 'Slot duration (minutes)';
$string['slotduration_help'] = 'Duration of each booking slot in minutes.';
$string['maxusersperslot'] = 'Maximum users per slot';
$string['maxusersperslot_help'] = 'Maximum number of users who can book the same time slot.';
$string['schedulestarttime'] = 'Schedule start time';
$string['scheduleendtime'] = 'Schedule end time';

// Schedule settings
$string['schedulesettings'] = 'Schedule Settings';
$string['schedulestarttime_help'] = 'Start date and time for the scheduling period (optional).';
$string['scheduleendtime_help'] = 'End date and time for the scheduling period (optional).';

// Settings strings
$string['configurationsettings'] = 'Configuration Settings';

// General strings.
$string['availableslots'] = 'Available time slots';
$string['bookedslots'] = 'My bookings';
$string['bookslot'] = 'Book time slot';
$string['cancelbooking'] = 'Cancel booking';
$string['confirmcancel'] = 'Are you sure you want to cancel this booking?';
$string['bookingsuccess'] = 'Time slot booked successfully!';
$string['cancelsuccess'] = 'Booking cancelled successfully!';
$string['noslots'] = 'No time slots available at the moment.';
$string['nobookings'] = 'You have no bookings.';
$string['slotfull'] = 'This time slot is full.';
$string['alreadybooked'] = 'You already have a booking for this quiz.';
$string['bookingclosed'] = 'Bookings are closed.';
$string['starttime'] = 'Start time';
$string['endtime'] = 'End time';
$string['status'] = 'Status';
$string['booked'] = 'Booked';
$string['completed'] = 'Completed';
$string['missed'] = 'Missed';
$string['cancelled'] = 'Cancelled';

// Reports.
$string['reports'] = 'Reports';
$string['bookingsreport'] = 'Bookings report';
$string['user'] = 'User';
$string['timebooked'] = 'Booking date';
$string['actions'] = 'Actions';
$string['manageslots'] = 'Manage time slots';
$string['addslot'] = 'Add time slot';
$string['editslot'] = 'Edit time slot';
$string['deleteslot'] = 'Delete time slot';

// Report strings
$string['bookingreport'] = 'Booking Report';
$string['viewreports'] = 'View Reports';
$string['filterbyslot'] = 'Filter by Slot';
$string['filterbystatus'] = 'Filter by Status';
$string['allslots'] = 'All slots';
$string['allstatuses'] = 'All statuses';
$string['downloadcsv'] = 'Download CSV';
$string['nobookings'] = 'No bookings found';
$string['timebooked'] = 'Booking Time';
$string['quizstatus'] = 'Quiz Status';
$string['quizcompleted'] = 'Quiz Completed';
$string['quizinprogress'] = 'Quiz In Progress';
$string['notstarted'] = 'Not Started';
$string['inprogress'] = 'In Progress';

// Management strings.
$string['generateslots'] = 'Generate time slots';
$string['currentslots'] = 'Current time slots';
$string['maxusers'] = 'Maximum users';
$string['bookings'] = 'Bookings';
$string['expired'] = 'Expired';
$string['confirmdeleteSlot'] = 'Are you sure you want to delete this time slot?';
$string['slotdeleted'] = 'Time slot deleted successfully';
$string['slotsgenerated'] = '{$a} time slots have been generated';
$string['selectslot'] = 'Select time slot';

// Events.
$string['eventbookingcreated'] = 'Booking created';
$string['eventbookingcancelled'] = 'Booking cancelled';
$string['eventcoursemoduleviewed'] = 'Course module viewed';
$string['eventquizcompleted'] = 'Scheduled quiz completed';
$string['eventscheduledquizstarted'] = 'Scheduled quiz started';
$string['eventslotbooked'] = 'Quiz slot booked';

// Task strings.
$string['cleanupexpiredbookings'] = 'Cleanup expired bookings';

// Additional strings for pages.
$string['back'] = 'Back';
$string['timing'] = 'Timing';
$string['settings'] = 'Settings';
$string['minutes'] = 'minutes';
$string['hour'] = 'hour';
$string['hours'] = 'hours';
$string['available'] = 'available';
$string['yes'] = 'Yes';
$string['no'] = 'No';
$string['download'] = 'Download';
$string['total'] = 'Total';
$string['delete'] = 'Delete';
$string['date'] = 'Date';
$string['administration'] = 'Administration';
$string['information'] = 'Information';

// Interface strings.
$string['adminpaneldesc'] = 'Use the options below to manage time slots and view reports.';
$string['admininfo'] = 'Use the options below to manage time slots and view reports.';
$string['bookinginfo'] = 'Booking Information';
$string['schedulinginfo'] = 'Scheduling Information';
$string['bookingperiod'] = 'Booking period: {$a->open} to {$a->close}';
$string['scheduleperiod'] = 'Schedule period';
$string['until'] = 'until';
$string['slotsinfo'] = '{$a->available} of {$a->total} slots available';
$string['bookingsleft'] = 'Bookings: {$a->current} of {$a->max} used';
$string['noslotscreated'] = 'No time slots have been created yet. Contact your teacher.';
$string['contactteacher'] = 'Please contact your teacher for more information about booking time slots.';
$string['cannotbook'] = 'Cannot book';
$string['totalslots'] = 'Total slots';
$string['totalbookings'] = 'Total bookings';
$string['createslotshelp'] = 'Use the form above to generate time slots for students to book.';
$string['full'] = 'Full';
$string['disabled'] = 'Disabled';
$string['quizstatus'] = 'Quiz status';
$string['quizcompleted'] = 'Quiz completed';
$string['notstarted'] = 'Not started';

// View page strings.
$string['yourbookings'] = 'Your Bookings';
$string['noavailableslots'] = 'No available time slots at the moment.';
$string['datetime'] = 'Date and Time';
$string['availability'] = 'Availability';
$string['book'] = 'Book';
$string['cancel'] = 'Cancel';
$string['bookingsstats'] = 'Bookings';
$string['slotsused'] = 'used';
$string['of'] = 'of';
$string['bookingcancelled'] = 'Booking cancelled successfully!';
$string['slotcreated'] = 'Time slot created successfully!';

// Status strings.
$string['status_booked'] = 'Booked';
$string['status_active'] = 'Active';
$string['status_completed'] = 'Completed';
$string['status_missed'] = 'Missed';
$string['upcoming'] = 'Upcoming';

// Errors.
$string['error:noquiz'] = 'No quiz found in this course.';
$string['error:invalidslot'] = 'Invalid time slot.';
$string['error:cannotbook'] = 'Cannot book this time slot.';
$string['error:cannotcancel'] = 'Cannot cancel this booking.';
$string['error:slothasbookings'] = 'Cannot delete a time slot that has bookings';
$string['error:invalidvalue'] = 'Invalid value';
$string['error:invalidtimes'] = 'Invalid start and end times';
$string['closebeforeopen'] = 'The closing date must be after the opening date.';
$string['nopermissions'] = 'You do not have permission to access this content.';
$string['bookingnotfound'] = 'Booking not found';
$string['slotnotfound'] = 'Slot not found';
$string['nopermissiontocancelbooking'] = 'You do not have permission to cancel this booking';
$string['cancellationtoolate'] = 'Cannot cancel. Deadline is 1 hour before start time';

// Privacy.
$string['privacy:metadata'] = 'The Quiz Scheduler plugin stores information about user bookings.';
$string['privacy:metadata:quizscheduler_bookings'] = 'Information about user bookings for quiz slots.';
$string['privacy:metadata:quizscheduler_bookings:userid'] = 'The ID of the user who made the booking.';
$string['privacy:metadata:quizscheduler_bookings:timebooked'] = 'When the booking was made.';
$string['privacy:metadata:quizscheduler_bookings:status'] = 'The status of the booking.';
$string['privacy:metadata:quizscheduler_bookings:timestarted'] = 'When the quiz was started.';
$string['privacy:metadata:quizscheduler_bookings:timefinished'] = 'When the quiz was finished.';

// Access control strings.
$string['accessgranted'] = 'Access granted until {$a}';
$string['accessdenied_future'] = 'Quiz access denied. Your next scheduled time is: {$a}';
$string['accessdenied_nobooking'] = 'Quiz access denied. You need to schedule a time slot first.';
$string['teachercanbypass'] = 'Teachers can access this quiz without scheduling restrictions.';
$string['timeleftwarning'] = 'Warning: You have {$a} remaining in your scheduled time slot.';
$string['timeexpired'] = 'Your scheduled time has expired';
$string['quizforcedsubmit'] = 'Quiz was automatically submitted due to time expiration';
$string['gotoschedule'] = 'Go to Schedule';
$string['schedulefirst'] = 'You must schedule a time slot before taking this quiz.';

// Enhanced access control strings.
$string['quizaccessdenied'] = 'Quiz Access Denied';
$string['schedulerequired'] = 'Scheduling Required';
$string['accessdenied'] = 'Access Denied';
$string['backtocourse'] = 'Back to Course';

// Error strings
$string['error:cannotgenerateslots'] = 'Could not generate time slots';
$string['error:cannotdelete'] = 'Could not delete time slot';
$string['error:overlappingslots'] = 'Time slots cannot overlap';
$string['error:missingfields'] = 'Required fields not filled';
$string['active'] = 'Active';

// Adicione estas strings:
$string['inprogress'] = 'In Progress';
$string['quizinprogress'] = 'Quiz in Progress';
$string['activebookingexists'] = 'You already have an active booking. You can only book a new slot after your current booking has ended.';
$string['youhaveactivebooking'] = 'You have an active booking. Wait for it to finish before making a new booking.';
$string['youcanrebooknow'] = 'Your previous booking has finished. You can make a new booking.';
$string['bookingexpired'] = 'Previous booking finished';
$string['activebooking'] = 'Active booking';
$string['nomorebookings'] = 'You already have an active booking for this questionnaire';
$string['canbook'] = 'Available for booking';
$string['yourbookings'] = 'Your Bookings';
$string['active'] = 'Active';
$string['completed'] = 'Completed';
$string['reserved'] = 'reserved';
$string['alreadybooked'] = 'Already booked';
$string['hasactivebooking'] = 'Existing active booking';
$string['book'] = 'Book';
$string['cancel'] = 'Cancel';

// Additional strings for rebooking functionality
$string['yourbookings'] = 'Your Bookings';
$string['datetime'] = 'Date and Time';
$string['availability'] = 'Availability';
$string['reserved'] = 'booked';
$string['slotpassed'] = 'Time slot has passed';
$string['bookingsuccess'] = 'Time slot booked successfully';
$string['bookingerror'] = 'Error booking the time slot';
$string['cancellationsuccess'] = 'Booking cancelled successfully';
$string['cancellationerror'] = 'Error cancelling the booking';
$string['nobooking'] = 'No booking found to cancel';
$string['slotnotavailable'] = 'This time slot is no longer available';
$string['invalidslot'] = 'Invalid time slot';
$string['invalidaction'] = 'Invalid action';
$string['youhaveactivebooking'] = 'You already have an active booking. Wait for completion to make a new booking.';
$string['hasactivebooking'] = 'You have an active booking';

// Email notifications
$string['message_provider:slot_booking_confirmation'] = 'Quiz slot booking confirmation';
$string['email_booking_subject'] = 'Quiz slot booking confirmation: {$a->quizname}';
$string['email_booking_body'] = 'Hi {$a->studentname},

You have successfully booked a slot for the quiz "{$a->quizname}".

Booking details:
- Date: {$a->date}
- Time: {$a->starttime} - {$a->endtime}
- Quiz: {$a->quizname}
- Course: {$a->coursename}

Please make sure to attend at the scheduled time.

Best regards,
{$a->sitename}';
$string['email_booking_small'] = 'Quiz slot booked: {$a->quizname} on {$a->date} at {$a->starttime}';

$string['show'] = 'Show';
$string['all'] = 'All';
$string['slotsperpage'] = 'slots per page';
$string['showingslots'] = 'Showing {$a->start} to {$a->end} of {$a->total} slots';
