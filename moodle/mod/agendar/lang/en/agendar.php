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

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Schedule';
$string['modulenameplural'] = 'Schedules';
$string['modulename_help'] = 'The schedule activity allows teachers to create available time slots for students to book meetings, consultations, presentations or other activities.';
$string['pluginname'] = 'Schedule';
$string['pluginadministration'] = 'Schedule administration';

// Capabilities
$string['agendar:addinstance'] = 'Add a new schedule activity';
$string['agendar:view'] = 'View schedule activity';
$string['agendar:book'] = 'Make bookings';
$string['agendar:manageslots'] = 'Manage available time slots';
$string['agendar:viewallbookings'] = 'View all bookings';

// Form strings
$string['agendarname'] = 'Schedule name';
$string['bookingsettings'] = 'Booking settings';
$string['maxbookingsperuser'] = 'Maximum bookings per user';
$string['maxbookingsperuser_help'] = 'Maximum number of slots each user can book';
$string['allowcancellation'] = 'Allow cancellations';
$string['allowcancellation_help'] = 'If enabled, users will be able to cancel their bookings';
$string['cancellationdeadline'] = 'Cancellation deadline';
$string['cancellationdeadlinenone'] = 'No deadline';
$string['emailnotifications'] = 'Email notifications';
$string['emailnotifications_help'] = 'Send confirmation emails and reminders';

// Slot management
$string['manageslots'] = 'Manage time slots';
$string['addslot'] = 'Add time slot';
$string['starttime'] = 'Start time';
$string['endtime'] = 'End time';
$string['maxbookings'] = 'Maximum bookings';
$string['currentbookings'] = 'Current bookings';
$string['location'] = 'Location';
$string['notes'] = 'Notes';
$string['visible'] = 'Visible';
$string['datetime'] = 'Date and time';
$string['availability'] = 'Availability';
$string['actions'] = 'Actions';

// Booking strings
$string['book'] = 'Book';
$string['cancel'] = 'Cancel';
$string['yourbookings'] = 'Your bookings';
$string['availableslots'] = 'Available time slots';
$string['noavailableslots'] = 'No time slots available at the moment';
$string['maxbookingsreached'] = 'You have reached the maximum number of bookings';
$string['bookingsuccess'] = 'Booking successful!';
$string['bookingfailed'] = 'Booking failed';
$string['cancellationsuccess'] = 'Booking cancelled successfully!';
$string['cancellationfailed'] = 'Cancellation failed';
$string['confirmcancel'] = 'Are you sure you want to cancel this booking?';
$string['bookingtime'] = 'Booking date';

// Slot management messages
$string['slotadded'] = 'Time slot added successfully';
$string['slotdeleted'] = 'Time slot deleted successfully';
$string['slothasbookings'] = 'Cannot delete a time slot that has bookings';
$string['noslots'] = 'No time slots have been created yet';
$string['confirmdelete'] = 'Are you sure you want to delete this time slot?';
$string['viewbookings'] = 'View bookings';
$string['bookingsfor'] = 'Bookings for';

// Validation messages
$string['endtimebeforestart'] = 'End time must be after start time';
$string['starttimepast'] = 'Start time must be in the future';
$string['maxbookingsmin'] = 'Maximum bookings must be at least 1';

// Events
$string['eventcoursemoduleviewed'] = 'Schedule module viewed';
$string['eventslotcreated'] = 'Time slot created';
$string['eventslotbooked'] = 'Time slot booked';
$string['eventslotcancelled'] = 'Booking cancelled';
