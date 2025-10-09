<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the For Software Foundation, either version 3 of the License, or
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
 * Auto-loader to ensure our interceptor runs early.
 *
 * @package     mod_quizscheduler
 * @copyright   2025 Oliveira. Mannuh <oliveira.mannuh@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Auto-loader class for quiz scheduler.
 */
class mod_quizscheduler_autoloader {
    
    /**
     * Initialize the interceptor.
     */
    public static function init() {
        // Check if we're dealing with a quiz request
        if (self::is_quiz_request()) {
            require_once(__DIR__ . '/global_interceptor.php');
            require_once(__DIR__ . '/quiz_access_manager.php');
            
            // Run interceptor
            \mod_quizscheduler\global_interceptor::intercept_quiz_requests();
        }
    }
    
    /**
     * Check if current request is quiz-related.
     */
    private static function is_quiz_request() {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Check for quiz module URLs
        if (strpos($uri, '/mod/quiz/') !== false) {
            return true;
        }
        
        // Check for quiz parameters
        if (isset($_GET['id']) || isset($_GET['quizid']) || isset($_GET['attempt'])) {
            return true;
        }
        
        return false;
    }
}

// Auto-initialize
mod_quizscheduler_autoloader::init();
