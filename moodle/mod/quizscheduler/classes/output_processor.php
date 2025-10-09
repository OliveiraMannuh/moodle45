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
 * Output processor for quiz access control.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quizscheduler\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Custom renderer for quiz access control.
 */
class renderer extends \plugin_renderer_base {

    /**
     * Process output and intercept quiz access.
     */
    protected function render_quiz_access_check($data) {
        // This method will be called to intercept quiz rendering.
        \mod_quizscheduler\hook_callbacks::intercept_quiz_access();
        return '';
    }
}
