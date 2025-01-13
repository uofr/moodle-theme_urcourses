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
 * Test student enrol external function.
 *
 * @module  theme_urcourses
 * @author  2024 John Lane <john.lane@uregina.ca>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_urcourses\external;

use \core_external\external_api;
use \core_external\external_function_parameters;
use \core_external\external_value;

require_once($CFG->dirroot . '/theme/urcourses/locallib.php');
require_once($CFG->dirroot . "/user/lib.php");

defined('MOODLE_INTERNAL') || die();

class enrol_test_student extends external_api {
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT)
        ]);
    }

    public static function execute_returns() {
        return new external_value(PARAM_BOOL);
    }

    public static function execute($courseid) {
        global $DB, $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid
        ]);

        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);

        $email = "$USER->username+urstudent@uregina.ca";
        $user = $DB->get_record('user', ['email' => $email]);

        if (!$user) {
            throw new \moodle_exception('teststudentnotexist', 'theme_urcourses');
        }

        if (is_enrolled($context, $user->id)) {
            throw new \moodle_exception('teststudentalreadyenrolled', 'theme_urcourses');
        }

        $enrolplugin = enrol_get_plugin('manual');
        $instances = enrol_get_instances($params['courseid'], true);
        $enrolinstance = null;
        foreach ($instances as $instance) {
            if ($instance->enrol == 'manual') {
                $enrolinstance = $instance;
                break;
            }
        }

        if ($enrolinstance == null) {
            throw new \moodle_exception('noenrolmethod', 'theme_urcourses');
        }

        $enrolplugin->enrol_user($enrolinstance, $user->id);

        if (!is_enrolled($context, $user->id)) {
            throw new \moodle_exception('teststudentcouldnotenrol', 'theme_urcourses');
        }

        return true;
    }
}